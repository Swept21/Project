<?php

session_start();
include("../config/database.php");

/*CHECK ADMIN ACCESS */

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

/* VALIDATE ID*/

if(!isset($_GET['id']) || empty($_GET['id'])){
    die("Invalid request: missing donation ID.");
}

$id = (int) $_GET['id'];

/* DELETE QUERY*/

$sql = "DELETE FROM donations WHERE donation_id = ?";

$stmt = mysqli_prepare($conn, $sql);

if(!$stmt){
    die("Prepare failed: " . mysqli_error($conn));
}

mysqli_stmt_bind_param($stmt, "i", $id);

if(!mysqli_stmt_execute($stmt)){
    die("Delete failed: " . mysqli_stmt_error($stmt));
}

/* REDIRECT BACK */

header("Location: view_donations.php");
exit;

?>