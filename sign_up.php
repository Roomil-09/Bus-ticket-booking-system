<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
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

        .signup-container {
            background-color: #fff;
            padding: 20px;
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

        .signup-container h2 {
            margin-bottom: 20px;
            color: rgb(5, 72, 101);
            font-size: 32px;
            text-transform: uppercase;
            text-align: center;
            font-weight: 900;
            text-decoration: underline;
        }

        .signup-form {
            margin-top: 20px;
        }

        .form-group {
            margin-bottom: 15px;
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

        .form-group input,
        .form-group select {
            width: calc(100% - 40px);
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            padding-left: 40px; /* Added */
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
            width: calc(100% - 40px); /* Equal width to input */
            padding: 10px; /* Equal padding to input */
            margin: 0 auto;
        }

        .form-group input[type="submit"]:hover {
            background-color: #044e75;
        }

        .signin-link {
            margin-top: 20px;
            color: rgb(5, 72, 101);
            font-size: 16px;
        }

        .signin-link a {
            color: rgb(5, 72, 101);
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>
<?php
// Database connection
$dbconn = pg_connect("host=localhost dbname=bus user=postgres password=1234");

// Define variables and initialize with empty values
$fullname = $email = $mobile = $password = "";
$error = $success = $sql_error = "";

// Form validation
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate full name
    if (empty(trim($_POST["fullname"]))) {
        $error = "Full name is required.";
    } else {
        $fullname = trim($_POST["fullname"]);
    }

    // Validate email
    if (empty(trim($_POST["email"]))) {
        $error = "Email is required.";
    } elseif (!filter_var(trim($_POST["email"]), FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $email = trim($_POST["email"]);
    }

    // Validate mobile
    if (empty(trim($_POST["mobile"]))) {
        $error = "Mobile number is required.";
    } elseif (!preg_match("/^[0-9]{10}$/", trim($_POST["mobile"]))) {
        $error = "Mobile number must contain 10 digits.";
    } else {
        $mobile = trim($_POST["mobile"]);
    }

    // Validate password
    if (empty(trim($_POST["password"]))) {
        $error = "Password is required.";
    } elseif (strlen(trim($_POST["password"])) < 8) {
        $error = "Password must have at least 8 characters.";
    } else {
        $password = trim($_POST["password"]);
    }

    // If no validation errors, insert user into the database
    if (empty($error)) {
        $query = "INSERT INTO users (fullname, email, mobile, password) VALUES ('$fullname', '$email', '$mobile', '$password')";
        $result = pg_query($dbconn, $query);

        if ($result) {
            $success = "User registered successfully.";
            echo "<script>alert('You're registered!');</script>";
            header("refresh:0.5;url=signin.php");
        } else {
            $sql_error = "SQL Error: " . pg_last_error($dbconn);
        }
    }
}
?>

<div class="signup-container">
    <h2>Sign Up</h2>
    <form class="signup-form" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="form-group">
            <span class="icon"><i class="fas fa-user"></i></span>
            <input type="text" id="fullname" name="fullname" placeholder="Enter your full name" required
                   value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
        </div>
        <div class="form-group">
            <span class="icon"><i class="fas fa-envelope"></i></span>
            <input type="email" id="email" name="email" placeholder="Enter your email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
        <div class="form-group">
            <span class="icon"><i class="fas fa-phone"></i></span>
            <input type="number" id="mobile" name="mobile" placeholder="Enter your mobile number" required
                   value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
        </div>
        <div class="form-group">
            <span class="icon"><i class="fas fa-lock"></i></span>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>
        <div class="form-group">
            <input type="submit" value="Sign Up">
        </div>
    </form>
    <div class="signin-link">
        <span>Already registered? </span><a href="signin.php">Sign In</a>
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
</div>
<?php
if (!empty($success)) {
    echo '<script>alert("You\'re registered!");</script>';
}
?>
</body>
</html>
