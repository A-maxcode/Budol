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
// GET PRODUCT IMAGE
// =========================================================

$sql = "
    SELECT image
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


// =========================================================
// PRODUCT NOT FOUND
// =========================================================

if (!$product) {
    header("Location: products.php");
    exit;
}


// =========================================================
// DELETE PRODUCT
// =========================================================

$sql = "
    DELETE FROM products
    WHERE id = ?
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

$deleted = mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


// =========================================================
// DELETE IMAGE
// =========================================================

if (
    $deleted &&
    !empty($product["image"])
) {

    $image_path =
        "../assets/images/"
        . $product["image"];


    if (
        file_exists($image_path)
    ) {

        unlink($image_path);

    }
}


// =========================================================
// RESULT
// =========================================================

if ($deleted) {

    header(
        "Location: products.php?success=deleted"
    );

    exit;
}


header(
    "Location: products.php?error=delete"
);

exit;

?>