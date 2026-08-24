<?php

session_start();

require_once "../backend/config.php";


// If already logged in as admin,
// go directly to the dashboard.

if (
    isset($_SESSION["user_id"]) &&
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
) {
    header("Location: index.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Admin Login | BUDOL</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/products.css"
    >

</head>

<body>

<div class="admin-login-page">

    <div class="admin-login-box">

        <div class="admin-login-logo">

            <div class="admin-logo-icon">

                <i class="fa-solid fa-shield-halved"></i>

            </div>

            <h1>BUDOL</h1>

            <span>ADMIN PANEL</span>

        </div>


        <div class="admin-login-title">

            <h2>Administrator Login</h2>

            <p>
                Sign in to manage your store.
            </p>

        </div>


        <?php if (isset($_GET["error"])): ?>

            <div class="admin-login-error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <span>

                    <?php

                    if ($_GET["error"] === "invalid") {

                        echo "Invalid email or password.";

                    } elseif ($_GET["error"] === "not_admin") {

                        echo "You do not have administrator access.";

                    } else {

                        echo "Unable to sign in.";

                    }

                    ?>

                </span>

            </div>

        <?php endif; ?>


        <form
            action="../backend/admin_login_process.php"
            method="POST"
            class="admin-login-form"
        >

            <div class="form-group">

                <label for="email">
                    Email Address
                </label>

                <div class="admin-input">

                    <i class="fa-regular fa-envelope"></i>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter admin email"
                        required
                    >

                </div>

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="admin-input">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter password"
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


            <button
                type="submit"
                class="admin-login-button"
            >

                Sign In

            </button>

        </form>

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