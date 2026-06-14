<?php
session_start();

include("../config/database.php");

// ADMIN ONLY ACCESS

if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// DETERMINE THE SELECTED FEATURE FILTER

$filter = isset($_GET['feature_filter']) ? $_GET['feature_filter'] : 'all';

// Base SQL logic for resolving the donor name
$donorNameFallback = "
    COALESCE(
        NULLIF(TRIM(CONCAT(dr.first_name, ' ', dr.last_name)), ''),
        u.username,
        'Unknown User'
    ) AS donor_name
";

// SWITCH QUERY BASED ON SELECTED DROPDOWN OPTION

switch ($filter) {
    case 'highest':
        // Feature: Find the highest completed donation record(s)
        $sql = "
        SELECT d.donation_id, d.amount, d.payment_method, d.donation_date, d.status, $donorNameFallback
        FROM donations d
        LEFT JOIN donors dr ON d.donor_id = dr.donor_id
        LEFT JOIN users u ON dr.user_id = u.user_id
        WHERE d.status = 'Completed'
        ORDER BY d.amount DESC
        LIMIT 5"; 
        $tableTitle = "Top Highest Donations (Completed)";
        break;

    case 'lowest':
        // Feature: Find the lowest completed donation record(s)
        $sql = "
        SELECT d.donation_id, d.amount, d.payment_method, d.donation_date, d.status, $donorNameFallback
        FROM donations d
        LEFT JOIN donors dr ON d.donor_id = dr.donor_id
        LEFT JOIN users u ON dr.user_id = u.user_id
        WHERE d.status = 'Completed'
        ORDER BY d.amount ASC
        LIMIT 5"; 
        $tableTitle = "Lowest Donations (Completed)";
        break;

    case 'payment_methods':
        // Feature: Groups by same payment method to see usage statistics
        $sql = "
        SELECT 
            MIN(d.donation_id) AS donation_id, 
            COUNT(*) AS amount, 
            d.payment_method, 
            MAX(d.donation_date) AS donation_date, 
            'Active' AS status,
            CONCAT('Total Vol: ₱', FORMAT(SUM(d.amount), 2)) AS donor_name 
        FROM donations d
        WHERE d.status = 'Completed'
        GROUP BY d.payment_method
        ORDER BY COUNT(*) DESC";
        $tableTitle = "Most Used Payment Methods Breakdown";
        break;

    case 'all':
    default:
        // Default View: All transaction records ordered by date
        $sql = "
        SELECT d.donation_id, d.amount, d.payment_method, d.donation_date, d.status, $donorNameFallback
        FROM donations d
        LEFT JOIN donors dr ON d.donor_id = dr.donor_id
        LEFT JOIN users u ON dr.user_id = u.user_id
        ORDER BY d.donation_date DESC";
        $tableTitle = "All Donations";
        break;
}



$result = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Donations</title>
    <link rel="stylesheet" href="../design/styles.css">
</head>
<body>

<div class="header">
    <div class="logo">ShareSphere</div>
    <div>Donation Records</div>
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

<?php if(isset($_SESSION['success'])){ ?>
    <div class="alert-success">
        <?= $_SESSION['success']; ?>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php } ?>

<div class="feature-container">
    <label for="feature_filter">Quick Insights View:</label>
    <form method="GET" action="view_donations.php" id="filterForm">
        <select name="feature_filter" id="feature_filter" class="feature-select" onchange="document.getElementById('filterForm').submit();">
            <option value="all" <?= $filter == 'all' ? 'selected' : ''; ?>>View All Donations (Default)</option>
            <option value="highest" <?= $filter == 'highest' ? 'selected' : ''; ?>>View Highest Donations</option>
            <option value="lowest" <?= $filter == 'lowest' ? 'selected' : ''; ?>>View Lowest Donations</option>
            <option value="payment_methods" <?= $filter == 'payment_methods' ? 'selected' : ''; ?>>View Most Used Payment Methods</option>
        </select>
    </form>
</div>

<h2><?= $tableTitle; ?></h2>

<table border="1" cellpadding="10" width="100%">
<tr>
    <th><?= $filter === 'payment_methods' ? 'Rank ID' : 'ID'; ?></th>
    <th><?= $filter === 'payment_methods' ? 'Financial Summary Volume' : 'Donor'; ?></th>
    <th><?= $filter === 'payment_methods' ? 'Total Transactions' : 'Amount'; ?></th>
    <th>Payment Method</th>
    <th><?= $filter === 'payment_methods' ? 'Last Used Date' : 'Date'; ?></th>
    <th>Status</th>
    <?php if($filter !== 'payment_methods'){ ?> <th>Action</th> <?php } ?>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= $row['donation_id']; ?></td>
    <td><?= htmlspecialchars($row['donor_name']); ?></td>
    
    <td>
        <?= $filter === 'payment_methods' ? $row['amount'] . ' payments' : '₱' . number_format($row['amount'], 2); ?>
    </td>
    
    <td><?= $row['payment_method']; ?></td>
    <td><?= $row['donation_date']; ?></td>
    <td><?= $row['status']; ?></td>

    <?php if($filter !== 'payment_methods'){ ?>
    <td>
        <a href="edit_donation.php?id=<?= $row['donation_id']; ?>" class="btn-edit">
            Edit
        </a>
        <a href="delete_donation.php?id=<?= $row['donation_id']; ?>" 
           onclick="return confirm('Are you sure you want to delete this donation?');" 
           class="btn-delete">
            Delete
        </a>
    </td>
    <?php } ?>
</tr>
<?php } ?>
</table>

</div>

<footer class="footer">
    <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
</footer>

</body>
</html>