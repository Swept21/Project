<?php
session_start();
include("../config/database.php");

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    exit("Access Denied.");
}

// 1. Configure the browser down-stream parameters
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=donations_report_' . date('Y-m-d') . '.csv');

// 2. Open up PHP memory write wrapper
$output = fopen('php://output', 'w');

// 3. Write Column Headers line matching table schemas
fputcsv($output, array('Donation ID', 'Donor Name', 'Amount', 'Payment Method', 'Date', 'Status'));

// 4. Fetch real-time records data tracking fallbacks correctly
$sql = "
SELECT d.donation_id,
       COALESCE(NULLIF(TRIM(CONCAT(dr.first_name, ' ', dr.last_name)), ''), u.username, 'Unknown User') AS donor_name,
       d.amount, d.payment_method, d.donation_date, d.status
FROM donations d
LEFT JOIN donors dr ON d.donor_id = dr.donor_id
LEFT JOIN users u ON dr.user_id = u.user_id
ORDER BY d.donation_date DESC";

$result = mysqli_query($conn, $sql);
while($row = mysqli_fetch_assoc($result)) {
    fputcsv($output, $row);
}

fclose($output);
exit;