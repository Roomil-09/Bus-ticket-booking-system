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

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture values from the form
    $from = $_POST['from'] ?? '';
    $to = $_POST['to'] ?? '';
    $departure_date = $_POST['departure-date'] ?? '';

    // Save values in session variables
    $_SESSION['from'] = $from;
    $_SESSION['to'] = $to;
    $_SESSION['departure_date'] = $departure_date;

    // Redirect to the search page if the user is logged in
    if(isset($_SESSION['email'])) {
        header("Location: details.php");
        exit;
    }
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | SkyBus</title>
    <link rel="icon" type="image/x-icon" href="bus logo final.png" sixe="64x64">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="home.css">
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
</body>

</html>

    <main>
        <a class="fixedButton" href="#">
            <div class="roundedFixedBtn"><i class="fa fa-support"></i></div>
        </a>
        <div class="search-bg" style=>
            <form id="search-form" onsubmit="handleSubmit(event)" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="search-container">
                <div class="from">
                    <span class="from-icon">
                        <i class="fa fa-bus"></i>
                    </span>
                    <div class="from-box">
                        <label for="from"></label>
                        <input type="text" placeholder="FROM" id="from" name="from" autocomplete="off" required>
                    </div>
                </div>

                <div class="to">
                    <span class="to-icon">
                        <i class="fa fa-location-arrow"></i>
                    </span>
                    <div class="to-box">
                    <label for="to"></label>
                    <input type="text" placeholder="TO" id="to" name="to" autocomplete="off" required>
                    </div>
                </div>
                <div class="date">
                    <span class="calendar-icon">
                        <!--<i class="fa fa-calendar"></i>-->
                    </span>
                    <div class="date-pick">
                        <input type="date" placeholder="ONWARD DATE" id="departure-date" name="departure-date" required>
                    </div>
                </div>
                <button type="submit" class="btn">Search Buses</button>
            </div>
        </form>
        </div>
    

        <!-- Discount Vouchers Section -->
        <div class="discount-vouchers-container">
            <p style="color: rgb(5, 72, 101); font-size: 38px; text-align: center; font-weight: 500;">Get discounts on
                your bus ticket by using our voucher codes.</p>
            <div class="discount-vouchers">
                <div class="discount-voucher">
                    <h2>Get 10% Off</h2>
                    <p>Use code <strong>SAVE10</strong> for your next booking</p>
                </div>
                <div class="discount-voucher">
                    <h2>Special Offer</h2>
                    <p>Book now and get a free meal on board.</p>
                    <p>Use code <strong>FREEMEAL</strong></p>
                </div>
                <div class="discount-voucher">
                    <h2>Student Discount</h2>
                    <p>For students aged between 16 and 25. Use code <strong>STUDE16</strong>. This offer is valid for
                        all routes.</p>
                </div>
            </div>
        </div>
        <!-- Features Section -->
        <div class="features-container">
            <h2 style="color: rgb(5, 72, 101); font-weight: bold; text-align: center; margin: 20px;">Features</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="feature">
                        <img src="easy-book.png" alt="Feature 1">
                        <h4>Easy Booking</h4>
                        <p>Book your bus tickets with just a few clicks. Our intuitive interface makes the booking
                            process hassle-free.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature">
                        <img src="bus-coverage.png" alt="Feature 2">
                        <h4>Wide Coverage</h4>
                        <p>With a vast network of routes and operators, we cover various destinations to suit your
                            travel needs.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature">
                        <img src="secure-payment.png" alt="Feature 3">
                        <h4>Secure Payments</h4>
                        <p>Enjoy peace of mind with secure payment options. Your transactions are protected every step
                            of the way.</p>
                    </div>
                </div>
            </div>
        </div>
        <!--content-->
        <div class="skybus-container">
            <h2>Book Bus Tickets with Skybus</h2>
            <p>
                Now, book your bus tickets on Skybus and elevate your bus booking experience to new heights. Skybus
                ensures a seamless and affordable journey for travelers across India.
            </p>
            <p>
                To book your bus tickets on Skybus, simply fill in the required details and customize your trip
                according to your preferences. Skybus offers a wide range of options for your travel needs. Whether you
                prefer sleeper, semi-sleeper, AC/non-AC, or any other type of bus, Skybus has got you covered. You can
                easily check the availability of buses by entering your desired time and date for ticket reservation.
                Additionally, you have the flexibility to choose your preferred seat from all available options.
            </p>
            <p>
                Thanks to our advanced autofill function, your details are conveniently populated based on your past
                booking history, saving you time and effort.
            </p>

            <h3>Easy Booking and Secure Payments</h3>
            <p>
                Skybus ensures a smooth and secure payment experience for users. With Skybus, you can book your bus
                tickets in just a few clicks without any hassle. Ensure you have sufficient balance in your Skybus
                wallet for faster checkout. Having a registered Skybus wallet also offers various benefits.
            </p>
            <p>
                We provide multiple payment options including Debit/Credit Card, Net Banking, and Wallet Payment. In the
                event of a failed booking, your money is promptly refunded back to your wallet within minutes. For any
                assistance, our dedicated 24/7 helpline service is available to provide support and assistance.
            </p>

            <h3>Why Choose Skybus for Bus Reservations</h3>
            <p>
                Skybus stands out among other online bus ticket booking platforms with its user-centric features and
                services tailored to meet the needs of modern travelers. From economy class to luxury buses, Skybus
                offers a diverse range of options to suit every traveler's preferences.
            </p>
            <ul>
                <li>Free Cancellation</li>
                <li>Instant Refunds</li>
                <li>Convenient and Quick Bus Booking</li>
                <li>Exciting Cashback and Offers</li>
                <li>Best Price Guarantee</li>
                <li>24/7 Customer Assistance</li>
            </ul>

            <p>
                Skip the long queues at bus booking counters and experience hassle-free bus ticket booking with Skybus,
                right from the comfort of your home.
            </p>
        </div>



        <!-- FAQ Section -->
        <div class="accordion-container">
            <h2 style="color: rgb(5, 72, 101); font-weight: bold; text-align: center; margin-bottom: 20px;">Frequently
                Asked Questions</h2>
            <div class="accordion" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            Q. What Are The Payment Options To Book Bus Tickets On Skybus?
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Skybus offers various payment options including credit/debit cards, net banking, mobile
                            wallets, and UPI. You can choose the payment method that is most convenient for you during
                            the booking process.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Q. Can I Cancel The Tickets Once Booked?
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Yes, you can cancel your tickets after booking, but cancellation charges may apply
                            depending on the cancellation policy of the specific bus operator. It's advisable to check
                            the cancellation policy before making a booking.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Q. I Am Unable To Select A Specific Seat/Operator/Date/Route. What Do I Do?
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            If you encounter difficulties in selecting a specific seat, operator, date, or route, please
                            ensure that
                            you have entered the correct details. If the issue persists, contact our customer support
                            for assistance.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Q. Will I Have To Pay Extra When I Book Tickets Online?
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            No, you will not have to pay extra when booking tickets online. However, convenience or
                            service fees may
                            apply depending on the ticket booking platform or payment method chosen.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingFive">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                            Q. I Have Applied The Promo Code, But Did Not Receive Any Cashback. What Do I Do?
                        </button>
                    </h2>
                    <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            If you have applied a promo code but did not receive any cashback, please ensure that you
                            have entered the
                            correct code and met all the terms and conditions associated with the offer. If the issue
                            persists, contact
                            customer support for further assistance.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSix">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                            Q. My Money Has Been Deducted, But The Ticket Was Not Booked. Kindly Help.
                        </button>
                    </h2>
                    <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            If your money has been deducted but the ticket was not booked, please check your email or
                            booking history
                            for any confirmation. If there is no confirmation, contact customer support with your
                            transaction details
                            for assistance in resolving the issue.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingSeven">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                            Q. Why Am I Not Able To Pay Through A Specific Bank/Net Banking/Card?
                        </button>
                    </h2>
                    <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            If you are unable to pay through a specific bank, net banking, or card, it could be due to
                            technical
                            issues or restrictions imposed by the payment gateway. Please try again later or use an
                            alternative payment
                            method. If the problem persists, contact your bank or customer support for assistance.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingEight">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                            Q. Will I Get A Refund In Case Of A Failed Transaction?
                        </button>
                    </h2>
                    <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            Yes, you will receive a refund in case of a failed transaction. The amount deducted from
                            your account will
                            be automatically refunded to the original payment method within a few business days. If you
                            do not receive
                            the refund, contact customer support for assistance.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingNine">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                            Q. What Should I Do If I’ve Lost My Ticket?
                        </button>
                    </h2>
                    <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            If you have lost your ticket, contact customer support with your booking details. They will
                            assist you in
                            reissuing your ticket or providing alternative solutions.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingTen">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                            Q. How Can I Check Operator Information For A Particular Route?
                        </button>
                    </h2>
                    <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen"
                        data-bs-parent="#accordionExample">
                        <div class="accordion-body">
                            To check operator information for a particular route, you can visit our website or app and
                            enter the
                            departure and destination cities along with the travel date. The available operators for the
                            selected route
                            will be displayed along with their schedules and other relevant details.
                        </div>
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




</body>
<script>
    // Function to handle form submission
function handleSubmit(event) {
    event.preventDefault(); // Prevent the default form submission

    // Check if user is logged in
    <?php if(isset($_SESSION['email'])) { ?>
        // User is logged in, proceed with the search
        const from = document.getElementById('from').value;
        const to = document.getElementById('to').value;
        const departureDate = document.getElementById('departure-date').value;

        // Redirect to the search page with query parameters
        window.location.href = `search.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&departure_date=${encodeURIComponent(departureDate)}`;
    <?php } else { ?>
        // User is not logged in, display alert
        alert("You need to login first.");
    <?php } ?>
}



    // Add event listener to the logout button
    document.getElementById('logoutBtn').addEventListener('click', function() {
            // Show the logout alert
            alert("You're logged out!");
        });


        // JavaScript to fetch the departure date and set the value of the hidden input field
window.addEventListener('DOMContentLoaded', function() {
    // Fetch the departure date from the DOM or any other source
    var departureDate = document.getElementById('departure-date').value; // Example: Fetch from a form element

    // Set the value of the hidden input field
    document.getElementById('departure-date').value = departureDate;
});
</script>

</html>