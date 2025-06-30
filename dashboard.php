<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$name = $_SESSION['name'] ?? '';
$country = $_SESSION['country'] ?? '';
$bgColor = $_COOKIE['user_color'] ?? 'rgb(75, 74, 74)';

$stmt = $conn->prepare("
    SELECT uc.city, a.country, a.aqi 
    FROM user_cities uc 
    JOIN aqi a ON uc.city = a.city 
    WHERE uc.user_id = ? 
    ORDER BY CAST(a.aqi AS UNSIGNED) DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

function getAQIStatus($aqi) {
    $aqiValue = intval($aqi);
    if ($aqiValue <= 50) {
        return ['status' => 'Good', 'color' => '#00e400', 'text_color' => '#ffffff'];
    } elseif ($aqiValue <= 100) {
        return ['status' => 'Moderate', 'color' => '#ffff00', 'text_color' => '#000000'];
    } elseif ($aqiValue <= 150) {
        return ['status' => 'Unhealthy for Sensitive Groups', 'color' => '#ff7e00', 'text_color' => '#ffffff'];
    } elseif ($aqiValue <= 200) {
        return ['status' => 'Unhealthy', 'color' => '#ff0000', 'text_color' => '#ffffff'];
    } elseif ($aqiValue <= 300) {
        return ['status' => 'Very Unhealthy', 'color' => '#8f3f97', 'text_color' => '#ffffff'];
    } else {
        return ['status' => 'Hazardous', 'color' => '#7e0023', 'text_color' => '#ffffff'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Air Quality Index</title>
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400;700;900&family=Noto+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: <?php echo htmlspecialchars($bgColor); ?>;
            padding: 20px;
            line-height: 1.6;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .header img {
            width: 150px;
            height: 120px;
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .welcome-msg {
            font-size: 1.2rem;
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
        }

        .dashboard-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
            transform: translateY(-2px);
        }

        .aqi-table-container {
            overflow-x: auto;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        }

        .aqi-table {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Noto Sans', sans-serif;
            background: white;
        }

        .aqi-table thead {
            background:  #667eea;
            color: white;
        }

        .aqi-table th {
            padding: 20px 15px;
            text-align: left;
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .aqi-table th:first-child {
            border-top-left-radius: 10px;
        }

        .aqi-table th:last-child {
            border-top-right-radius: 10px;
        }

        .aqi-table tbody tr {
            transition: all 0.3s ease;
            border-bottom: 1px solid #ecf0f1;
        }

        .aqi-table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .aqi-table tbody tr:last-child {
            border-bottom: none;
        }

        .aqi-table td {
            padding: 18px 15px;
            font-size: 14px;
            vertical-align: middle;
        }

        .city-name {
            font-weight: 700;
            color: #2c3e50;
            font-size: 16px;
        }

        .country-name {
            color: #7f8c8d;
            font-size: 14px;
        }

        .aqi-value {
            font-weight: 900;
            font-size: 18px;
            text-align: center;
            padding: 8px 15px;
            border-radius: 25px;
            min-width: 80px;
            display: inline-block;
        }

        .aqi-status {
            font-weight: 600;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .no-cities {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
        }

        .no-cities h3 {
            font-size: 1.5rem;
            margin-bottom: 15px;
            color: #95a5a6;
        }

        .no-cities p {
            font-size: 1rem;
            margin-bottom: 30px;
        }

        .stats-row {
            display: flex;
            justify-content: space-around;
            margin-bottom: 30px;
            gap: 20px;
        }

        .stat-card {
            background: #667eea;
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            flex: 1;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .stat-card:hover {
            background:rgb(75, 91, 163);
            box-shadow: rgb(75, 91, 163);
            transition: background 0.2s ease-in-out;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            .dashboard-header {
                flex-direction: column;
                gap: 20px;
                text-align: center;
            }

            .stats-row {
                flex-direction: column;
            }

            .aqi-table th,
            .aqi-table td {
                padding: 12px 8px;
                font-size: 12px;
            }

            .header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/logo.png" alt="AQI Logo">
        <h1>Air Quality Dashboard</h1>
        <div class="welcome-msg">Welcome back, <?php echo htmlspecialchars($name); ?>!</div>
    </div>

    <div class="container">
        <div class="dashboard-header">
            <h2 class="dashboard-title">Your Preferred Cities</h2>
            <div class="action-buttons">
                <a href="profile.php" class="btn btn-primary">Profile</a>
                <a href="select_cities.php" class="btn btn-primary">Modify Cities</a>
                <form action="logout.php" method="POST" style="display: inline;">
                    <button type="submit" class="btn btn-danger">Logout</button>
                </form>
            </div>
        </div>

        <?php if ($result->num_rows > 0): ?>
            <?php
            // Calculate statistics
            $total_cities = $result->num_rows;
            $aqi_values = [];
            $cities_data = [];
            
            while ($row = $result->fetch_assoc()) {
                $cities_data[] = $row;
                $aqi_values[] = intval($row['aqi']);
            }
            
            $avg_aqi = round(array_sum($aqi_values) / count($aqi_values));
            $max_aqi = max($aqi_values);
            $min_aqi = min($aqi_values);
            ?>

            <div class="stats-row">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $total_cities; ?></div>
                    <div class="stat-label">Total Cities</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $avg_aqi; ?></div>
                    <div class="stat-label">Average AQI</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">
                        <?php echo $min_aqi; ?>
                        <?php 
                            $result = mysqli_query($conn, "SELECT city FROM aqi WHERE aqi = $min_aqi LIMIT 1");
                            if ($row = mysqli_fetch_assoc($result)) {
                                echo " (" . $row['city'] . ")";
                            }
                        ?>               
                </div>
                    <div class="stat-label">Best AQI</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $max_aqi; ?>
                    <?php 
                            $result = mysqli_query($conn, "SELECT city FROM aqi WHERE aqi = $max_aqi LIMIT 1");
                            if ($row = mysqli_fetch_assoc($result)) {
                                echo " (" . $row['city'] . ")";
                            }
                        ?>  
                
                </div>
                    <div class="stat-label">Worst AQI</div>
                </div>
            </div>

            <div class="aqi-table-container">
                <table class="aqi-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>City</th>
                            <th>Country</th>
                            <th>AQI Value</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 1;
                        foreach ($cities_data as $row): 
                            $aqiInfo = getAQIStatus($row['aqi']);
                        ?>
                            <tr>
                                <td><?php echo $count++; ?></td>
                                <td>
                                    <div class="city-name"><?php echo htmlspecialchars($row['city']); ?></div>
                                </td>
                                <td>
                                    <div class="country-name"><?php echo htmlspecialchars($row['country']); ?></div>
                                </td>
                                <td>
                                    <span class="aqi-value" style="background-color: <?php echo $aqiInfo['color']; ?>; color: <?php echo $aqiInfo['text_color']; ?>;">
                                        <?php echo htmlspecialchars($row['aqi']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="aqi-status" style="color: <?php echo $aqiInfo['color']; ?>;">
                                        <?php echo $aqiInfo['status']; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        <?php else: ?>
            <div class="no-cities">
                <h3>No Cities Selected</h3>
                <p>You haven't selected any cities yet. Choose your preferred cities to monitor their air quality.</p>
                <a href="select_cities.php" class="btn btn-primary">Select Cities</a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>