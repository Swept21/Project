<?php
session_start(); // Enable session handling to read redirection errors
?>
<!DOCTYPE html>
<html>
<head>
    <title>ShareSphere Login</title>
    <link rel="stylesheet" href="../design/styless/loginStyles.css">
</head>

<body>

    <div class="login-container">

        <div class="left-panel">

            <h1>ShareSphere</h1>

            <p>
                One Click, One Smile!
            </p>

            <div class="circle circle1"></div>
            <div class="circle circle2"></div>
            <div class="circle circle3"></div>

        </div>

        <div class="right-panel">

            <div class="login-box">

                <h2>Account Sign-in</h2>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="login-error-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <span><?= htmlspecialchars($_SESSION['error']); ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="login_process.php" method="POST">

                    <input type="text" name="username" placeholder="Username" required>

                    <input type="password" name="password" placeholder="Password" required>

                    <button type="submit">Login</button>

                    <a href="register.php">Create new account</a>

                </form>
            </div>
        </div>
    </div>

</body>
</html>