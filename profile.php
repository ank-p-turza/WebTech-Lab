<?php
session_start();
require "db.php";

if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$bgColor = $_COOKIE['user_color'] ?? 'rgb(75, 74, 74)';

// Fetch current user data
$stmt = $conn->prepare("SELECT name, email, country FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$error_message = $_SESSION['error'] ?? '';
$success_message = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);

// Get list of countries for dropdown
$countries = [
    'Afghanistan', 'Albania', 'Algeria', 'Argentina', 'Armenia', 'Australia', 
    'Austria', 'Azerbaijan', 'Bahrain', 'Bangladesh', 'Belarus', 'Belgium', 
    'Bolivia', 'Bosnia and Herzegovina', 'Brazil', 'Bulgaria', 'Cambodia', 
    'Canada', 'Chile', 'China', 'Colombia', 'Croatia', 'Czech Republic', 
    'Denmark', 'Ecuador', 'Egypt', 'Estonia', 'Finland', 'France', 'Georgia', 
    'Germany', 'Ghana', 'Greece', 'Hungary', 'Iceland', 'India', 'Indonesia', 
    'Iran', 'Iraq', 'Ireland', 'Italy', 'Japan', 'Jordan', 'Kazakhstan', 
    'Kenya', 'Kuwait', 'Latvia', 'Lebanon', 'Lithuania', 'Luxembourg', 
    'Malaysia', 'Mexico', 'Morocco', 'Netherlands', 'New Zealand', 'Norway', 
    'Pakistan', 'Peru', 'Philippines', 'Poland', 'Portugal', 'Qatar', 
    'Romania', 'Russia', 'Saudi Arabia', 'Serbia', 'Singapore', 'Slovakia', 
    'Slovenia', 'South Africa', 'South Korea', 'Spain', 'Sri Lanka', 'Sweden', 
    'Switzerland', 'Thailand', 'Turkey', 'Ukraine', 'United Arab Emirates', 
    'United Kingdom', 'United States', 'Uruguay', 'Venezuela', 'Vietnam'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Air Quality Index</title>
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

        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #ecf0f1;
        }

        .form-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
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

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-2px);
        }

        .btn-primary {
            background-color: #3498db;
            color: white;
            width: 100%;
            margin-top: 20px;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .color-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
        }

        .color-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
            gap: 15px;
            margin-top: 15px;
        }

        .color-option {
            text-align: center;
        }

        .color-preview {
            width: 100%;
            height: 60px;
            border-radius: 8px;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s ease;
            margin-bottom: 5px;
        }

        .color-preview:hover {
            transform: scale(1.05);
            border-color: #3498db;
        }

        .color-preview.selected {
            border-color: #2c3e50;
            transform: scale(1.1);
        }

        .color-label {
            font-size: 12px;
            color: #666;
            font-weight: 500;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
                margin: 10px;
            }

            .form-header {
                flex-direction: column;
                gap: 15px;
                text-align: center;
            }

            .color-options {
                grid-template-columns: repeat(3, 1fr);
            }
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="assets/logo.png" alt="AQI Logo">
        <h1>User Profile</h1>
    </div>

    <div class="container">
        <div class="form-header">
            <h2 class="form-title">Update Profile</h2>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>

        <?php if ($error_message): ?>
            <div class="message error-message"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="message success-message"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <form action="profile_handler.php" method="POST">
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="<?php echo htmlspecialchars($user['name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="country">Country</label>
                <select id="country" name="country" class="form-control" required>
                    <option value="">Select your country</option>
                    <?php foreach ($countries as $country): ?>
                        <option value="<?php echo htmlspecialchars($country); ?>" 
                                <?php echo ($user['country'] === $country) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($country); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="current_password">Current Password (leave blank to keep current)</label>
                <input type="password" id="current_password" name="current_password" class="form-control">
            </div>

            <div class="form-group">
                <label for="new_password">New Password (leave blank to keep current)</label>
                <input type="password" id="new_password" name="new_password" class="form-control">
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control">
            </div>

            <div class="color-section">
                <label>Choose Background Color</label>
                <div class="color-options">
                    <div class="color-option">
                        <div class="color-preview" data-color="rgb(75, 74, 74)" style="background-color: rgb(75, 74, 74);"></div>
                        <div class="color-label">Default</div>
                    </div>
                    <div class="color-option">
                        <div class="color-preview" data-color="rgb(67, 125, 190)" style="background-color: rgb(67, 125, 190);"></div>
                        <div class="color-label">Blue</div>
                    </div>
                    <div class="color-option">
                        <div class="color-preview" data-color="rgb(76, 175, 80)" style="background-color: rgb(76, 175, 80);"></div>
                        <div class="color-label">Green</div>
                    </div>
                    <div class="color-option">
                        <div class="color-preview" data-color="rgb(156, 39, 176)" style="background-color: rgb(156, 39, 176);"></div>
                        <div class="color-label">Purple</div>
                    </div>
                    <div class="color-option">
                        <div class="color-preview" data-color="rgb(255, 87, 34)" style="background-color: rgb(255, 87, 34);"></div>
                        <div class="color-label">Orange</div>
                    </div>
                    <div class="color-option">
                        <div class="color-preview" data-color="rgb(96, 125, 139)" style="background-color: rgb(96, 125, 139);"></div>
                        <div class="color-label">Slate</div>
                    </div>
                </div>
                <input type="hidden" id="selected_color" name="background_color" value="<?php echo htmlspecialchars($bgColor); ?>">
            </div>

            <button type="submit" class="btn btn-primary">Update Profile</button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const colorPreviews = document.querySelectorAll('.color-preview');
            const selectedColorInput = document.getElementById('selected_color');
            const currentColor = selectedColorInput.value;

            // Set initial selected color
            colorPreviews.forEach(preview => {
                if (preview.dataset.color === currentColor) {
                    preview.classList.add('selected');
                }

                preview.addEventListener('click', () => {
                    // Remove selected class from all previews
                    colorPreviews.forEach(p => p.classList.remove('selected'));
                    
                    // Add selected class to clicked preview
                    preview.classList.add('selected');
                    
                    // Update hidden input value
                    selectedColorInput.value = preview.dataset.color;
                    
                    // Update body background color for preview
                    document.body.style.backgroundColor = preview.dataset.color;
                });
            });

            // Password validation
            const newPassword = document.getElementById('new_password');
            const confirmPassword = document.getElementById('confirm_password');
            const currentPassword = document.getElementById('current_password');

            function validatePasswords() {
                if (newPassword.value && !currentPassword.value) {
                    currentPassword.setCustomValidity('Current password is required to set a new password');
                } else {
                    currentPassword.setCustomValidity('');
                }

                if (newPassword.value && newPassword.value !== confirmPassword.value) {
                    confirmPassword.setCustomValidity('Passwords do not match');
                } else {
                    confirmPassword.setCustomValidity('');
                }
            }

            newPassword.addEventListener('input', validatePasswords);
            confirmPassword.addEventListener('input', validatePasswords);
            currentPassword.addEventListener('input', validatePasswords);
        });
    </script>
</body>
</html>
