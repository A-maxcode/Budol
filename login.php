<?php
require_once "backend/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | BUDOL</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="assets/css/auth.css">

</head>

<body>

<div class="auth-container">

    <div class="auth-card">

        <a href="index.php" class="auth-logo">
            BUDOL
        </a>

        <div class="auth-header">

            <h1>Welcome Back</h1>

            <p>
                Sign in to continue shopping.
            </p>

        </div>

        <form action="backend/login_process.php" method="POST">

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <div class="input-wrapper">

                    <i class="fa-regular fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        aria-label="Show password"
                        data-target="password"
                    >
                        <i class="fa-regular fa-eye"></i>
                    </button>

                </div>

            </div>


            <button type="submit" class="auth-button">
                Sign In
            </button>

        </form>


        <div class="auth-footer">

            <p>
                Don't have an account?
                <a href="register.php">Create Account</a>
            </p>

        </div>

    </div>

</div>

<script>
    document.querySelectorAll('.password-toggle').forEach(function (toggleButton) {
        toggleButton.addEventListener('click', function () {
            const targetId = this.dataset.target;
            const passwordInput = document.getElementById(targetId);
            const icon = this.querySelector('i');

            if (!passwordInput || !icon) {
                return;
            }

            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            icon.classList.toggle('fa-eye', !isHidden);
            icon.classList.toggle('fa-eye-slash', isHidden);
            this.setAttribute('aria-label', isHidden ? 'Hide password' : 'Show password');
        });
    });
</script>

</body>
</html>