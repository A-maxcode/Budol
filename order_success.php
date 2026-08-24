<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET["order_id"])
    ? (int) $_GET["order_id"]
    : 0;

if ($order_id <= 0) {
    header("Location: orders.php");
    exit;
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
        Order Confirmed | BUDOL
    </title>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link
        rel="stylesheet"
        href="assets/css/order_success.css"
    >

</head>


<body>


<main class="success-container">


    <div class="success-card">


        <div class="success-icon">

            <i class="fa-solid fa-check"></i>

        </div>


        <span class="success-label">
            ORDER CONFIRMED
        </span>


        <h1>
            Thank You for Your Order
        </h1>


        <p>
            Your BUDOL order has been successfully placed.
        </p>


        <div class="success-order">

            <span>
                Order Number
            </span>

            <strong>
                #<?php echo $order_id; ?>
            </strong>

        </div>


        <div class="success-status">

            <i class="fa-solid fa-clock"></i>

            <div>

                <strong>
                    Order Status
                </strong>

                <span>
                    Pending
                </span>

            </div>

        </div>


        <div class="success-actions">


            <a
                href="orders.php"
                class="success-primary"
            >

                <i class="fa-solid fa-receipt"></i>

                View My Orders

            </a>


            <a
                href="products.php"
                class="success-secondary"
            >

                <i class="fa-solid fa-bag-shopping"></i>

                Continue Shopping

            </a>


        </div>


    </div>


</main>


</body>

</html>