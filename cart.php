<?php

session_start();

require_once "backend/config.php";


// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];


// Get cart items
$sql = "
    SELECT
        cart.id AS cart_id,

        products.id AS product_id,
        products.name,
        products.price,
        products.stock,
        products.image,

        categories.name AS category_name,

        cart.quantity

    FROM cart

    INNER JOIN products
        ON cart.product_id = products.id

    LEFT JOIN categories
        ON products.category_id = categories.id

    WHERE cart.user_id = ?

    ORDER BY cart.id DESC
";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


$cart_items = [];

$cart_total = 0;


while ($item = mysqli_fetch_assoc($result)) {

    $item["subtotal"] =
        (float)$item["price"] *
        (int)$item["quantity"];

    $cart_total += $item["subtotal"];

    $cart_items[] = $item;
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
        Shopping Cart | BUDOL
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

<main class="cart-page">


    <div class="cart-container">


        <!-- =====================
             PAGE HEADER
        ====================== -->

        <div class="cart-header">


            <div>

                <p class="section-label">
                    BUDOL SHOPPING
                </p>


                <h1>
                    Shopping Cart
                </h1>


                <p>

                    <?php echo count($cart_items); ?>

                    item(s) in your cart

                </p>

            </div>


        </div>


        <?php if (count($cart_items) > 0): ?>


            <div class="cart-layout">


                <!-- =====================
                     CART ITEMS
                ====================== -->

                <section class="cart-items">


                    <?php foreach ($cart_items as $item): ?>


                        <article class="cart-item">


                            <!-- IMAGE -->

                            <a
                                href="product.php?id=<?php echo $item["product_id"]; ?>"
                                class="cart-item-image"
                            >


                                <?php if (!empty($item["image"])): ?>

                                    <img
                                        src="assets/images/products/<?php echo htmlspecialchars($item["image"]); ?>"
                                        alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                    >

                                <?php else: ?>

                                    <div class="no-image">

                                        <i class="fa-solid fa-image"></i>

                                    </div>

                                <?php endif; ?>


                            </a>


                            <!-- DETAILS -->

                            <div class="cart-item-details">


                                <p class="product-category">

                                    <?php
                                    echo htmlspecialchars(
                                        $item["category_name"]
                                        ?? "Uncategorized"
                                    );
                                    ?>

                                </p>


                                <a
                                    href="product.php?id=<?php echo $item["product_id"]; ?>"
                                    class="cart-item-name"
                                >

                                    <?php
                                    echo htmlspecialchars(
                                        $item["name"]
                                    );
                                    ?>

                                </a>


                                <p class="cart-item-price">

                                    ₱<?php

                                    echo number_format(
                                        (float)$item["price"],
                                        2
                                    );

                                    ?>

                                </p>


                                <!-- UPDATE QUANTITY -->

                                <form
                                    action="backend/cart_process.php"
                                    method="POST"
                                    class="cart-quantity-form"
                                >


                                    <input
                                        type="hidden"
                                        name="action"
                                        value="update"
                                    >


                                    <input
                                        type="hidden"
                                        name="cart_id"
                                        value="<?php echo $item["cart_id"]; ?>"
                                    >


                                    <label>
                                        Quantity
                                    </label>


                                    <input
                                        type="number"
                                        name="quantity"
                                        value="<?php echo (int)$item["quantity"]; ?>"
                                        min="1"
                                        max="<?php echo (int)$item["stock"]; ?>"
                                        required
                                    >


                                    <button type="submit">

                                        Update

                                    </button>


                                </form>


                                <!-- REMOVE -->

                                <form
                                    action="backend/cart_process.php"
                                    method="POST"
                                >


                                    <input
                                        type="hidden"
                                        name="action"
                                        value="remove"
                                    >


                                    <input
                                        type="hidden"
                                        name="cart_id"
                                        value="<?php echo $item["cart_id"]; ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="remove-cart"
                                    >

                                        <i class="fa-solid fa-trash"></i>

                                        Remove

                                    </button>


                                </form>


                            </div>


                            <!-- SUBTOTAL -->

                            <div class="cart-item-subtotal">


                                <span>
                                    Subtotal
                                </span>


                                <strong>

                                    ₱<?php

                                    echo number_format(
                                        $item["subtotal"],
                                        2
                                    );

                                    ?>

                                </strong>


                            </div>


                        </article>


                    <?php endforeach; ?>


                </section>


                <!-- =====================
                     CART SUMMARY
                ====================== -->

                <aside class="cart-summary">


                    <h2>
                        Order Summary
                    </h2>


                    <div class="summary-row">

                        <span>
                            Items
                        </span>

                        <span>

                            <?php echo count($cart_items); ?>

                        </span>

                    </div>


                    <div class="summary-row">

                        <span>
                            Subtotal
                        </span>

                        <span>

                            ₱<?php

                            echo number_format(
                                $cart_total,
                                2
                            );

                            ?>

                        </span>

                    </div>


                    <div class="summary-row">

                        <span>
                            Shipping
                        </span>

                        <span>
                            Calculated at checkout
                        </span>

                    </div>


                    <div class="summary-divider"></div>


                    <div class="summary-total">

                        <span>
                            Total
                        </span>

                        <strong>

                            ₱<?php

                            echo number_format(
                                $cart_total,
                                2
                            );

                            ?>

                        </strong>

                    </div>


                    <a
                        href="checkout.php"
                        class="checkout-button"
                    >

                        Proceed to Checkout

                        <i class="fa-solid fa-arrow-right"></i>

                    </a>


                    <a
                        href="products.php"
                        class="continue-shopping"
                    >

                        Continue Shopping

                    </a>


                </aside>


            </div>


        <?php else: ?>


            <!-- =====================
                 EMPTY CART
            ====================== -->

            <section class="empty-cart">


                <div class="empty-cart-icon">

                    <i class="fa-solid fa-cart-shopping"></i>

                </div>


                <h2>
                    Your Cart Is Empty
                </h2>


                <p>
                    You haven't added any products
                    to your shopping cart yet.
                </p>


                <a
                    href="products.php"
                    class="shop-button"
                >

                    Start Shopping

                </a>


            </section>


        <?php endif; ?>


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