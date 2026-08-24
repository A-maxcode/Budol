<?php

session_start();

require_once "backend/config.php";


// =========================================================
// CUSTOMER LOGIN CHECK
// =========================================================

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}


// =========================================================
// MAKE SURE ONLY CUSTOMERS USE THIS PAGE
// =========================================================

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "customer"
) {

    header("Location: index.php");

    exit;
}


$user_id = (int) $_SESSION["user_id"];


// =========================================================
// GET ONLY THIS CUSTOMER'S ORDERS
// =========================================================

$sql = "
    SELECT
        id,
        full_name,
        phone,
        address,
        city,
        postal_code,
        payment_method,
        total_amount,
        status,
        created_at

    FROM orders

    WHERE user_id = ?

    ORDER BY created_at DESC
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!$stmt) {

    die(
        "Unable to prepare orders query."
    );
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result(
    $stmt
);


$orders = [];


// =========================================================
// GET ORDERS
// =========================================================

while (
    $order = mysqli_fetch_assoc($result)
) {

    $order_id =
        (int) $order["id"];


    // =====================================================
    // GET ITEMS FOR THIS ORDER
    // =====================================================

    $items_sql = "
        SELECT
            order_items.id,
            order_items.product_id,
            order_items.price,
            order_items.quantity,
            products.name AS product_name

        FROM order_items

        INNER JOIN products
            ON order_items.product_id = products.id

        WHERE order_items.order_id = ?

        ORDER BY order_items.id ASC
    ";


    $items_stmt = mysqli_prepare(
        $conn,
        $items_sql
    );


    if (!$items_stmt) {

        continue;
    }


    mysqli_stmt_bind_param(
        $items_stmt,
        "i",
        $order_id
    );


    mysqli_stmt_execute(
        $items_stmt
    );


    $items_result =
        mysqli_stmt_get_result(
            $items_stmt
        );


    $items = [];


    while (
        $item = mysqli_fetch_assoc(
            $items_result
        )
    ) {

        $items[] = $item;
    }


    mysqli_stmt_close(
        $items_stmt
    );


    $order["items"] = $items;


    $orders[] = $order;
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
        My Orders | BUDOL
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- GENERAL STORE CSS -->

    <link
        rel="stylesheet"
        href="assets/css/products.css"
    >


    <!-- ORDERS CSS -->

    <link
        rel="stylesheet"
        href="assets/css/orders.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

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


        <!-- HEADER ACTIONS -->

        <nav class="header-actions">


            <!-- WISHLIST -->

            <a
                href="wishlist.php"
                class="header-action"
            >

                <i class="fa-regular fa-heart"></i>

                <span>
                    Wishlist
                </span>

            </a>


            <!-- CART -->

            <a
                href="cart.php"
                class="header-action"
            >

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Cart
                </span>

            </a>


            <!-- PROFILE -->

            <a
                href="profile.php"
                class="header-action"
            >

                <i class="fa-regular fa-user"></i>

                <span>

                    <?php

                    echo htmlspecialchars(
                        $_SESSION["first_name"] ?? "Account"
                    );

                    ?>

                </span>

            </a>


        </nav>


    </div>

</header>


<!-- =====================================================
     NAVIGATION
===================================================== -->

<nav class="category-nav">


    <div class="category-container">


        <a href="products.php">

            <i class="fa-solid fa-arrow-left"></i>

            Continue Shopping

        </a>


    </div>

</nav>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="orders-page">


    <div class="orders-container">


        <!-- =================================================
             PAGE TITLE
        ================================================== -->

        <div class="orders-title">


            <p class="section-label">
                ACCOUNT
            </p>


            <h1>
                My Orders
            </h1>


            <p>
                View and track your BUDOL purchases.
            </p>


        </div>


        <!-- =================================================
             SUCCESS MESSAGE
        ================================================== -->

        <?php if (
            isset($_GET["success"]) &&
            $_GET["success"] === "order"
        ): ?>


            <div class="order-success">


                <div class="order-success-icon">

                    <i class="fa-solid fa-check"></i>

                </div>


                <div>

                    <strong>
                        Order placed successfully.
                    </strong>


                    <p>
                        Thank you for shopping with BUDOL.
                    </p>

                </div>


            </div>


        <?php endif; ?>


        <!-- =================================================
             ORDERS
        ================================================== -->

        <?php if (count($orders) > 0): ?>


            <div class="orders-list">


                <?php foreach (
                    $orders as $order
                ): ?>


                    <article class="order-card">


                        <!-- =================================
                             ORDER HEADER
                        ================================== -->

                        <div class="order-card-header">


                            <div>


                                <span class="order-label">

                                    My Order

                                </span>


                                <div class="order-date">

                                    <?php

                                    echo date(
                                        "M d, Y h:i A",
                                        strtotime(
                                            $order["created_at"]
                                        )
                                    );

                                    ?>

                                </div>


                            </div>


                            <span
                                class="order-status status-<?php echo strtolower(
                                    $order["status"]
                                ); ?>"
                            >

                                <?php

                                echo htmlspecialchars(
                                    $order["status"]
                                );

                                ?>

                            </span>


                        </div>


                        <!-- =================================
                             ORDER CONTENT
                        ================================== -->

                        <div class="order-card-content">


                            <!-- =================================
                                 ITEMS
                            ================================== -->

                            <div class="order-section">


                                <div class="order-section-title">


                                    <i class="fa-solid fa-box"></i>


                                    Items


                                </div>


                                <div class="order-items-list">


                                    <?php if (
                                        count(
                                            $order["items"]
                                        ) > 0
                                    ): ?>


                                        <?php foreach (
                                            $order["items"] as $item
                                        ): ?>


                                            <div class="order-item-row">


                                                <!-- ICON -->

                                                <div class="order-item-icon">

                                                    <i class="fa-solid fa-box"></i>

                                                </div>


                                                <!-- PRODUCT -->

                                                <div class="order-item-info">


                                                    <strong>

                                                        <?php

                                                        echo htmlspecialchars(
                                                            $item["product_name"]
                                                        );

                                                        ?>

                                                    </strong>


                                                    <span>

                                                        ₱<?php

                                                        echo number_format(
                                                            (float)
                                                            $item["price"],
                                                            2
                                                        );

                                                        ?>

                                                        ×

                                                        <?php

                                                        echo (int)
                                                            $item["quantity"];

                                                        ?>

                                                    </span>


                                                </div>


                                                <!-- ITEM TOTAL -->

                                                <strong class="order-item-total">

                                                    ₱<?php

                                                    echo number_format(
                                                        (float)
                                                        $item["price"]
                                                        *
                                                        (int)
                                                        $item["quantity"],
                                                        2
                                                    );

                                                    ?>

                                                </strong>


                                            </div>


                                        <?php endforeach; ?>


                                    <?php else: ?>


                                        <div class="order-no-items">

                                            No items found.

                                        </div>


                                    <?php endif; ?>


                                </div>


                            </div>


                            <!-- =================================
                                 DELIVERY
                            ================================== -->

                            <div class="order-section">


                                <div class="order-section-title">


                                    <i class="fa-solid fa-location-dot"></i>


                                    Delivery


                                </div>


                                <div class="delivery-info">


                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $order["full_name"]
                                        );

                                        ?>

                                    </strong>


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $order["phone"]
                                        );

                                        ?>

                                    </span>


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $order["address"]
                                        );

                                        ?>

                                    </span>


                                    <span>

                                        <?php

                                        echo htmlspecialchars(
                                            $order["city"]
                                        );

                                        ?>


                                        ,


                                        <?php

                                        echo htmlspecialchars(
                                            $order["postal_code"]
                                        );

                                        ?>

                                    </span>


                                </div>


                            </div>


                            <!-- =================================
                                 PAYMENT
                            ================================== -->

                            <div class="order-section">


                                <div class="order-section-title">


                                    <i class="fa-solid fa-credit-card"></i>


                                    Payment


                                </div>


                                <div class="payment-info">

                                    <?php

                                    echo htmlspecialchars(
                                        $order["payment_method"]
                                    );

                                    ?>

                                </div>


                            </div>


                            <!-- =================================
                                 TOTAL
                            ================================== -->

                            <div class="order-section order-total-section">


                                <div class="order-section-title">

                                    Total

                                </div>


                                <strong class="order-grand-total">

                                    ₱<?php

                                    echo number_format(
                                        (float)
                                        $order["total_amount"],
                                        2
                                    );

                                    ?>

                                </strong>


                            </div>


                        </div>


                    </article>


                <?php endforeach; ?>


            </div>


        <?php else: ?>


            <!-- =================================================
                 EMPTY ORDERS
            ================================================== -->

            <section class="empty-orders">


                <div class="empty-orders-icon">

                    <i class="fa-solid fa-box-open"></i>

                </div>


                <h2>
                    No Orders Yet
                </h2>


                <p>
                    You haven't purchased anything from BUDOL yet.
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


<!-- =====================================================
     FOOTER
===================================================== -->

<footer class="main-footer">


    <div class="footer-container">


        <!-- BUDOL -->

        <div>

            <h2>
                BUDOL
            </h2>


            <p>
                Your simple and convenient
                online shopping store.
            </p>

        </div>


        <!-- SHOP -->

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


        <!-- ACCOUNT -->

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

        <?php

        echo date("Y");

        ?>

        BUDOL. All rights reserved.


    </div>


</footer>


</body>

</html>