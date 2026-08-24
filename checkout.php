<?php

session_start();

require_once "backend/config.php";


// =========================================================
// LOGIN CHECK
// =========================================================

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;
}


$user_id = (int) $_SESSION["user_id"];


// =========================================================
// GET CART ITEMS
// =========================================================

$sql = "
    SELECT
        cart.product_id,
        cart.quantity,
        products.name,
        products.price,
        products.image

    FROM cart

    INNER JOIN products
        ON cart.product_id = products.id

    WHERE cart.user_id = ?

    ORDER BY cart.id DESC
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

mysqli_stmt_close($stmt);


// =========================================================
// CHECK CART
// =========================================================

if (mysqli_num_rows($result) === 0) {

    header("Location: cart.php?error=empty");

    exit;
}


// =========================================================
// CART DATA
// =========================================================

$cart_items = [];

$subtotal = 0;


while (
    $item = mysqli_fetch_assoc($result)
) {

    $quantity =
        (int) $item["quantity"];

    $price =
        (float) $item["price"];

    $item_total =
        $price * $quantity;

    $subtotal +=
        $item_total;


    $item["item_total"] =
        $item_total;


    $cart_items[] =
        $item;
}


$total =
    $subtotal;


// =========================================================
// GET USER INFORMATION
// =========================================================

$user_sql = "
    SELECT
        first_name,
        last_name,
        email

    FROM users

    WHERE id = ?

    LIMIT 1
";


$user_stmt = mysqli_prepare(
    $conn,
    $user_sql
);

mysqli_stmt_bind_param(
    $user_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute(
    $user_stmt
);

$user_result =
    mysqli_stmt_get_result(
        $user_stmt
    );

$user =
    mysqli_fetch_assoc(
        $user_result
    );

mysqli_stmt_close(
    $user_stmt
);


$default_name = "";

if ($user) {

    $default_name =
        trim(
            $user["first_name"]
            . " "
            . $user["last_name"]
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
        Checkout | BUDOL
    </title>


    <!-- FONT AWESOME -->

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >


    <!-- CHECKOUT CSS -->

    <link
        rel="stylesheet"
        href="assets/css/checkout.css"
    >

</head>


<body>


<!-- =====================================================
     HEADER
===================================================== -->

<header class="checkout-header">

    <div class="checkout-header-inner">


        <a
            href="index.php"
            class="checkout-logo"
        >

            <i class="fa-solid fa-bag-shopping"></i>

            <span>
                BUDOL
            </span>

        </a>


        <div class="checkout-title">

            <i class="fa-solid fa-lock"></i>

            Secure Checkout

        </div>


        <a
            href="cart.php"
            class="checkout-back"
        >

            <i class="fa-solid fa-arrow-left"></i>

            Back to Cart

        </a>


    </div>

</header>


<!-- =====================================================
     MAIN
===================================================== -->

<main class="checkout-container">


    <!-- PAGE TITLE -->

    <div class="checkout-page-title">

        <span>
            ORDER PROCESSING
        </span>

        <h1>
            Checkout
        </h1>

        <p>
            Complete your shipping information to place your order.
        </p>

    </div>


    <!-- ERROR MESSAGE -->

    <?php if (isset($_GET["error"])): ?>

        <div class="checkout-alert">

            <i class="fa-solid fa-circle-exclamation"></i>

            <span>

                <?php

                if (
                    $_GET["error"] === "empty"
                ) {

                    echo "Please complete all required fields.";

                } elseif (
                    $_GET["error"] === "payment"
                ) {

                    echo "Invalid payment method.";

                } else {

                    echo "Unable to place your order. Please try again.";

                }

                ?>

            </span>

        </div>

    <?php endif; ?>


    <!-- =================================================
         CHECKOUT GRID
    ================================================== -->

    <div class="checkout-grid">


        <!-- =================================================
             CUSTOMER INFORMATION
        ================================================== -->

        <section class="checkout-panel">


            <div class="checkout-panel-header">

                <div>

                    <span>
                        STEP 1
                    </span>

                    <h2>
                        Shipping Information
                    </h2>

                </div>

                <i class="fa-solid fa-location-dot"></i>

            </div>


            <form
                action="backend/checkout_process.php"
                method="POST"
                id="checkout-form"
            >


                <!-- NAME -->

                <div class="checkout-form-group">

                    <label for="full_name">

                        Full Name

                        <span>
                            *
                        </span>

                    </label>

                    <div class="checkout-input">

                        <i class="fa-regular fa-user"></i>

                        <input
                            type="text"
                            id="full_name"
                            name="full_name"
                            value="<?php echo htmlspecialchars($default_name); ?>"
                            placeholder="Enter your full name"
                            required
                        >

                    </div>

                </div>


                <!-- PHONE -->

                <div class="checkout-form-group">

                    <label for="phone">

                        Phone Number

                        <span>
                            *
                        </span>

                    </label>

                    <div class="checkout-input">

                        <i class="fa-solid fa-phone"></i>

                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            placeholder="09XXXXXXXXX"
                            required
                        >

                    </div>

                </div>


                <!-- ADDRESS -->

                <div class="checkout-form-group">

                    <label for="address">

                        Complete Address

                        <span>
                            *
                        </span>

                    </label>

                    <div class="checkout-input checkout-textarea">

                        <i class="fa-solid fa-house"></i>

                        <textarea
                            id="address"
                            name="address"
                            placeholder="House number, street, barangay"
                            required
                        ></textarea>

                    </div>

                </div>


                <!-- CITY + POSTAL -->

                <div class="checkout-form-row">


                    <div class="checkout-form-group">

                        <label for="city">

                            City / Municipality

                            <span>
                                *
                            </span>

                        </label>

                        <div class="checkout-input">

                            <i class="fa-solid fa-city"></i>

                            <input
                                type="text"
                                id="city"
                                name="city"
                                placeholder="City / Municipality"
                                required
                            >

                        </div>

                    </div>


                    <div class="checkout-form-group">

                        <label for="postal_code">

                            Postal Code

                            <span>
                                *
                            </span>

                        </label>

                        <div class="checkout-input">

                            <i class="fa-solid fa-envelope"></i>

                            <input
                                type="text"
                                id="postal_code"
                                name="postal_code"
                                placeholder="Postal code"
                                required
                            >

                        </div>

                    </div>


                </div>


                <!-- PAYMENT -->

                <!-- PAYMENT -->

<div class="checkout-payment-section">

    <div class="checkout-payment-title">

        <span>
            STEP 2
        </span>

        <h2>
            Payment Method
        </h2>

    </div>


    <!-- CASH ON DELIVERY -->

    <label class="payment-option">

        <input
            type="radio"
            name="payment_method"
            value="Cash on Delivery"
            checked
        >

        <span class="payment-radio">
            <i class="fa-solid fa-check"></i>
        </span>

        <span class="payment-icon">
            <i class="fa-solid fa-money-bill"></i>
        </span>

        <span class="payment-details">

            <strong>
                Cash on Delivery
            </strong>

            <small>
                Pay when your order arrives.
            </small>

        </span>

    </label>


    <!-- GCASH -->

    <label class="payment-option">

        <input
            type="radio"
            name="payment_method"
            value="GCash"
        >

        <span class="payment-radio">
            <i class="fa-solid fa-check"></i>
        </span>

        <span class="payment-icon">

            <i class="fa-solid fa-mobile-screen-button"></i>

        </span>

        <span class="payment-details">

            <strong>
                GCash
            </strong>

            <small>
                Pay using your GCash account.
            </small>

        </span>

    </label>


    <!-- MAYA -->

    <label class="payment-option">

        <input
            type="radio"
            name="payment_method"
            value="Maya"
        >

        <span class="payment-radio">
            <i class="fa-solid fa-check"></i>
        </span>

        <span class="payment-icon">

            <i class="fa-solid fa-wallet"></i>

        </span>

        <span class="payment-details">

            <strong>
                Maya
            </strong>

            <small>
                Pay using your Maya account.
            </small>

        </span>

    </label>


    <!-- BANK TRANSFER -->

    <label class="payment-option">

        <input
            type="radio"
            name="payment_method"
            value="Bank Transfer"
        >

        <span class="payment-radio">
            <i class="fa-solid fa-check"></i>
        </span>

        <span class="payment-icon">

            <i class="fa-solid fa-building-columns"></i>

        </span>

        <span class="payment-details">

            <strong>
                Bank Transfer
            </strong>

            <small>
                Pay through bank transfer.
            </small>

        </span>

    </label>

</div>


                <!-- PLACE ORDER -->

                <button
                    type="submit"
                    class="checkout-submit"
                >

                    <i class="fa-solid fa-lock"></i>

                    Place Order

                </button>


            </form>


        </section>


        <!-- =================================================
             ORDER SUMMARY
        ================================================== -->

        <aside class="checkout-summary">


            <div class="checkout-summary-header">

                <div>

                    <span>
                        ORDER SUMMARY
                    </span>

                    <h2>
                        Your Items
                    </h2>

                </div>

                <i class="fa-solid fa-bag-shopping"></i>

            </div>


            <div class="checkout-items">


                <?php foreach (
                    $cart_items as $item
                ): ?>


                    <div class="checkout-item">


                        <div class="checkout-item-image">

                            <?php if (
                                !empty(
                                    $item["image"]
                                )
                            ): ?>

                                <img
                                    src="<?php echo htmlspecialchars($item["image"]); ?>"
                                    alt="<?php echo htmlspecialchars($item["name"]); ?>"
                                >

                            <?php else: ?>

                                <i class="fa-solid fa-image"></i>

                            <?php endif; ?>

                        </div>


                        <div class="checkout-item-info">

                            <strong>

                                <?php

                                echo htmlspecialchars(
                                    $item["name"]
                                );

                                ?>

                            </strong>


                            <span>

                                Qty:
                                <?php

                                echo $item["quantity"];

                                ?>

                            </span>


                            <b>

                                ₱<?php

                                echo number_format(
                                    $item["item_total"],
                                    2
                                );

                                ?>

                            </b>

                        </div>


                    </div>


                <?php endforeach; ?>


            </div>


            <!-- TOTAL -->

            <div class="checkout-total">


                <div>

                    <span>
                        Subtotal
                    </span>

                    <strong>

                        ₱<?php

                        echo number_format(
                            $subtotal,
                            2
                        );

                        ?>

                    </strong>

                </div>


                <div>

                    <span>
                        Shipping
                    </span>

                    <strong>
                        FREE
                    </strong>

                </div>


                <div class="checkout-grand-total">

                    <span>
                        Total
                    </span>

                    <strong>

                        ₱<?php

                        echo number_format(
                            $total,
                            2
                        );

                        ?>

                    </strong>

                </div>


            </div>


            <div class="checkout-secure">

                <i class="fa-solid fa-shield-halved"></i>

                <span>
                    Your order information is securely processed.
                </span>

            </div>


        </aside>


    </div>


</main>


</body>

</html>