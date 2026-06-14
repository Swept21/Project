<?php
session_start();
include("../config/database.php");

/*
|--------------------------------------------------------------------------
| ADMIN ONLY ACCESS
|--------------------------------------------------------------------------
*/
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Export Reports</title>
   <link rel="stylesheet" href="../design/styles.css">
</head>
<body>

<div class="header">
    <div class="logo">ShareSphere</div>
    <div>System Report Engine</div>
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

<div class="container" style="max-width: 900px; margin: 40px auto;">
    <h2>Export System Data Reports</h2>
    <p style="color: #666; font-size: 15px;">Select your preferred output file type structure below to extract raw financial records and complete donor information from the operational database engine.</p>
    <hr>

    <div class="report-grid">

        <!-- EXCEL FORMAT CARD OPTION -->
        <div class="format-card">
            <span class="format-icon">📊</span>
            <h3 style="margin: 0 0 10px 0; color: #333;">MS Excel Sheet</h3>
            <p style="color: #777; font-size: 13px; margin: 0;">Perfect for sorting, accounting formulations, and viewing cross-tables.</p>
            <a href="export_excel.php" class="btn-export excel-theme">Download .XLSX</a>
        </div>

        <!-- CSV FORMAT CARD OPTION -->
        <div class="format-card">
            <span class="format-icon">📁</span>
            <h3 style="margin: 0 0 10px 0; color: #333;">CSV Plain Data</h3>
            <p style="color: #777; font-size: 13px; margin: 0;">Ideal for feeding records directly into external tools or automated scripts.</p>
            <a href="export_csv.php" class="btn-export csv-theme">Download .CSV</a>
        </div>

    </div>
</div>
    <footer class="footer">
        <p>Copyright &copy; 2025 Group 3 | All Rights Reserved</p>
    </footer>
</body>
</html>