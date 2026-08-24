<?php
require_once "backend/config.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Create Account | BUDOL</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="stylesheet" href="assets/css/auth.css">

</head>

<body>

<div class="auth-container">

    <div class="auth-card register-card">

        <a href="index.php" class="auth-logo">
            BUDOL
        </a>

        <div class="auth-header">

            <h1>Create Account</h1>

            <p>
                Create your BUDOL customer account.
            </p>

        </div>


        <form action="backend/register_process.php" method="POST">

            <div class="form-row">

                <div class="form-group">

                    <label for="first_name">
                        First Name
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-regular fa-user"></i>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            placeholder="First name"
                            required
                        >

                    </div>

                </div>


                <div class="form-group">

                    <label for="last_name">
                        Last Name
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-regular fa-user"></i>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            placeholder="Last name"
                            required
                        >

                    </div>

                </div>

            </div>


            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-at"></i>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Choose a username"
                        required
                    >

                </div>

            </div>


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
                        placeholder="Create a password"
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


            <div class="form-group">

                <label for="confirm_password">
                    Confirm Password
                </label>

                <div class="input-wrapper">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="confirm_password"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        aria-label="Show password"
                        data-target="confirm_password"
                    >
                        <i class="fa-regular fa-eye"></i>
                    </button>

                </div>

            </div>


            <button type="submit" class="auth-button">
                Create Account
            </button>

        </form>


        <div class="auth-footer">

            <p>
                Already have an account?
                <a href="login.php">Sign In</a>
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