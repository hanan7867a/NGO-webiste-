<?php
include 'configer.php';

// Fetch projects from the database
$sql_projects = "SELECT * FROM projects ORDER BY created_at DESC";
$result_projects = $conn->query($sql_projects);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rah-e-Umeed Welfare</title>
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
            color: #333;
            line-height: 1.6;
            overflow-x: hidden;
        }

        header {
            position: sticky;
            top: 0;
            width: 100%;
            z-index: 1000;
            background: rgba(0, 152, 121, 0.95);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: 0 auto;
            padding: 15px 30px;
        }

        .header-container h1 {
            font-size: 1.8rem;
            color: #fff;
            font-weight: 600;
        }

        .header-container img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
        }

        .menu-toggle {
            display: none;
            font-size: 1.8rem;
            color: #fff;
            background: none;
            border: none;
            cursor: pointer;
        }

        .mobile-nav {
            display: flex;
            justify-content: center;
        }

        .mobile-nav ul {
            display: flex;
            list-style: none;
            gap: 20px;
        }

        .mobile-nav a {
            color: #fff;
            text-decoration: none;
            font-size: 1rem;
            font-weight: 400;
            transition: transform 0.2s ease, color 0.2s ease;
        }

        .mobile-nav a:hover {
            color: #e0f7fa;
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .menu-toggle {
                display: block;
            }

            .mobile-nav {
                display: none;
                position: absolute;
                top: 70px;
                left: 0;
                width: 100%;
                background: rgba(0, 152, 121, 0.95);
                padding: 20px;
                text-align: center;
                animation: slideIn 0.3s ease-in-out;
            }

            .mobile-nav.active {
                display: block;
            }

            .mobile-nav ul {
                flex-direction: column;
                gap: 15px;
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

        #hero {
            position: relative;
            background: linear-gradient(rgba(0, 152, 121, 0.7), rgba(70, 128, 187, 0.7));
            padding: 120px 20px;
            text-align: center;
            color: #fff;
            animation: fadeIn 1s ease-in-out;
        }

        #hero h2 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            animation: slideUp 1s ease-in-out;
            word-wrap: break-word;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        #hero p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            animation: slideUp 1.2s ease-in-out;
            word-wrap: break-word;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        #hero button {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            animation: slideUp 1.4s ease-in-out;
            margin: 5px;
        }

        #donatebtn {
            background: linear-gradient(90deg, #4CAF50, #66BB6A);
            color: #fff;
            margin-right: 10px;
        }

        #joinusbtn {
            background: linear-gradient(90deg, #4680bb, #66BB6A);
            color: #fff;
        }

        #hero button:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        #about {
            padding: 60px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .about-container {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            animation: fadeIn 1s ease-in-out;
        }

        .about-container h2 {
            font-size: 2rem;
            color: #fff;
            text-align: center;
            margin-bottom: 20px;
        }

        .about-container p,
        .about-container ul {
            color: #e0e0e0;
            font-size: 1rem;
            text-align: left;
        }

        .mission-section ul {
            list-style: none;
            padding-left: 0;
        }

        .mission-section li {
            margin: 15px 0;
            display: flex;
            align-items: flex-start;
            gap: 10px;
            word-wrap: break-word;
        }

        .mission-section li::before {
            content: '🌟';
            font-size: 1.2rem;
        }

        .vision {
            font-style: italic;
            text-align: center;
            padding: 20px;
            background: rgba(0, 152, 121, 0.2);
            border-radius: 10px;
            transition: transform 0.3s ease;
            word-wrap: break-word;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        .vision:hover {
            transform: scale(1.02);
        }

        #projects {
            padding: 60px 20px;
            background: rgba(0, 0, 0, 0.1);
        }

        #projects h2 {
            font-size: 2rem;
            color: #fff;
            text-align: center;
            margin-bottom: 40px;
        }

        .carousel-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            position: relative;
        }

        .carousel {
            overflow: hidden;
            position: relative;
        }

        .carousel-track {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }

        .carousel-item {
            flex: 0 0 33.33%;
            padding: 15px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            text-align: center;
            transition: transform 0.3s ease;
        }

        .carousel-item:hover {
            transform: translateY(-5px);
        }

        .carousel-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            background: linear-gradient(135deg, #4CAF50, #4680bb);
        }

        .carousel-item h3 {
            font-size: 1.5rem;
            color: #fff;
            margin: 15px 0 10px;
            word-wrap: break-word;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        .carousel-item p {
            font-size: 1rem;
            color: #e0e0e0;
            padding: 0 10px 15px;
            word-wrap: break-word;
            max-width: 90%;
            margin-left: auto;
            margin-right: auto;
        }

        .arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 152, 121, 0.95);
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 1.5rem;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.3s ease;
        }

        .arrow:hover {
            background: rgba(0, 152, 121, 1);
        }

        .arrow.left {
            left: 0;
        }

        .arrow.right {
            right: 0;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1500;
            justify-content: center;
            align-items: center;
        }

        .popup-form,
        .donation-form {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 40px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: #fff;
            position: relative;
        }

        .popup-form h3,
        .donation-form h3 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            position: relative;
            margin-bottom: 20px;
        }

        .form-group input,
        .form-group textarea {
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

        .form-group textarea {
            height: 100px;
            resize: vertical;
        }

        .form-group input:focus,
        .form-group textarea:focus {
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
        .form-group input:not(:placeholder-shown)+label,
        .form-group textarea:focus+label,
        .form-group textarea:not(:placeholder-shown)+label {
            top: 0;
            font-size: 0.8rem;
            color: #4CAF50;
            background: transparent;
            padding: 0 5px;
        }

        .popup-form button,
        .donation-form button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 10px;
        }

        .popup-form button[type="submit"],
        .donation-form button[type="submit"] {
            background: linear-gradient(90deg, #4CAF50, #66BB6A);
            color: #fff;
        }

        .popup-form button[type="button"],
        .donation-form button[type="button"] {
            background: linear-gradient(90deg, #e74c3c, #ef5350);
            color: #fff;
        }

        .popup-form button:hover,
        .donation-form button:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .form-message {
            background: rgba(40, 167, 69, 0.9);
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
            position: relative;
            animation: shake 0.3s ease;
        }

        .form-message.error {
            background: rgba(231, 76, 60, 0.9);
        }

        .form-message .close {
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

        footer {
            background: #2c3e50;
            color: #e0e0e0;
            padding: 40px 20px;
            text-align: center;
        }

        footer h1 {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #fff;
        }

        footer a img {
            filter: invert(1);
            transition: transform 0.2s ease;
        }

        footer a img:hover {
            transform: scale(1.2);
        }

        footer p {
            margin: 10px 0;
        }

        @media (max-width: 768px) {
            #hero {
                padding: 80px 20px;
            }

            #hero h2 {
                font-size: 1.5rem;
            }

            #hero p {
                font-size: 1rem;
            }

            .about-container {
                padding: 20px;
            }

            .about-container h2 {
                font-size: 1.5rem;
            }

            .about-container p,
            .about-container ul {
                font-size: 0.9rem;
            }

            .mission-section li {
                font-size: 0.9rem;
            }

            .popup-form,
            .donation-form {
                padding: 20px;
                margin: 20px;
            }

            .carousel-item {
                flex: 0 0 100%;
            }

            .carousel-item img {
                height: 150px;
            }

            .arrow {
                width: 30px;
                height: 30px;
                font-size: 1.2rem;
            }

            .carousel-item h3 {
                font-size: 1.2rem;
            }

            .carousel-item p {
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .header-container h1 {
                font-size: 1.5rem;
            }

            .header-container img {
                width: 40px;
                height: 40px;
            }

            #hero h2 {
                font-size: 1.2rem;
            }

            #hero p {
                font-size: 0.9rem;
            }

            .about-container h2 {
                font-size: 1.2rem;
            }

            .about-container p,
            .about-container ul {
                font-size: 0.85rem;
            }

            .mission-section li {
                font-size: 0.85rem;
            }

            .carousel-item img {
                height: 120px;
            }

            .carousel-item h3 {
                font-size: 1rem;
            }

            .carousel-item p {
                font-size: 0.85rem;
            }

            footer h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="header-container">
            <h1>Rah-e-Umeed</h1>
            <img src="statics\logo with background.jpg" alt="Rah-e-Umeed Logo">
            <button class="menu-toggle" aria-label="Toggle navigation menu" onclick="toggleMenu()">☰</button>
        </div>
        <nav class="mobile-nav">
            <ul>
                <li><a href="#about">About</a></li>
                <li><a href="#projects">Projects</a></li>
                <li><a href="donate.php">Donate</a></li>
                <li><a href="#contact">Contact</a></li>
                <li><a href="admin portal.php">Admin Portal</a></li>
            </ul>
        </nav>
    </header>
    <section id="hero">
        <h2 id="hero-text">What if your kindness today could change a life forever?</h2>
        <p id="jointxt">Join us in spreading hope through education, food, and care.</p>
        <button id="donatebtn" onclick="openDonationForm()">Donate Now</button>
        <button id="joinusbtn" onclick="openVolunteerForm()">Join Us</button>
    </section>
    <section id="about">
        <div class="about-container">
            <h2>💫 Who We Are – Rah-e-Umeed</h2>
            <p class="intro">
                <strong>Rah-e-Umeed</strong> is a non-profit welfare organization devoted to bringing hope and support
                to those
                who need it the most. We believe that every individual—regardless of their background—deserves access to
                education,
                food, and a life of dignity.
            </p>
            <div class="mission-section">
                <h3>🌱 Our Mission:</h3>
                <ul>
                    <li><strong>Education for All:</strong> We provide books, uniforms, chairs, and benches to
                        underprivileged children—so they can learn in peace and build a brighter future.</li>
                    <li><strong>Feeding the Hungry:</strong> We support struggling families with essential food
                        supplies, making sure no one sleeps hungry.</li>
                    <li><strong>Environmental Awareness:</strong> We educate communities on the impact of deforestation
                        and encourage tree planting for a greener, healthier future.</li>
                    <li><strong>Fighting Poverty:</strong> By helping those in need, we empower them to become
                        self-reliant and live with honor.</li>
                    <li><strong>Building a Better Tomorrow:</strong> We dream of a Kashmir where every child goes to
                        school, no family suffers from hunger, and nature thrives.</li>
                </ul>
            </div>
            <p class="vision">
                <em>"Rah-e-Umeed envisions a future where hope is shared, lives are uplifted, and no one is left
                    behind."</em><br>
                Let’s come together to be the light in someone’s darkness — <strong>join us in creating real
                    change.</strong>
            </p>
        </div>
    </section>
    <section id="projects">
        <h2>📸 Our Work in Action</h2>
        <div class="carousel-container">
            <div class="carousel">
                <div class="carousel-track" id="carouselTrack">
                    <?php
                    if ($result_projects->num_rows > 0) {
                        while ($project = $result_projects->fetch_assoc()) {
                            echo '<div class="carousel-item">';
                            echo '<img src="' . htmlspecialchars($project['image_path']) . '" alt="' . htmlspecialchars($project['title']) . '">';
                            echo '<h3>' . htmlspecialchars($project['title']) . '</h3>';
                            echo '<p>' . htmlspecialchars($project['description']) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="carousel-item"><p>No projects available at the moment.</p></div>';
                    }
                    ?>
                </div>
                <button class="arrow left" id="leftArrow">
                    << /button>
                        <button class="arrow right" id="rightArrow">></button>
            </div>
        </div>
    </section>
    <div id="volunteerOverlay" class="overlay">
        <div class="popup-form">
            <h3>Become a Volunteer</h3>
            <?php
            if (isset($_SESSION['volunteer_alert'])) {
                echo $_SESSION['volunteer_alert'];
                unset($_SESSION['volunteer_alert']);
            }
            if (isset($_SESSION['volunteer_email'])) {
                $email = $_SESSION['volunteer_email'];
                $stmt = $conn->prepare("SELECT status FROM volunteers WHERE email = ? ORDER BY submitted_at DESC LIMIT 1");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $status = $row['status'];
                    if ($status == 'Approved') {
                        echo "<div class='form-message'>Your application has been approved! Check your email for details.</div>";
                    } elseif ($status == 'Rejected') {
                        echo "<div class='form-message error'>Your application was not approved. Please contact us for details.</div>";
                    } else {
                        echo "<div class='form-message'>Your application is pending review.</div>";
                    }
                }
                $stmt->close();
            }
            ?>
            <div id="volunteerMessage" class="form-message" style="display: none;"></div>
            <form id="volunteerForm" action="submit_volunteer.php" method="POST">
                <div class="form-group">
                    <input type="text" id="volunteerName" name="full_name" placeholder=" " required>
                    <label for="volunteerName">Full Name</label>
                </div>
                <div class="form-group">
                    <input type="tel" id="volunteerPhone" name="phone" placeholder=" " required>
                    <label for="volunteerPhone">Phone Number</label>
                </div>
                <div class="form-group">
                    <input type="email" id="volunteerEmail" name="email" placeholder=" " required>
                    <label for="volunteerEmail">Email</label>
                </div>
                <div class="form-group">
                    <textarea id="volunteerReason" name="reason" placeholder=" " required></textarea>
                    <label for="volunteerReason">Why do you want to join us?</label>
                </div>
                <button type="submit">Submit</button>
                <button type="button" onclick="closeVolunteerForm()">Close</button>
            </form>
        </div>
    </div>
    <div id="donationOverlay" class="overlay">
        <div class="donation-form">
            <h3>Make a Donation</h3>
            <div id="donationMessage" class="form-message" style="display: none;"></div>
            <form id="donationForm" action="https://ipg.payfast.pk/transaction/initialize" method="POST">
                <input type="hidden" name="merchant_id" value="YOUR_MERCHANT_ID">
                <input type="hidden" name="merchant_username" value="YOUR_USERNAME">
                <input type="hidden" name="merchant_password" value="YOUR_PASSWORD">
                <div class="form-group">
                    <input type="text" name="donor_name" id="donorName" placeholder=" " required>
                    <label for="donorName">Your Name</label>
                </div>
                <div class="form-group">
                    <input type="email" name="donor_email" id="donorEmail" placeholder=" " required>
                    <label for="donorEmail">Your Email</label>
                </div>
                <div class="form-group">
                    <input type="number" name="amount" id="donorAmount" placeholder=" " required>
                    <label for="donorAmount">Amount (PKR)</label>
                </div>
                <input type="hidden" name="order_id" value="DONATE_001">
                <input type="hidden" name="return_url" value="https://yourdomain.com/success">
                <input type="hidden" name="cancel_url" value="https://yourdomain.com/cancel">
                <button type="submit">Donate Now</button>
                <button type="button" onclick="closeDonationForm()">Cancel</button>
            </form>
        </div>
    </div>
    <footer id="contact">
        <h1>Contact Us</h1>
        <p>Follow us on social media</p>
        <div>
            <a href="https://facebook.com/yourpage" target="_blank">
                <img src="https://cdn.jsdelivr.net/npm/simple-icons@v9/icons/facebook.svg" alt="Facebook" width="24"
                    height="24">
            </a>
        </div>
        <p>Email: abdulhanan7867a@gmail.com | Phone: +92-3109309317</p>
        <p>© 2025 Rah-e-Umeed Welfare. All Rights Reserved.</p>
    </footer>
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loader"></div>
    </div>
    <script>
        function toggleMenu() {
            const nav = document.querySelector('.mobile-nav');
            nav.classList.toggle('active');
        }
        function openVolunteerForm() {
            document.getElementById('volunteerOverlay').style.display = 'flex';
        }
        function closeVolunteerForm() {
            document.getElementById('volunteerOverlay').style.display = 'none';
            document.getElementById('volunteerMessage').style.display = 'none';
        }
        function openDonationForm() {
            document.getElementById('donationOverlay').style.display = 'flex';
        }
        function closeDonationForm() {
            document.getElementById('donationOverlay').style.display = 'none';
            document.getElementById('donationMessage').style.display = 'none';
        }
        document.getElementById('volunteerForm').addEventListener('submit', function () {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });
        document.getElementById('donationForm').addEventListener('submit', function () {
            document.getElementById('loadingOverlay').style.display = 'flex';
        });
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
        setTimeout(() => {
            const message = document.querySelector('.form-message');
            if (message) message.style.display = 'none';
        }, 5000);

        // Carousel JavaScript
        const track = document.getElementById('carouselTrack');
        const items = document.querySelectorAll('.carousel-item');
        const leftArrow = document.getElementById('leftArrow');
        const rightArrow = document.getElementById('rightArrow');
        let currentIndex = 0;
        const totalItems = items.length;
        let itemsPerView = 3;

        function updateItemsPerView() {
            if (window.innerWidth <= 768) {
                itemsPerView = 1;
            } else {
                itemsPerView = 3;
            }
        }

        function updateCarousel() {
            const itemWidth = 100 / itemsPerView;
            track.style.transform = `translateX(-${currentIndex * itemWidth}%)`;

            leftArrow.style.display = currentIndex === 0 ? 'none' : 'flex';
            rightArrow.style.display = currentIndex >= totalItems - itemsPerView ? 'none' : 'flex';

            items.forEach((item, index) => {
                if (index >= currentIndex && index < currentIndex + itemsPerView) {
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                } else {
                    item.style.opacity = '0';
                    item.style.transform = 'translateY(20px)';
                }
            });
        }

        leftArrow.addEventListener('click', () => {
            if (currentIndex > 0) {
                currentIndex--;
                updateCarousel();
            }
        });

        rightArrow.addEventListener('click', () => {
            if (currentIndex < totalItems - itemsPerView) {
                currentIndex++;
                updateCarousel();
            }
        });

        window.addEventListener('resize', () => {
            updateItemsPerView();
            updateCarousel();
        });

        updateItemsPerView();
        updateCarousel();

        // Intersection Observer for fade-in effect
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    updateCarousel();
                }
            });
        }, { threshold: 0.2 });

        observer.observe(document.getElementById('projects'));
    </script>
</body>

</html>