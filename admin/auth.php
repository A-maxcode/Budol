<?php

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        "lifetime" => 86400,
        "path" => "/",
        "secure" => false,
        "httponly" => true,
        "samesite" => "Lax"
    ]);

    session_start();
}


if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: /BUDOL/admin/login.php");
    exit;
}

?>