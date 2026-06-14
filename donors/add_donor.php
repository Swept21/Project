<!DOCTYPE html>
<html>
<head>
    <title>Add Donor</title>
    <link rel="stylesheet" href="../design/styles.css">
</head>
<body>

<div class="header">
    <div class="logo">ShareSphere</div>
    <div>Donor Profiles</div>
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

<div class="container" style="max-width: 550px; margin: 30px auto;">
    
    <h2>Add New Donor Profile</h2>
    <p style="color: #666; font-size: 14px; margin-bottom: 20px;">Fill out the fields below to register a new physical goods donor or community volunteer.</p>
    <hr><br>

    <form action="insert_donor.php" method="POST" class="form-container">
        
        <div class="form-group">
            <label>First Name <span style="color: red;">*</span></label>
            <input type="text" name="first_name" placeholder="e.g., Juan" required>
        </div>

        <div class="form-group">
            <label>Last Name <span style="color: red;">*</span></label>
            <input type="text" name="last_name" placeholder="e.g., Dela Cruz" required>
        </div>

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="e.g., juan@gmail.com">
        </div>

        <div class="form-group">
            <label>Contact Number</label>
            <input type="text" name="contact_number" placeholder="e.g., 09123456789">
        </div>

        <div class="form-group">
            <label>City</label>
            <input type="text" name="city" placeholder="e.g., Rosario">
        </div>

        <div class="form-group">
            <label>Province</label>
            <input type="text" name="province" placeholder="e.g., Cavite">
        </div>

        <div class="form-group">
            <label>Region</label>
            <input type="text" name="region" placeholder="e.g., Region IV-A">
        </div>

        <button type="submit" class="btn-submit">
            Save Donor Profile
        </button>

        <div style="text-align: center; margin-top: 15px;">
            <a href="view_donors.php" style="color: #666; text-decoration: none; font-size: 14px;">← Back to Donors List</a>
        </div>

    </form>
</div>

</body>
</html>