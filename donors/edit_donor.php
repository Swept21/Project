<?php
session_start();
include("../config/database.php");

// AUTH CHECK (ADMIN ONLY)

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Initialize messages
$error_msg = "";
$success_msg = "";

// POST METHOD: UPDATE GIVEN DONOR PROFILE

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $donor_id   = $_POST['donor_id'];
    $first_name = $_POST['first_name'];
    $last_name  = $_POST['last_name'];
    $city       = $_POST['city'];
    $province   = $_POST['province'];
    $region     = $_POST['region'];

    if (!empty($donor_id) && !empty($first_name) && !empty($last_name)) {
        $update_sql = "
            UPDATE donors 
            SET first_name = ?, last_name = ?, city = ?, province = ?, region = ? 
            WHERE donor_id = ?
        ";
        
        $stmt = mysqli_prepare($conn, $update_sql);
        mysqli_stmt_bind_param($stmt, "sssssi", $first_name, $last_name, $city, $province, $region, $donor_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_msg = "Donor profile updated successfully!";
        } else {
            $error_msg = "Something went wrong updating the record.";
        }
    } else {
        $error_msg = "Please fill in all mandatory fields.";
    }
}

// GET METHOD: FETCH EXISTING CURRENT DONOR DETAILS

if (isset($_GET['id'])) {
    $donor_id = $_GET['id'];
    
    $fetch_sql = "SELECT * FROM donors WHERE donor_id = ?";
    $stmt = mysqli_prepare($conn, $fetch_sql);
    mysqli_stmt_bind_param($stmt, "i", $donor_id);
    mysqli_stmt_execute($stmt);
    
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) > 0) {
        $donor = mysqli_fetch_assoc($result);
    } else {
        die("Donor record not found. <a href='view_donors.php'>Go Back</a>");
    }
} else if (!isset($_POST['donor_id'])) {
    // If no ID is passed via URL query string and it wasn't a post edit submission
    header("Location: view_donors.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Donor Details</title>
    <link rel="stylesheet" href="../design/styles.css">
    <style>
        /* Basic utility styling matching your standard admin forms */
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-success { background-color: #d4edda; color: #155724; }
        .alert-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">Admin - DonorInsight</div>
    <div>Modify Profile Information</div>
</div>

<div class="navbar">
    <a href="../dashboard/dashboard.php">Dashboard</a>
    <a href="view_donors.php">Donors</a>
    <a href="../donations/view_donations.php">Donations</a>
    <a href="../analytics/analytics_dashboard.php">Analytics</a>
    <a href="../etl/sync_olap.php">ETL</a>
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="container">
    <h2>Edit Donor Profile</h2>
    <a href="view_donors.php" style="text-decoration: none;">&larr; Back to Donor List</a>
    <br><br>

    <?php if(!empty($success_msg)){ ?>
        <div class="alert alert-success"><?= $success_msg; ?></div>
    <?php } ?>
    <?php if(!empty($error_msg)){ ?>
        <div class="alert alert-danger"><?= $error_msg; ?></div>
    <?php } ?>

    <form action="edit_donor.php?id=<?= urlencode($donor_id ?? $_POST['donor_id']); ?>" method="POST" style="max-width: 500px;">
        
        <input type="hidden" name="donor_id" value="<?= htmlspecialchars($donor['donor_id'] ?? $_POST['donor_id']); ?>">

        <div class="form-group">
            <label>First Name</label>
            <input type="text" name="first_name" value="<?= htmlspecialchars($donor['first_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Last Name</label>
            <input type="text" name="last_name" value="<?= htmlspecialchars($donor['last_name'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" value="<?= htmlspecialchars($donor['city'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Province</label>
            <input type="text" name="province" value="<?= htmlspecialchars($donor['province'] ?? ''); ?>" required>
        </div>

        <div class="form-group">
            <label>Region</label>
            <input type="text" name="region" value="<?= htmlspecialchars($donor['region'] ?? ''); ?>" required>
        </div>

        <br>
        <button type="submit" class="btn btn-primary" style="padding: 10px 20px; cursor: pointer;">
            Save / Update Changes
        </button>
    </form>
</div>

</body>
</html>