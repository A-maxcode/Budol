<?php

session_start();

require_once "config.php";


// User must be logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}


$user_id = (int) $_SESSION["user_id"];

$action = $_POST["action"] ?? "";


/*
|--------------------------------------------------------------------------
| ADD TO CART
|--------------------------------------------------------------------------
*/

if ($action === "add_to_cart") {

    $product_id = isset($_POST["product_id"])
        ? (int) $_POST["product_id"]
        : 0;

    $quantity = isset($_POST["quantity"])
        ? (int) $_POST["quantity"]
        : 1;


    if ($product_id <= 0 || $quantity <= 0) {
        header("Location: ../products.php");
        exit;
    }


    // Get product and stock
    $sql = "
        SELECT id, stock
        FROM products
        WHERE id = ?
        LIMIT 1
    ";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $product_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $product = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    // Product doesn't exist
    if (!$product) {
        header("Location: ../products.php");
        exit;
    }


    $stock = (int) $product["stock"];


    // Product out of stock
    if ($stock <= 0) {
        header(
            "Location: ../product.php?id=" . $product_id
        );
        exit;
    }


    // Don't allow more than available stock
    if ($quantity > $stock) {
        $quantity = $stock;
    }


    /*
    |--------------------------------------------------------------------------
    | Check existing cart item
    |--------------------------------------------------------------------------
    */

    $check_sql = "
        SELECT id, quantity
        FROM cart
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

    $existing = mysqli_fetch_assoc(
        $check_result
    );

    mysqli_stmt_close($check_stmt);


    /*
    |--------------------------------------------------------------------------
    | Update existing item
    |--------------------------------------------------------------------------
    */

    if ($existing) {

        $new_quantity =
            (int)$existing["quantity"]
            + $quantity;


        if ($new_quantity > $stock) {
            $new_quantity = $stock;
        }


        $update_sql = "
            UPDATE cart
            SET quantity = ?
            WHERE id = ?
            AND user_id = ?
        ";

        $update_stmt = mysqli_prepare(
            $conn,
            $update_sql
        );

        mysqli_stmt_bind_param(
            $update_stmt,
            "iii",
            $new_quantity,
            $existing["id"],
            $user_id
        );

        mysqli_stmt_execute($update_stmt);

        mysqli_stmt_close($update_stmt);

    }


    /*
    |--------------------------------------------------------------------------
    | Add new item
    |--------------------------------------------------------------------------
    */

    else {

        $insert_sql = "
            INSERT INTO cart
            (user_id, product_id, quantity)
            VALUES (?, ?, ?)
        ";

        $insert_stmt = mysqli_prepare(
            $conn,
            $insert_sql
        );

        mysqli_stmt_bind_param(
            $insert_stmt,
            "iii",
            $user_id,
            $product_id,
            $quantity
        );

        mysqli_stmt_execute($insert_stmt);

        mysqli_stmt_close($insert_stmt);

    }


    header("Location: ../cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| UPDATE CART
|--------------------------------------------------------------------------
*/

if ($action === "update") {

    $cart_id = isset($_POST["cart_id"])
        ? (int) $_POST["cart_id"]
        : 0;

    $quantity = isset($_POST["quantity"])
        ? (int) $_POST["quantity"]
        : 1;


    if ($cart_id <= 0) {
        header("Location: ../cart.php");
        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | Get cart item + current stock
    |--------------------------------------------------------------------------
    */

    $sql = "
        SELECT
            cart.id,
            products.stock
        FROM cart

        INNER JOIN products
            ON cart.product_id = products.id

        WHERE cart.id = ?
        AND cart.user_id = ?

        LIMIT 1
    ";

    $stmt = mysqli_prepare(
        $conn,
        $sql
    );

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $cart_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $item = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    if (!$item) {
        header("Location: ../cart.php");
        exit;
    }


    $stock = (int)$item["stock"];


    /*
    |--------------------------------------------------------------------------
    | Quantity <= 0 = remove
    |--------------------------------------------------------------------------
    */

    if ($quantity <= 0 || $stock <= 0) {

        $delete_sql = "
            DELETE FROM cart
            WHERE id = ?
            AND user_id = ?
        ";

        $delete_stmt = mysqli_prepare(
            $conn,
            $delete_sql
        );

        mysqli_stmt_bind_param(
            $delete_stmt,
            "ii",
            $cart_id,
            $user_id
        );

        mysqli_stmt_execute($delete_stmt);

        mysqli_stmt_close($delete_stmt);

    }

    else {

        if ($quantity > $stock) {
            $quantity = $stock;
        }


        $update_sql = "
            UPDATE cart
            SET quantity = ?
            WHERE id = ?
            AND user_id = ?
        ";

        $update_stmt = mysqli_prepare(
            $conn,
            $update_sql
        );

        mysqli_stmt_bind_param(
            $update_stmt,
            "iii",
            $quantity,
            $cart_id,
            $user_id
        );

        mysqli_stmt_execute($update_stmt);

        mysqli_stmt_close($update_stmt);
    }


    header("Location: ../cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| REMOVE FROM CART
|--------------------------------------------------------------------------
*/

if ($action === "remove") {

    $cart_id = isset($_POST["cart_id"])
        ? (int) $_POST["cart_id"]
        : 0;


    if ($cart_id > 0) {

        $delete_sql = "
            DELETE FROM cart
            WHERE id = ?
            AND user_id = ?
        ";

        $delete_stmt = mysqli_prepare(
            $conn,
            $delete_sql
        );

        mysqli_stmt_bind_param(
            $delete_stmt,
            "ii",
            $cart_id,
            $user_id
        );

        mysqli_stmt_execute($delete_stmt);

        mysqli_stmt_close($delete_stmt);
    }


    header("Location: ../cart.php");

    exit;
}


/*
|--------------------------------------------------------------------------
| INVALID ACTION
|--------------------------------------------------------------------------
*/

header("Location: ../cart.php");

exit;

?>