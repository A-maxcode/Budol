<?php

session_start();

require_once "backend/config.php";


// =========================================================
// CHECK LOGIN
// =========================================================

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];


// =========================================================
// GET USER
// =========================================================

$sql = "
    SELECT
        id,
        first_name,
        last_name,
        username,
        email
    FROM users
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// =========================================================
// USER NOT FOUND
// =========================================================

if (!$user) {
    session_destroy();

    header("Location: login.php");
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

    <title>
        My Profile | BUDOL
    </title>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <link
        rel="stylesheet"
        href="assets/css/products.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header class="main-header">

    <div class="header-container">


        <a
            href="index.php"
            class="logo"
        >
            BUDOL
        </a>


        <form
            action="products.php"
            method="GET"
            class="search-bar"
        >

            <input
                type="text"
                name="search"
                placeholder="Search products..."
            >

            <button type="submit">

                <i class="fa-solid fa-magnifying-glass"></i>

            </button>

        </form>


        <nav class="header-actions">


            <a
                href="wishlist.php"
                class="header-action"
            >

                <i class="fa-regular fa-heart"></i>

                <span>
                    Wishlist
                </span>

            </a>


            <a
                href="cart.php"
                class="header-action"
            >

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Cart
                </span>

            </a>


            <a
                href="profile.php"
                class="header-action active"
            >

                <i class="fa-regular fa-user"></i>

                <span>
                    Account
                </span>

            </a>


        </nav>

    </div>

</header>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="profile-page">


    <div class="profile-container">


        <!-- TITLE -->

        <div class="profile-title">

            <p class="section-label">
                ACCOUNT
            </p>

            <h1>
                My Profile
            </h1>

            <p>
                Manage your BUDOL account information.
            </p>

        </div>

<!-- PROFILE ALERTS -->

<?php if (isset($_GET["success"])): ?>

    <div class="profile-alert profile-alert-success">

        <i class="fa-solid fa-circle-check"></i>

        <span>

            <?php if ($_GET["success"] === "profile"): ?>

                Profile information updated successfully.

            <?php elseif ($_GET["success"] === "password"): ?>

                Password updated successfully.

            <?php endif; ?>

        </span>

    </div>

<?php endif; ?>


<?php if (isset($_GET["error"])): ?>

    <div class="profile-alert profile-alert-error">

        <i class="fa-solid fa-circle-exclamation"></i>

        <span>

            <?php

            switch ($_GET["error"]) {

                case "empty":
                    echo "Please complete all profile fields.";
                    break;

                case "email":
                    echo "Please enter a valid email address.";
                    break;

                case "email_exists":
                    echo "That email address is already being used.";
                    break;

                case "password_empty":
                    echo "Please complete all password fields.";
                    break;

                case "password_length":
                    echo "New password must contain at least 8 characters.";
                    break;

                case "password_match":
                    echo "New passwords do not match.";
                    break;

                case "password_wrong":
                    echo "Your current password is incorrect.";
                    break;

                case "user":
                    echo "Unable to find your account.";
                    break;

                default:
                    echo "Something went wrong. Please try again.";
            }

            ?>

        </span>

    </div>

<?php endif; ?>

        <div class="profile-layout">


            <!-- =================================================
                 SIDEBAR
            ================================================== -->

            <aside class="profile-sidebar">


                <div class="profile-avatar">

                    <i class="fa-regular fa-user"></i>

                </div>


                <h2>

                    <?php

                    echo htmlspecialchars(
                        $user["first_name"]
                        . " "
                        . $user["last_name"]
                    );

                    ?>

                </h2>


                <p>

                    @<?php

                    echo htmlspecialchars(
                        $user["username"]
                    );

                    ?>

                </p>


                <nav class="profile-menu">


                    <a
                        href="profile.php"
                        class="profile-menu-item active"
                    >

                        <i class="fa-regular fa-user"></i>

                        Profile

                    </a>


                    <a
                        href="orders.php"
                        class="profile-menu-item"
                    >

                        <i class="fa-solid fa-box"></i>

                        My Orders

                    </a>


                    <a
                        href="wishlist.php"
                        class="profile-menu-item"
                    >

                        <i class="fa-regular fa-heart"></i>

                        Wishlist

                    </a>


                    <a
                        href="cart.php"
                        class="profile-menu-item"
                    >

                        <i class="fa-solid fa-cart-shopping"></i>

                        Cart

                    </a>


                    <a
                        href="logout.php"
                        class="profile-menu-item logout-link"
                    >

                        <i class="fa-solid fa-right-from-bracket"></i>

                        Logout

                    </a>


                </nav>

            </aside>


            <!-- =================================================
                 PROFILE CONTENT
            ================================================== -->

            <section class="profile-content">


                <!-- ACCOUNT INFORMATION -->

                <div class="profile-card">


                    <div class="profile-card-header">

                        <div>

                            <h2>
                                Account Information
                            </h2>

                            <p>
                                Your basic account details.
                            </p>

                        </div>


                        <div class="profile-card-icon">

                            <i class="fa-regular fa-user"></i>

                        </div>

                    </div>


                    <div class="profile-info-grid">


                        <div class="profile-info-item">

                            <span>
                                First Name
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $user["first_name"]
                                );

                                ?>

                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <span>
                                Last Name
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $user["last_name"]
                                );

                                ?>

                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <span>
                                Username
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $user["username"]
                                );

                                ?>

                            </strong>

                        </div>


                        <div class="profile-info-item">

                            <span>
                                Email
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $user["email"]
                                );

                                ?>

                            </strong>

                        </div>


                    </div>

                </div>


                <!-- EDIT PROFILE -->

                <div class="profile-card">


                    <div class="profile-card-header">

                        <div>

                            <h2>
                                Edit Profile
                            </h2>

                            <p>
                                Update your personal information.
                            </p>

                        </div>


                        <div class="profile-card-icon">

                            <i class="fa-solid fa-pen"></i>

                        </div>

                    </div>


                    <form
                        action="backend/profile_process.php"
                        method="POST"
                        class="profile-form"
                    >


                        <input
                            type="hidden"
                            name="action"
                            value="update_profile"
                        >


                        <div class="profile-form-row">


                            <div class="form-group">

                                <label>
                                    First Name
                                </label>

                                <input
                                    type="text"
                                    name="first_name"
                                    value="<?php echo htmlspecialchars($user["first_name"]); ?>"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label>
                                    Last Name
                                </label>

                                <input
                                    type="text"
                                    name="last_name"
                                    value="<?php echo htmlspecialchars($user["last_name"]); ?>"
                                    required
                                >

                            </div>


                        </div>


                        <div class="form-group">

                            <label>
                                Username
                            </label>

                            <input
                                type="text"
                                value="<?php echo htmlspecialchars($user["username"]); ?>"
                                disabled
                            >

                            <small>
                                Username cannot be changed.
                            </small>

                        </div>


                        <div class="form-group">

                            <label>
                                Email Address
                            </label>

                            <input
                                type="email"
                                name="email"
                                value="<?php echo htmlspecialchars($user["email"]); ?>"
                                required
                            >

                        </div>


                        <button
                            type="submit"
                            class="profile-save-button"
                        >

                            <i class="fa-solid fa-check"></i>

                            Save Changes

                        </button>


                    </form>

                </div>


                <!-- PASSWORD -->

                <div class="profile-card">


                    <div class="profile-card-header">

                        <div>

                            <h2>
                                Change Password
                            </h2>

                            <p>
                                Keep your BUDOL account secure.
                            </p>

                        </div>


                        <div class="profile-card-icon">

                            <i class="fa-solid fa-lock"></i>

                        </div>

                    </div>


                    <form
                        action="backend/profile_process.php"
                        method="POST"
                        class="profile-form"
                    >


                        <input
                            type="hidden"
                            name="action"
                            value="change_password"
                        >


                        <div class="form-group">

                            <label>
                                Current Password
                            </label>

                            <input
                                type="password"
                                name="current_password"
                                required
                            >

                        </div>


                        <div class="profile-form-row">


                            <div class="form-group">

                                <label>
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    name="new_password"
                                    required
                                >

                            </div>


                            <div class="form-group">

                                <label>
                                    Confirm Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    required
                                >

                            </div>


                        </div>


                        <button
                            type="submit"
                            class="profile-save-button"
                        >

                            <i class="fa-solid fa-key"></i>

                            Update Password

                        </button>


                    </form>

                </div>


            </section>


        </div>

    </div>

</main>


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="main-footer">


    <div class="footer-container">


        <div>

            <h2>
                BUDOL
            </h2>

            <p>
                Your simple and convenient
                online shopping store.
            </p>

        </div>


        <div>

            <h3>
                Shop
            </h3>

            <a href="products.php">
                Products
            </a>

            <a href="wishlist.php">
                Wishlist
            </a>

            <a href="cart.php">
                Cart
            </a>

        </div>


        <div>

            <h3>
                Account
            </h3>

            <a href="profile.php">
                Profile
            </a>

            <a href="orders.php">
                Orders
            </a>

            <a href="logout.php">
                Logout
            </a>

        </div>


    </div>


    <div class="footer-bottom">

        &copy;

        <?php echo date("Y"); ?>

        BUDOL. All rights reserved.

    </div>


</footer>


</body>

</html>