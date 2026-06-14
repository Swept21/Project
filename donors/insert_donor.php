<?php

include("../config/database.php");

$sql = "
INSERT INTO donors
(
first_name,
last_name,
email,
contact_number,
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
?,
?,
?
)
";

$stmt = mysqli_prepare(
$conn,
$sql
);

mysqli_stmt_bind_param(
$stmt,
"sssssss",
$_POST['first_name'],
$_POST['last_name'],
$_POST['email'],
$_POST['contact_number'],
$_POST['city'],
$_POST['province'],
$_POST['region']
);

mysqli_stmt_execute($stmt);

header(
"Location:view_donors.php"
);
?>