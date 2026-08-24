<?php

session_start();

require_once "backend/config.php";


// Search
$search = trim($_GET["search"] ?? "");

// Category
$category = trim($_GET["category"] ?? "");


// Get categories
$category_sql = "
    SELECT id, name
    FROM categories
    ORDER BY name ASC
";

$category_result = mysqli_query(
    $conn,
    $category_sql
);


// Get products
$sql = "
    SELECT
        p.id,
        p.name,
        p.description,
        p.price,
        p.stock,
        p.image,
        c.name AS category_name

    FROM products p

    LEFT JOIN categories c
        ON p.category_id = c.id

    WHERE 1=1
";

$params = [];
$types = "";


// Search filter
if ($search !== "") {

    $sql .= "
        AND (
            p.name LIKE ?
            OR p.description LIKE ?
        )
    ";

    $search_value = "%" . $search . "%";

    $params[] = $search_value;
    $params[] = $search_value;

    $types .= "ss";
}


// Category filter
if ($category !== "") {

    $sql .= "
        AND c.name = ?
    ";

    $params[] = $category;

    $types .= "s";
}


$sql .= "
    ORDER BY p.created_at DESC
";


$stmt = mysqli_prepare($conn, $sql);


if (!empty($params)) {

    mysqli_stmt_bind_param(
        $stmt,
        $types,
        ...$params
    );

}


mysqli_stmt_execute($stmt);

$product_result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Products | BUDOL</title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- USE YOUR EXISTING CSS -->

    <link
        rel="stylesheet"
        href="assets/css/products.css"
    >

</head>

<body>


<!-- HEADER -->

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
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">

                <i class="fa-solid fa-magnifying-glass"></i>

            </button>

        </form>


        <!-- ACTIONS -->

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


            <?php if (isset($_SESSION["user_id"])): ?>

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

            <?php else: ?>

                <a
                    href="login.php"
                    class="header-action"
                >

                    <i class="fa-regular fa-user"></i>

                    <span>
                        Account
                    </span>

                </a>

            <?php endif; ?>


        </nav>

    </div>

</header>


<!-- CATEGORY NAVIGATION -->

<nav class="category-nav">

    <div class="category-container">


        <a href="products.php">

            <i class="fa-solid fa-border-all"></i>

            All Products

        </a>


        <?php while ($cat = mysqli_fetch_assoc($category_result)): ?>

            <a
                href="products.php?category=<?php echo urlencode($cat["name"]); ?>"
            >

                <?php
                echo htmlspecialchars(
                    $cat["name"]
                );
                ?>

            </a>

        <?php endwhile; ?>


    </div>

</nav>


<!-- MAIN -->

<main class="products-page">


    <!-- PAGE TITLE -->

    <section class="page-header">

        <div>

            <p class="section-label">
                BUDOL STORE
            </p>


            <h1>

                <?php

                if ($category !== "") {

                    echo htmlspecialchars($category);

                } elseif ($search !== "") {

                    echo "Search Results";

                } else {

                    echo "All Products";

                }

                ?>

            </h1>


            <p class="product-count">

                <?php
                echo mysqli_num_rows($product_result);
                ?>

                product(s) found

            </p>

        </div>


        <?php if ($search !== "" || $category !== ""): ?>

            <a
                href="products.php"
                class="clear-filter"
            >

                <i class="fa-solid fa-xmark"></i>

                Clear Filter

            </a>

        <?php endif; ?>


    </section>


    <!-- PRODUCTS -->

    <?php if (mysqli_num_rows($product_result) > 0): ?>


        <section class="product-grid">


            <?php while ($product = mysqli_fetch_assoc($product_result)): ?>


                <article class="product-card">


                    <!-- PRODUCT IMAGE -->

                    <a
                        href="product.php?id=<?php echo $product["id"]; ?>"
                        class="product-image"
                    >


                        <?php if (!empty($product["image"])): ?>

                            <img
                                src="assets/images/products/<?php echo htmlspecialchars($product["image"]); ?>"
                                alt="<?php echo htmlspecialchars($product["name"]); ?>"
                            >

                        <?php else: ?>

                            <div class="no-image">

                                <i class="fa-solid fa-image"></i>

                            </div>

                        <?php endif; ?>


                        <?php if ((int)$product["stock"] <= 0): ?>

                            <span class="stock-badge out">

                                Out of Stock

                            </span>

                        <?php elseif ((int)$product["stock"] <= 5): ?>

                            <span class="stock-badge low">

                                Low Stock

                            </span>

                        <?php endif; ?>


                    </a>


                    <!-- PRODUCT INFORMATION -->

                    <div class="product-info">


                        <p class="product-category">

                            <?php
                            echo htmlspecialchars(
                                $product["category_name"]
                                ?? "Uncategorized"
                            );
                            ?>

                        </p>


                        <a
                            href="product.php?id=<?php echo $product["id"]; ?>"
                            class="product-name"
                        >

                            <?php
                            echo htmlspecialchars(
                                $product["name"]
                            );
                            ?>

                        </a>


                        <div class="product-bottom">


                            <span class="product-price">

                                ₱<?php

                                echo number_format(
                                    (float)$product["price"],
                                    2
                                );

                                ?>

                            </span>


                            <a
                                href="product.php?id=<?php echo $product["id"]; ?>"
                                class="view-product"
                                title="View Product"
                            >

                                <i class="fa-solid fa-arrow-right"></i>

                            </a>


                        </div>


                    </div>


                </article>


            <?php endwhile; ?>


        </section>


    <?php else: ?>


        <!-- NO PRODUCTS -->

        <section class="empty-products">


            <div class="empty-icon">

                <i class="fa-solid fa-box-open"></i>

            </div>


            <h2>

                No Products Found

            </h2>


            <p>

                We couldn't find any products
                matching your search.

            </p>


            <a
                href="products.php"
                class="shop-button"
            >

                View All Products

            </a>


        </section>


    <?php endif; ?>


</main>


<!-- FOOTER -->

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

            <a href="cart.php">
                Cart
            </a>

            <a href="wishlist.php">
                Wishlist
            </a>

        </div>


        <div>

            <h3>
                Account
            </h3>

            <a href="login.php">
                Login
            </a>

            <a href="register.php">
                Register
            </a>

            <a href="profile.php">
                Profile
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