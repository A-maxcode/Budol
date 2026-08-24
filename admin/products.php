<?php

require_once "auth.php";

require_once "../backend/config.php";


// =========================================================
// SEARCH
// =========================================================

$search = trim(
    $_GET["search"] ?? ""
);


// =========================================================
// PRODUCT QUERY
// =========================================================

if ($search !== "") {

    $sql = "
        SELECT
            products.*,
            categories.name AS category_name

        FROM products

        LEFT JOIN categories
            ON products.category_id = categories.id

        WHERE
            products.name LIKE ?
            OR categories.name LIKE ?

        ORDER BY products.id DESC
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    $search_value = "%" . $search . "%";

    mysqli_stmt_bind_param(
        $stmt,
        "ss",
        $search_value,
        $search_value
    );

    mysqli_stmt_execute($stmt);

    $products = mysqli_stmt_get_result($stmt);

} else {

    $sql = "
        SELECT
            products.*,
            categories.name AS category_name

        FROM products

        LEFT JOIN categories
            ON products.category_id = categories.id

        ORDER BY products.id DESC
    ";

    $products = mysqli_query(
        $conn,
        $sql
    );
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
        Products | BUDOL Admin
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
                class="admin-nav active"
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
         CONTENT
    ================================================== -->

    <main class="admin-content">

<?php if (isset($_GET["success"])): ?>

    <div class="admin-form-message success">

        <i class="fa-solid fa-circle-check"></i>

        <?php

        if ($_GET["success"] === "added") {

            echo "Product added successfully.";

        } elseif ($_GET["success"] === "updated") {

            echo "Product updated successfully.";

        } elseif ($_GET["success"] === "deleted") {

            echo "Product deleted successfully.";

        }

        ?>

    </div>

<?php endif; ?>

<?php if (isset($_GET["error"]) && $_GET["error"] === "delete"): ?>

    <div class="admin-form-message error">

        <i class="fa-solid fa-circle-exclamation"></i>

        Unable to delete the product.

    </div>

<?php endif; ?>

        <!-- PAGE HEADER -->

        <div class="admin-page-header">

            <div>

                <span>
                    STORE MANAGEMENT
                </span>

                <h1>
                    Products
                </h1>

                <p>
                    Manage your BUDOL product catalog.
                </p>

            </div>


            <a
                href="add_product.php"
                class="admin-primary-button"
            >

                <i class="fa-solid fa-plus"></i>

                Add Product

            </a>

        </div>


        <!-- =================================================
             SEARCH
        ================================================== -->

        <section class="admin-panel">


            <div class="admin-product-toolbar">


                <form
                    method="GET"
                    class="admin-search"
                >

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input
                        type="text"
                        name="search"
                        value="<?php
                        echo htmlspecialchars($search);
                        ?>"
                        placeholder="Search products..."
                    >

                    <?php if ($search !== ""): ?>

                        <a
                            href="products.php"
                            class="admin-search-clear"
                        >

                            <i class="fa-solid fa-xmark"></i>

                        </a>

                    <?php endif; ?>

                </form>


                <div class="admin-product-count">

                    <i class="fa-solid fa-box"></i>

                    <?php

                    echo mysqli_num_rows(
                        $products
                    );

                    ?>

                    Products

                </div>


            </div>


            <!-- =================================================
                 TABLE
            ================================================== -->

            <div class="admin-table-wrapper">


                <table class="admin-table admin-products-table">


                    <thead>

                        <tr>

                            <th>
                                Product
                            </th>

                            <th>
                                Category
                            </th>

                            <th>
                                Price
                            </th>

                            <th>
                                Stock
                            </th>

                            <th>
                                Status
                            </th>

                            <th>
                                Actions
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                    <?php if (
                        mysqli_num_rows($products) > 0
                    ): ?>


                        <?php while (
                            $product =
                            mysqli_fetch_assoc($products)
                        ): ?>


                            <tr>


                                <!-- PRODUCT -->

                                <td>

                                    <div class="admin-product-info">


                                        <?php

                                        $image =
                                            $product["image"]
                                            ?? "";

                                        ?>


                                        <?php if (
                                            !empty($image)
                                        ): ?>

                                            <img
                                                src="../assets/images/<?php
                                                echo htmlspecialchars(
                                                    $image
                                                );
                                                ?>"
                                                alt="<?php
                                                echo htmlspecialchars(
                                                    $product["name"]
                                                );
                                                ?>"
                                            >

                                        <?php else: ?>

                                            <div class="admin-product-placeholder">

                                                <i class="fa-solid fa-image"></i>

                                            </div>

                                        <?php endif; ?>


                                        <div>

                                            <strong>

                                                <?php

                                                echo htmlspecialchars(
                                                    $product["name"]
                                                );

                                                ?>

                                            </strong>

                                            <span>

                                                Product #<?php

                                                echo $product["id"];

                                                ?>

                                            </span>

                                        </div>


                                    </div>

                                </td>


                                <!-- CATEGORY -->

                                <td>

                                    <?php

                                    echo htmlspecialchars(
                                        $product["category_name"]
                                        ?? "Uncategorized"
                                    );

                                    ?>

                                </td>


                                <!-- PRICE -->

                                <td>

                                    <strong>

                                        ₱<?php

                                        echo number_format(
                                            (float)
                                            $product["price"],
                                            2
                                        );

                                        ?>

                                    </strong>

                                </td>


                                <!-- STOCK -->

                                <td>

                                    <?php

                                    $stock =
                                        (int)
                                        $product["stock"];

                                    echo $stock;

                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td>


                                    <?php if (
                                        $stock > 0
                                    ): ?>

                                        <span class="admin-stock in-stock">

                                            <i class="fa-solid fa-circle"></i>

                                            In Stock

                                        </span>

                                    <?php else: ?>

                                        <span class="admin-stock out-stock">

                                            <i class="fa-solid fa-circle"></i>

                                            Out of Stock

                                        </span>

                                    <?php endif; ?>


                                </td>


                                <!-- ACTIONS -->

                                <td>

                                    <div class="admin-product-actions">


                                        <a
                                            href="../product.php?id=<?php
                                            echo $product["id"];
                                            ?>"
                                            target="_blank"
                                            class="admin-action-icon"
                                            title="View Product"
                                        >

                                            <i class="fa-regular fa-eye"></i>

                                        </a>


                                        <a
                                            href="edit_product.php?id=<?php
                                            echo $product["id"];
                                            ?>"
                                            class="admin-action-icon"
                                            title="Edit Product"
                                        >

                                            <i class="fa-solid fa-pen"></i>

                                        </a>


                                        <a
                                            href="delete_product.php?id=<?php
                                            echo $product["id"];
                                            ?>"
                                            class="admin-action-icon danger"
                                            title="Delete Product"
                                            onclick="
                                                return confirm(
                                                    'Are you sure you want to delete this product?'
                                                );
                                            "
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </a>


                                    </div>

                                </td>


                            </tr>


                        <?php endwhile; ?>


                    <?php else: ?>


                        <tr>

                            <td
                                colspan="6"
                                class="admin-empty"
                            >

                                <i class="fa-solid fa-box-open"></i>

                                <span>
                                    No products found.
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