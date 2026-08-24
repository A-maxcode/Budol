<?php

require_once "auth.php";

require_once "../backend/config.php";


// =========================================================
// GET ALL ORDERS
// =========================================================

$sql = "
    SELECT
        orders.id,
        orders.full_name,
        orders.phone,
        orders.total_amount,
        orders.status,
        orders.payment_method,
        orders.created_at

    FROM orders

    ORDER BY orders.created_at DESC
";

$result = mysqli_query($conn, $sql);

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
        Orders | BUDOL Admin
    </title>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
    >

    <link
        rel="stylesheet"
        href="../assets/css/admin-orders.css"
    >

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
     ADMIN LAYOUT
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


        <!-- PAGE HEADER -->

        <div class="admin-page-header">

            <div>

                <span>
                    ORDER MANAGEMENT
                </span>

                <h1>
                    Orders
                </h1>

                <p>
                    View and manage customer orders.
                </p>

            </div>

        </div>


        <!-- =================================================
             ORDERS TABLE
        ================================================== -->

        <section class="admin-panel">


            <div class="admin-panel-header">

                <div>

                    <span>
                        SALES
                    </span>

                    <h2>
                        All Orders
                    </h2>

                </div>

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
                                Payment
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

                            <th>
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        mysqli_num_rows($result) > 0
                    ): ?>


                        <?php while (
                            $order =
                            mysqli_fetch_assoc($result)
                        ): ?>


                            <tr>


                                <!-- ORDER -->

                                <td>

                                    <strong>

                                        #<?php

                                        echo (int)
                                            $order["id"];

                                        ?>

                                    </strong>

                                </td>


                                <!-- CUSTOMER -->

                                <td>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $order["full_name"]
                                        );

                                        ?>

                                    </strong>

                                    <br>

                                    <small>

                                        <?php

                                        echo htmlspecialchars(
                                            $order["phone"]
                                        );

                                        ?>

                                    </small>

                                </td>


                                <!-- PAYMENT -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $order["payment_method"]
                                    );

                                    ?>

                                </td>


                                <!-- TOTAL -->

                                <td>

                                    <strong>

                                        ₱<?php

                                        echo number_format(
                                            (float)
                                            $order["total_amount"],
                                            2
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <!-- STATUS -->

                                <td>

                                    <span
                                        class="admin-order-status status-<?php
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


                                <!-- DATE -->

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


                                <!-- ACTION -->

                                <td>

                                    <a
                                       href="order_view.php?id=<?php echo (int)$order["id"]; ?>"
                                        class="admin-order-view"
                                    >

                                        <i class="fa-solid fa-eye"></i>

                                        View

                                    </a>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="7"
                                class="admin-empty"
                            >

                                <i class="fa-solid fa-box-open"></i>

                                <span>
                                    No orders found.
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