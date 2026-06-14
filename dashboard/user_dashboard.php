<?php
session_start();

if(
    !isset($_SESSION['role']) ||
    $_SESSION['role'] != 'user'
){
    header("Location: ../auth/login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>

<head>

<title>User Dashboard</title>

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
        <?= $_SESSION['username']; ?>
    </div>

</div>

<!-- USER NAVIGATION -->

<div class="navbar">
    <a href="user_dashboard.php">Dashboard</a>
    <a href="../donations/add_donation.php">Make Donation</a>
    <a href="../donations/view_myDonations.php">My Donations</a>
    <a href="../dashboard/developers.html">Developers</a>
    <a href="../auth/logout.php">Logout</a>     
</div>

<!-- MAIN CONTENT -->

<div class="container">

    <!-- DASHBOARD CARDS -->

    <div class="cards">

        <div class="card">

            <h3>
                Make a Donation
            </h3>

            <p>
                Submit a new donation to the system.
            </p>

        </div>

        <div class="card">

            <h3>
                View My Donations
            </h3>

            <p>
                Check your donation history and status.
            </p>

        </div>

        <div class="card">

            <h3>
                Share generosity!
            </h3>

            <p>
                Help communities in need through fund raising created by our developer team.
            </p>

        </div>

    </div>

    <!-- QUICK ACTIONS -->

    <div class="content-box">

        <h2>
            Quick Actions
        </h2>

        <br>

        <a
        class="btn btn-success"
        href="../donations/add_donation.php">

            Make Donation

        </a>

        <a
        class="btn btn-primary"
        href="../donations/view_myDonations.php">

            View My Donations

        </a>

    </div>

    <!-- INFORMATION -->

    <div class="content-box">

        <h2>
            Welcome to ShareSphere
        </h2>

        <p>

            Thank you for supporting charitable causes.

            This portal allows you to manage your donations,
            view your donation history, and monitor the status
            of your contributions.

        </p>

    </div>

</div>

<!-- FOOTER -->

    <footer class="footer">
        <p>Copyright &copy; 2026 Group 2 | All Rights Reserved</p>
    </footer>

</body>
</html>