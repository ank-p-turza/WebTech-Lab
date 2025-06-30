<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_button'])) {
    if (!isset($_POST['cities']) || !is_array($_POST['cities'])) {
        $_SESSION['error'] = "No cities selected. Please select at least one city.";
        header("Location: select_cities.php");
        exit();
    }

    $selectedCities = $_POST['cities'];
    $maxAllowed = 10;

    if (count($selectedCities) > $maxAllowed) {
        $_SESSION['error'] = "You can only select up to $maxAllowed cities.";
        header("Location: select_cities.php");
        exit();
    }

    if (count($selectedCities) < 1) {
        $_SESSION['error'] = "Please select at least one city.";
        header("Location: select_cities.php");
        exit();
    }

    try {
        $conn->begin_transaction();

        // First, delete any existing selections for this user
        $deleteStmt = $conn->prepare("DELETE FROM user_cities WHERE user_id = ?");
        $deleteStmt->bind_param("i", $user_id);
        $deleteStmt->execute();

        $insertStmt = $conn->prepare("INSERT INTO user_cities (user_id, city) VALUES (?, ?)");
        
        foreach ($selectedCities as $city) {
            // Validate that the city exists in the aqi table
            $validateStmt = $conn->prepare("SELECT city FROM aqi WHERE city = ? LIMIT 1");
            $validateStmt->bind_param("s", $city);
            $validateStmt->execute();
            $result = $validateStmt->get_result();
            
            if ($result->num_rows === 0) {
                throw new Exception("Invalid city selected: " . htmlspecialchars($city));
            }
            
            $insertStmt->bind_param("is", $user_id, $city);
            $insertStmt->execute();
        }

        // Update users table to mark that cities have been selected
        $updateUserStmt = $conn->prepare("UPDATE users SET selected_cities = ? WHERE id = ?");
        $cityCount = count($selectedCities);
        $updateUserStmt->bind_param("ii", $cityCount, $user_id);
        $updateUserStmt->execute();

        $conn->commit();

        $cityCount = count($selectedCities);
        $_SESSION['success'] = "Successfully selected $cityCount " . ($cityCount === 1 ? 'city' : 'cities') . "!";
        header("Location: dashboard.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        
        error_log("Error saving user cities: " . $e->getMessage());
        
        $_SESSION['error'] = "An error occurred while saving your selections. Please try again.";
        header("Location: select_cities.php");
        exit();
    }

} else {
    header("Location: select_cities.php");
    exit();
}
?>