<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    // Redirect to login page if not logged in
    header("Location: login.php");
    exit;
}

// Database connection
$dbhost = "localhost";
$dbname = "bus";
$dbuser = "postgres";
$dbpass = "1234";

$conn = pg_connect("host=$dbhost dbname=$dbname user=$dbuser password=$dbpass");

if (!$conn) {
    die("Connection failed: " . pg_last_error());
}

// Check if user is logged in
if (isset($_SESSION['email'])) {
    // User is logged in, fetch the fullname from the database
    $email = $_SESSION['email'];
    $query = "SELECT fullname FROM users WHERE email = $1";
    $result = pg_query_params($conn, $query, array($email));
    if ($result && pg_num_rows($result) > 0) {
        $row = pg_fetch_assoc($result);
        $fullname = $row['fullname'];
        // Store the fullname in the session
        $_SESSION['fullname'] = $fullname;
    }
}

// Function to sanitize input data
function sanitize($data)
{
    // Remove leading and trailing whitespaces
    $data = trim($data);
    // Remove backslashes
    $data = stripslashes($data);
    // Convert special characters to HTML entities
    $data = htmlspecialchars($data, ENT_QUOTES);
    return $data;
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve form data
    $passengerDetails = $_POST['passengerDetails'];
    $selectedSeats = json_decode($_POST['seats'], true); // Decode JSON string to PHP array
    $dateOfTravel = $_POST['departure_date'];
    $busID = $_GET['busID']; // Retrieve busID from URL query parameters
    // Retrieve the seat numbers parameter from the URL
$seatNumbers = isset($_GET['seat_numbers']) ? explode(',', $_GET['seat_numbers']) : array();

// Set the value of the selectedSeatsInput hidden input field
echo '<input type="hidden" id="selectedSeatsInput" name="selectedSeats" value="' . htmlspecialchars(json_encode($seatNumbers), ENT_QUOTES, 'UTF-8') . '">';

// Dynamically generate input fields for seat numbers
foreach ($seatNumbers as $seatNumber) {
    echo '<input type="hidden" name="seatNumbers[]" value="' . htmlspecialchars($seatNumber, ENT_QUOTES, 'UTF-8') . '">';
}


    // Prepare and execute SELECT statement to check seat availability
    $availableSeats = [];
    foreach ($selectedSeats as $seat) {
        $query = "SELECT * FROM Booking WHERE BusID = $1 AND DateOfTravel = $2 AND SeatNumber = $3";
        $result = pg_query_params($conn, $query, array($busID, $dateOfTravel, $seat));
        if (!$result) {
            echo "Error: " . pg_last_error($conn);
            exit;
        }
        if (pg_num_rows($result) == 0) {
            // Seat is available
            $availableSeats[] = $seat;
        }
    }

    if (count($availableSeats) == count($selectedSeats)) {
        // Prepare and execute INSERT statement
        $query = "INSERT INTO Booking (UserID, BusID, DateOfTravel, SeatNumber, PassengerName, PassengerEmail, PassengerPhoneNo, PassengerAge, PassengerGender) 
    VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";

        foreach ($passengerDetails as $passenger) {
            // Sanitize input data
            $passenger = array_map('sanitize', $passenger);

            // Extract passenger details
            $name = $passenger['name'];
            $email = $passenger['email'];
            $phone = $passenger['phone'];
            $age = $passenger['age'];
            $gender = $passenger['gender'];

            // Get seat number from selected seats array
            $seatNumber = $selectedSeats[$passenger['seat']];

            // Insert passenger details into the database
            $result = pg_query_params($conn, $query, array($_SESSION['userID'], $busID, $dateOfTravel, $seatNumber, $name, $email, $phone, $age, $gender));
            if (!$result) {
                echo "Error: " . pg_last_error($conn);
                exit;
            }
        }
        echo "All selected seats are available. Proceed with booking.";
    } else {
        // Some selected seats are not available
        // Display an error message
        echo "Some selected seats are not available. Please choose different seats.";
        // You can redirect back to the seat selection page or handle it based on your UI flow
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Passenger Details</title>
    <link rel="icon" type="image/x-icon" href="bus logo final.png" sixe="64x64">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="details.css">
</head>

<body>
    <!-- Header -->
    <header>
        <div>
            <a href="home.php">
                <img src="bus logo final.png" alt="Logo" style="height: 100px; width: 150px; text-align:left;">
            </a>
        </div>
        <div>
            <h1 id="top">Bus Ticket Booking System</h1>
        </div>
        <?php
            // Check if user is logged in
            if (isset($fullname)) {
                // User is logged in, display personalized greeting and logout button
                echo '<div style="font-style: italic; margin: 10px; font-size: 20px; color: rgb(5, 72, 101); padding: left 8px;">Hello,' . $fullname . '!</div>';
                echo '<a href="logout.php" class="btna btn-danger" style="color: rgb(5, 72, 101); font-size: 15px; padding-left:15px" id="logoutBtn"><i class="fa fa-sign-out"></i> Logout</a>';
            } else {
                // User is not logged in, display the dropdown button
                echo '<!-- Dropdown button -->
        <div class="dropdown" style="display: inline-block;">
            <button class="dropbtn">
                <i class="fa fa-user" style="font-size:32px"> </i>
            </button>
            <!-- Dropdown content -->
            <div class="dropdown-content">
                <a href="signin.php">Sign In</a>
                <a href="sign_up.php">Sign Up</a>
            </div>
        </div>';
            }
            ?>
        </div>
    </header>
    <div class="breadcrumb-container">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="home.php">Home</a></li>
                    <li class="breadcrumb-item"><a href="search.php">Search Buses</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Select Seats</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Passenger Details Form -->
    <form id="passengerDetailsForm" method="POST" action="details.php">
    <h2>Passenger Details</h2>
    <input type="hidden" id="selectedSeatsInput" name="selectedSeats" value="<?php echo htmlspecialchars($_GET['seats'] ?? '', ENT_QUOTES); ?>">
    <input type="hidden" id="departureDateInput" name="departureDate" value="<?php echo htmlspecialchars($_GET['departure_date'] ?? '', ENT_QUOTES); ?>">



    <!-- Passenger details will be dynamically added here -->
    <div id="passengerDetailsContainer"></div>

    <button type="submit">Submit</button>
</form>

    <!-- Ticket Modal -->
    <div class="modal fade" id="ticketModal" tabindex="-1" role="dialog" aria-labelledby="ticketModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="ticketModalLabel">Ticket Information</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="ticketInfo"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <!-- Your footer content here -->
    </footer>

    <!-- JavaScript to dynamically generate passenger details fields -->
    <script>
       document.addEventListener("DOMContentLoaded", function() {
    // Get the number of seats from the URL query parameter
    const params = new URLSearchParams(window.location.search);
    const numberOfSeats = parseInt(params.get('seats'));
    const departureDate = params.get('departure_date');
    const seatNumbers = params.get('seat_numbers').split(',');

    // Populate the departure date input field
    document.getElementById('departureDateInput').value = departureDate;

    // Generate passenger details fields based on the number of seats
    generatePassengerDetailsFields(numberOfSeats, seatNumbers);
});

// Function to dynamically generate passenger details fields
function generatePassengerDetailsFields(numberOfSeats, seatNumbers) {
    var container = document.getElementById('passengerDetailsContainer');
    container.innerHTML = ''; // Clear previous fields

    for (var i = 0; i < numberOfSeats; i++) {
        var passengerFields = `
            <div>
                <h3>Passenger ${i + 1}</h3>
                <label for="passengerName${i}">Name:</label>
                <input type="text" id="passengerName${i}" name="passengerName${i}" required>
                
                <label for="passengerAge${i}">Age:</label>
                <input type="number" id="passengerAge${i}" name="passengerAge${i}" required>
                
                <label for="passengerGender${i}">Gender:</label>
                <select id="passengerGender${i}" name="passengerGender${i}" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                    <option value="other">Other</option>
                </select>
                
                <label for="passengerEmail${i}">Email:</label>
                <input type="email" id="passengerEmail${i}" name="passengerEmail${i}" required>
                
                <label for="passengerPhone${i}">Phone:</label>
                <input type="tel" id="passengerPhone${i}" name="passengerPhone${i}" required>

                <input type="hidden" name="seatNumber${i}" value="${seatNumbers[i]}">
            </div>
        `;
        container.innerHTML += passengerFields;
    }
}


        // Event listener for form submission
        document.getElementById('passengerDetailsForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Prevent form submission
            generateTicket(); // Generate ticket
        });

        // Function to generate ticket
function generateTicket() {
    // Get form data
    var formData = new FormData(document.getElementById('passengerDetailsForm'));
    var ticketInfo = '<h4>Passenger Information:</h4>';
    // Process form data and generate ticket information
    for (var pair of formData.entries()) {
        ticketInfo += `<p><strong>${pair[0]}:</strong> ${pair[1]}</p>`;
    }
    // Display ticket information in modal
    document.getElementById('ticketInfo').innerHTML = ticketInfo;
    
    // Get the selected seats
    var selectedSeats = [];
    document.querySelectorAll('[id^="passengerDetailsContainer"] select').forEach(function(select) {
        selectedSeats.push(select.value);
    });
    
    // Set the value of the selectedSeatsInput hidden input field
    document.getElementById('selectedSeatsInput').value = JSON.stringify(selectedSeats);
    
    // Set the value of the departureDateInput hidden input field
    var departureDate = document.getElementById('departureDateInput').value;
    
    // Show the ticket modal
    $('#ticketModal').modal('show');
}

    </script>
    <!-- JavaScript libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

</body>

</html>