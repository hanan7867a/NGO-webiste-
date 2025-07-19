<!-- Bootstrap CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<?php
include('configer.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_name = $_POST['donor_name'];
    $donor_phone = $_POST['phone'];
    $amount = $_POST['amount'];

    // Insert donation into the database
    $sql = "INSERT INTO donations (donor_name, donor_phone, amount) VALUES ('$donor_name', '$donor_phone', '$amount')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success text-center'>Donation successful! Thank you for your support.</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error: " . $sql . "<br>" . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donate - Rah e Umeed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background-color: #f8f9fa;">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white text-center">
                <h2>Donate to Rah e Umeed</h2>
            </div>
            <div class="card-body">
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="donor_name" class="form-label">Full Name:</label>
                        <input type="text" name="donor_name" id="donor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone Number:</label>
                        <input type="text" name="phone" id="phone" class="form-control" required pattern="[0-9]{10,15}" placeholder="e.g., 03001234567">
                    </div>
                    <div class="mb-3">
                        <label for="amount" class="form-label">Donation Amount (PKR):</label>
                        <input type="number" name="amount" id="amount" class="form-control" required>
                    </div>
                    <button type="submit" name="submit" class="btn btn-success w-100">Donate</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
