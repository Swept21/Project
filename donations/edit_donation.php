<?php
session_start();

include("../config/database.php");

/* ADMIN ONLY*/
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

/* GET DONATION ID */
if(!isset($_GET['id'])){
    die("Donation ID not provided.");
}

$id = (int)$_GET['id'];

/* UPDATE RECORD */
if($_SERVER['REQUEST_METHOD'] == 'POST'){

    $amount = $_POST['amount'];
    $payment_method = $_POST['payment_method'];
    $donation_date = $_POST['donation_date'];
    $status = $_POST['status'];

    $sql = "
    UPDATE donations
    SET
        amount = ?,
        payment_method = ?,
        donation_date = ?,
        status = ?
    WHERE donation_id = ?
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "dsssi",
        $amount,
        $payment_method,
        $donation_date,
        $status,
        $id
    );

    if(mysqli_stmt_execute($stmt)){

        $_SESSION['success'] = "Donation updated successfully.";

        header("Location: view_donations.php");
        exit;

    } else {

        echo "Update failed: " . mysqli_stmt_error($stmt);
    }
}

/* FETCH CURRENT DONATION */
$sql = "
SELECT *
FROM donations
WHERE donation_id = ?
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if(mysqli_num_rows($result) == 0){
    die("Donation not found.");
}

$donation = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Donation</title>

<link rel="stylesheet" href="../design/styles.css">

</head>
<body>

<div class="header">
    <div class="logo">Admin - ShareSphere</div>
    <div>Edit Donation</div>
</div>

<div class="navbar">

    <a href="../dashboard/dashboard.php">Dashboard</a>
    <a href="../donors/view_donors.php">Donors</a>
    <a href="view_donations.php">Donations</a>
    <a href="../analytics/analytics_dashboard.php">Analytics</a>
    <a href="../etl/sync_olap.php">ETL</a>
    <a href="../auth/logout.php">Logout</a>

</div>

<div class="container">

    <div class="content-box">

        <h2>Edit Donation</h2>

        <form method="POST" class="form-container">

            <div class="form-group">
                <label>Amount</label>

                <input
                    type="number"
                    step="0.01"
                    name="amount"
                    value="<?= htmlspecialchars($donation['amount']); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Payment Method</label>

                <input
                    type="text"
                    name="payment_method"
                    value="<?= htmlspecialchars($donation['payment_method']); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Donation Date</label>

                <input
                    type="date"
                    name="donation_date"
                    value="<?= htmlspecialchars($donation['donation_date']); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label>Status</label>

                <select name="status" required>

                    <option value="Pending"
                        <?= $donation['status'] == 'Pending' ? 'selected' : ''; ?>>
                        Pending
                    </option>

                    <option value="Completed"
                        <?= $donation['status'] == 'Completed' ? 'selected' : ''; ?>>
                        Completed
                    </option>

                    <option value="Cancelled"
                        <?= $donation['status'] == 'Cancelled' ? 'selected' : ''; ?>>
                        Cancelled
                    </option>

                </select>
            </div>

            <div class="button-group">

                <button type="submit" class="btn btn-success">
                    Update Donation
                </button>

                <a href="view_donations.php" class="btn btn-danger">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</body>
</html>