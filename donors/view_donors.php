<?php
session_start();
include("../config/database.php");

/* AUTH CHECK (ADMIN ONLY)*/
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

/* FETCH DONORS*/
$query = "SELECT * FROM donors ORDER BY donor_id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>

<title>View Donors</title>

<link rel="stylesheet" href="../design/styles.css">

</head>
<body>

<!-- HEADER -->
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

<!-- MAIN CONTENT -->
<div class="container">

    <div class="content-box">

        <h2>Donor Records</h2>

        <br>

        <a href="add_donor.php" class="btn btn-success">
            + Add Donor
        </a>

        <br><br>

        <table>

            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>City</th>
                <th>Province</th>
                <th>Region</th>
                <th>Action</th>
            </tr>

            <?php while($row = mysqli_fetch_assoc($result)){ ?>

            <tr>

                <td><?= $row['donor_id']; ?></td>

                <td><?= $row['first_name']; ?></td>

                <td><?= $row['last_name']; ?></td>

                <td><?= $row['city']; ?></td>

                <td><?= $row['province']; ?></td>

                <td><?= $row['region']; ?></td>

                <td>

                    <a href="../donors/edit_donor.php?id=<?= $row['donor_id']; ?>"
                       class="btn btn-primary">
                        Edit
                    </a>

                </td>

            </tr>

            <?php } ?>

        </table>

    </div>

</div>

    <footer class="footer">
        <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
    </footer>

<script src="../assets/js/admin.js"></script>

</body>
</html>