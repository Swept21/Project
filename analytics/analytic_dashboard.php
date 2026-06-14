<?php
session_start();
include("../config/database.php");

/*
|--------------------------------------------------------------------------
| AUTH CHECK
|--------------------------------------------------------------------------
*/
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| CONNECT TO OLAP DATABASE
|--------------------------------------------------------------------------
*/
$conn = mysqli_connect("localhost", "root", "", "donation_olap");

if(!$conn){
    die("Connection Failed");
}

/*
|--------------------------------------------------------------------------
| KPI - TOTAL DONATIONS
|--------------------------------------------------------------------------
*/
$totalQuery = mysqli_query($conn, "SELECT SUM(donation_amount) AS total FROM fact_donations");
$totalData = mysqli_fetch_assoc($totalQuery);
$totalDonations = $totalData['total'] ?? 0;

/*
|--------------------------------------------------------------------------
| ROLL-UP (YEAR)
|--------------------------------------------------------------------------
*/
$yearQuery = mysqli_query($conn, "
    SELECT dt.year_num, SUM(fd.donation_amount) AS total
    FROM fact_donations fd
    JOIN dim_time dt ON fd.time_key = dt.time_key
    GROUP BY dt.year_num
    ORDER BY dt.year_num
");

$yearLabels = [];
$yearTotals = [];
while($row = mysqli_fetch_assoc($yearQuery)){
    $yearLabels[] = $row['year_num'];
    $yearTotals[] = $row['total'];
}

/*
|--------------------------------------------------------------------------
| DRILL DOWN (MONTH)
|--------------------------------------------------------------------------
*/
$monthQuery = mysqli_query($conn, "
    SELECT dt.month_name, SUM(fd.donation_amount) AS total
    FROM fact_donations fd
    JOIN dim_time dt ON fd.time_key = dt.time_key
    GROUP BY dt.month_num
    ORDER BY dt.month_num
");

$monthLabels = [];
$monthTotals = [];
while($row = mysqli_fetch_assoc($monthQuery)){
    $monthLabels[] = $row['month_name'];
    $monthTotals[] = $row['total'];
}

/*
|--------------------------------------------------------------------------
| SLICE (PAYMENT)
|--------------------------------------------------------------------------
*/
$paymentQuery = mysqli_query($conn, "
    SELECT dp.payment_method, SUM(fd.donation_amount) AS total
    FROM fact_donations fd
    JOIN dim_payment dp ON fd.payment_key = dp.payment_key
    GROUP BY dp.payment_method
");

$paymentLabels = [];
$paymentTotals = [];
while($row = mysqli_fetch_assoc($paymentQuery)){
    $paymentLabels[] = $row['payment_method'];
    $paymentTotals[] = $row['total'];
}

/*
|--------------------------------------------------------------------------
| DICE (REGION)
|--------------------------------------------------------------------------
*/
$regionQuery = mysqli_query($conn, "
    SELECT dl.region, SUM(fd.donation_amount) AS total
    FROM fact_donations fd
    JOIN dim_location dl ON fd.location_key = dl.location_key
    GROUP BY dl.region
");

$regionLabels = [];
$regionTotals = [];
while($row = mysqli_fetch_assoc($regionQuery)){
    $regionLabels[] = $row['region'];
    $regionTotals[] = $row['total'];
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics Dashboard - ShareSphere</title>
    <link rel="stylesheet" href="../design/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<!-- HEADER -->
<div class="header">
    <div class="logo">Admin - ShareSphere</div>
    <div>Welcome, <?= htmlspecialchars(ucfirst($_SESSION['role'])); ?></div>
</div>

<!-- NAVIGATION -->
<div class="navbar">
    <a href="../dashboard/dashboard.php">Dashboard</a>
    <a href="../donors/view_donors.php">Donors</a>
    <a href="../donations/view_donations.php">Donations</a>
    <a href="../analytics/analytic_dashboard.php">Analytics</a>
    <a href="../etl/sync_olap.php">ETL</a>
    <a href="../reports/reports_dashboard.php">Reports</a> 
    <a href="../auth/logout.php">Logout</a>
</div>

<div class="container" style="max-width: 1200px; margin: 30px auto; padding: 20px; border: none; background: transparent; box-shadow: none;">

    <div class="analytics-topbar">
        <a href="../dashboard/dashboard.php" class="btn-back">← Back to Dashboard</a>
        
        <div class="kpi-card">
            <h3>Total Donations Valuation</h3>
            <div class="value">₱<?= number_format($totalDonations, 2); ?></div>
        </div>
    </div>

    <div class="analytics-grid">

        <!--YEAR -->
        <div class="chart-card">
            <h2>Donations per Year</h2>
            <div class="chart-container">
                <canvas id="yearChart"></canvas>
            </div>
        </div>

        <!--MONTH -->
        <div class="chart-card">
            <h2>Donations per Month</h2>
            <div class="chart-container">
                <canvas id="monthChart"></canvas>
            </div>
        </div>

        <!--PAYMENT -->
        <div class="chart-card">
            <h2>Payment Method Analysis</h2>
            <div class="chart-container">
                <canvas id="paymentChart"></canvas>
            </div>
        </div>

        <!--REGION -->
        <div class="chart-card">
            <h2>Region Analysis</h2>
            <div class="chart-container">
                <canvas id="regionChart"></canvas>
            </div>
        </div>

    </div>
</div>

<footer class="footer">
    <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
</footer>

<script>
const primaryGreen = '#2d6a4f';
const hoverGreen = '#1b4332';
const accentColors = ['#2d6a4f', '#40916c', '#52b788', '#74c69d', '#95d5b2', '#b7e4c7'];

/* ==========Year CHART=========*/
new Chart(document.getElementById('yearChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($yearLabels); ?>,
        datasets: [{
            label: 'Total Annual Donations',
            data: <?= json_encode($yearTotals); ?>,
            backgroundColor: primaryGreen,
            hoverBackgroundColor: hoverGreen,
            borderRadius: 4
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});

/* ==========Month CHART=========*/
new Chart(document.getElementById('monthChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($monthLabels); ?>,
        datasets: [{
            label: 'Monthly Trend Valuation',
            data: <?= json_encode($monthTotals); ?>,
            borderColor: primaryGreen,
            backgroundColor: 'rgba(45, 106, 79, 0.1)',
            fill: true,
            tension: 0.2,
            pointBackgroundColor: hoverGreen
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

/* ==========Payment  CHART=========*/
new Chart(document.getElementById('paymentChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($paymentLabels); ?>,
        datasets: [{
            data: <?= json_encode($paymentTotals); ?>,
            backgroundColor: accentColors
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: {
            legend: { position: 'right' }
        }
    }
});

/* ==========REGION CHART=========*/
new Chart(document.getElementById('regionChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($regionLabels); ?>,
        datasets: [{
            label: 'Donations by Location',
            data: <?= json_encode($regionTotals); ?>,
            backgroundColor: '#40916c',
            hoverBackgroundColor: hoverGreen,
            borderRadius: 4
        }]
    },
    options: {
        maintainAspectRatio: false,
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

</body>
</html>