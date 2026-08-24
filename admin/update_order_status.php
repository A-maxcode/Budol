<?php

require_once "auth.php";

require_once "../backend/config.php";


// =========================================================
// POST CHECK
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: orders.php");
    exit;
}


// =========================================================
// GET DATA
// =========================================================

$order_id = (int) ($_POST["order_id"] ?? 0);

$status = trim(
    $_POST["status"] ?? ""
);


// =========================================================
// VALIDATE ORDER ID
// =========================================================

if ($order_id <= 0) {
    header("Location: orders.php?error=invalid");
    exit;
}


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


if (!in_array(
    $status,
    $allowed_statuses,
    true
)) {
    header(
        "Location: order_view.php?id="
        . $order_id
        . "&error=status"
    );

    exit;
}


// =========================================================
// UPDATE STATUS
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
        "Location: order_view.php?id="
        . $order_id
        . "&success=status"
    );

    exit;
}


mysqli_stmt_close($stmt);


header(
    "Location: order_view.php?id="
    . $order_id
    . "&error=update"
);

exit;