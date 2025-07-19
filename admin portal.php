<?php
session_start();

// Prevent caching of this page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header("Location: login.php");
    exit();
}

include('configer.php');

$search = '';
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = trim($_GET['search']);
    $sql = "SELECT * FROM donations WHERE donor_name LIKE '%$search%' ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM donations ORDER BY id DESC";
}

$result = $conn->query($sql);

// Handle deletion of donations
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM donations WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $_SESSION['alert'] = "<div class='alert success'>Donation deleted successfully!</div>";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle deletion of spending
if (isset($_GET['delete_spending_id'])) {
    $delete_id = intval($_GET['delete_spending_id']);
    $stmt = $conn->prepare("DELETE FROM spending WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $_SESSION['alert'] = "<div class='alert success'>Spending record deleted successfully!</div>";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle updating donation
if (isset($_POST['update_donation'])) {
    $id = intval($_POST['id']);
    $donor_name = trim($_POST['donor_name']);
    $phone = trim($_POST['phone']);
    $amount = floatval($_POST['amount']);

    $stmt = $conn->prepare("UPDATE donations SET donor_name = ?, phone = ?, amount = ? WHERE id = ?");
    $stmt->bind_param("ssdi", $donor_name, $phone, $amount, $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = "<div class='alert success'>Donation updated successfully!</div>";
    } else {
        $_SESSION['alert'] = "<div class='alert error'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle adding a manual donation
if (isset($_POST['submit_manual'])) {
    $donor_name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $amount = floatval($_POST['amount']);
    $date = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("INSERT INTO donations (donor_name, phone, amount, date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $donor_name, $phone, $amount, $date);

    if ($stmt->execute()) {
        $_SESSION['alert'] = "<div class='alert success'>Donation added successfully!</div>";
    } else {
        $_SESSION['alert'] = "<div class='alert error'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle adding spending
if (isset($_POST['submit_spending'])) {
    $category = trim($_POST['category']);
    $spent_amount = floatval($_POST['spent_amount']);
    if ($spent_amount <= 0) {
        $_SESSION['alert'] = "<div class='alert error'>Amount must be positive.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO spending (category, amount) VALUES (?, ?)");
        $stmt->bind_param("sd", $category, $spent_amount);
        if ($stmt->execute()) {
            $_SESSION['alert'] = "<div class='alert success'>Spending recorded successfully!</div>";
        } else {
            $_SESSION['alert'] = "<div class='alert error'>Error recording spending: " . htmlspecialchars($conn->error) . "</div>";
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Handle adding a project
if (isset($_POST['submit_project'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);

    if (empty($title) || empty($description) || !isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $_SESSION['alert'] = "<div class='alert error'>All fields are required, including the image.</div>";
    } else {
        $upload_dir = 'uploads/';
        $file_name = uniqid() . '-' . basename($_FILES['image']['name']);
        $target_path = $upload_dir . $file_name;

        // Validate image
        $image_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($image_type, $allowed_types)) {
            $_SESSION['alert'] = "<div class='alert error'>Only JPG, JPEG, PNG, and GIF files are allowed.</div>";
        } elseif ($_FILES['image']['size'] > 5000000) { // 5MB limit
            $_SESSION['alert'] = "<div class='alert error'>Image size must be less than 5MB.</div>";
        } else {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $stmt = $conn->prepare("INSERT INTO projects (title, description, image_path) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $title, $description, $target_path);
                if ($stmt->execute()) {
                    $_SESSION['alert'] = "<div class='alert success'>Project added successfully!</div>";
                } else {
                    $_SESSION['alert'] = "<div class='alert error'>Error adding project: " . htmlspecialchars($conn->error) . "</div>";
                }
            } else {
                $_SESSION['alert'] = "<div class='alert error'>Failed to upload image.</div>";
            }
        }
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#projects");
    exit();
}

// Handle deleting a project
if (isset($_GET['delete_project_id'])) {
    $delete_id = intval($_GET['delete_project_id']);
    // Fetch image path to delete the file
    $stmt = $conn->prepare("SELECT image_path FROM projects WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $image_path = $row['image_path'];
        if (file_exists($image_path)) {
            unlink($image_path); // Delete the image file
        }
        $stmt = $conn->prepare("DELETE FROM projects WHERE id = ?");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $_SESSION['alert'] = "<div class='alert success'>Project deleted successfully!</div>";
    } else {
        $_SESSION['alert'] = "<div class='alert error'>Project not found.</div>";
    }
    header("Location: " . $_SERVER['PHP_SELF'] . "#projects");
    exit();
}

// Query to get total donations
$sql_total = "SELECT SUM(amount) AS total FROM donations";
$result_total = $conn->query($sql_total);
$total_donations = 0;
if ($result_total && $row_total = $result_total->fetch_assoc()) {
    $total_donations = $row_total['total'] ?? 0;
}

// Calculate total spent
$total_spent = 0;
$result_spending = $conn->query("SELECT SUM(amount) AS total_spent FROM spending");
if ($result_spending) {
    $total_spent = $result_spending->fetch_assoc()['total_spent'] ?? 0;
}
$remaining_donation = $total_donations - $total_spent;

// Query for volunteer applications
$sql_volunteers = "SELECT * FROM volunteers WHERE status = 'Pending' ORDER BY submitted_at DESC";
$result_volunteers = $conn->query($sql_volunteers);
$volunteer_count = $result_volunteers ? $result_volunteers->num_rows : 0;

// Query for projects
$sql_projects = "SELECT * FROM projects ORDER BY created_at DESC";
$result_projects = $conn->query($sql_projects);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal - Donations</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            margin: 0;
            padding: 0;
        }

        .container-wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
            box-sizing: border-box;
        }

        header {
            background: linear-gradient(90deg, #009879, #006d5a);
            color: white;
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .main-nav {
            width: 100%;
            display: flex;
            justify-content: center;
            gap: 1rem;
            padding: 0.5rem 0;
            background: rgba(0, 152, 121, 0.9);
            border-radius: 0;
            margin: 0.5rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .main-nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 0.6rem 1rem;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            position: relative;
        }

        .main-nav a::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 0;
            background: #fff;
            transition: width 0.3s ease;
        }

        .main-nav a:hover::after {
            width: 100%;
        }

        .main-nav a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .main-nav .badge {
            background: #dc3545;
            color: white;
            font-size: 0.7rem;
            padding: 0.2rem 0.5rem;
            border-radius: 10px;
            position: relative;
            top: -5px;
        }

        .menu-toggle {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            display: none;
        }

        .toggle-nav {
            display: none;
            background: #006d5a;
            padding: 0.5rem;
            border-radius: 5px;
            margin: 0.5rem 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .toggle-nav.show {
            display: block;
        }

        .toggle-nav a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 0.5rem;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .toggle-nav a:hover {
            color: #e0f7fa;
        }

        .search-form {
            display: flex;
            justify-content: center;
            margin: 0.5rem 0;
        }

        .search-input {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px 0 0 5px;
            width: 70%;
            max-width: 250px;
            font-size: 0.9rem;
        }

        .search-button {
            padding: 0.5rem 1rem;
            border: none;
            background-color: #006d5a;
            color: white;
            border-radius: 0 5px 5px 0;
            cursor: pointer;
            font-size: 0.9rem;
            transition: background-color 0.3s;
        }

        .search-button:hover {
            background-color: #005043;
        }

        h1 {
            color: #333;
            text-align: center;
            margin: 1rem 0;
            background: linear-gradient(90deg, #009879, #006d5a);
            color: white;
            padding: 0.75rem;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            font-size: 1.5rem;
            font-weight: 700;
            width: 100%;
        }

        .section {
            display: none;
            padding: 1rem;
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            width: 100%;
            box-sizing: border-box;
        }

        .section.active {
            display: block;
        }

        .donation-form {
            background: #f9f9f9;
            padding: 1rem;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            width: 100%;
            box-sizing: border-box;
        }

        .donation-form input,
        .form-container input,
        .form-container textarea {
            width: 100%;
            padding: 0.5rem;
            margin: 0.5rem 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 0.9rem;
        }

        .donation-form button,
        .form-container button {
            background: linear-gradient(90deg, #009879, #006d5a);
            color: white;
            border: none;
            padding: 0.5rem;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: transform 0.3s;
        }

        .donation-form button:hover,
        .form-container button:hover {
            transform: scale(1.05);
        }

        .total {
            text-align: center;
            font-size: 1rem;
            margin: 0.5rem 0;
            font-weight: 600;
            color: #006d5a;
            width: 100%;
        }

        table {
            width: 100% !important;
            border-collapse: collapse;
            background: white;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }

        th,
        td {
            padding: 0.5rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background: #009879;
            color: white;
            font-weight: 600;
        }

        tr:hover {
            background: #f1f1f1;
        }

        .btn-edit,
        .btn-danger,
        .btn-success,
        .btn-details {
            padding: 0.3rem 0.6rem;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0.2rem;
            font-size: 0.8rem;
            transition: transform 0.3s;
        }

        .btn-edit {
            background: #007bff;
            color: white;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-success {
            background: #28a745;
            color: white;
        }

        .btn-details {
            background: #6c757d;
            color: white;
        }

        .btn-edit:hover,
        .btn-danger:hover,
        .btn-success:hover,
        .btn-details:hover {
            transform: scale(1.1);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background: white;
            padding: 1rem;
            border-radius: 5px;
            max-width: 90%;
            width: 90%;
            position: relative;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 1.2rem;
            cursor: pointer;
            color: #dc3545;
            transition: color 0.3s;
        }

        .modal-content .close:hover {
            color: #a71d2a;
        }

        .modal-content div {
            margin: 0.5rem 0;
        }

        .modal-content input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.9rem;
        }

        .modal-content button {
            background: linear-gradient(90deg, #009879, #006d5a);
            color: white;
            border: none;
            padding: 0.5rem;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: transform 0.3s;
        }

        .modal-content button:hover {
            transform: scale(1.05);
        }

        .modal-error {
            color: #dc3545;
            font-weight: bold;
            font-size: 0.9rem;
        }

        .notification {
            position: fixed;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            background: #28a745;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 3px;
            z-index: 2000;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            font-size: 0.9rem;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .notification .close {
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            transition: color 0.3s;
        }

        .notification .close:hover {
            color: #fff;
        }

        .alert {
            padding: 0.5rem;
            border-radius: 3px;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .alert.success {
            background: #28a745;
            color: white;
        }

        .alert.error {
            background: #dc3545;
            color: white;
        }

        .project-image {
            width: 80px;
            height: auto;
            border-radius: 3px;
            transition: transform 0.3s;
        }

        .project-image:hover {
            transform: scale(1.1);
        }

        @media (max-width: 768px) {
            .container-wrapper {
                padding: 0 10px;
            }

            .header-container h1 {
                font-size: 1.2rem;
            }

            .main-nav {
                flex-direction: row;
                justify-content: space-around;
                padding: 0.3rem 0;
                gap: 0.3rem;
            }

            .main-nav a {
                padding: 0.4rem 0.6rem;
                font-size: 0.8rem;
            }

            .main-nav .badge {
                font-size: 0.6rem;
                padding: 0.15rem 0.4rem;
                top: -4px;
            }

            .menu-toggle {
                display: block;
            }

            .toggle-nav {
                width: 100%;
                text-align: center;
            }

            .toggle-nav a {
                padding: 0.4rem;
                font-size: 0.8rem;
            }

            .search-form {
                flex-direction: column;
                align-items: center;
                gap: 0.5rem;
            }

            .search-input {
                width: 100%;
                max-width: 100%;
                border-radius: 5px;
            }

            .search-button {
                width: 100%;
                border-radius: 5px;
            }

            .section {
                padding: 0.75rem;
            }

            h1 {
                font-size: 1.2rem;
                padding: 0.5rem;
            }

            .donation-form,
            .form-container {
                padding: 0.75rem;
            }

            .donation-form input,
            .form-container input,
            .form-container textarea {
                padding: 0.4rem;
                font-size: 0.8rem;
            }

            .donation-form button,
            .form-container button {
                padding: 0.4rem;
                font-size: 0.8rem;
            }

            table {
                font-size: 0.8rem;
            }

            th,
            td {
                padding: 0.4rem;
            }

            .btn-edit,
            .btn-danger,
            .btn-success,
            .btn-details {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }

            .project-image {
                width: 60px;
            }
        }

        @media (max-width: 480px) {
            .container-wrapper {
                padding: 0 5px;
            }

            .header-container h1 {
                font-size: 1rem;
            }

            .main-nav {
                gap: 0.2rem;
            }

            .main-nav a {
                padding: 0.3rem 0.5rem;
                font-size: 0.7rem;
            }

            .main-nav .badge {
                font-size: 0.5rem;
                padding: 0.1rem 0.3rem;
                top: -3px;
            }

            .search-input {
                padding: 0.3rem;
                font-size: 0.7rem;
            }

            .search-button {
                padding: 0.3rem 0.8rem;
                font-size: 0.7rem;
            }

            .section {
                padding: 0.5rem;
            }

            h1 {
                font-size: 1rem;
                padding: 0.3rem;
            }

            .donation-form,
            .form-container {
                padding: 0.5rem;
            }

            .donation-form input,
            .form-container input,
            .form-container textarea {
                padding: 0.3rem;
                font-size: 0.7rem;
            }

            .donation-form button,
            .form-container button {
                padding: 0.3rem;
                font-size: 0.7rem;
            }

            table {
                font-size: 0.7rem;
            }

            th,
            td {
                padding: 0.3rem;
            }

            .btn-edit,
            .btn-danger,
            .btn-success,
            .btn-details {
                padding: 0.2rem 0.3rem;
                font-size: 0.6rem;
            }

            .project-image {
                width: 50px;
            }
        }
    </style>
</head>

<body>
    <header>
        <div class="container-wrapper">
            <div class="header-container">
                <h1 id="nav-txt">Rah-e-Umeed</h1>
                <button class="menu-toggle" aria-label="Toggle menu" onclick="toggleMenu()">☰</button>
            </div>
        </div>
        <nav class="main-nav">
            <a href="#add-donation" onclick="showSection('donations-section')">Add Donation</a>
            <a href="#projects" onclick="showSection('projects-section')">Projects</a>
            <a href="#calculator" onclick="showSection('calculator-section')">Calculator</a>
            <a href="#volunteers" onclick="showSection('volunteers-section')">
                <i class="fas fa-envelope"></i>
                <?php if ($volunteer_count > 0): ?>
                    <span class="badge"><?php echo $volunteer_count; ?></span>
                <?php endif; ?>
            </a>
        </nav>
        <div class="container-wrapper">
            <div class="toggle-nav" id="toggleMenu">
                <form action="logout.php" method="post" style="display:inline;">
                    <button type="submit" class="logout-button">Logout</button>
                </form>
                <a href="index.php">Home</a>
            </div>
            <form class="search-form" method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="text" name="search" placeholder="Search donor name..." class="search-input" />
                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
    </header>

    <?php
    // Display alert if set
    if (isset($_SESSION['admin_alert'])) {
        echo "<div class='notification'>";
        echo $_SESSION['admin_alert'];
        echo "<span class='close' onclick='this.parentElement.style.display=\"none\"'>×</span>";
        echo "</div>";
        unset($_SESSION['admin_alert']);
    }
    if (isset($_SESSION['alert'])) {
        echo "<div class='notification'>";
        echo $_SESSION['alert'];
        echo "<span class='close' onclick='this.parentElement.style.display=\"none\"'>×</span>";
        echo "</div>";
        unset($_SESSION['alert']);
    }
    ?>

    <div class="container-wrapper">
        <!-- Add Manual Donations Section -->
        <div id="donations-section" class="section active">
            <h1>Admin Portal - Donation Records</h1>
            <div class="donation-form">
                <form method="POST">
                    <input type="text" name="name" placeholder="Name" required>
                    <input type="text" name="phone" placeholder="Phone" required>
                    <input type="number" name="amount" placeholder="Amount (PKR)" required>
                    <button type="submit" name="submit_manual">Add Donation</button>
                </form>
            </div>
            <div class="total">
                Total Donations Collected: <strong>PKR <?php echo number_format($total_donations, 2); ?></strong>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor Name</th>
                        <th>Phone Number</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $counter++ . "</td>";
                            echo "<td>
                                <span id='name_display_{$row['id']}'>" . htmlspecialchars($row['donor_name']) . "</span>
                                <input type='text' id='name_input_{$row['id']}' value='" . htmlspecialchars($row['donor_name']) . "' style='display:none;width:80%;'>
                                <button class='btn-edit' onclick='toggleEdit({$row['id']}, \"name\", \"donations\")'>✎</button>
                            </td>";
                            echo "<td>
                                <span id='phone_display_{$row['id']}'>" . htmlspecialchars($row['phone']) . "</span>
                                <input type='text' id='phone_input_{$row['id']}' value='" . htmlspecialchars($row['phone']) . "' style='display:none;width:80%;'>
                                <button class='btn-edit' onclick='toggleEdit({$row['id']}, \"phone\", \"donations\")'>✎</button>
                            </td>";
                            echo "<td>
                                <span id='amount_display_{$row['id']}'>PKR " . number_format($row['amount'], 2) . "</span>
                                <input type='number' id='amount_input_{$row['id']}' value='" . htmlspecialchars($row['amount']) . "' style='display:none;width:80%;'>
                                <button class='btn-edit' onclick='toggleEdit({$row['id']}, \"amount\", \"donations\")'>✎</button>
                            </td>";
                            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                            echo "<td><a href='?delete_id=" . $row['id'] . "' class='btn-danger'>Delete</a></td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No donations found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Modal for Editing Donation -->
        <div id="editModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">×</span>
                <h2>Edit Donation</h2>
                <form method="POST" action="">
                    <input type="hidden" id="donation_id" name="id">
                    <div>
                        <label for="donor_name">Donor Name:</label>
                        <input type="text" id="donor_name" name="donor_name" required>
                    </div>
                    <div>
                        <label for="phone">Phone Number:</label>
                        <input type="text" id="phone" name="phone" required>
                    </div>
                    <div>
                        <label for="amount">Donation Amount (PKR):</label>
                        <input type="number" id="amount" name="amount" required>
                    </div>
                    <div>
                        <button type="submit" name="update_donation">Update Donation</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Calculator Section -->
        <div id="calculator-section" class="section">
            <h1>Donation Expense Tracker</h1>
            <div class="donation-form">
                <form method="POST">
                    <input type="text" name="category" placeholder="Spending Category" required>
                    <input type="number" step="0.01" name="spent_amount" placeholder="Amount (PKR)" required>
                    <button type="submit" name="submit_spending">Submit</button>
                </form>
            </div>
            <div class="mt-4 text-center">
                <h4>Total Donations: PKR <?php echo number_format($total_donations, 2); ?></h4>
                <h4>Total Spent: PKR <?php echo number_format($total_spent, 2); ?></h4>
                <h4><strong>Remaining Balance: PKR <?php echo number_format($remaining_donation, 2); ?></strong></h4>
            </div>
            <div class="mt-5">
                <h3>Spending History</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $expenses = $conn->query("SELECT * FROM spending ORDER BY date DESC");
                        if ($expenses->num_rows > 0) {
                            while ($expense = $expenses->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . htmlspecialchars($expense['date']) . "</td>";
                                echo "<td>
                                    <span id='category_display_{$expense['id']}'>" . htmlspecialchars($expense['category']) . "</span>
                                    <input type='text' id='category_input_{$expense['id']}' value='" . htmlspecialchars($expense['category']) . "' style='display:none;width:80%;'>
                                    <button class='btn-edit' onclick='toggleEdit({$expense['id']}, \"category\", \"spending\")'>✎</button>
                                </td>";
                                echo "<td>
                                    <span id='amount_display_{$expense['id']}'>PKR " . number_format($expense['amount'], 2) . "</span>
                                    <input type='number' step='0.01' id='amount_input_{$expense['id']}' value='" . htmlspecialchars($expense['amount']) . "' style='display:none;width:80%;'>
                                    <button class='btn-edit' onclick='toggleEdit({$expense['id']}, \"amount\", \"spending\")'>✎</button>
                                </td>";
                                echo "<td><a href='?delete_spending_id=" . $expense['id'] . "' class='btn-danger'>Delete</a></td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No spending records found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Volunteer Applications Section -->
        <div id="volunteers-section" class="section">
            <h1>Volunteer Applications</h1>
            <?php if ($volunteer_count > 0): ?>
                <h3>(<?php echo $volunteer_count; ?> Pending)</h3>
                <div class="accordion" id="volunteerAccordion">
                    <?php
                    $index = 1;
                    $result_volunteers->data_seek(0); // Reset pointer for re-use
                    while ($volunteer = $result_volunteers->fetch_assoc()): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="volunteerHeading<?php echo $volunteer['id']; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#volunteerCollapse<?php echo $volunteer['id']; ?>" aria-expanded="false"
                                    aria-controls="volunteerCollapse<?php echo $volunteer['id']; ?>">
                                    <?php echo $index++ . '. ' . htmlspecialchars($volunteer['full_name']); ?>
                                </button>
                            </h2>
                            <div id="volunteerCollapse<?php echo $volunteer['id']; ?>" class="accordion-collapse collapse"
                                aria-labelledby="volunteerHeading<?php echo $volunteer['id']; ?>"
                                data-bs-parent="#volunteerAccordion">
                                <div class="accordion-body">
                                    <p><strong>Email:</strong> <?php echo htmlspecialchars($volunteer['email']); ?></p>
                                    <p><strong>Submitted:</strong> <?php echo htmlspecialchars($volunteer['submitted_at']); ?>
                                    </p>
                                    <div class="d-flex gap-2">
                                        <a href="process_volunteer.php?action=approve&id=<?php echo $volunteer['id']; ?>"
                                            class="btn btn-success"
                                            onclick="return confirm('Approve this volunteer?')">Approve</a>
                                        <a href="process_volunteer.php?action=reject&id=<?php echo $volunteer['id']; ?>"
                                            class="btn btn-danger" onclick="return confirm('Reject this volunteer?')">Reject</a>
                                        <a href="#" class="btn btn-details"
                                            onclick="openVolunteerModal(<?php echo $volunteer['id']; ?>)">View Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No pending volunteer applications.</p>
            <?php endif; ?>
        </div>

        <!-- Projects Section -->
        <div id="projects-section" class="section">
            <h1>Manage Projects</h1>
            <div class="donation-form form-container">
                <h2>Add New Project</h2>
                <form method="POST" enctype="multipart/form-data">
                    <input type="text" name="title" placeholder="Project Title" required>
                    <textarea name="description" placeholder="Project Description" required></textarea>
                    <input type="file" name="image" accept="image/*" required>
                    <button type="submit" name="submit_project">Add Project</button>
                </form>
            </div>
            <div class="mt-5">
                <h3>Existing Projects</h3>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Image</th>
                            <th>Created At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $project_counter = 1;
                        if ($result_projects->num_rows > 0) {
                            while ($project = $result_projects->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $project_counter++ . "</td>";
                                echo "<td>" . htmlspecialchars($project['title']) . "</td>";
                                echo "<td>" . htmlspecialchars($project['description']) . "</td>";
                                echo "<td><img src='" . htmlspecialchars($project['image_path']) . "' alt='Project Image' class='project-image'></td>";
                                echo "<td>" . htmlspecialchars($project['created_at']) . "</td>";
                                echo "<td>
                                    <a href='?delete_project_id=" . $project['id'] . "' class='btn-danger' onclick='return confirm(\"Are you sure you want to delete this project?\")'>Delete</a>
                                </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='6'>No projects found.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal for Volunteer Details -->
        <div id="volunteerModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeVolunteerModal()">×</span>
                <h2>Volunteer Application Details</h2>
                <div id="volunteerDetails">
                    <p id="modalError" class="modal-error" style="display: none;">Failed to load volunteer details.
                        Please try again.</p>
                    <p><strong>Name:</strong> <span id="volunteer_name">Loading...</span></p>
                    <p><strong>Email:</strong> <span id="volunteer_email">Loading...</span></p>
                    <p><strong>Phone:</strong> <span id="volunteer_phone">Loading...</span></p>
                    <p><strong>Reason:</strong> <span id="volunteer_reason">Loading...</span></p>
                    <p><strong>Submitted:</strong> <span id="volunteer_submitted">Loading...</span></p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Fallback for Bootstrap JavaScript
        window.bootstrap = window.bootstrap || {};
        function loadBootstrapFallback() {
            if (typeof bootstrap.Collapse === 'undefined') {
                console.warn('Primary Bootstrap CDN failed, attempting fallback...');
                var script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js';
                script.integrity = 'sha512-7Pi/otdlbbCR+5W3OpRjG8Y0K+eMIrQ7s4nZ3z4e4hKCS/xS+ndj8C4h7veohSD2TJCaAyJSvS94nbcFEdDe1Ow==';
                script.crossOrigin = 'anonymous';
                script.onload = function () {
                    if (typeof bootstrap.Collapse !== 'undefined') {
                        console.log('Bootstrap loaded from fallback CDN');
                    } else {
                        console.error('Bootstrap failed to load from fallback CDN');
                    }
                };
                script.onerror = function () {
                    console.error('Failed to load Bootstrap from fallback CDN');
                };
                document.head.appendChild(script);
            }
        }

        // Check Bootstrap after scripts load
        window.addEventListener('load', function () {
            setTimeout(function () {
                if (typeof bootstrap.Collapse === 'undefined') {
                    console.error('Bootstrap JavaScript not loaded');
                    loadBootstrapFallback();
                } else {
                    console.log('Bootstrap JavaScript loaded successfully');
                }
            }, 1000); // Delay to ensure scripts have time to load
        });

        function openModal(id) {
            var modal = document.getElementById("editModal");
            modal.style.display = "block";
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "fetch_donor_details.php?id=" + id, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    var donor = JSON.parse(xhr.responseText);
                    document.getElementById("donation_id").value = donor.id;
                    document.getElementById("donor_name").value = donor.donor_name;
                    document.getElementById("phone").value = donor.phone;
                    document.getElementById("amount").value = donor.amount;
                } else {
                    console.error('Failed to fetch donor details:', xhr.status, 'Response:', xhr.responseText);
                }
            };
            xhr.onerror = function () {
                console.error('Error fetching donor details');
            };
            xhr.send();
        }

        function closeModal() {
            var modal = document.getElementById("editModal");
            modal.style.display = "none";
        }

        function openVolunteerModal(id) {
            var modal = document.getElementById("volunteerModal");
            var errorElement = document.getElementById("modalError");
            var fields = ['volunteer_name', 'volunteer_email', 'volunteer_phone', 'volunteer_reason', 'volunteer_submitted'];

            // Show modal and set loading state
            modal.style.display = "block";
            errorElement.style.display = "none";
            fields.forEach(field => {
                document.getElementById(field).innerText = "Loading...";
            });

            // Validate id
            if (!id || isNaN(id)) {
                console.error('Invalid volunteer ID:', id);
                errorElement.innerText = "Invalid volunteer ID.";
                errorElement.style.display = "block";
                fields.forEach(field => {
                    document.getElementById(field).innerText = "N/A";
                });
                return;
            }

            // Construct dynamic path
            var basePath = window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1);
            var url = basePath + 'fetch_volunteer_details.php?id=' + encodeURIComponent(id);

            var xhr = new XMLHttpRequest();
            xhr.open("GET", url, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    try {
                        var volunteer = JSON.parse(xhr.responseText);
                        if (volunteer && volunteer.id) {
                            document.getElementById("volunteer_name").innerText = volunteer.full_name || "N/A";
                            document.getElementById("volunteer_email").innerText = volunteer.email || "N/A";
                            document.getElementById("volunteer_phone").innerText = volunteer.phone || "N/A";
                            document.getElementById("volunteer_reason").innerText = volunteer.reason || "N/A";
                            document.getElementById("volunteer_submitted").innerText = volunteer.submitted_at || "N/A";
                        } else {
                            console.error('No volunteer data returned:', volunteer);
                            errorElement.innerText = "No volunteer data found.";
                            errorElement.style.display = "block";
                            fields.forEach(field => {
                                document.getElementById(field).innerText = "N/A";
                            });
                        }
                    } catch (e) {
                        console.error('JSON parsing error:', e, 'Response:', xhr.responseText);
                        errorElement.innerText = "Error parsing volunteer data.";
                        errorElement.style.display = "block";
                        // Display raw response for debugging
                        document.getElementById("volunteer_name").innerText = "Raw: " + xhr.responseText.substring(0, 50) + "...";
                        fields.slice(1).forEach(field => {
                            document.getElementById(field).innerText = "N/A";
                        });
                    }
                } else {
                    console.error('Failed to fetch volunteer details. Status:', xhr.status, 'Response:', xhr.responseText);
                    errorElement.innerText = "Failed to load volunteer details (Error " + xhr.status + ").";
                    errorElement.style.display = "block";
                    fields.forEach(field => {
                        document.getElementById(field).innerText = "N/A";
                    });
                }
            };
            xhr.onerror = function () {
                console.error('Network error fetching volunteer details');
                errorElement.innerText = "Network error. Please check your connection.";
                errorElement.style.display = "block";
                fields.forEach(field => {
                    document.getElementById(field).innerText = "N/A";
                });
            };
            xhr.send();
        }

        function closeVolunteerModal() {
            var modal = document.getElementById("volunteerModal");
            modal.style.display = "none";
            document.getElementById("modalError").style.display = "none";
        }

        function toggleEdit(id, field, table) {
            const displaySpan = document.getElementById(field + '_display_' + id);
            const inputField = document.getElementById(field + '_input_' + id);
            if (inputField.style.display === 'none') {
                displaySpan.style.display = 'none';
                inputField.style.display = 'inline-block';
                inputField.focus();
            } else {
                const newValue = inputField.value;
                const xhr = new XMLHttpRequest();
                const url = table === 'spending' ? 'update_spending_field.php' : 'update_field.php';
                xhr.open("POST", url, true);
                xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                xhr.onload = function () {
                    if (xhr.status === 200) {
                        displaySpan.innerText = (field === 'amount' ? 'PKR ' : '') + (field === 'amount' ? parseFloat(newValue).toFixed(2) : newValue);
                        inputField.style.display = 'none';
                        displaySpan.style.display = 'inline';
                    } else {
                        alert("Failed to update.");
                    }
                };
                xhr.onerror = function () {
                    console.error('Error updating field');
                };
                xhr.send("id=" + id + "&field=" + field + "&value=" + encodeURIComponent(newValue));
            }
        }

        function toggleMenu() {
            const menu = document.getElementById('toggleMenu');
            menu.classList.toggle('show');
        }

        // Show/Hide Sections
        function showSection(sectionId) {
            document.querySelectorAll('.section').forEach(section => {
                section.classList.remove('active');
            });
            const targetSection = document.getElementById(sectionId);
            if (targetSection) {
                targetSection.classList.add('active');
                targetSection.scrollIntoView({ behavior: 'smooth' });
            }
        }

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

        // Auto-dismiss notification after 5 seconds
        setTimeout(() => {
            const notification = document.querySelector('.notification');
            if (notification) notification.style.display = 'none';
        }, 5000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"
        onerror="loadBootstrapFallback()"></script>
</body>

</html>

<?php $conn->close(); ?>