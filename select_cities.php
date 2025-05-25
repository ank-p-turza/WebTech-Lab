<?php
session_start();
$email = $_SESSION['email'] ?? '';
$name = $_SESSION['name'] ?? '';
$country = $_SESSION['country'] ?? '';
$bgColor = $_COOKIE['user_color'] ?? 'rgb(255, 255, 255)';
if(!isset($_SESSION['email'])){
    header("Location: index.php");
    die("User not signed in.");
}

require "db.php";

$check_ = $conn->query("SELECT * FROM users WHERE email = '$email'");
if($check_->num_rows > 0){
    $row = $check_->fetch_assoc();
    if($row['selected_cities'] > 0){
        header("Location: dashboard.php");
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if(isset($_POST['confirm_button'])){
        if (!isset($_POST['cities']) || !is_array($_POST['cities'])) {
            die("No cities selected.");
        }
    
        $selectedCities = $_POST['cities'];
        $maxAllowed = 10;
    
        if (count($selectedCities) > $maxAllowed) {
            die("You can only select up to $maxAllowed cities.");
        }
    }
}

$result = $conn->query("SELECT city, country
FROM aqi
ORDER BY country desc, city ASC;
");



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Select Cities</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            line-height: 1.6;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .cities-list {
            max-height: 400px;
            overflow-y: auto;
            margin-bottom: 20px;
            border: 1px solid #eee;
            padding: 20px;
            border-radius: 5px;
        }

        .city-item {
            margin-bottom: 10px;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .city-item:hover {
            background-color: #f5f5f5;
        }

        .city-item label {
            cursor: pointer;
            display: flex;
            align-items: center;
            font-size: 14px;
        }

        .city-item input[type="checkbox"] {
            margin-right: 10px;
            transform: scale(1.2);
        }

        #confirm_btn {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            display: block;
            margin: 20px auto;
            transition: background-color 0.3s;
        }

        #confirm_btn:hover {
            background-color: #0056b3;
        }

        .selection-info {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            color: #666;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 10px;
            }
            
            .cities-list {
                max-height: 300px;
            }
        }
    </style>
</head>
<body style="background-color: <?php echo htmlspecialchars($bgColor)?>;">    <h1>Select Cities</h1>
    <div class="container">
        <div class="selection-info">
            Select up to 10 cities (0 selected)
        </div>
        <form action="handler.php" method="POST">
            <div class="cities-list">
                <?php
                    if($result->num_rows > 0){
                        while($row=$result->fetch_assoc()){
                            $country = htmlspecialchars(($row['country']));
                            $city = htmlspecialchars($row['city']);
                            echo "<div class='city-item'>";
                            echo "<label><input type='checkbox' name='cities[]' value='$city'> $city</label>";
                            echo "</div>";                  
                        }
                    }
                    else{
                        echo "No cities found.";
                    }
                ?>
            </div>
            <button type="submit" name="confirm_button" id="confirm_btn">Confirm Selection</button>
        </form>
    </div>
    
<script>
document.addEventListener('DOMContentLoaded', () => {
    const checkboxes = document.querySelectorAll('input[type="checkbox"][name="cities[]"]');
    const selectionInfo = document.querySelector('.selection-info');
    const maxAllowed = 10;

    function updateSelectionInfo() {
        const checkedCount = document.querySelectorAll('input[type="checkbox"][name="cities[]"]:checked').length;
        selectionInfo.textContent = `Select up to 10 cities (${checkedCount} selected)`;
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', () => {
            const checkedCount = document.querySelectorAll('input[type="checkbox"][name="cities[]"]:checked').length;
            
            if (checkedCount > maxAllowed) {
                checkbox.checked = false;
                alert(`You can select a maximum of ${maxAllowed} cities.`);
            }
            
            updateSelectionInfo();
        });
    });
});
</script>
</body>
</html>