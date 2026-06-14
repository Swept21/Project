<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../config/database.php");

$username = "adminko";
$password = "admin123";

/* Tignan kung may admin na */

$check = mysqli_query(
    $conn,
    "SELECT * FROM users WHERE role='admin'"
);

if(mysqli_num_rows($check) > 0){

    die("Admin already exists.");

}

/* Hash Password */

$hash = password_hash($password, PASSWORD_DEFAULT);

/* insert Admin*/

$sql = "
INSERT INTO users
(username, password_hash, role)
VALUES
(?, ?, 'admin')
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ss",
    $username,
    $hash
);

mysqli_stmt_execute($stmt);

echo "Admin account created successfully.";

?>