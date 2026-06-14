<?php
session_start();
include("../config/database.php");

/* AUTH CHECK (ALLOW BOTH ADMIN AND USER)*/
if (!isset($_SESSION['role'])) {
    header("Location: ../auth/login.php");
    exit;
}

$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

/* IF ADMIN, FETCH ALL DONORS FOR THE DROPDOWN SELECT*/
$donors_list = [];
if ($role === 'admin') {
    $donorQuery = "SELECT donor_id, first_name, last_name FROM donors ORDER BY last_name ASC";
    $donorResult = mysqli_query($conn, $donorQuery);
    while ($donorRow = mysqli_fetch_assoc($donorResult)) {
        $donors_list[] = $donorRow;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Make a Donation</title>
    <link rel="stylesheet" href="../design/styles.css">
</head>
<body>

    <div class="header">
        <div class="logo"><?= ucfirst($role); ?> - ShareSphere</div>
        <div>Welcome, <?= htmlspecialchars($_SESSION['username']); ?></div>
    </div>

    <div class="navbar">
        <?php if ($role === 'admin'): ?>
            <a href="../dashboard/dashboard.php">Dashboard</a>
            <a href="../donors/view_donors.php">Donors</a>
            <a href="view_donations.php">Donations</a>
            <a href="../analytics/analytics_dashboard.php">Analytics</a>
            <a href="../etl/sync_olap.php">ETL</a>
        <?php else: ?>
            <a href="../dashboard/user_dashboard.php">Dashboard</a>
            <a href="../donations/add_donation.php">Make Donation</a>
            <a href="../donations/view_myDonations.php">My Donations</a>
            <a href="../dashboard/developers.html">Developers</a>     
        <?php endif; ?>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="container">
        
        <?php if (isset($_SESSION['success'])): ?>
            <div style="background:#d4edda; color:#155724; padding:10px; margin-bottom:15px; border-radius:5px;">
                <?= $_SESSION['success']; ?>
                <?php unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <div class="content-box form-container">
            <h2>Create New Donation</h2>
            <br>
            
            <form action="insert_donation.php" method="POST">
                
                <?php if ($role === 'admin'): ?>
                    <div class="form-group">
                        <label for="donor_id">Select Donor Profile</label>
                        <select name="donor_id" id="donor_id" required>
                            <option value="">-- Choose a Donor --</option>
                            <?php foreach ($donors_list as $donor): ?>
                                <option value="<?= $donor['donor_id']; ?>">
                                    <?= htmlspecialchars($donor['first_name'] . ' ' . $donor['last_name']); ?> (ID: <?= $donor['donor_id']; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="donor_id" value="<?= $_SESSION['donor_id']; ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="amount">Donation Amount (₱)</label>
                    <input type="number" step="0.01" min="1" name="amount" id="amount" placeholder="0.00" required>
                </div>

                <div class="form-group">
                    <label for="payment_method">Payment Method</label>
                    <select name="payment_method" id="payment_method" required>
                        <option value="GCash">GCash</option>
                        <option value="Maya">Maya</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cash">Cash</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="donation_date">Donation Date</label>
                    <input type="date" name="donation_date" id="donation_date" value="<?= date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select name="status" id="status" required>
                        <?php if ($role === 'admin'): ?>
                            <option value="Completed" selected>Completed</option>
                            <option value="Pending">Pending</option>
                            <option value="Cancelled">Cancelled</option>
                        <?php else: ?>
                            <option value="Completed" selected>Completed</option>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="button-group" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-success" style="padding: 10px 20px; background: #2E7D32; color: white; border: none; border-radius: 4px; cursor: pointer;">
                        Submit Donation
                    </button>
                    
                    <?php if ($role === 'admin'): ?>
                        <a href="view_donations.php" class="btn btn-danger" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Cancel</a>
                    <?php else: ?>
                        <a href="../dashboard/user_dashboard.php" class="btn btn-danger" style="padding: 10px 20px; background: #dc3545; color: white; text-decoration: none; border-radius: 4px; margin-left: 10px;">Cancel</a>
                    <?php endif; ?>
                </div>

            </form>
        </div>

    </div>
        <footer class="footer">
        <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
    </footer>
</body>
</html>