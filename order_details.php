<?php

session_start();

require_once "backend/config.php";


// =========================================================
// LOGIN CHECK
// =========================================================

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}


$user_id = (int) $_SESSION["user_id"];


// =========================================================
// GET ORDER ID
// =========================================================

$order_id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;


if ($order_id <= 0) {

    header("Location: orders.php");

    exit;
}


// =========================================================
// GET ORDER
// =========================================================

$order_sql = "
    SELECT
        id,
        user_id,
        total_amount,
        status,
        shipping_address,
        full_name,
        phone,
        address,
        city,
        postal_code,
        payment_method,
        created_at

    FROM orders

    WHERE id = ?
      AND user_id = ?

    LIMIT 1
";


$order_stmt = mysqli_prepare(
    $conn,
    $order_sql
);

mysqli_stmt_bind_param(
    $order_stmt,
    "ii",
    $order_id,
    $user_id
);

mysqli_stmt_execute(
    $order_stmt
);

$order_result =
    mysqli_stmt_get_result(
        $order_stmt
    );

$order =
    mysqli_fetch_assoc(
        $order_result
    );

mysqli_stmt_close(
    $order_stmt
);


// =========================================================
// ORDER NOT FOUND
// =========================================================

if (!$order) {

    header("Location: orders.php?error=not_found");

    exit;
}


// =========================================================
// GET ORDER ITEMS
// =========================================================

$items_sql = "
    SELECT
        order_items.product_id,
        order_items.quantity,
        order_items.price,
        products.name,
        products.image

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

mysqli_stmt_close(
    $items_stmt
);

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
        Order #<?php echo $order_id; ?> | BUDOL
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- ORDER DETAILS CSS -->

    <link
        rel="stylesheet"
        href="assets/css/order_details.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header class="order-details-header">

    <div class="order-details-header-inner">


        <a
            href="index.php"
            class="order-details-logo"
        >

            <i class="fa-solid fa-bag-shopping"></i>

            <strong>
                BUDOL
            </strong>

        </a>


        <a
            href="orders.php"
            class="order-details-back"
        >

            <i class="fa-solid fa-arrow-left"></i>

            My Orders

        </a>


    </div>

</header>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="order-details-container">


    <!-- PAGE HEADER -->

    <div class="order-details-page-header">

        <div>

            <span>
                ORDER DETAILS
            </span>

            <h1>
                Order #<?php echo $order_id; ?>
            </h1>

            <p>

                Placed on

                <?php

                echo date(
                    "F d, Y · h:i A",
                    strtotime(
                        $order["created_at"]
                    )
                );

                ?>

            </p>

        </div>


        <span
            class="order-detail-status status-<?php echo strtolower($order["status"]); ?>"
        >

            <?php

            echo htmlspecialchars(
                $order["status"]
            );

            ?>

        </span>

    </div>


    <!-- =================================================
         ORDER STATUS
    ================================================== -->

    <section class="order-details-panel">


        <div class="order-details-panel-header">

            <div>

                <span>
                    ORDER PROGRESS
                </span>

                <h2>
                    Order Status
                </h2>

            </div>

            <i class="fa-solid fa-truck-fast"></i>

        </div>


        <div class="order-progress">


            <div class="progress-step active">

                <div class="progress-icon">

                    <i class="fa-solid fa-receipt"></i>

                </div>

                <span>
                    Pending
                </span>

            </div>


            <div class="progress-line"></div>


            <div class="progress-step">

                <div class="progress-icon">

                    <i class="fa-solid fa-box"></i>

                </div>

                <span>
                    Processing
                </span>

            </div>


            <div class="progress-line"></div>


            <div class="progress-step">

                <div class="progress-icon">

                    <i class="fa-solid fa-truck"></i>

                </div>

                <span>
                    Shipped
                </span>

            </div>


            <div class="progress-line"></div>


            <div class="progress-step">

                <div class="progress-icon">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

                <span>
                    Delivered
                </span>

            </div>


        </div>

    </section>


    <!-- =================================================
         ORDER GRID
    ================================================== -->

    <div class="order-details-grid">


        <!-- =================================================
             PRODUCTS
        ================================================== -->

        <section class="order-details-panel">


            <div class="order-details-panel-header">

                <div>

                    <span>
                        PURCHASE
                    </span>

                    <h2>
                        Items Ordered
                    </h2>

                </div>

                <i class="fa-solid fa-box-open"></i>

            </div>


            <div class="order-items">


                <?php while (
                    $item =
                    mysqli_fetch_assoc(
                        $items_result
                    )
                ): ?>


                    <div class="order-item">


                        <div class="order-item-image">

                            <?php if (
                                !empty(
                                    $item["image"]
                                )
                            ): ?>

                                <img
                                    src="<?php echo htmlspecialchars($item["image"]); ?>"
                                    alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                >

                            <?php else: ?>

                                <i class="fa-solid fa-image"></i>

                            <?php endif; ?>

                        </div>


                        <div class="order-item-info">

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $item["name"]
                                );

                                ?>

                            </strong>


                            <span>

                                Quantity:
                                <?php

                                echo (int)
                                    $item["quantity"];

                                ?>

                            </span>

                        </div>


                        <strong class="order-item-price">

                            ₱<?php

                            echo number_format(
                                $item["price"]
                                * $item["quantity"],
                                2
                            );

                            ?>

                        </strong>


                    </div>


                <?php endwhile; ?>


            </div>


            <!-- TOTAL -->

            <div class="order-total">

                <span>
                    Order Total
                </span>

                <strong>

                    ₱<?php

                    echo number_format(
                        $order["total_amount"],
                        2
                    );

                    ?>

                </strong>

            </div>


        </section>


        <!-- =================================================
             SHIPPING
        ================================================== -->

        <aside>


            <section class="order-details-panel">


                <div class="order-details-panel-header">

                    <div>

                        <span>
                            DELIVERY
                        </span>

                        <h2>
                            Shipping Information
                        </h2>

                    </div>

                    <i class="fa-solid fa-location-dot"></i>

                </div>


                <div class="shipping-info">


                    <div>

                        <i class="fa-regular fa-user"></i>

                        <div>

                            <span>
                                Recipient
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $order["full_name"]
                                );

                                ?>

                            </strong>

                        </div>

                    </div>


                    <div>

                        <i class="fa-solid fa-phone"></i>

                        <div>

                            <span>
                                Phone
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $order["phone"]
                                );

                                ?>

                            </strong>

                        </div>

                    </div>


                    <div>

                        <i class="fa-solid fa-house"></i>

                        <div>

                            <span>
                                Address
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $order["address"]
                                );

                                ?>

                            </strong>

                        </div>

                    </div>


                    <div>

                        <i class="fa-solid fa-city"></i>

                        <div>

                            <span>
                                City
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $order["city"]
                                );

                                ?>

                            </strong>

                        </div>

                    </div>


                    <div>

                        <i class="fa-solid fa-envelopes-bulk"></i>

                        <div>

                            <span>
                                Postal Code
                            </span>

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $order["postal_code"]
                                );

                                ?>

                            </strong>

                        </div>

                    </div>


                </div>


            </section>


            <!-- PAYMENT -->

            <section class="order-details-panel payment-panel">


                <div class="order-details-panel-header">

                    <div>

                        <span>
                            PAYMENT
                        </span>

                        <h2>
                            Payment Method
                        </h2>

                    </div>

                    <i class="fa-solid fa-credit-card"></i>

                </div>


                <div class="payment-info">

                    <i class="fa-solid fa-wallet"></i>

                    <div>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $order["payment_method"]
                            );

                            ?>

                        </strong>

                        <span>
                            Selected payment method
                        </span>

                    </div>

                </div>


            </section>


        </aside>


    </div>


</main>


</body>

</html>