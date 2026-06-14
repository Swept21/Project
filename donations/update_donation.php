<?php

include("../config/database.php");

$sql = "
UPDATE donations
SET

donor_id=?,
amount=?,
payment_method=?,
donation_date=?,
status=?

WHERE donation_id=?
";

$stmt = mysqli_prepare($conn,$sql);

mysqli_stmt_bind_param(
$stmt,
"idsssi",

$_POST['donor_id'],
$_POST['amount'],
$_POST['payment_method'],
$_POST['donation_date'],
$_POST['status'],
$_POST['donation_id']
);

mysqli_stmt_execute($stmt);

header("Location:view_donations.php");

?>