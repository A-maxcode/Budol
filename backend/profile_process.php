<?php

session_start();

require_once "config.php";


// =========================================================
// CHECK LOGIN
// =========================================================

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.php");
    exit;
}

$user_id = (int) $_SESSION["user_id"];


// =========================================================
// CHECK REQUEST
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../profile.php");
    exit;
}


$action = $_POST["action"] ?? "";


// =========================================================
// UPDATE PROFILE
// =========================================================

if ($action === "update_profile") {

    $first_name = trim(
        $_POST["first_name"] ?? ""
    );

    $last_name = trim(
        $_POST["last_name"] ?? ""
    );

    $email = trim(
        $_POST["email"] ?? ""
    );


    // ---------------------------------------------
    // Validate
    // ---------------------------------------------

    if (
        $first_name === "" ||
        $last_name === "" ||
        $email === ""
    ) {

        header(
            "Location: ../profile.php?error=empty"
        );

        exit;
    }


    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        header(
            "Location: ../profile.php?error=email"
        );

        exit;
    }


    // ---------------------------------------------
    // Check if email belongs to another user
    // ---------------------------------------------

    $check_sql = "
        SELECT id
        FROM users
        WHERE email = ?
        AND id != ?
        LIMIT 1
    ";

    $check_stmt = mysqli_prepare(
        $conn,
        $check_sql
    );

    mysqli_stmt_bind_param(
        $check_stmt,
        "si",
        $email,
        $user_id
    );

    mysqli_stmt_execute(
        $check_stmt
    );

    $check_result = mysqli_stmt_get_result(
        $check_stmt
    );


    if (mysqli_num_rows($check_result) > 0) {

        mysqli_stmt_close(
            $check_stmt
        );

        header(
            "Location: ../profile.php?error=email_exists"
        );

        exit;
    }


    mysqli_stmt_close(
        $check_stmt
    );


    // ---------------------------------------------
    // Update user
    // ---------------------------------------------

    $update_sql = "
        UPDATE users

        SET
            first_name = ?,
            last_name = ?,
            email = ?

        WHERE id = ?
    ";


    $update_stmt = mysqli_prepare(
        $conn,
        $update_sql
    );


    mysqli_stmt_bind_param(
        $update_stmt,
        "sssi",
        $first_name,
        $last_name,
        $email,
        $user_id
    );


    mysqli_stmt_execute(
        $update_stmt
    );


    mysqli_stmt_close(
        $update_stmt
    );


    // ---------------------------------------------
    // Update session name
    // ---------------------------------------------

    $_SESSION["first_name"] = $first_name;


    header(
        "Location: ../profile.php?success=profile"
    );

    exit;
}


// =========================================================
// CHANGE PASSWORD
// =========================================================

if ($action === "change_password") {

    $current_password =
        $_POST["current_password"] ?? "";

    $new_password =
        $_POST["new_password"] ?? "";

    $confirm_password =
        $_POST["confirm_password"] ?? "";


    // ---------------------------------------------
    // Check fields
    // ---------------------------------------------

    if (
        $current_password === "" ||
        $new_password === "" ||
        $confirm_password === ""
    ) {

        header(
            "Location: ../profile.php?error=password_empty"
        );

        exit;
    }


    // ---------------------------------------------
    // Check password length
    // ---------------------------------------------

    if (strlen($new_password) < 8) {

        header(
            "Location: ../profile.php?error=password_length"
        );

        exit;
    }


    // ---------------------------------------------
    // Confirm password
    // ---------------------------------------------

    if ($new_password !== $confirm_password) {

        header(
            "Location: ../profile.php?error=password_match"
        );

        exit;
    }


    // ---------------------------------------------
    // Get current password
    // ---------------------------------------------

    $password_sql = "
        SELECT password
        FROM users
        WHERE id = ?
        LIMIT 1
    ";


    $password_stmt = mysqli_prepare(
        $conn,
        $password_sql
    );


    mysqli_stmt_bind_param(
        $password_stmt,
        "i",
        $user_id
    );


    mysqli_stmt_execute(
        $password_stmt
    );


    $password_result = mysqli_stmt_get_result(
        $password_stmt
    );


    $user = mysqli_fetch_assoc(
        $password_result
    );


    mysqli_stmt_close(
        $password_stmt
    );


    if (!$user) {

        header(
            "Location: ../profile.php?error=user"
        );

        exit;
    }


    // ---------------------------------------------
    // Verify current password
    // ---------------------------------------------

    if (
        !password_verify(
            $current_password,
            $user["password"]
        )
    ) {

        header(
            "Location: ../profile.php?error=password_wrong"
        );

        exit;
    }


    // ---------------------------------------------
    // Hash new password
    // ---------------------------------------------

    $hashed_password = password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );


    // ---------------------------------------------
    // Update password
    // ---------------------------------------------

    $update_password_sql = "
        UPDATE users

        SET password = ?

        WHERE id = ?
    ";


    $update_password_stmt = mysqli_prepare(
        $conn,
        $update_password_sql
    );


    mysqli_stmt_bind_param(
        $update_password_stmt,
        "si",
        $hashed_password,
        $user_id
    );


    mysqli_stmt_execute(
        $update_password_stmt
    );


    mysqli_stmt_close(
        $update_password_stmt
    );


    header(
        "Location: ../profile.php?success=password"
    );

    exit;
}


// =========================================================
// INVALID ACTION
// =========================================================

header(
    "Location: ../profile.php"
);

exit;

?>