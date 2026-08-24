<?php

session_start();

require_once "config.php";


// =========================================================
// LOGIN CHECK
// =========================================================

if (!isset($_SESSION["user_id"])) {

    header("Location: ../login.php");

    exit;
}


$user_id = (int) $_SESSION["user_id"];


// =========================================================
// POST CHECK
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../checkout.php");

    exit;
}


// =========================================================
// GET CHECKOUT INFORMATION
// =========================================================

$full_name = trim(
    $_POST["full_name"] ?? ""
);

$phone = trim(
    $_POST["phone"] ?? ""
);

$address = trim(
    $_POST["address"] ?? ""
);

$city = trim(
    $_POST["city"] ?? ""
);

$postal_code = trim(
    $_POST["postal_code"] ?? ""
);

$payment_method = trim(
    $_POST["payment_method"] ?? "Cash on Delivery"
);


// =========================================================
// VALIDATION
// =========================================================

if (
    $full_name === "" ||
    $phone === "" ||
    $address === "" ||
    $city === "" ||
    $postal_code === ""
) {

    header(
        "Location: ../checkout.php?error=empty"
    );

    exit;
}


// =========================================================
// PAYMENT METHOD
// =========================================================

$allowed_payment_methods = [
    "Cash on Delivery",
    "GCash",
    "Maya",
    "Bank Transfer"
];

if (
    !in_array(
        $payment_method,
        $allowed_payment_methods,
        true
    )
) {

    header(
        "Location: ../checkout.php?error=payment"
    );

    exit;
}


// =========================================================
// GET CART
// =========================================================

$cart_sql = "
    SELECT
        cart.product_id,
        cart.quantity,
        products.price

    FROM cart

    INNER JOIN products
        ON cart.product_id = products.id

    WHERE cart.user_id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $cart_sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$cart_result =
    mysqli_stmt_get_result($stmt);

mysqli_stmt_close($stmt);


// =========================================================
// CHECK CART
// =========================================================

if (
    mysqli_num_rows($cart_result) === 0
) {

    header(
        "Location: ../cart.php?error=empty"
    );

    exit;
}


// =========================================================
// CALCULATE TOTAL
// =========================================================

$total_amount = 0;

$cart_items = [];


while (
    $item = mysqli_fetch_assoc($cart_result)
) {

    $product_id =
        (int) $item["product_id"];

    $quantity =
        (int) $item["quantity"];

    $price =
        (float) $item["price"];


    if ($quantity <= 0) {
        continue;
    }


    $subtotal =
        $price * $quantity;


    $total_amount +=
        $subtotal;


    $cart_items[] = [
        "product_id" => $product_id,
        "quantity" => $quantity,
        "price" => $price
    ];
}


// =========================================================
// CHECK TOTAL
// =========================================================

if (
    $total_amount <= 0 ||
    empty($cart_items)
) {

    header(
        "Location: ../cart.php?error=empty"
    );

    exit;
}


// =========================================================
// SHIPPING ADDRESS
// =========================================================

$shipping_address =
    $address
    . ", "
    . $city
    . ", "
    . $postal_code;


// =========================================================
// START TRANSACTION
// =========================================================

mysqli_begin_transaction($conn);


try {


    // =====================================================
    // CREATE ORDER
    // =====================================================

    $order_sql = "
        INSERT INTO orders (
            user_id,
            total_amount,
            status,
            shipping_address,
            full_name,
            phone,
            address,
            city,
            postal_code,
            payment_method
        )

        VALUES (
            ?,
            ?,
            'Pending',
            ?,
            ?,
            ?,
            ?,
            ?,
            ?,
            ?
        )
    ";


    $order_stmt = mysqli_prepare(
        $conn,
        $order_sql
    );


    mysqli_stmt_bind_param(
        $order_stmt,
        "idsssssss",
        $user_id,
        $total_amount,
        $shipping_address,
        $full_name,
        $phone,
        $address,
        $city,
        $postal_code,
        $payment_method
    );


    if (
        !mysqli_stmt_execute(
            $order_stmt
        )
    ) {

        throw new Exception(
            "Unable to create order."
        );
    }


    $order_id =
        mysqli_insert_id($conn);


    mysqli_stmt_close(
        $order_stmt
    );


    // =====================================================
    // CREATE ORDER ITEMS
    // =====================================================

    foreach (
        $cart_items as $item
    ) {


        $item_sql = "
            INSERT INTO order_items (
                order_id,
                product_id,
                quantity,
                price
            )

            VALUES (
                ?,
                ?,
                ?,
                ?
            )
        ";


        $item_stmt =
            mysqli_prepare(
                $conn,
                $item_sql
            );


        mysqli_stmt_bind_param(
            $item_stmt,
            "iiid",
            $order_id,
            $item["product_id"],
            $item["quantity"],
            $item["price"]
        );


        if (
            !mysqli_stmt_execute(
                $item_stmt
            )
        ) {

            throw new Exception(
                "Unable to create order items."
            );
        }


        mysqli_stmt_close(
            $item_stmt
        );

    }


    // =====================================================
    // CLEAR CART
    // =====================================================

    $clear_sql = "
        DELETE FROM cart
        WHERE user_id = ?
    ";


    $clear_stmt =
        mysqli_prepare(
            $conn,
            $clear_sql
        );


    mysqli_stmt_bind_param(
        $clear_stmt,
        "i",
        $user_id
    );


    if (
        !mysqli_stmt_execute(
            $clear_stmt
        )
    ) {

        throw new Exception(
            "Unable to clear cart."
        );
    }


    mysqli_stmt_close(
        $clear_stmt
    );


    // =====================================================
    // COMPLETE TRANSACTION
    // =====================================================

    mysqli_commit($conn);


header(
    "Location: ../order_success.php?order_id=" . $order_id
);

exit;


} catch (
    Throwable $e
) {


    mysqli_rollback($conn);


    header(
        "Location: ../checkout.php?error=order"
    );

    exit;

}

?>