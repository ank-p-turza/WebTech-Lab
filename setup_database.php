<?php
// Database setup script to ensure user_cities table exists
require "db.php";

try {
    // Create user_cities table if it doesn't exist
    $sql = "
    CREATE TABLE IF NOT EXISTS `user_cities` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `user_id` int(11) NOT NULL,
      `city` varchar(20) NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `user_city_unique` (`user_id`, `city`),
      KEY `user_id` (`user_id`),
      KEY `city` (`city`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
    ";
    
    if ($conn->query($sql) === TRUE) {
        echo "user_cities table is ready.\n";
    } else {
        echo "Error creating table: " . $conn->error . "\n";
    }
    
    // Check if the table exists and has data
    $result = $conn->query("SHOW TABLES LIKE 'user_cities'");
    if ($result->num_rows > 0) {
        echo "user_cities table exists.\n";
        
        $count = $conn->query("SELECT COUNT(*) as count FROM user_cities");
        $row = $count->fetch_assoc();
        echo "Current user_cities records: " . $row['count'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

$conn->close();
?>
