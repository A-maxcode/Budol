<?php

session_start();

require_once "config.php";


// =========================================================
// CHECK REQUEST
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../login.php");
    exit;
}


// =========================================================
// GET LOGIN DATA
// =========================================================

$email = trim(
    $_POST["email"] ?? ""
);

$password = $_POST["password"] ?? "";


// =========================================================
// VALIDATION
// =========================================================

if ($email === "" || $password === "") {

    header(
        "Location: ../login.php?error=empty"
    );

    exit;
}


// =========================================================
// FIND USER
// =========================================================

$sql = "
    SELECT
        id,
        first_name,
        last_name,
        username,
        email,
        password,
        role

    FROM users

    WHERE email = ?

    LIMIT 1
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


mysqli_stmt_bind_param(
    $stmt,
    "s",
    $email
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


$user = mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


// =========================================================
// USER NOT FOUND
// =========================================================

if (!$user) {

    header(
        "Location: ../login.php?error=invalid"
    );

    exit;
}


// =========================================================
// CHECK PASSWORD
// =========================================================

if (!password_verify(
    $password,
    $user["password"]
)) {

    header(
        "Location: ../login.php?error=invalid"
    );

    exit;
}


// =========================================================
// CUSTOMER LOGIN ONLY
// =========================================================

// This login page is ONLY for customers.

if ($user["role"] !== "customer") {

    header(
        "Location: ../login.php?error=customer_only"
    );

    exit;
}


// =========================================================
// CREATE CUSTOMER SESSION
// =========================================================

session_regenerate_id(true);

$_SESSION["user_id"] = (int) $user["id"];

$_SESSION["first_name"] = $user["first_name"];
$_SESSION["last_name"] = $user["last_name"];
$_SESSION["username"] = $user["username"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = "customer";


// =========================================================
// CUSTOMER REDIRECT
// =========================================================

header(
    "Location: ../index.php"
);

exit;

?>