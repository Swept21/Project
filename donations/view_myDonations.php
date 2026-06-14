<?php
session_start();

include("../config/database.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] != 'user') {
    header("Location: ../auth/login.php");
    exit;
}

$donor_id = $_SESSION['donor_id'];

$sql = "
SELECT *
FROM donations
WHERE donor_id = ?
ORDER BY donation_date DESC
";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $donor_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html>
<head>

    <title>My Donations</title>

    <link rel="stylesheet" href="../design/styles.css">

</head>

<body>

    <div class="header">
        <div class="logo">ShareSphere</div>
        <div>My Donation Records</div>
    </div>

    <div class="navbar">
        <a href="../dashboard/user_dashboard.php">Dashboard</a>
        <a href="../donations/add_donation.php">Make Donation</a>
        <a href="../donations/view_myDonations.php">My Donations</a>
        <a href="../dashboard/developers.html">Developers</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="container">

        <h2>My Donation History</h2>

        <table border="1" cellpadding="10" width="100%">

            <tr>
                <th>ID</th>
                <th>Amount</th>
                <th>Payment Method</th>
                <th>Date</th>
                <th>Status</th>
            </tr>

            <?php while ($row = mysqli_fetch_assoc($result)) { ?>

                <tr>
                    <td><?= $row['donation_id']; ?></td>
                    <td>₱<?= number_format($row['amount'], 2); ?></td>
                    <td><?= $row['payment_method']; ?></td>
                    <td><?= $row['donation_date']; ?></td>
                    <td><?= $row['status']; ?></td>
                </tr>

            <?php } ?>

        </table>

    </div>

    <footer class="footer">
        <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
    </footer>

</body>
</html>