<?php

session_start();

require_once "config.php";


// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


$user_id = (int) $_SESSION["user_id"];


// Get action
$action = $_POST["action"] ?? "";


// Get product ID
$product_id = isset($_POST["product_id"])
    ? (int) $_POST["product_id"]
    : 0;


// Validate product ID
if ($product_id <= 0) {
    header("Location: ../products.php");
    exit;
}


/*
|--------------------------------------------------------------------------
| ADD TO WISHLIST
|--------------------------------------------------------------------------
*/

if ($action === "add") {

    // Check whether product exists
    $check_sql = "
        SELECT id
        FROM products
        WHERE id = ?
        LIMIT 1
    ";

    $check_stmt = mysqli_prepare(
        $conn,
        $check_sql
    );

    mysqli_stmt_bind_param(
        $check_stmt,
        "i",
        $product_id
    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result(
        $check_stmt
    );

    $product_exists =
        mysqli_num_rows($check_result) > 0;

    mysqli_stmt_close($check_stmt);


    if (!$product_exists) {
        header("Location: ../products.php");
        exit;
    }


    // Check if already in wishlist
    $check_sql = "
        SELECT id
        FROM wishlist
        WHERE user_id = ?
        AND product_id = ?
        LIMIT 1
    ";

    $check_stmt = mysqli_prepare(
        $conn,
        $check_sql
    );

    mysqli_stmt_bind_param(
        $check_stmt,
        "ii",
        $user_id,
        $product_id
    );

    mysqli_stmt_execute($check_stmt);

    $check_result = mysqli_stmt_get_result(
        $check_stmt
    );

    $already_exists =
        mysqli_num_rows($check_result) > 0;

    mysqli_stmt_close($check_stmt);


    // Only insert if it doesn't already exist
    if (!$already_exists) {

        $insert_sql = "
            INSERT INTO wishlist
            (user_id, product_id)
            VALUES (?, ?)
        ";

        $insert_stmt = mysqli_prepare(
            $conn,
            $insert_sql
        );

        mysqli_stmt_bind_param(
            $insert_stmt,
            "ii",
            $user_id,
            $product_id
        );

        mysqli_stmt_execute($insert_stmt);

        mysqli_stmt_close($insert_stmt);
    }


    header(
        "Location: ../wishlist.php"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| REMOVE FROM WISHLIST
|--------------------------------------------------------------------------
*/

if ($action === "remove") {

    $delete_sql = "
        DELETE FROM wishlist
        WHERE user_id = ?
        AND product_id = ?
    ";

    $delete_stmt = mysqli_prepare(
        $conn,
        $delete_sql
    );

    mysqli_stmt_bind_param(
        $delete_stmt,
        "ii",
        $user_id,
        $product_id
    );

    mysqli_stmt_execute($delete_stmt);

    mysqli_stmt_close($delete_stmt);


    header(
        "Location: ../wishlist.php"
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| INVALID ACTION
|--------------------------------------------------------------------------
*/

header("Location: ../wishlist.php");

exit;

?>