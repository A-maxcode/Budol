<?php

require_once "auth.php";

require_once "../backend/config.php";



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

    WHERE id = ?

    LIMIT 1
";


$order_stmt = mysqli_prepare(
    $conn,
    $order_sql
);

mysqli_stmt_bind_param(
    $order_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute(
    $order_stmt
);

$order_result = mysqli_stmt_get_result(
    $order_stmt
);

$order = mysqli_fetch_assoc(
    $order_result
);

mysqli_stmt_close(
    $order_stmt
);


// =========================================================
// ORDER NOT FOUND
// =========================================================

if (!$order) {
    header("Location: orders.php?error=notfound");
    exit;
}


// =========================================================
// GET ORDER ITEMS
// =========================================================

$items_sql = "
    SELECT
        order_items.id,
        order_items.product_id,
        order_items.quantity,
        order_items.price,
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

mysqli_stmt_bind_param(
    $items_stmt,
    "i",
    $order_id
);

mysqli_stmt_execute(
    $items_stmt
);

$items_result = mysqli_stmt_get_result(
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
        Order #<?php echo $order_id; ?> | BUDOL Admin
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- ADMIN CSS -->

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >


    <style>

        .order-details-page {
            max-width: 1200px;
        }


        .order-details-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;

            margin-bottom: 25px;

            color: #555;
            text-decoration: none;

            font-size: 14px;
            font-weight: 600;
        }


        .order-details-back:hover {
            color: #111;
        }


        .order-details-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;

            gap: 20px;

            margin-bottom: 25px;
        }


        .order-details-header h1 {
            margin: 5px 0 8px;

            font-size: 30px;
        }


        .order-details-header p {
            margin: 0;

            color: #777;

            font-size: 14px;
        }


        .order-details-status {
            padding: 9px 16px;

            border-radius: 20px;

            background: #eee;

            color: #333;

            font-size: 13px;

            font-weight: 700;
        }


        .order-details-grid {
            display: grid;

            grid-template-columns:
                minmax(0, 2fr)
                minmax(280px, 1fr);

            gap: 20px;
        }


        .order-details-panel {
            background: #fff;

            border: 1px solid #e5e5e5;

            border-radius: 10px;

            overflow: hidden;

            margin-bottom: 20px;
        }


        .order-details-panel-header {
            padding: 18px 20px;

            border-bottom: 1px solid #eee;

            display: flex;

            justify-content: space-between;

            align-items: center;
        }


        .order-details-panel-header h2 {
            margin: 0;

            font-size: 18px;
        }


        .order-details-panel-body {
            padding: 20px;
        }


        /* ITEMS */

        .order-item {
            display: grid;

            grid-template-columns:
                1fr
                auto
                auto;

            gap: 20px;

            align-items: center;

            padding: 18px 0;

            border-bottom: 1px solid #eee;
        }


        .order-item:first-child {
            padding-top: 0;
        }


        .order-item:last-child {
            padding-bottom: 0;

            border-bottom: none;
        }


        .order-item-name {
            font-size: 15px;

            font-weight: 700;

            color: #222;
        }


        .order-item-meta {
            margin-top: 5px;

            color: #777;

            font-size: 13px;
        }


        .order-item-price {
            font-size: 14px;

            color: #555;
        }


        .order-item-total {
            font-size: 15px;

            font-weight: 700;

            color: #111;
        }


        /* SUMMARY */

        .order-summary-row {
            display: flex;

            justify-content: space-between;

            gap: 20px;

            padding: 10px 0;

            color: #555;

            font-size: 14px;
        }


        .order-summary-total {
            margin-top: 10px;

            padding-top: 16px;

            border-top: 1px solid #ddd;

            font-size: 18px;

            font-weight: 700;

            color: #111;
        }


        /* CUSTOMER */

        .customer-info-row {
            margin-bottom: 16px;
        }


        .customer-info-row:last-child {
            margin-bottom: 0;
        }


        .customer-info-row span {
            display: block;

            margin-bottom: 5px;

            color: #888;

            font-size: 12px;

            text-transform: uppercase;

            letter-spacing: .5px;
        }


        .customer-info-row strong {
            display: block;

            color: #222;

            font-size: 14px;

            line-height: 1.5;
        }


        /* EMPTY ITEMS */

        .order-items-empty {
            padding: 30px;

            text-align: center;

            color: #777;

            font-size: 14px;
        }


        @media (max-width: 850px) {

            .order-details-grid {
                grid-template-columns: 1fr;
            }


            .order-details-header {
                flex-direction: column;
            }

        }


        @media (max-width: 600px) {

            .order-item {
                grid-template-columns: 1fr;
                gap: 8px;
            }

        }
        /* =====================================================
   ORDER STATUS FORM
===================================================== */

.order-status-form {
    display: flex;

    align-items: center;

    gap: 10px;
}


.order-status-select {
    min-width: 150px;

    height: 40px;

    padding: 0 12px;

    border: 1px solid #ddd;

    border-radius: 6px;

    background: #fff;

    color: #222;

    font-size: 14px;

    font-weight: 600;

    outline: none;

    cursor: pointer;
}


.order-status-select:focus {
    border-color: #111;
}


.order-status-button {
    height: 40px;

    padding: 0 15px;

    border: none;

    border-radius: 6px;

    background: #111;

    color: #fff;

    font-size: 13px;

    font-weight: 600;

    cursor: pointer;

    display: inline-flex;

    align-items: center;

    gap: 8px;

    transition: .2s ease;
}


.order-status-button:hover {
    background: #333;
}


@media (max-width: 700px) {

    .order-status-form {
        width: 100%;

        flex-direction: column;

        align-items: stretch;
    }


    .order-status-select,
    .order-status-button {
        width: 100%;
    }

}

    </style>

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header class="admin-header">

    <div class="admin-header-container">


        <a
            href="index.php"
            class="admin-brand"
        >

            <div class="admin-brand-icon">

                <i class="fa-solid fa-bag-shopping"></i>

            </div>

            <div>

                <strong>
                    BUDOL
                </strong>

                <span>
                    ADMIN PANEL
                </span>

            </div>

        </a>


        <div class="admin-header-right">


            <div class="admin-user">

                <div class="admin-user-icon">

                    <i class="fa-regular fa-user"></i>

                </div>

                <div>

                    <strong>
                        <?php
                        echo htmlspecialchars(
                            $_SESSION["first_name"] ?? "Admin"
                        );
                        ?>
                    </strong>

                    <span>
                        Administrator
                    </span>

                </div>

            </div>


            <a
                href="logout.php"
                class="admin-logout"
            >

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>


        </div>

    </div>

</header>


<!-- =====================================================
     ADMIN BODY
===================================================== -->

<div class="admin-layout">


    <!-- SIDEBAR -->

    <aside class="admin-sidebar">


        <div class="admin-sidebar-section">

            <span>
                MAIN
            </span>


            <a
                href="index.php"
                class="admin-nav"
            >

                <i class="fa-solid fa-chart-line"></i>

                <span>
                    Dashboard
                </span>

            </a>


            <a
                href="products.php"
                class="admin-nav"
            >

                <i class="fa-solid fa-box"></i>

                <span>
                    Products
                </span>

            </a>


            <a
                href="orders.php"
                class="admin-nav active"
            >

                <i class="fa-solid fa-cart-shopping"></i>

                <span>
                    Orders
                </span>

            </a>


            <a
                href="users.php"
                class="admin-nav"
            >

                <i class="fa-solid fa-users"></i>

                <span>
                    Customers
                </span>

            </a>

        </div>


        <div class="admin-sidebar-section">

            <span>
                STORE
            </span>


            <a
                href="../products.php"
                class="admin-nav"
                target="_blank"
            >

                <i class="fa-solid fa-store"></i>

                <span>
                    View Store
                </span>

            </a>


            <a
                href="../index.php"
                class="admin-nav"
                target="_blank"
            >

                <i class="fa-solid fa-house"></i>

                <span>
                    Home Page
                </span>

            </a>

        </div>


    </aside>


    <!-- =================================================
         MAIN CONTENT
    ================================================== -->

    <main class="admin-content">


        <div class="order-details-page">


            <!-- BACK -->

            <a
                href="orders.php"
                class="order-details-back"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Orders

            </a>


            <!-- HEADER -->

            <div class="order-details-header">

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
                            "M d, Y h:i A",
                            strtotime(
                                $order["created_at"]
                            )
                        );
                        ?>

                    </p>

                </div>


                <div>

    <form
        action="update_order_status.php"
        method="POST"
        class="order-status-form"
    >

        <input
            type="hidden"
            name="order_id"
            value="<?php echo (int)$order["id"]; ?>"
        >

        <select
            name="status"
            class="order-status-select"
        >

            <option
                value="Pending"
                <?php
                echo $order["status"] === "Pending"
                    ? "selected"
                    : "";
                ?>
            >
                Pending
            </option>

            <option
                value="Processing"
                <?php
                echo $order["status"] === "Processing"
                    ? "selected"
                    : "";
                ?>
            >
                Processing
            </option>

            <option
                value="Shipped"
                <?php
                echo $order["status"] === "Shipped"
                    ? "selected"
                    : "";
                ?>
            >
                Shipped
            </option>

            <option
                value="Delivered"
                <?php
                echo $order["status"] === "Delivered"
                    ? "selected"
                    : "";
                ?>
            >
                Delivered
            </option>

            <option
                value="Cancelled"
                <?php
                echo $order["status"] === "Cancelled"
                    ? "selected"
                    : "";
                ?>
            >
                Cancelled
            </option>

        </select>


        <button
            type="submit"
            class="order-status-button"
        >

            <i class="fa-solid fa-floppy-disk"></i>

            Update Status

        </button>

    </form>

</div>

            </div>


            <!-- GRID -->

            <div class="order-details-grid">


                <!-- =================================================
                     LEFT
                ================================================== -->

                <div>


                    <!-- PRODUCTS -->

                    <section class="order-details-panel">


                        <div class="order-details-panel-header">

                            <h2>
                                Ordered Products
                            </h2>

                            <i class="fa-solid fa-box"></i>

                        </div>


                        <div class="order-details-panel-body">


                            <?php if (count($items) > 0): ?>


                                <?php foreach ($items as $item): ?>


                                    <div class="order-item">


                                        <div>

                                            <div class="order-item-name">

                                                <?php
                                                echo htmlspecialchars(
                                                    $item["product_name"]
                                                );
                                                ?>

                                            </div>


                                            <div class="order-item-meta">

                                                Quantity:
                                                <?php
                                                echo (int)$item["quantity"];
                                                ?>

                                            </div>

                                        </div>


                                        <div class="order-item-price">

                                            ₱<?php

                                            echo number_format(
                                                (float)$item["price"],
                                                2
                                            );

                                            ?>

                                        </div>


                                        <div class="order-item-total">

                                            ₱<?php

                                            echo number_format(
                                                (float)$item["price"]
                                                * (int)$item["quantity"],
                                                2
                                            );

                                            ?>

                                        </div>


                                    </div>


                                <?php endforeach; ?>


                            <?php else: ?>


                                <div class="order-items-empty">

                                    <i class="fa-solid fa-box-open"></i>

                                    <br><br>

                                    No products found for this order.

                                </div>


                            <?php endif; ?>


                        </div>

                    </section>


                    <!-- TOTAL -->

                    <section class="order-details-panel">


                        <div class="order-details-panel-header">

                            <h2>
                                Order Summary
                            </h2>

                        </div>


                        <div class="order-details-panel-body">


                            <div class="order-summary-row">

                                <span>
                                    Order Total
                                </span>

                                <strong>

                                    ₱<?php

                                    echo number_format(
                                        (float)$order["total_amount"],
                                        2
                                    );

                                    ?>

                                </strong>

                            </div>


                            <div class="order-summary-row">

                                <span>
                                    Payment Method
                                </span>

                                <strong>

                                    <?php

                                    echo htmlspecialchars(
                                        $order["payment_method"]
                                    );

                                    ?>

                                </strong>

                            </div>


                            <div class="order-summary-row order-summary-total">

                                <span>
                                    Total
                                </span>

                                <strong>

                                    ₱<?php

                                    echo number_format(
                                        (float)$order["total_amount"],
                                        2
                                    );

                                    ?>

                                </strong>

                            </div>


                        </div>

                    </section>


                </div>


                <!-- =================================================
                     RIGHT
                ================================================== -->

                <div>


                    <!-- CUSTOMER -->

                    <section class="order-details-panel">


                        <div class="order-details-panel-header">

                            <h2>
                                Customer
                            </h2>

                            <i class="fa-solid fa-user"></i>

                        </div>


                        <div class="order-details-panel-body">


                            <div class="customer-info-row">

                                <span>
                                    Full Name
                                </span>

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $order["full_name"]
                                    );
                                    ?>

                                </strong>

                            </div>


                            <div class="customer-info-row">

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

                    </section>


                    <!-- DELIVERY -->

                    <section class="order-details-panel">


                        <div class="order-details-panel-header">

                            <h2>
                                Delivery Address
                            </h2>

                            <i class="fa-solid fa-location-dot"></i>

                        </div>


                        <div class="order-details-panel-body">


                            <div class="customer-info-row">

                                <span>
                                    Address
                                </span>

                                <strong>

                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $order["address"]
                                        )
                                    );
                                    ?>

                                </strong>

                            </div>


                            <div class="customer-info-row">

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


                            <div class="customer-info-row">

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

                    </section>


                </div>


            </div>


        </div>


    </main>


</div>


</body>

</html>