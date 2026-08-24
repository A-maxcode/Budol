<?php

require_once "auth.php";

require_once "../backend/config.php";



// =========================================================
// GET ORDER ID
// =========================================================

$order_id = (int) ($_GET["id"] ?? 0);

if ($order_id <= 0) {
    header("Location: orders.php");
    exit;
}


// =========================================================
// GET ORDER
// =========================================================

$sql = "
    SELECT
        orders.*,
        users.email

    FROM orders

    LEFT JOIN users
        ON orders.user_id = users.id

    WHERE orders.id = ?

    LIMIT 1
";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $order_id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$order =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$order) {
    header("Location: orders.php");
    exit;
}


// =========================================================
// GET ORDER ITEMS
// =========================================================

$items = false;


/*
    We first try the common order_items structure.

    If your table has different column names,
    we will adjust it based on your database.
*/

$sql = "
    SELECT
        order_items.*,
        products.name AS product_name,
        products.image AS product_image

    FROM order_items

    LEFT JOIN products
        ON order_items.product_id = products.id

    WHERE order_items.order_id = ?

    ORDER BY order_items.id ASC
";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $order_id
    );

    mysqli_stmt_execute($stmt);

    $items =
        mysqli_stmt_get_result($stmt);

    mysqli_stmt_close($stmt);
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
        Order #<?php echo $order_id; ?> | BUDOL Admin
    </title>


    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <link
        rel="stylesheet"
        href="../assets/css/admin.css"
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
     LAYOUT
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
                href="../index.php"
                class="admin-nav"
                target="_blank"
            >

                <i class="fa-solid fa-store"></i>

                <span>
                    View Store
                </span>

            </a>

        </div>


    </aside>


    <!-- =================================================
         CONTENT
    ================================================== -->

    <main class="admin-content">


        <!-- PAGE HEADER -->

        <div class="admin-page-header">

            <div>

                <span>
                    ORDER MANAGEMENT
                </span>

                <h1>
                    Order #<?php echo $order_id; ?>
                </h1>

                <p>
                    View order information and update its status.
                </p>

            </div>


            <a
                href="orders.php"
                class="admin-secondary-button"
            >

                <i class="fa-solid fa-arrow-left"></i>

                Back to Orders

            </a>

        </div>


        <!-- =================================================
             ORDER INFORMATION
        ================================================== -->

        <div class="admin-order-grid">


            <!-- CUSTOMER -->

<?php if (isset($_GET["success"]) && $_GET["success"] === "updated"): ?>

    <div class="admin-form-message success">

        <i class="fa-solid fa-circle-check"></i>

        Order status updated successfully.

    </div>

<?php endif; ?>


<?php if (isset($_GET["error"])): ?>

    <div class="admin-form-message error">

        <i class="fa-solid fa-circle-exclamation"></i>

        Unable to update order status.

    </div>

<?php endif; ?>

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <h2>
                        Customer Information
                    </h2>

                    <i class="fa-regular fa-user"></i>

                </div>


                <div class="admin-order-information">


                    <div>

                        <span>
                            Name
                        </span>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $order["full_name"]
                            );

                            ?>

                        </strong>

                    </div>


                    <div>

                        <span>
                            Email
                        </span>

                        <strong>

                            <?php

                            echo htmlspecialchars(
                                $order["email"] ?? "N/A"
                            );

                            ?>

                        </strong>

                    </div>


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

            </section>


            <!-- PAYMENT -->

            <section class="admin-panel">

                <div class="admin-panel-header">

                    <h2>
                        Payment Information
                    </h2>

                    <i class="fa-solid fa-credit-card"></i>

                </div>


                <div class="admin-order-information">


                    <div>

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


                    <div>

                        <span>
                            Total Amount
                        </span>

                        <strong>

                            ₱<?php

                            echo number_format(
                                (float)
                                $order["total_amount"],
                                2
                            );

                            ?>

                        </strong>

                    </div>


                    <div>

                        <span>
                            Order Date
                        </span>

                        <strong>

                            <?php

                            echo date(
                                "M d, Y h:i A",
                                strtotime(
                                    $order["created_at"]
                                )
                            );

                            ?>

                        </strong>

                    </div>


                </div>

            </section>


        </div>


        <!-- =================================================
             SHIPPING
        ================================================== -->

        <section class="admin-panel admin-shipping-panel">


            <div class="admin-panel-header">

                <h2>
                    Shipping Information
                </h2>

                <i class="fa-solid fa-location-dot"></i>

            </div>


            <div class="admin-shipping-information">


                <div>

                    <span>
                        Address
                    </span>

                    <strong>

                        <?php

                        echo nl2br(
                            htmlspecialchars(
                                $order["shipping_address"]
                            )
                        );

                        ?>

                    </strong>

                </div>


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


        </section>


        <!-- =================================================
             STATUS
        ================================================== -->

        <section class="admin-panel">


            <div class="admin-panel-header">

                <h2>
                    Order Status
                </h2>

                <i class="fa-solid fa-rotate"></i>

            </div>


            <form
                action="../backend/admin_update_order.php"
                method="POST"
                class="admin-status-form"
            >

                <input
                    type="hidden"
                    name="order_id"
                    value="<?php echo $order_id; ?>"
                >


                <select
                    name="status"
                    required
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
                    class="admin-primary-button"
                >

                    <i class="fa-solid fa-floppy-disk"></i>

                    Update Status

                </button>

            </form>


        </section>


        <!-- =================================================
             ORDER ITEMS
        ================================================== -->

        <section class="admin-panel">


            <div class="admin-panel-header">

                <h2>
                    Order Items
                </h2>

                <i class="fa-solid fa-box"></i>

            </div>


            <div class="admin-table-wrapper">


                <table class="admin-table">


                    <thead>

                        <tr>

                            <th>
                                Product
                            </th>

                            <th>
                                Quantity
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Subtotal
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        $items &&
                        mysqli_num_rows($items) > 0
                    ): ?>


                        <?php while (
                            $item =
                            mysqli_fetch_assoc($items)
                        ): ?>


                            <tr>


                                <td>

                                    <strong>

                                        <?php

                                        echo htmlspecialchars(
                                            $item["product_name"]
                                            ?? "Product"
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <td>

                                    <?php

                                    echo (int)
                                        ($item["quantity"] ?? 0);

                                    ?>

                                </td>


                                <td>

                                    ₱<?php

                                    echo number_format(
                                        (float)
                                        ($item["price"] ?? 0),
                                        2
                                    );

                                    ?>

                                </td>


                                <td>

                                    <strong>

                                        ₱<?php

                                        echo number_format(
                                            (float)
                                            (
                                                $item["price"]
                                                *
                                                $item["quantity"]
                                            ),
                                            2
                                        );

                                        ?>

                                    </strong>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="4"
                                class="admin-empty"
                            >

                                <i class="fa-solid fa-box-open"></i>

                                <span>
                                    No order items found.
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