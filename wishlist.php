<?php

session_start();

require_once "backend/config.php";


// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}


$user_id = (int) $_SESSION["user_id"];


// Get wishlist products
$sql = "
    SELECT
        wishlist.id AS wishlist_id,

        products.id AS product_id,
        products.name,
        products.description,
        products.price,
        products.stock,
        products.image,

        categories.name AS category_name

    FROM wishlist

    INNER JOIN products
        ON wishlist.product_id = products.id

    LEFT JOIN categories
        ON products.category_id = categories.id

    WHERE wishlist.user_id = ?

    ORDER BY wishlist.id DESC
";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$wishlist_items = [];

while ($item = mysqli_fetch_assoc($result)) {

    $wishlist_items[] = $item;

}

mysqli_stmt_close($stmt);

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
        Wishlist | BUDOL
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


        <!-- HEADER ACTIONS -->

        <nav class="header-actions">


            <a
                href="wishlist.php"
                class="header-action"
            >

                <i class="fa-solid fa-heart"></i>

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

            Continue Shopping

        </a>


    </div>

</nav>


<!-- =========================
     MAIN
========================= -->

<main class="products-page">


    <!-- PAGE HEADER -->

    <section class="page-header">


        <div>

            <p class="section-label">
                BUDOL ACCOUNT
            </p>


            <h1>
                My Wishlist
            </h1>


            <p class="product-count">

                <?php echo count($wishlist_items); ?>

                saved product(s)

            </p>

        </div>


    </section>


    <!-- =========================
         WISHLIST
    ========================= -->

    <?php if (count($wishlist_items) > 0): ?>


        <section class="product-grid">


            <?php foreach ($wishlist_items as $product): ?>


                <article class="product-card">


                    <!-- IMAGE -->

                    <a
                        href="product.php?id=<?php echo $product["product_id"]; ?>"
                        class="product-image"
                    >


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


                        <?php if ((int)$product["stock"] <= 0): ?>

                            <span class="stock-badge out">

                                Out of Stock

                            </span>

                        <?php endif; ?>


                    </a>


                    <!-- INFORMATION -->

                    <div class="product-info">


                        <p class="product-category">

                            <?php
                            echo htmlspecialchars(
                                $product["category_name"]
                                ?? "Uncategorized"
                            );
                            ?>

                        </p>


                        <a
                            href="product.php?id=<?php echo $product["product_id"]; ?>"
                            class="product-name"
                        >

                            <?php
                            echo htmlspecialchars(
                                $product["name"]
                            );
                            ?>

                        </a>


                        <div class="product-bottom">


                            <span class="product-price">

                                ₱<?php

                                echo number_format(
                                    (float)$product["price"],
                                    2
                                );

                                ?>

                            </span>


                            <!-- REMOVE -->

                            <form
                                action="backend/wishlist_process.php"
                                method="POST"
                            >


                                <input
                                    type="hidden"
                                    name="wishlist_id"
                                    value="<?php echo $product["wishlist_id"]; ?>"
                                >


                                <input
                                    type="hidden"
                                    name="product_id"
                                    value="<?php echo $product["product_id"]; ?>"
                                >


                                <input
                                    type="hidden"
                                    name="action"
                                    value="remove"
                                >


                                <button
                                    type="submit"
                                    class="view-product"
                                    title="Remove from Wishlist"
                                >

                                    <i class="fa-solid fa-heart"></i>

                                </button>


                            </form>


                        </div>


                    </div>


                </article>


            <?php endforeach; ?>


        </section>


    <?php else: ?>


        <!-- EMPTY WISHLIST -->

        <section class="empty-products">


            <div class="empty-icon">

                <i class="fa-regular fa-heart"></i>

            </div>


            <h2>

                Your Wishlist Is Empty

            </h2>


            <p>

                Save products you like and
                find them here later.

            </p>


            <a
                href="products.php"
                class="shop-button"
            >

                Browse Products

            </a>


        </section>


    <?php endif; ?>


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