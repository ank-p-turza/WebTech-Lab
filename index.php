<?php
session_start();
require('db.php');
$fullname = '';
$email = '';
$country = '';
$password = '';


$error = '';

if (isset($_SESSION['email'])) {
    header("Location: process.php");
}
$bgColor = ($_COOKIE['user_color'] ?? '#ffffff');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['register_btn'])) {

        $fullname = $_POST['name'];
        $email = $_POST['email'];
        $country = $_POST['country'];
        $password = $_POST['password'];
        $bgColor = $_POST['color'] ?? '#ffffff';
        setcookie("user_color", $bgColor, time() + (30 * 24 * 60 * 60), "/");
        if ((!$country) || (!$fullname) || (!$email) || (!$password)) {
            echo "Please fill all the fields";
            exit();
        } else {
            $_SESSION['name'] = $fullname;
            $_SESSION['email'] = $email;
            $_SESSION['country'] = $country;
            $_SESSION['password'] = $password;

            header("Location: process.php");
        }
    }
    if (isset($_POST['login_btn'])) {
        $email = trim($_POST['login_email']);
        $password = $_POST['login_password'];


        if (empty($email) || empty($password)) {
            $error = "Fields can not be empty.";
        } else {
            $stmt = $conn->prepare('SELECT * FROM users WHERE email = ?');
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();

                if (password_verify($password, $user['password'])) {
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['email'] = $email;
                    $_SESSION['country'] = $user['country'];
                    $_SESSION['user_id'] = $user['id'];
                    header("Location: select_cities.php");
                    exit;
                } else {
                    $error = "Invalid credentials";
                }
            } else {
                $error = "Invalid credentials";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Page</title>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/registration.css">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        .loading-dots {
            display: inline-block;
        }

        .loading-dots::after {
            content: '';
            animation: dots 1.5s infinite;
        }

        @keyframes dots {

            0%,
            20% {
                content: '.';
            }

            40% {
                content: '..';
            }

            60%,
            100% {
                content: '...';
            }
        }
    </style>
</head>

<body>
    <img src="assets/logo.png" style="width: 180px; height: 100px;">
    <div class="popup" id="popup">
        <h2 id="popup_title"></h2>
        <p id="popup_message"></p>
    </div>
    <h1>Air Quality Index</h1>
    <div class="containter">
        <div id="box-1">
            <h3></h3>
            <table>
                <thead>
                    <tr>
                        <th colspan='3'> Air Quality Index of 10 Cities</th>
                    </tr>
                    <tr>
                        <th>City</th>
                        <th>Country</th>
                        <th>AQI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require "db.php";
                    $result = $conn->query("SELECT city, country, aqi FROM aqi ORDER BY  city ASC;");
                    $count = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            if ($count > 10) {
                                echo "<tr><td>---</td><td>---</td><td>---</td></tr>";
                                if ($count > 15) break;
                            }
                            $count++;
                            echo "<tr><td>{$row['city']}</td><td>{$row['country']}</td><td>---</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='2'>No data available</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <div id="box-2">
            <div class="form-container">
                <h2>Create Account</h2>
                <form method="POST">

                    <!-- Full Name Field -->
                    <div class="form-group">
                        <div class="input-field">
                            <input type="text" name="name" id="fullname" placeholder=" " autocomplete="off">
                            <label for="fullname">Full Name</label>
                            <div class="error-message" id="fullname-error">Please enter your full name</div>
                            <div class="validation-icon" id="fullname-icon">✓</div>
                        </div>
                    </div>

                    <!-- Email Field -->
                    <div class="form-group">
                        <div class="input-field">
                            <input type="email" name="email" id="email" placeholder=" " autocomplete="off">
                            <label for="email">Email</label>
                            <div class="error-message" id="email-error">Please enter a valid email address</div>
                            <div class="validation-icon" id="email-icon">✓</div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="input-field">
                            <input type="password" name="password" id="password" placeholder=" " autocomplete="off">
                            <label for="password">Password</label>
                            <div class="error-message" id="password-error">Password must be at least 8 characters long</div>
                            <div class="validation-icon" id="password-icon">✓</div>
                        </div>
                    </div>

                    <!-- Confirm Password Field -->
                    <div class="form-group">
                        <div class="input-field">
                            <input type="password" name="confirm_password" id="confirm-password" placeholder=" " autocomplete="off">
                            <label for="confirm-password">Confirm Password</label>
                            <div class="error-message" id="confirm-password-error">Passwords do not match</div>
                            <div class="validation-icon" id="confirm-password-icon">✓</div>
                        </div>
                    </div>

                    <div class="form-group-selection">
                        <div class="selection-field">
                            <label for="your-country">Your Country: </label>
                            <select name="country" id="your_country">
                                <?php
                                require "db.php";
                                $result = $conn->query("SELECT DISTINCT country FROM aqi ORDER BY  country ASC;");
                                if ($result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        $country = $row['country'];
                                        echo "<option value='{$country}'>{$country}</option>";
                                    }
                                } else {
                                    echo "<option value='select_country'>Select country:</option>";
                                } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group-selection">
                        <label for="select-color">Select Color: </label><br>
                        <input type="color" id="select-color" name="color" value="#ffffff">
                    </div>


                    <!-- Terms and Conditions Checkbox -->
                    <div class="checkbox-container">
                        <div class="terms-text">
                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                            <input type="checkbox" id="terms" name="terms">
                            <div class="error-message" id="terms-error">You must accept the terms to continue</div>
                        </div>
                    </div>

                    <!-- Register Button -->
                    <button class="button" name="register_btn" id="register-btn" type="submit" disabled>
                        <span class="button-text">Register</span>
                        <span class="loading-dots" id="loading-spinner" style="display: none;">Loading...</span>
                    </button>
                </form>
            </div>
        </div>

        <div id="box-3">
        </div>

        <!-- Login Form-->
        <div id="box-4">
            <div class="form-container">
                <h2>Log In</h2>
                <!-- Authentication Error Message Box -->
                <div class="auth-error" id="auth-error">
                    <?php if ($error): ?>
                        <p class="auth-error-message" id="auth-error-message"><?php echo $error; ?></p>
                    <?php endif; ?>
                </div>

                <form action="index.php" method="POST" id="login-form">
                    <!-- Email/Username Field -->
                    <div class="form-group">
                        <div class="input-field">
                            <input type="text" id="login-email" name="login_email" placeholder=" " autocomplete="off">
                            <label for="email">Email</label>
                            <div class="error-message" id="login-email-error">Please enter a valid email address</div>
                            <div class="validation-icon" id="login-email-icon">✓</div>
                        </div>
                    </div>

                    <!-- Password Field -->
                    <div class="form-group">
                        <div class="input-field">
                            <input type="password" id="login-password" name="login_password" placeholder=" " autocomplete="off">
                            <label for="password">Password</label>
                            <div class="error-message" id="login-password-error">Password is required</div>
                            <div class="validation-icon" id="login-password-icon">✓</div>
                        </div>
                    </div>

                    <div class="terms-text">
                        Trouble loggin in? Try <a href="#">Forgot Password.</a>
                        <div class="error-message" id="terms-error">You must accept the terms to continue</div>
                    </div>

                    <!-- Login Button -->
                    <button class="button" id="login-btn" name="login_btn" disabled>
                        <span class="button-text">Log In</span>
                    </button>
                </form>
            </div>


            <script src="./scripts/validation.js"></script>
        </div>

    </div>
    <script>
        document.getElementById('select-color').addEventListener('input', function() {
            var selectedColor = this.value;
            // document.cookie = "user_color=" + selectedColor + "; path=/; max-age=" + (30*24*60*60);
            //document.body.style.backgroundColor = selectedColor;
        });

        // function getComplementaryColor(hex) {
        //     hex = hex.replace(/^#/, '');
        //     let r = parseInt(hex.substr(0, 2), 16);
        //     let g = parseInt(hex.substr(2, 2), 16);
        //     let b = parseInt(hex.substr(4, 2), 16);
        //     let compR = (255 - r).toString(16).padStart(2, '0');
        //     let compG = (255 - g).toString(16).padStart(2, '0');
        //     let compB = (255 - b).toString(16).padStart(2, '0');
        //     return `#${compR}${compG}${compB}`;
        // }
        // document.getElementById('select-color').addEventListener('input', function () {
        //     var selectedColor = this.value;
        //     var complementaryColor = getComplementaryColor(selectedColor);
        //     document.getElementById('login-btn').style.backgroundColor = complementaryColor;
        //     document.getElementById('register-btn').style.backgroundColor = complementaryColor;
        //     // update all the text colors to the complementary color
        //     // document.querySelectorAll('.form-container h2, .form-container label, .form-container .terms-text').forEach(function(element) {
        //     //     element.style.color = complementaryColor;
        //     // });
        // });
        // document.getElementById('select-color').dispatchEvent(new Event('input')); // Trigger the event to set initial colors
    </script>
    <!--
    <script src="scripts/validation.js"></script>
    -->
</body>

</html>