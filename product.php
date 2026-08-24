<?php

session_start();

require_once "backend/config.php";


// Get product ID
$product_id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


// Invalid ID
if ($product_id <= 0) {
    header("Location: products.php");
    exit;
}


// Get product information
$sql = "
    SELECT
        p.id,
        p.name,
        p.description,
        p.price,
        p.stock,
        p.image,
        c.name AS category_name
    FROM products p
    LEFT JOIN categories c
        ON p.category_id = c.id
    WHERE p.id = ?
    LIMIT 1
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// Product not found
if (!$product) {
    header("Location: products.php");
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
        <?php echo htmlspecialchars($product["name"]); ?> | BUDOL
    </title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- USE YOUR EXISTING CSS -->

    <link
        rel="stylesheet"
        href="assets/css/products.css"
    >

</head>

<body>


<!-- =========================
     HEADER
========================= -->

<header class="main-header">

    <div class="header-container">


        <!-- LOGO -->

        <a
            href="index.php"
            class="logo"
        >
            BUDOL
        </a>


        <!-- SEARCH -->

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


        <!-- ACTIONS -->

        <nav class="header-actions">


            <a
                href="wishlist.php"
                class="header-action"
                title="Wishlist"
            >

                <i class="fa-regular fa-heart"></i>

                <span>
                    Wishlist
                </span>

            </a>


            <a
                href="cart.php"
                class="header-action"
                title="Cart"
            >

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Cart
                </span>

            </a>


            <?php if (isset($_SESSION["user_id"])): ?>

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
     NAVIGATION
========================= -->

<nav class="category-nav">

    <div class="category-container">

        <a href="products.php">

            <i class="fa-solid fa-arrow-left"></i>

            Back to Products

        </a>

    </div>

</nav>


<!-- =========================
     PRODUCT DETAILS
========================= -->

<main class="products-page">


    <div class="product-details">


        <!-- PRODUCT IMAGE -->

        <div class="product-details-image">


            <?php if (!empty($product["image"])): ?>

                <img
                    src="assets/images/products/<?php echo htmlspecialchars($product["image"]); ?>"
                    alt="<?php echo htmlspecialchars($product["name"]); ?>"
                >

            <?php else: ?>

                <div class="no-image">

                    <i class="fa-solid fa-image"></i>

                </div>

            <?php endif; ?>


        </div>


        <!-- PRODUCT INFORMATION -->

        <div class="product-details-info">


            <!-- CATEGORY -->

            <p class="product-category">

                <?php
                echo htmlspecialchars(
                    $product["category_name"]
                    ?? "Uncategorized"
                );
                ?>

            </p>


            <!-- NAME -->

            <h1>

                <?php
                echo htmlspecialchars(
                    $product["name"]
                );
                ?>

            </h1>


            <!-- PRICE -->

            <div class="product-details-price">

                ₱<?php

                echo number_format(
                    (float)$product["price"],
                    2
                );

                ?>

            </div>


            <!-- DESCRIPTION -->

            <div class="product-description">

                <?php

                if (!empty($product["description"])) {

                    echo nl2br(
                        htmlspecialchars(
                            $product["description"]
                        )
                    );

                } else {

                    echo "No description available.";

                }

                ?>

            </div>


            <!-- STOCK -->

            <div class="product-stock">


                <?php if ((int)$product["stock"] > 0): ?>


                    <i class="fa-solid fa-circle-check"></i>

                    In Stock

                    <span>
                        (<?php echo (int)$product["stock"]; ?> available)
                    </span>


                <?php else: ?>


                    <i class="fa-solid fa-circle-xmark"></i>

                    Out of Stock


                <?php endif; ?>


            </div>


            <?php if ((int)$product["stock"] > 0): ?>


                <!-- ADD TO CART -->

                <form
                    action="backend/cart_process.php"
                    method="POST"
                    class="product-action"
                >


                    <input
                        type="hidden"
                        name="product_id"
                        value="<?php echo $product["id"]; ?>"
                    >


                    <input
                        type="hidden"
                        name="action"
                        value="add_to_cart"
                    >


                    <!-- QUANTITY -->

                    <div class="quantity-control">


                        <label for="quantity">
                            Quantity
                        </label>


                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            value="1"
                            min="1"
                            max="<?php echo (int)$product["stock"]; ?>"
                            required
                        >


                    </div>


                    <button
                        type="submit"
                        class="add-cart-button"
                    >

                        <i class="fa-solid fa-cart-plus"></i>

                        Add to Cart

                    </button>


                </form>


                <!-- WISHLIST -->

                <form
                    action="backend/wishlist_process.php"
                    method="POST"
                    class="wishlist-form"
                >


                    <input
                        type="hidden"
                        name="product_id"
                        value="<?php echo $product["id"]; ?>"
                    >


                    <input
                        type="hidden"
                        name="action"
                        value="add"
                    >


                    <button
                        type="submit"
                        class="wishlist-button"
                    >

                        <i class="fa-regular fa-heart"></i>

                        Add to Wishlist

                    </button>


                </form>


            <?php else: ?>


                <button
                    class="add-cart-button disabled"
                    disabled
                >

                    Out of Stock

                </button>


            <?php endif; ?>


        </div>


    </div>


</main>


<!-- =========================
     FOOTER
========================= -->

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

            <a href="cart.php">
                Cart
            </a>

            <a href="wishlist.php">
                Wishlist
            </a>

        </div>


        <div>

            <h3>
                Account
            </h3>

            <a href="login.php">
                Login
            </a>

            <a href="register.php">
                Register
            </a>

            <a href="profile.php">
                Profile
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