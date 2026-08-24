<?php

require_once "auth.php";

require_once "config.php";




// =========================================================
// POST CHECK
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/orders.php");
    exit;
}


// =========================================================
// GET DATA
// =========================================================

$order_id = (int) ($_POST["order_id"] ?? 0);

$status = $_POST["status"] ?? "";


// =========================================================
// ALLOWED STATUS
// =========================================================

$allowed_statuses = [
    "Pending",
    "Processing",
    "Shipped",
    "Delivered",
    "Cancelled"
];


if (
    $order_id <= 0 ||
    !in_array(
        $status,
        $allowed_statuses,
        true
    )
) {

    header(
        "Location: ../admin/orders.php?error=update"
    );

    exit;
}


// =========================================================
// UPDATE
// =========================================================

$sql = "
    UPDATE orders

    SET status = ?

    WHERE id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $status,
    $order_id
);


if (
    mysqli_stmt_execute($stmt)
) {

    mysqli_stmt_close($stmt);

    header(
        "Location: ../admin/order_view.php?id="
        . $order_id
        . "&success=updated"
    );

    exit;
}


mysqli_stmt_close($stmt);


header(
    "Location: ../admin/order_view.php?id="
    . $order_id
    . "&error=update"
);

exit;

?>