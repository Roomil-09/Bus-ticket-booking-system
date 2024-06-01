<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <!-- Link to Font Awesome stylesheet -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="icon" type="image/x-icon" href="bus logo final.png" sixe="64x64">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
            color: rgb(5, 72, 101);
            display: flex;
            height: 100vh;
        }

        .signin-container {
            background-color: #fff;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
            text-align: center;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .signin-container h2 {
            margin-bottom: 70px;
            color: rgb(5, 72, 101);
            font-size: 32px;
            font-weight: 900;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .signin-form {
            margin-top: 30px;
        }

        .form-group {
            margin-bottom: 40px;
            text-align: left;
            position: relative; /* Added */
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
            font-weight: bold;
            color: rgb(5, 72, 101);
        }

        .form-group input {
            width: calc(100% - 40px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            padding-left: 40px; 
        }

        .icon {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .form-group input[type="submit"] {
            background-color: rgb(5, 72, 101);
            color: #fff;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            width: 120px; 
            padding: 10px; 
            margin: 0 auto; 
        }

        .form-group input[type="submit"]:hover {
            background-color: #044e75;
        }

        .register-link {
            margin-top: 20px;
            color: rgb(5, 72, 101);
            font-size: 16px;
        }

        .register-link span {
            color: rgb(5, 72, 101);
        }

        .register-link a {
            color: rgb(5, 72, 101);
            text-decoration: none;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>
<?php
session_start();

// Database connection
$dbconn = pg_connect("host=localhost dbname=bus user=postgres password=1234");

// Define variables and initialize with empty values
$email = $password = "";
$error = $success = $sql_error = "";

// Form validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $error = "Email is required.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $error = "Password is required.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no validation errors, check authentication
    if (empty($error)) {
        // SQL query to check if the user exists with the provided email and password
        $query = "SELECT * FROM users WHERE email = $1 AND password = $2";
        $result = pg_query_params($dbconn, $query, array($email, $password));

        if (!$result) {
            $sql_error = "SQL Error: " . pg_last_error($dbconn);
        } else {
            // Check if a row is returned
            if (pg_num_rows($result) == 1) {
                // Authentication successful
                $_SESSION["email"] = $email;
                echo "<script>alert('You're registered!');</script>";
                header("refresh:0.5;url=home.php");
                exit();
            } else {
                // Authentication failed
                $error = "Invalid email or password. Please try again.";
            }
        }
    }
}
?>

    <div class="signin-container">
        <h2>Sign In</h2>
        <form class="signin-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <span class="icon"><i class="fa fa-envelope"></i></span>
                <input type="email" id="email" name="email" placeholder="abc@gmail.com" required>
            </div>
            <div class="form-group">
                <span class="icon"><i class="fa fa-lock"></i></span>
                <input type="password" id="password" name="password" placeholder="password" required>
            </div>
            <div class="form-group">
                <input type="submit" name="verify" value="Sign In">
            </div>
        </form>
        <div class="register-link">
            <span>Not registered? </span><a href="sign_up.php">Register</a>
        </div>
    </div>
    <!-- Display errors at the bottom of the page -->
<div class="error-message">
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($sql_error)): ?>
        <div class="error sql-error"><?php echo $sql_error; ?></div>
    <?php endif; ?>
</div>

<!-- Display success message -->
<div class="success-message">
    <?php if (!empty($success)): ?>
        <div class="success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php
if (!empty($success)) {
    echo '<script>alert("You\'re registered!");</script>';
}
?>
</div>


</body>
</html>

