<?php
session_start();
require 'configer.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['authenticated'] = true;
            $_SESSION['username'] = $username;
            header("Location: admin portal.php");
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Rah-e-Umeed</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #4680bb 0%, #4CAF50 100%);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow-x: hidden;
        }

        /* Navbar */
        header {
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            background-color: rgba(0, 152, 121, 0.95);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .navbar .logo {
            font-size: 1.8rem;
            font-weight: 600;
            color: #fff;
        }

        .navbar-links {
            display: flex;
            gap: 20px;
        }

        .navbar-links a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 400;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .navbar-links a:hover {
            color: #e0f7fa;
            transform: scale(1.1);
        }

        .hamburger {
            display: none;
            font-size: 1.8rem;
            color: #fff;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }

            .navbar-links {
                display: none;
                flex-direction: column;
                position: absolute;
                top: 60px;
                left: 0;
                width: 100%;
                background-color: rgba(0, 152, 121, 0.95);
                padding: 20px;
                text-align: center;
                animation: slideIn 0.3s ease-in-out;
            }

            .navbar-links.active {
                display: flex;
            }

            .navbar-links a {
                margin: 10px 0;
            }
        }

        @keyframes slideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Login Container */
        .login-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            margin: 80px auto;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeIn 1s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo-container h1 {
            font-size: 2rem;
            font-weight: 600;
            color: #fff;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        h2 {
            text-align: center;
            color: #fff;
            font-weight: 400;
            margin-bottom: 20px;
        }

        /* Form */
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .form-group input:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.3);
        }

        .form-group label {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: #e0e0e0;
            font-size: 1rem;
            pointer-events: none;
            transition: all 0.3s ease;
        }

        .form-group input:focus+label,
        .form-group input:not(:placeholder-shown)+label {
            top: 0;
            font-size: 0.8rem;
            color: #4CAF50;
            background: transparent;
            padding: 0 5px;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #4CAF50, #66BB6A);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 1.1rem;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        button[type="submit"]:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.4);
        }

        /* Error Message */
        .error-message {
            background: rgba(231, 76, 60, 0.9);
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
            position: relative;
            animation: shake 0.3s ease;
        }

        .error-message .close {
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            font-weight: bold;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        /* Loading Overlay */
        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .loader {
            width: 60px;
            height: 60px;
            position: relative;
            animation: pulse 1.5s infinite ease-in-out;
        }

        .loader::before,
        .loader::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background: #4CAF50;
            border-radius: 50%;
            top: 50%;
            transform: translateY(-50%);
        }

        .loader::before {
            left: 0;
            animation: moveLeft 1s infinite ease-in-out;
        }

        .loader::after {
            right: 0;
            animation: moveRight 1s infinite ease-in-out;
        }

        @keyframes moveLeft {

            0%,
            100% {
                transform: translate(0, -50%) scale(1);
            }

            50% {
                transform: translate(20px, -50%) scale(0.8);
            }
        }

        @keyframes moveRight {

            0%,
            100% {
                transform: translate(0, -50%) scale(1);
            }

            50% {
                transform: translate(-20px, -50%) scale(0.8);
            }
        }

        @keyframes pulse {

            0%,
            100% {
                transform: scale(1);
            }

            50% {
                transform: scale(1.1);
            }
        }

        /* Responsive Design */
        @media (max-width: 480px) {
            .login-container {
                padding: 20px;
                margin: 40px 20px;
            }

            .logo-container h1 {
                font-size: 1.5rem;
            }

            h2 {
                font-size: 1.2rem;
            }

            .form-group input {
                font-size: 0.9rem;
            }

            button[type="submit"] {
                font-size: 1rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <nav class="navbar">
            <div class="logo">Rah-e-Umeed</div>
            <div class="hamburger" onclick="toggleMenu()">☰</div>
            <div class="navbar-links" id="navLinks">
                <a href="index.php">Home</a>
                <a href="#about">About</a>
                <a href="#projects">Projects</a>
                <a href="#donate">Donate</a>
                <a href="#contact">Contact</a>
            </div>
        </nav>
    </header>

    <div class="login-container">
        <div class="logo-container">
            <h1>Rah-e-Umeed</h1>
        </div>
        <h2>Admin Login</h2>
        <?php
        if (isset($error_message)) {
            echo "<div class='error-message'>";
            echo htmlspecialchars($error_message);
            echo "<span class='close' onclick='this.parentElement.style.display=\"none\"'>×</span>";
            echo "</div>";
        }
        ?>
        <form method="POST" id="loginForm">
            <div class="form-group">
                <input type="text" name="username" id="username" placeholder=" " required>
                <label for="username">Username</label>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" placeholder=" " required>
                <label for="password">Password</label>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
    </div>

    <script>
        // Toggle navbar menu on mobile
        function toggleMenu() {
            const navLinks = document.getElementById('navLinks');
            navLinks.classList.toggle('active');
        }

        // Show loading animation on form submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });

        // Auto-dismiss error message after 5 seconds
        setTimeout(() => {
            const errorMessage = document.querySelector('.error-message');
            if (errorMessage) errorMessage.style.display = 'none';
        }, 5000);

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href');
                const targetElement = document.querySelector(targetId);
                if (targetElement) {
                    targetElement.scrollIntoView({ behavior: 'smooth' });
                }
            });
        });
    </script>
</body>

</html>