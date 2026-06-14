<?php
session_start();
include("../config/database.php");

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ACCESS
|--------------------------------------------------------------------------
*/
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    exit("Unauthorized access.");
}

/*
|--------------------------------------------------------------------------
| STREAM HEADERS FOR MICROSOFT EXCEL
|--------------------------------------------------------------------------
*/
$filename = "Donation_Report_" . date('Y-m-d') . ".xls";
header("Content-Type: application/vnd.ms-excel; charset=utf-8");
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);

/*
|--------------------------------------------------------------------------
| FETCH RECORDS (IDENTICAL FALLBACK FIX TO PREVENT 'UNKNOWN USER')
|--------------------------------------------------------------------------
*/
$sql = "
SELECT
    d.donation_id,
    COALESCE(NULLIF(TRIM(CONCAT(dr.first_name, ' ', dr.last_name)), ''), u.username, 'Unknown User') AS donor_name,
    d.amount,
    d.payment_method,
    d.donation_date,
    d.status
FROM donations d
LEFT JOIN donors dr ON d.donor_id = dr.donor_id
LEFT JOIN users u ON dr.user_id = u.user_id
ORDER BY d.donation_date DESC
";
$result = mysqli_query($conn, $sql);
?>
<!-- Excel interprets this XML layout seamlessly -->
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
    <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
    <style>
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #2d6a4f; color: #ffffff; font-weight: bold; border: 1px solid #cccccc; }
        td { border: 1px solid #cccccc; vertical-align: middle; }
        .number-format { mso-number-format:"\#\,\#\#0\.00"; text-align: right; } /* Forces Excel Currency Text Alignment Rules */
        .text-format { mso-number-format:"\@"; }
    </style>
</head>
<body>
    <h3>DonorInsight System Financial Report - Generated on <?= date('Y-m-d H:i:s'); ?></h3>
    <table>
        <thead>
            <tr>
                <th>Donation ID</th>
                <th>Donor Name</th>
                <th>Amount (PHP)</th>
                <th>Payment Method</th>
                <th>Donation Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td class="text-format"><?= $row['donation_id']; ?></td>
                    <td><?= htmlspecialchars($row['donor_name']); ?></td>
                    <td class="number-format"><?= number_format($row['amount'], 2, '.', ''); ?></td>
                    <td><?= htmlspecialchars($row['payment_method']); ?></td>
                    <td><?= htmlspecialchars($row['donation_date']); ?></td>
                    <td><?= htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>