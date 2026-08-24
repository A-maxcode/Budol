<?php

require_once "auth.php";

require_once "../backend/config.php";




// GET CATEGORIES

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
        Add Product | BUDOL Admin
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


<div class="admin-layout">


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


    </aside>


    <main class="admin-content">

<?php if (isset($_GET["error"])): ?>

    <div class="admin-form-message error">

        <i class="fa-solid fa-circle-exclamation"></i>

        <?php

        switch ($_GET["error"]) {

            case "invalid":
                echo "Please complete all required fields.";
                break;

            case "image":
                echo "Only JPG, PNG, WEBP, and GIF images are allowed.";
                break;

            case "upload":
                echo "The image could not be uploaded.";
                break;

            case "database":
                echo "Unable to add the product.";
                break;

            default:
                echo "Something went wrong.";
        }

        ?>

    </div>

<?php endif; ?>

        <div class="admin-page-header">

            <div>

                <span>
                    PRODUCT MANAGEMENT
                </span>

                <h1>
                    Add Product
                </h1>

                <p>
                    Add a new product to your BUDOL store.
                </p>

            </div>

        </div>


        <section class="admin-panel">


            <form
                action="../backend/admin_add_product.php"
                method="POST"
                enctype="multipart/form-data"
                class="admin-product-form"
            >


                <div class="admin-form-grid">


                    <div class="admin-form-group full">

                        <label>
                            Product Name
                        </label>

                        <input
                            type="text"
                            name="name"
                            placeholder="Enter product name"
                            required
                        >

                    </div>


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


                    <div class="admin-form-group">

                        <label>
                            Price
                        </label>

                        <input
                            type="number"
                            name="price"
                            step="0.01"
                            min="0"
                            placeholder="0.00"
                            required
                        >

                    </div>


                    <div class="admin-form-group">

                        <label>
                            Stock
                        </label>

                        <input
                            type="number"
                            name="stock"
                            min="0"
                            placeholder="0"
                            required
                        >

                    </div>


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


                    <div class="admin-form-group full">

                        <label>
                            Description
                        </label>

                        <textarea
                            name="description"
                            rows="6"
                            placeholder="Enter product description"
                        ></textarea>

                    </div>


                </div>


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

                        <i class="fa-solid fa-plus"></i>

                        Add Product

                    </button>


                </div>


            </form>


        </section>


    </main>


</div>


</body>

</html>