<?php

session_start();

require_once "backend/config.php";

$is_logged_in = isset($_SESSION["user_id"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>BUDOL | Online Shopping</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/index.css"
    >

</head>

<body>


<!-- =========================
     HEADER
========================= -->

<header class="main-header">

    <div class="header-container">


        <!-- LOGO -->

        <a href="index.php" class="logo">
            BUDOL
        </a>


        <!-- SEARCH -->

        <form
            class="search-bar"
            action="products.php"
            method="GET"
        >

            <input
                type="text"
                name="search"
                placeholder="Search products..."
                autocomplete="off"
            >

            <button type="submit">

                <i class="fa-solid fa-magnifying-glass"></i>

            </button>

        </form>


        <!-- HEADER ACTIONS -->

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


            <?php if ($is_logged_in): ?>

                <a
                    href="profile.php"
                    class="header-action"
                >

                    <i class="fa-regular fa-user"></i>

                    <span>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION["first_name"]
                        );
                        ?>
                    </span>

                </a>


                <a
                    href="logout.php"
                    class="header-action"
                >

                    <i class="fa-solid fa-arrow-right-from-bracket"></i>

                    <span>
                        Logout
                    </span>

                </a>

            <?php else: ?>

                <a
                    href="login.php"
                    class="header-action"
                >

                    <i class="fa-regular fa-user"></i>

                    <span>
                        Account
                    </span>

                </a>

            <?php endif; ?>


        </nav>

    </div>

</header>



<!-- =========================
     CATEGORY NAVIGATION
========================= -->

<nav class="category-nav">

    <div class="category-container">


        <a href="products.php">

            <i class="fa-solid fa-bars"></i>

            All Products

        </a>


        <a href="products.php?category=Fashion">
            Fashion
        </a>


        <a href="products.php?category=Electronics">
            Electronics
        </a>


        <a href="products.php?category=Beauty">
            Beauty
        </a>


        <a href="products.php?category=Home">
            Home & Living
        </a>


        <a href="products.php?category=Accessories">
            Accessories
        </a>


    </div>

</nav>



<!-- =========================
     MAIN CONTENT
========================= -->

<main>


    <!-- HERO -->

    <section class="hero">

        <div class="hero-content">


            <p class="hero-label">
                BUDOL ONLINE STORE
            </p>


            <h1>
                Discover products
                <br>
                worth buying.
            </h1>


            <p class="hero-description">

                Discover products you'll love,
                shop your favorites, and enjoy
                a simple and convenient online
                shopping experience.

            </p>


            <a
                href="products.php"
                class="hero-button"
            >

                Shop Now

                <i class="fa-solid fa-arrow-right"></i>

            </a>


        </div>


        <div class="hero-image">

            <div class="hero-image-content">

                <i class="fa-solid fa-bag-shopping"></i>

            </div>

        </div>

    </section>



    <!-- =========================
         CATEGORIES
    ========================= -->

    <section class="categories-section">


        <div class="section-header">

            <div>

                <p class="section-label">
                    SHOP BY CATEGORY
                </p>

                <h2>
                    Find what you need
                </h2>

            </div>


            <a
                href="products.php"
                class="view-all"
            >

                View All

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <div class="category-grid">


            <!-- FASHION -->

            <a
                href="products.php?category=Fashion"
                class="category-card"
            >

                <div class="category-icon">

                    <i class="fa-solid fa-shirt"></i>

                </div>


                <h3>
                    Fashion
                </h3>


                <p>
                    Clothing and everyday styles
                </p>

            </a>



            <!-- ELECTRONICS -->

            <a
                href="products.php?category=Electronics"
                class="category-card"
            >

                <div class="category-icon">

                    <i class="fa-solid fa-mobile-screen-button"></i>

                </div>


                <h3>
                    Electronics
                </h3>


                <p>
                    Gadgets and useful devices
                </p>

            </a>



            <!-- BEAUTY -->

            <a
                href="products.php?category=Beauty"
                class="category-card"
            >

                <div class="category-icon">

                    <i class="fa-solid fa-wand-magic-sparkles"></i>

                </div>


                <h3>
                    Beauty
                </h3>


                <p>
                    Beauty and personal care
                </p>

            </a>



            <!-- HOME -->

            <a
                href="products.php?category=Home"
                class="category-card"
            >

                <div class="category-icon">

                    <i class="fa-solid fa-house"></i>

                </div>


                <h3>
                    Home & Living
                </h3>


                <p>
                    Products for your home
                </p>

            </a>


        </div>

    </section>



    <!-- =========================
         FEATURED PRODUCTS
    ========================= -->

    <section class="featured-section">


        <div class="section-header">

            <div>

                <p class="section-label">
                    FEATURED PRODUCTS
                </p>

                <h2>
                    Popular products
                </h2>

            </div>


            <a
                href="products.php"
                class="view-all"
            >

                View All

                <i class="fa-solid fa-arrow-right"></i>

            </a>

        </div>



        <div class="empty-products">


            <div class="empty-icon">

                <i class="fa-solid fa-box-open"></i>

            </div>


            <h3>
                Products coming soon
            </h3>


            <p>
                Our product catalog will appear here.
            </p>


        </div>

    </section>


</main>



<!-- =========================
     FOOTER
========================= -->

<footer class="main-footer">


    <div class="footer-container">


        <div class="footer-brand">

            <h2>
                BUDOL
            </h2>

            <p>

                A simple and convenient online
                shopping experience.

            </p>

        </div>



        <div class="footer-column">

            <h3>
                Shop
            </h3>

            <a href="products.php">
                All Products
            </a>

            <a href="products.php?category=Fashion">
                Fashion
            </a>

            <a href="products.php?category=Electronics">
                Electronics
            </a>

            <a href="products.php?category=Beauty">
                Beauty
            </a>

        </div>



        <div class="footer-column">

            <h3>
                Account
            </h3>

            <a href="login.php">
                Login
            </a>

            <a href="register.php">
                Create Account
            </a>

            <a href="wishlist.php">
                Wishlist
            </a>

            <a href="cart.php">
                Shopping Cart
            </a>

        </div>



        <div class="footer-column">

            <h3>
                Support
            </h3>

            <a href="#">
                Contact Us
            </a>

            <a href="#">
                Shipping Information
            </a>

            <a href="#">
                Return Policy
            </a>

        </div>


    </div>



    <div class="footer-bottom">

        <p>

            &copy;
            <?php echo date("Y"); ?>
            BUDOL. All rights reserved.

        </p>

    </div>


</footer>


</body>

</html>