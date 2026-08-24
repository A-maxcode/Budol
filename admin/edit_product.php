<?php

require_once "auth.php";

require_once "../backend/config.php";


// =========================================================
// ADMIN ACCESS CHECK
// =========================================================

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: login.php");
    exit;
}


// =========================================================
// GET PRODUCT ID
// =========================================================

$product_id = (int) ($_GET["id"] ?? 0);

if ($product_id <= 0) {
    header("Location: products.php");
    exit;
}


// =========================================================
// GET PRODUCT
// =========================================================

$sql = "
    SELECT *
    FROM products
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $product_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$product = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$product) {
    header("Location: products.php");
    exit;
}


// =========================================================
// GET CATEGORIES
// =========================================================

$categories = mysqli_query(
    $conn,
    "SELECT * FROM categories ORDER BY name ASC"
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
        Edit Product | BUDOL Admin
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

        </div>


    </aside>


    <!-- =================================================
         CONTENT
    ================================================== -->

    <main class="admin-content">


        <div class="admin-page-header">

            <div>

                <span>
                    PRODUCT MANAGEMENT
                </span>

                <h1>
                    Edit Product
                </h1>

                <p>
                    Update product information.
                </p>

            </div>

        </div>


        <?php if (isset($_GET["error"])): ?>

            <div class="admin-form-message error">

                <i class="fa-solid fa-circle-exclamation"></i>

                <?php

                switch ($_GET["error"]) {

                    case "invalid":
                        echo "Please complete all required fields.";
                        break;

                    case "image":
                        echo "Invalid image format.";
                        break;

                    case "upload":
                        echo "The image could not be uploaded.";
                        break;

                    case "database":
                        echo "Unable to update the product.";
                        break;

                    default:
                        echo "Something went wrong.";
                }

                ?>

            </div>

        <?php endif; ?>


        <section class="admin-panel">


            <form
                action="../backend/admin_update_product.php"
                method="POST"
                enctype="multipart/form-data"
                class="admin-product-form"
            >


                <input
                    type="hidden"
                    name="id"
                    value="<?php
                    echo $product["id"];
                    ?>"
                >


                <div class="admin-form-grid">


                    <!-- PRODUCT NAME -->

                    <div class="admin-form-group full">

                        <label>
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?php
                            echo htmlspecialchars(
                                $product["name"]
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- CATEGORY -->

                    <div class="admin-form-group">

                        <label>
                            Category
                        </label>

                        <select
                            name="category_id"
                            required
                        >

                            <option value="">
                                Select Category
                            </option>


                            <?php while (
                                $category =
                                mysqli_fetch_assoc(
                                    $categories
                                )
                            ): ?>

                                <option
                                    value="<?php
                                    echo $category["id"];
                                    ?>"
                                    <?php

                                    if (
                                        $product["category_id"]
                                        ==
                                        $category["id"]
                                    ) {

                                        echo "selected";

                                    }

                                    ?>
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $category["name"]
                                    );

                                    ?>

                                </option>

                            <?php endwhile; ?>

                        </select>

                    </div>


                    <!-- PRICE -->

                    <div class="admin-form-group">

                        <label>
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            value="<?php
                            echo htmlspecialchars(
                                $product["price"]
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- STOCK -->

                    <div class="admin-form-group">

                        <label>
                            Stock
                        </label>

                        <input
                            type="number"
                            name="stock"
                            min="0"
                            value="<?php
                            echo htmlspecialchars(
                                $product["stock"]
                            );
                            ?>"
                            required
                        >

                    </div>


                    <!-- IMAGE -->

                    <div class="admin-form-group">

                        <label>
                            Product Image
                        </label>

                        <input
                            type="file"
                            name="image"
                            accept="image/*"
                        >

                    </div>


                    <!-- DESCRIPTION -->

                    <div class="admin-form-group full">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="6"
                        ><?php

                        echo htmlspecialchars(
                            $product["description"] ?? ""
                        );

                        ?></textarea>

                    </div>


                </div>


                <!-- CURRENT IMAGE -->

                <?php if (
                    !empty($product["image"])
                ): ?>

                    <div class="admin-current-image">

                        <span>
                            Current Image
                        </span>

                        <img
                            src="../assets/images/<?php
                            echo htmlspecialchars(
                                $product["image"]
                            );
                            ?>"
                            alt="Current Product Image"
                        >

                    </div>

                <?php endif; ?>


                <!-- ACTIONS -->

                <div class="admin-form-actions">


                    <a
                        href="products.php"
                        class="admin-secondary-button"
                    >

                        <i class="fa-solid fa-arrow-left"></i>

                        Cancel

                    </a>


                    <button
                        type="submit"
                        class="admin-primary-button"
                    >

                        <i class="fa-solid fa-floppy-disk"></i>

                        Save Changes

                    </button>


                </div>


            </form>


        </section>


    </main>


</div>


</body>

</html>