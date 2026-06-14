<?php
session_start();

/* Authentication Check*/
if (
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'admin'
) {
    header("Location: ../auth/login.php");
    exit;
}

/* Database Connection*/
include("../config/database.php");


/* TOTAL DONORS */
$donorQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM donors");
$donorData = mysqli_fetch_assoc($donorQuery);
$totalDonors = $donorData['total'];

/* TOTAL DONATIONS */
$donationQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM donations");
$donationData = mysqli_fetch_assoc($donationQuery);
$totalDonations = $donationData['total'];

/* TOTAL AMOUNT (FIXED: Only sums up 'Completed' donations) */
$amountQuery = mysqli_query($conn, "SELECT SUM(amount) AS total FROM donations WHERE status = 'Completed'");
$amountData = mysqli_fetch_assoc($amountQuery);
$totalAmount = $amountData['total'] ?? 0;

/* COMPLETED DONATIONS */
$completedQuery = mysqli_query($conn, "SELECT COUNT(*) AS total FROM donations WHERE status='Completed'");
$completedData = mysqli_fetch_assoc($completedQuery);
$totalCompleted = $completedData['total'];

?>

<!DOCTYPE html>
<html>

<head>

    <title>Admin Dashboard</title>

    <link rel="stylesheet" href="../design/styles.css">

</head>

<body>

    <div class="header">

        <div class="logo">
            ShareSphere
        </div>

        <div>
            Welcome,
            <?= $_SESSION['role']; ?>
        </div>

    </div>

    <div class="navbar">
        <a href="../dashboard/dashboard.php">Dashboard</a>
        <a href="../donors/view_donors.php">Donors</a>
        <a href="../donations/view_donations.php">Donations</a>
        <a href="../analytics/analytic_dashboard.php">Analytics</a>
        <a href="../etl/sync_olap.php">ETL</a>
        <a href="../reports/reports_dashboard.php">Reports</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="container">

        <div class="cards">

            <div class="card">
                <h3>Total Donors</h3>
                <div class="value">
                    <?= $totalDonors ?>
                </div>
            </div>

            <div class="card">
                <h3>Total Donations</h3>
                <div class="value">
                    <?= $totalDonations ?>
                </div>
            </div>

            <div class="card">
                <h3>Total Amount</h3>
                <div class="value">
                    ₱<?= number_format($totalAmount, 2) ?>
                </div>
            </div>

            <div class="card">
                <h3>Completed Donations</h3>
                <div class="value">
                    <?= $totalCompleted ?>
                </div>
            </div>

        </div>

        <div class="content-box">

            <h2>Quick Actions</h2>
            <br>

            <a class="btn btn-success"
               href="../donors/add_donor.php">
                Add Donor
            </a>

            <a class="btn btn-primary"
               href="../donations/add_donation.php">
                Add Donation
            </a>

            <a class="btn btn-warning"
               href="../etl/sync_olap.php">
                Run ETL Sync
            </a>

        </div>

        <div class="content-box">

            <h2>Admin Overview</h2>

            <p>
                This dashboard provides a unified view of real-time OLTP transactions,
                seamlessly integrated with OLAP-based analytical insights. Through
                continuous ETL synchronization, operational data is transformed into
                meaningful reports and trends, enabling efficient monitoring, analysis,
                and data-driven decision-making across the system.
            </p>

        </div>

    </div>

    <footer class="footer">
        <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
    </footer>


</body>
</html>