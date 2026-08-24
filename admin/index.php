<?php

require_once "auth.php";

require_once "../backend/config.php";

// =========================================================
// PRODUCT COUNT
// =========================================================

$product_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM products"
);

$product_data = mysqli_fetch_assoc(
    $product_query
);

$total_products = (int) $product_data["total"];


// =========================================================
// CUSTOMER COUNT
// =========================================================

$user_query = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM users
    WHERE role = 'customer'
    "
);

$user_data = mysqli_fetch_assoc(
    $user_query
);

$total_users = (int) $user_data["total"];


// =========================================================
// ORDER COUNT
// =========================================================

$order_query = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM orders"
);

$order_data = mysqli_fetch_assoc(
    $order_query
);

$total_orders = (int) $order_data["total"];


// =========================================================
// TOTAL SALES
// =========================================================

$sales_query = mysqli_query(
    $conn,
    "
    SELECT COALESCE(SUM(total_amount), 0) AS total
    FROM orders
    WHERE status != 'Cancelled'
    "
);

$sales_data = mysqli_fetch_assoc(
    $sales_query
);

$total_sales = (float) $sales_data["total"];


// =========================================================
// PENDING ORDERS
// =========================================================

$pending_query = mysqli_query(
    $conn,
    "
    SELECT COUNT(*) AS total
    FROM orders
    WHERE status = 'Pending'
    "
);

$pending_data = mysqli_fetch_assoc(
    $pending_query
);

$pending_orders = (int) $pending_data["total"];


// =========================================================
// RECENT ORDERS
// =========================================================

$recent_orders_query = mysqli_query(
    $conn,
    "
    SELECT
        id,
        full_name,
        total_amount,
        status,
        created_at

    FROM orders

    ORDER BY created_at DESC

    LIMIT 5
    "
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
        Dashboard | BUDOL Admin
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

</head>


<body>


<!-- =====================================================
     ADMIN HEADER
===================================================== -->

<header class="admin-header">

    <div class="admin-header-container">


        <!-- BRAND -->

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


        <!-- HEADER RIGHT -->

        <div class="admin-header-right">


            <div class="admin-user">

                <div class="admin-user-icon">

                    <i class="fa-regular fa-user"></i>

                </div>

                <div>

                    <strong>

                        <?php

                        echo htmlspecialchars(
                            $_SESSION["first_name"]
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


    <!-- =================================================
         SIDEBAR
    ================================================== -->

    <aside class="admin-sidebar">


        <div class="admin-sidebar-section">

            <span>
                MAIN
            </span>


            <a
                href="index.php"
                class="admin-nav active"
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
                class="admin-nav"
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


        <!-- PAGE TITLE -->

        <div class="admin-page-header">

            <div>

                <span>
                    BUDOL ADMINISTRATION
                </span>

                <h1>
                    Dashboard
                </h1>

                <p>
                    Manage your store and monitor activity.
                </p>

            </div>

        </div>


        <!-- =================================================
             STATISTICS
        ================================================== -->

        <section class="admin-stat-grid">


            <!-- PRODUCTS -->

            <div class="admin-stat-card">

                <div class="admin-stat-top">

                    <div class="admin-stat-icon">

                        <i class="fa-solid fa-box"></i>

                    </div>

                    <span>
                        Products
                    </span>

                </div>


                <strong>

                    <?php

                    echo number_format(
                        $total_products
                    );

                    ?>

                </strong>


                <p>
                    Total products in store
                </p>

            </div>


            <!-- ORDERS -->

            <div class="admin-stat-card">

                <div class="admin-stat-top">

                    <div class="admin-stat-icon">

                        <i class="fa-solid fa-cart-shopping"></i>

                    </div>

                    <span>
                        Orders
                    </span>

                </div>


                <strong>

                    <?php

                    echo number_format(
                        $total_orders
                    );

                    ?>

                </strong>


                <p>
                    Total customer orders
                </p>

            </div>


            <!-- CUSTOMERS -->

            <div class="admin-stat-card">

                <div class="admin-stat-top">

                    <div class="admin-stat-icon">

                        <i class="fa-solid fa-users"></i>

                    </div>

                    <span>
                        Customers
                    </span>

                </div>


                <strong>

                    <?php

                    echo number_format(
                        $total_users
                    );

                    ?>

                </strong>


                <p>
                    Registered accounts
                </p>

            </div>


            <!-- SALES -->

            <div class="admin-stat-card">

                <div class="admin-stat-top">

                    <div class="admin-stat-icon">

                        <i class="fa-solid fa-peso-sign"></i>

                    </div>

                    <span>
                        Sales
                    </span>

                </div>


                <strong class="sales-value">

                    ₱<?php

                    echo number_format(
                        $total_sales,
                        2
                    );

                    ?>

                </strong>


                <p>
                    Total store sales
                </p>

            </div>


        </section>

<!-- =================================================
     PENDING ORDERS
================================================== -->

<section class="admin-panel admin-pending-panel">


    <div class="admin-panel-header">

        <div>

            <span>
                ORDER MANAGEMENT
            </span>

            <h2>
                Pending Orders
            </h2>

        </div>

        <i class="fa-solid fa-clock"></i>

    </div>


    <div class="admin-pending-content">


        <div class="admin-pending-number">

            <?php

            echo number_format(
                $pending_orders
            );

            ?>

        </div>


        <div class="admin-pending-text">

            <strong>
                Orders Waiting for Processing
            </strong>

            <span>
                Review and process pending customer orders.
            </span>

        </div>


        <a
            href="orders.php"
            class="admin-secondary-button"
        >

            <i class="fa-solid fa-arrow-right"></i>

            View Orders

        </a>


    </div>


</section>


        <!-- =================================================
             QUICK ACTIONS
        ================================================== -->

        <section class="admin-panel">
        


            <div class="admin-panel-header">

                <div>

                    <span>
                        MANAGEMENT
                    </span>

                    <h2>
                        Quick Actions
                    </h2>

                </div>

            </div>


            <div class="admin-quick-actions">


                <a
                    href="add_product.php"
                    class="admin-quick-action"
                >

                    <div>

                        <i class="fa-solid fa-plus"></i>

                    </div>

                    <section>

                        <strong>
                            Add Product
                        </strong>

                        <span>
                            Add a new product to your store.
                        </span>

                    </section>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a
                    href="products.php"
                    class="admin-quick-action"
                >

                    <div>

                        <i class="fa-solid fa-boxes-stacked"></i>

                    </div>

                    <section>

                        <strong>
                            Manage Products
                        </strong>

                        <span>
                            View and manage your products.
                        </span>

                    </section>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a
                    href="orders.php"
                    class="admin-quick-action"
                >

                    <div>

                        <i class="fa-solid fa-list-check"></i>

                    </div>

                    <section>

                        <strong>
                            Manage Orders
                        </strong>

                        <span>
                            Review customer orders.
                        </span>

                    </section>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


                <a
                    href="users.php"
                    class="admin-quick-action"
                >

                    <div>

                        <i class="fa-solid fa-user-group"></i>

                    </div>

                    <section>

                        <strong>
                            Customers
                        </strong>

                        <span>
                            View registered customers.
                        </span>

                    </section>

                    <i class="fa-solid fa-arrow-right"></i>

                </a>


            </div>


        </section>


        <!-- =================================================
             RECENT ORDERS
        ================================================== -->

        <section class="admin-panel">


            <div class="admin-panel-header">

                <div>

                    <span>
                        SALES
                    </span>

                    <h2>
                        Recent Orders
                    </h2>

                </div>


                <a
                    href="orders.php"
                    class="admin-view-all"
                >

                    View All

                    <i class="fa-solid fa-arrow-right"></i>

                </a>

            </div>


            <div class="admin-table-wrapper">


                <table class="admin-table">

                    <thead>

                        <tr>

                            <th>
                                Order
                            </th>

                            <th>
                                Customer
                            </th>

                            <th>
                                Total
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Date
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        mysqli_num_rows(
                            $recent_orders_query
                        ) > 0
                    ): ?>


                        <?php while (
                            $order =
                            mysqli_fetch_assoc(
                                $recent_orders_query
                            )
                        ): ?>


                            <tr>

                                <td>

                                    <strong>
                                        #<?php
                                        echo $order["id"];
                                        ?>
                                    </strong>

                                </td>


                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $order["full_name"]
                                    );

                                    ?>

                                </td>


                                <td>

                                    <strong>

                                        ₱<?php

                                        echo number_format(
                                            $order["total_amount"],
                                            2
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <td>

                                    <span
                                        class="order-status
                                        status-<?php
                                        echo strtolower(
                                            $order["status"]
                                        );
                                        ?>"
                                    >

                                        <?php

                                        echo htmlspecialchars(
                                            $order["status"]
                                        );

                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <?php

                                    echo date(
                                        "M d, Y",
                                        strtotime(
                                            $order["created_at"]
                                        )
                                    );

                                    ?>

                                </td>

                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="5"
                                class="admin-empty"
                            >

                                <i class="fa-solid fa-box-open"></i>

                                <span>
                                    No orders yet.
                                </span>

                            </td>

                        </tr>


                    <?php endif; ?>


                    </tbody>

                </table>


            </div>


        </section>


    </main>


</div>


</body>

</html>