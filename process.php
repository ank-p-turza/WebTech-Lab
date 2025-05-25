<?php
session_start();
$fullname = '';
$email = '';
$country = ''; 
$password = '';


include "db.php";
$fullname = htmlspecialchars($_SESSION['name']);
$email = htmlspecialchars($_SESSION['email']);
$country = htmlspecialchars($_SESSION['country']);
$password = htmlspecialchars($_SESSION['password']);

$bgColor = $_COOKIE['user_color'] ?? 'rgb(255, 255, 255)'; 

if(!isset($_SESSION['email'])){
    header("Location: index.php");
}
else if(!isset($_SESSION['password'])){
    header("Location: select_cities.php");
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_button'])) {

        // check email exist statement
        $check_stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $selected_cities = 0;
        // inserting data int database sttement
        $insert_stmt = $conn->prepare("INSERT INTO users (name, email, password, country, selected_cities) VALUES (?, ?, ?, ?, ?)");  
        $insert_stmt->bind_param("ssssi", $fullname, $email, $hashed_password, $country, $selected_cities); 
        
        if($check_stmt->execute()) {
            $result = $check_stmt->get_result();
            if ($result->num_rows > 0) {
                echo "Email already exists. Please use a different email.";
            } else {
                // Insert new user data into the database
                if ($insert_stmt->execute()) {
                    // Registration successful
                    echo "Registration successful!";
                    // Clear session variables
                    unset($_SESSION['name']);
                    unset($_SESSION['email']);
                    unset($_SESSION['country']);
                    unset($_SESSION['password']);
                    echo '<script>
                        setTimeout(function() {
                            window.location.href = "index.php";
                        }, 2000); // 2000 milliseconds = 2 seconds
                    </script>';
                } else {
                    $file = fopen("error_log.txt", "a");
                    fwrite($file, "Error: " . $insert_stmt->error . "\n");
                    fclose($file);
                    echo "Registration Failed.";
                }
            }
        } else {
            $file = fopen("error_log.txt", "a");
            fwrite($file, "Error: " . $insert_stmt->error . "\n");
            fclose($file);
            echo "Registration Failed.";
        }
        unset($_SESSION['password']);
    }
    if (isset($_POST['back_button'])){
            unset($_SESSION['name']);
            unset($_SESSION['email']);
            unset($_SESSION['country']);
            unset($_SESSION['password']);
            header("Location: index.php");
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome!</title>
    <style>
        body { 
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: sans-serif;
            padding: 20px; 
        }
        .highlight { color: #007bff; }
        
        .pp_button{
            height: 40px;
            width: 100px;
            color: white;
            border: none;
            border-radius: 5px;
        }
        #back_btn{
            margin-top: 30px;
            background-color:rgb(133, 1, 1);
            margin-right: 10px;
        }
        #confirm_btn{
            margin-top: 30px;
            background-color:rgb(2, 61, 0);
        }
        .container {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            border-radius: 8px;
            background-color: rgb(255, 255, 255);
            width: 40%;
            min-width: 450px;
            min-height: 400px;
            box-shadow: #a7bad3 0px 15px 25px;
        }
        .container:hover {
            box-shadow: #a7bad3 0px 20px 30px;
        }
    </style>
</head>
<body style="background-color: <?php echo htmlspecialchars($bgColor); ?>;">
    <div class="container">
        <img src="assets/logo.png" style="width: 150px; height: 120px;">
        <h1>Welcome, <span class="highlight"><?php echo $fullname; ?></span>!</h1>
        <p>Thank you for registering.</p>
        <p>Your registered email address is: <span class="highlight"><?php echo $email; ?></span></p>
        <p>Your Current Country is: <span class="highlight"><?php echo $country; ?></span></p>
        <form method="POST" action="process.php">
            <button type="submit" id="back_btn" name="back_button" class="pp_button"><strong>Back</strong></button>
            <button type="submit" id="confirm_btn" name="confirm_button" class="pp_button"><strong>Confirm</strong></button>
        </form>
    </div>
    <script>
        function getComplementaryColor(hex) {
            hex = hex.replace(/^#/, '');
            let r = parseInt(hex.substr(0, 2), 16);
            let g = parseInt(hex.substr(2, 2), 16);
            let b = parseInt(hex.substr(4, 2), 16);
            let compR = (255 - r).toString(16).padStart(2, '0');
            let compG = (255 - g).toString(16).padStart(2, '0');
            let compB = (255 - b).toString(16).padStart(2, '0');
            return `#${compR}${compG}${compB}`;
        }

        backBtn = document.getElementById("back_btn");
        confirmBtn = document.getElementById("confirm_btn");

        backBtn.addEventListener("click",()=>{
            setTimeout(()=>{
                window.location.href = "index.php";
            },
            5000
            );
        });

        confirmBtn.addEventListener("click",()=>{
            setTimeout(()=>{
                window.location.href = "select_cities.php";
            },
            5000
            );
        });

    </script>
</body>
</html>