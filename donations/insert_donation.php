<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../config/database.php");

/*VALIDATE SESSION FIRST*/
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';

/* DETERMINE TARGET DONOR ID*/
if ($role === 'admin' && isset($_POST['donor_id']) && !empty($_POST['donor_id'])) {
    $donor_id = intval($_POST['donor_id']);
} else {
    // Check session first, otherwise query via account fields
    if (isset($_SESSION['donor_id']) && $_SESSION['donor_id'] > 0) {
        $donor_id = $_SESSION['donor_id'];
    } else {
        // Fallback check to match either link direction
        $result = mysqli_query($conn, "SELECT donor_id FROM users WHERE user_id = $user_id LIMIT 1");
        $row = mysqli_fetch_assoc($result);
        
        if ($row && !empty($row['donor_id'])) {
            $donor_id = $row['donor_id'];
            $_SESSION['donor_id'] = $donor_id; // Sync back to session
        } else {
            $result = mysqli_query($conn, "SELECT donor_id FROM donors WHERE user_id = $user_id LIMIT 1");
            $row = mysqli_fetch_assoc($result);
            $donor_id = $row ? $row['donor_id'] : null;
        }
    }
}

if (!$donor_id) {
    die("Error: Could not identify your donor profile records. Please contact support.");
}

// COLLECT FORM VALUES

$amount        = $_POST['amount'];
$pay_method    = $_POST['payment_method'];
$donation_date = date('Y-m-d'); // Default to today's date if not passed explicitly
$status        = 'Completed';

// SQL INSERT

$sql = "
INSERT INTO donations (donor_id, amount, payment_method, donation_date, status, user_id)
VALUES (?, ?, ?, ?, ?, ?)
";

$stmt = mysqli_prepare($conn, $sql);
if(!$stmt){
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "idsssi", $donor_id, $amount, $pay_method, $donation_date, $status, $user_id);

if(!mysqli_stmt_execute($stmt)){
    die("Execute failed: " . mysqli_stmt_error($stmt));
}

// REDIRECT BASED ON ROLE

$_SESSION['success'] = "Donation processed successfully! Thank you!";

if($role === 'admin') {
    header("Location: view_donations.php");
} else {
    header("Location: ../dashboard/user_dashboard.php"); 
}
exit;
?>