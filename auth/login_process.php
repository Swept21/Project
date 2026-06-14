<?php

session_start(); // start session to store user login data
include("../config/database.php"); // include database connection file

$username = $_POST['username']; // get username from login form
$password = $_POST['password']; // get password from login form

$sql = "SELECT * FROM users WHERE username = ?"; // query to find user by username

$stmt = mysqli_prepare($conn, $sql); // prepare SQL statement to prevent SQL injection
mysqli_stmt_bind_param($stmt, "s", $username); // bind username parameter to query
mysqli_stmt_execute($stmt); // execute prepared statement

$result = mysqli_stmt_get_result($stmt); // get query result

if(mysqli_num_rows($result) > 0){ // check if user exists

    $user = mysqli_fetch_assoc($result); // fetch user data as associative array

    if(password_verify($password, $user['password_hash'])){ // verify hashed password

        $_SESSION['user_id'] = $user['user_id']; // store user ID in session
        $_SESSION['role'] = $user['role']; // store user role in session
        $_SESSION['username'] = $user['username']; // store username in session

        $donorQuery = mysqli_prepare(
            $conn,
            "SELECT donor_id FROM donors WHERE user_id = ? LIMIT 1" // check if user has donor profile
        );

        mysqli_stmt_bind_param($donorQuery, "i", $user['user_id']); // bind user ID
        mysqli_stmt_execute($donorQuery); // execute donor query

        $resultDonor = mysqli_stmt_get_result($donorQuery); // get donor query result
        $donorData = mysqli_fetch_assoc($resultDonor); // fetch donor data

        if($donorData){
            $_SESSION['donor_id'] = $donorData['donor_id']; // store donor ID if exists
        } else {
            // user can still donate WITHOUT donor profile
            $_SESSION['donor_id'] = 0; // set default donor ID if none exists
        }

        if($user['role'] == 'admin'){
            header("Location: ../dashboard/dashboard.php"); // redirect admin to admin dashboard
            exit;
        } else {
            header("Location: ../dashboard/user_dashboard.php"); // redirect normal user to user dashboard
            exit;
        }

    } else {
        // Styled alternative: save error in session and redirect back
        $_SESSION['error'] = "Incorrect password. Please try again.";
        header("Location: login.php");
        exit;
    }

} else {
    // Styled alternative: save error in session and redirect back
    $_SESSION['error'] = "User account not found.";
    header("Location: login.php");
    exit;
}

?>