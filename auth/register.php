<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShareSphere</title>
    <link rel="stylesheet" href="../design/styless/loginStyles.css">
</head>
<body>

<div class="auth-wrapper">
    <div class="register-box">
        
        <div class="auth-header">
            <div class="brand-logo">ShareSphere</div>
            <h2>Create Account</h2>
            <p>Join our community and track your contributions seamlessly.</p>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($_SESSION['error']); ?>
                <?php unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form action="register_process.php" method="POST">

            <h3 class="section-title">Personal Profile</h3>
            <div class="form-grid">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" class="restricted-input" placeholder="e.g., Juan" pattern="^[a-zA-Z\s\-]+$" title="Only letters, spaces, and hyphens are allowed." required>
                </div>

                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" class="restricted-input" placeholder="e.g., Luna" pattern="^[a-zA-Z\s\-]+$" title="Only letters, spaces, and hyphens are allowed." required>
                </div>
            </div>

            <h3 class="section-title">Address Information</h3>
            <div class="form-group">
                <label>City / Municipality</label>
                <input type="text" name="city" class="restricted-input" placeholder="e.g., Rosario" pattern="^[a-zA-Z\s\-]+$" title="Only letters, spaces, and hyphens are allowed." required>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Province</label>
                    <input type="text" name="province" class="restricted-input" placeholder="e.g., Cavite" pattern="^[a-zA-Z\s\-]+$" title="Only letters, spaces, and hyphens are allowed." required>
                </div>

                <div class="form-group">
                    <label>Region</label>
                    <input type="text" name="region" class="restricted-input" placeholder="e.g., Region IV-A" pattern="^[a-zA-Z\s\-]+$" title="Only letters, spaces, and hyphens are allowed." required>
                </div>
            </div>

            <h3 class="section-title">Account Credentials</h3>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="Choose a username" required>
            </div>

            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Create a strong password" required>
            </div>

            <button type="submit" class="btn-auth">
                Sign Up & Register
            </button>

        </form>

        <div class="auth-footer">
            <a href="login.php">Already have an account? <strong>Login here</strong></a>
        </div>

    </div>
</div>

<script>
document.querySelectorAll('.restricted-input').forEach(function(input) {
    input.addEventListener('input', function() {
        // Automatically replaces any character that isn't a letter, space, or hyphen
        this.value = this.value.replace(/[^a-zA-Z\s\-]/g, '');
    });
});
</script>

</body>
</html>