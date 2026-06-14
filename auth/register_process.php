<?php

session_start(); // Required to send error messages back to register.php via $_SESSION
include("../config/database.php");

// 1. Collect POST data from your form
$first_name = $_POST['first_name'];
$last_name  = $_POST['last_name'];
$city       = $_POST['city'];
$province   = $_POST['province'];
$region     = $_POST['region'];
$username   = $_POST['username'];
$password   = $_POST['password'];

/* BACKEND VALIDATION: Block Numbers and Special Characters*/
$alpha_pattern = "/^[a-zA-Z\s\-]+$/";

if (!preg_match($alpha_pattern, $first_name) || 
    !preg_match($alpha_pattern, $last_name)  || 
    !preg_match($alpha_pattern, $city)       || 
    !preg_match($alpha_pattern, $province)   || 
    !preg_match($alpha_pattern, $region)) {
    
    // Set a user-friendly alert and return to registration form
    $_SESSION['error'] = "Numbers and special characters are not allowed in name or address fields.";
    header("Location: register.php");
    exit; // Stop executing the rest of the file completely
}

/* Check if user exist */
$check = mysqli_prepare(
    $conn,
    "SELECT user_id FROM users WHERE username = ?"
);

mysqli_stmt_bind_param($check, "s", $username);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if(mysqli_stmt_num_rows($check) > 0){
    die("Username already exists. <a href='register.php'>Go back</a>");
}

/* hash password */
$password_hash = password_hash(
    $password, 
    PASSWORD_DEFAULT
);

/* creation of donor profile */
$sql = "
INSERT INTO donors
(
    first_name,
    last_name,
    city,
    province,
    region
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?
)
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $first_name,
    $last_name,
    $city,
    $province,
    $region
);
mysqli_stmt_execute($stmt);

/* Get Donor ID*/
$donor_id = mysqli_insert_id($conn);

/* Create User */
$sql = "
INSERT INTO users
(
    username,
    password_hash,
    role,
    donor_id
)
VALUES
(
    ?,
    ?,
    'user',
    ?
)
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param(
    $stmt,
    "ssi",
    $username,
    $password_hash,
    $donor_id
);
mysqli_stmt_execute($stmt);

/* BACK-FILL USER ID INTO DONORS TABLE */
$new_user_id = mysqli_insert_id($conn);

$update_donor_sql = "UPDATE donors SET user_id = ? WHERE donor_id = ?";
$update_stmt = mysqli_prepare($conn, $update_donor_sql);
mysqli_stmt_bind_param($update_stmt, "ii", $new_user_id, $donor_id);
mysqli_stmt_execute($update_stmt);

// Redirect to login page
header("Location: login.php");
exit;
?>