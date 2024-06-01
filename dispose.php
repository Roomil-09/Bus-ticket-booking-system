<?php
session_start();

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

// Fetch bus data from the route table
$from = $_GET['from'];
$to = $_GET['to'];
$departure_date = $_GET['departure_date'];
$query = "SELECT * FROM route WHERE LOWER(origin) = LOWER($1) AND LOWER(destination) = LOWER($2)";
$result = pg_query_params($conn, $query, array($from, $to));
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Search | SkyBus</title>
    <link rel="icon" type="image/x-icon" href="bus logo final.png" sixe="64x64">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="search.css">
</head>

<body>
    <header>
        <div>
            <a href="home.php">
                <img src="bus logo final.png" alt="Logo" style="height: 100px; width: 150px; text-align:left;">
            </a>
        </div>
        <div>
            <h1 id="top">Bus Ticket Booking System</h1>
        </div>
        <div>
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
                    <li class="breadcrumb-item active" aria-current="page">Search Buses</li>
                </ol>
            </nav>
        </div>
    </div>
    <main>
        <div class="container">
            <div class="row">
                <div class="col">
                    <p style="font-weight: bolder; font-size: 18px; text-transform: uppercase;">From: <span style="font: size 24px;" id="from"></span></p>
                    <p style="font-weight: bolder; font-size: 18px; text-transform: uppercase;">To: <span id="to"></span></p>
                    <p style="font-weight: bolder; font-size: 18px; text-transform: uppercase;">Departure Date: <span id="departure-date"></span></p>
                </div>
            </div>
        </div>

        <div class="container">
            <?php
            // Loop through each row of the result
            while ($row = pg_fetch_assoc($result)) {
                echo '<div class="bus-container">';
                echo '<div class="bus-deets">';
                echo '<p class="bus-type">' . $row['bustype'] . '</p>';
                echo '</div>';
                echo '<div class="bus-details">';
                echo '<p class="departure">' . $row['departure_time'] . '</p>';
                echo '<p class="travel-time">' . $row['travel_time'] . ' hours</p>';
                echo '<p class="arrival">' . $row['arrival_time'] . '</p>';
                echo '</div>';
                echo '<p class="distance">' . $row['distance'] . ' km</p>';
                echo '<p class="price">₹' . $row['ticket_price'] . '</p>';
                // Pass bus details to JavaScript function
                echo '<button class="select-seat-button" onclick="openSeatSelectionModal(\'' . $row['origin'] . '\', \'' . $row['destination'] . '\', \'' . $row['departure_time'] . '\', \'' . $row['arrival_time'] . '\')">Select Seat</button>';
                echo '</div>';
            }
            ?>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="seatSelectionModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Add interactive seat selection here -->
                        <input type="hidden" name="selectedSeats" id="selectedSeatsInput" value="">
                        <label>Choose Seat</label>
                        <div class="bus seat2-2 border-0 p-0">
                            <div class="seat-row-1">
                                <ol class="seats">
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-1" value="1" required="" type="checkbox">
                                        <label for="seat-checkbox-1-1">
                                            1 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-2" value="2" required="" type="checkbox">
                                        <label for="seat-checkbox-1-2">
                                            2 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-3" value="3" required="" type="checkbox">
                                        <label for="seat-checkbox-1-3">
                                            3 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-4" value="4" required="" type="checkbox">
                                        <label for="seat-checkbox-1-4">
                                            4 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-2">
                                <ol class="seats">
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-5" value="5" required="" type="checkbox">
                                        <label for="seat-checkbox-1-5">
                                            5 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-6" value="6" required="" type="checkbox">
                                        <label for="seat-checkbox-1-6">
                                            6 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-7" value="7" required="" type="checkbox">
                                        <label for="seat-checkbox-1-7">
                                            7 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-8" value="8" required="" type="checkbox">
                                        <label for="seat-checkbox-1-8">
                                            8 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-3">
                                <ol class="seats">
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-9" value="9" required="" type="checkbox">
                                        <label for="seat-checkbox-1-9">
                                            9 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-10" value="10" required="" type="checkbox">
                                        <label for="seat-checkbox-1-10">
                                            10 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-11" value="11" required="" type="checkbox">
                                        <label for="seat-checkbox-1-11">
                                            11 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-12" value="12" required="" type="checkbox">
                                        <label for="seat-checkbox-1-12">
                                            12 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-4">
                                <ol class="seats">
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-13" value="13" required="" type="checkbox">
                                        <label for="seat-checkbox-1-13">
                                            13 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-14" value="14" required="" type="checkbox">
                                        <label for="seat-checkbox-1-14">
                                            14 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-15" value="15" required="" type="checkbox">
                                        <label for="seat-checkbox-1-15">
                                            15 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-16" value="16" required="" type="checkbox">
                                        <label for="seat-checkbox-1-16">
                                            16 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-5">
                                <ol class="seats">
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-17" value="17" required="" type="checkbox">
                                        <label for="seat-checkbox-1-17">
                                            17 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-18" value="18" required="" type="checkbox">
                                        <label for="seat-checkbox-1-18">
                                            18 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-19" value="19" required="" type="checkbox">
                                        <label for="seat-checkbox-1-19">
                                            19 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-20" value="20" required="" type="checkbox">
                                        <label for="seat-checkbox-1-20">
                                            20 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-6">
                                <ol class="seats">
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-21" value="21" required="" type="checkbox">
                                        <label for="seat-checkbox-1-21">
                                            21 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-22" value="22" required="" type="checkbox">
                                        <label for="seat-checkbox-1-22">
                                            22 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-23" value="23" required="" type="checkbox">
                                        <label for="seat-checkbox-1-23">
                                            23 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-24" value="24" required="" type="checkbox">
                                        <label for="seat-checkbox-1-24">
                                            24 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-7">
                                <ol class="seats">
                                    <li class="seat">
                                        <label for="seat-checkbox-1-BLANK" style="background: none;"></label>
                                    </li>
                                    <li class="seat">
                                        <label for="seat-checkbox-1-BLANK" style="background: none;"></label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-25" value="25" required="" type="checkbox">
                                        <label for="seat-checkbox-1-25">
                                            25 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-26" value="26" required="" type="checkbox">
                                        <label for="seat-checkbox-1-26">
                                            26 </label>
                                    </li>
                                </ol>
                            </div>
                            <div class="seat-row-8">
                                <ol class="seats">
                                    <li class="seat">
                                        <label for="seat-checkbox-1-BLANK" style="background: none;"></label>
                                    </li>
                                    <li class="seat">
                                        <label for="seat-checkbox-1-BLANK" style="background: none;"></label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-27" value="27" required="" type="checkbox">
                                        <label for="seat-checkbox-1-27">
                                            27 </label>
                                    </li>
                                    <li class="seat">
                                        <input role="input-passenger-seat" name="passengers[1][seat]" id="seat-checkbox-1-28" value="28" required="" type="checkbox">
                                        <label for="seat-checkbox-1-28">
                                            28 </label>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <div class="text-left mt-2">
                            <button class="btn btn-primary btn-xs mb-2">Available</button>
                            <button class="btn btn-success btn-xs mb-2">Choosen</button>
                            <button class="btn btn-danger btn-xs mb-2">Booked</button>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="redirectToDetailsPage()">Next</button>
                    </div>

                </div>
            </div>
        </div>

    </main>
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5 class="footer-heading">About Us</h5>
                    <p class="footer-text">We are committed to providing convenient and reliable bus ticket booking
                        services to our customers. With a wide range of routes and operators, we strive to make your
                        travel experience seamless and enjoyable.</p>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-heading">Contact Us</h5>
                    <p>Email: info@busbooking.com<br> Phone: +1234567890</p>
                </div>
                <div class="col-md-4">
                    <h5 class="footer-heading">Follow Us</h5>
                    <ul class="list-unstyled footer-social">
                        <li><a href="#" class="social-link"><i class="fa fa-facebook"></i></a></li>
                        <li><a href="#" class="social-link"><i class="fa fa-twitter"></i></a></li>
                        <li><a href="#" class="social-link"><i class="fa fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
            <hr class="footer-divider">
        </div>
        <div class="text-center mt-3">
            <p class="footer-text">&copy; 2024 Bus Ticket Booking System. All rights reserved.</p>
        </div>
    </footer>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
    const params = new URLSearchParams(window.location.search);
    const from = params.get('from');
    const to = params.get('to');
    const departureDate = params.get('departure_date');

    document.getElementById('from').textContent = from;
    document.getElementById('to').textContent = to;
    document.getElementById('departure-date').textContent = departureDate;
});

// Function to open the seat selection modal
function openSeatSelectionModal(from, to, departureDate) {
    // Open modal
    var myModal = new bootstrap.Modal(document.getElementById('seatSelectionModal'));
    myModal.show();
}

// Function to redirect to details page
function redirectToDetailsPage() {
    // Get all checked checkboxes
    const selectedSeats = document.querySelectorAll('#seatSelectionModal input[type="checkbox"]:checked');
    
    // Initialize arrays to store selected seat numbers and counts
    const selectedSeatNumbers = [];
    let numberOfSelectedSeats = 0;
    
    // Iterate over checked checkboxes
    selectedSeats.forEach(function(checkbox) {
        // Increment the count of selected seats
        numberOfSelectedSeats++;
        // Push the seat number to the array
        selectedSeatNumbers.push(checkbox.value);
    });
    
    // Get the departure date from the page
    const departureDate = document.getElementById('departure-date').textContent;
    
    // Redirect to details.php with the number of selected seats, seat numbers, and departure date as query parameters
    const url = 'details.php?seats=' + numberOfSelectedSeats + '&seat_numbers=' + selectedSeatNumbers.join(',') + '&departure_date=' + departureDate;
    window.location.href = url;
}

// Add event listener to the logout button
document.getElementById('logoutBtn').addEventListener('click', function() {
    // Show the logout alert
    alert("You're logged out!");
});

// Function to update selected seats value in the hidden input field
function updateSelectedSeats() {
    // Get all checked checkboxes
    const selectedSeats = document.querySelectorAll('input[type="checkbox"]:checked');
    // Initialize an array to store selected seat numbers
    const selectedSeatNumbers = [];
    // Iterate over checked checkboxes and push their values to the array
    selectedSeats.forEach(function(checkbox) {
        selectedSeatNumbers.push(checkbox.value);
    });
    // Set the value of the hidden input field to the selected seat numbers array
    document.getElementById('selectedSeatsInput').value = selectedSeatNumbers.join(',');
}

// Add event listeners to checkboxes to update selected seats
const checkboxes = document.querySelectorAll('input[type="checkbox"]');
checkboxes.forEach(function(checkbox) {
    checkbox.addEventListener('change', updateSelectedSeats);
});


    </script>



</body>

</html>