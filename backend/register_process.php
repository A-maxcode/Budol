<?php

require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../register.php");
    exit;
}

$first_name = trim($_POST["first_name"] ?? "");
$last_name = trim($_POST["last_name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirm_password = $_POST["confirm_password"] ?? "";


/*
|--------------------------------------------------------------------------
| Validate required fields
|--------------------------------------------------------------------------
*/

if (
    empty($first_name) ||
    empty($last_name) ||
    empty($username) ||
    empty($email) ||
    empty($password) ||
    empty($confirm_password)
) {
    die("Please complete all required fields.");
}


/*
|--------------------------------------------------------------------------
| Validate email
|--------------------------------------------------------------------------
*/

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("Please enter a valid email address.");
}


/*
|--------------------------------------------------------------------------
| Validate password
|--------------------------------------------------------------------------
*/

if ($password !== $confirm_password) {
    die("Passwords do not match.");
}

if (strlen($password) < 8) {
    die("Password must be at least 8 characters.");
}


/*
|--------------------------------------------------------------------------
| Check existing username or email
|--------------------------------------------------------------------------
*/

$check_sql = "
    SELECT id
    FROM users
    WHERE username = ? OR email = ?
    LIMIT 1
";

$check_stmt = mysqli_prepare($conn, $check_sql);

mysqli_stmt_bind_param(
    $check_stmt,
    "ss",
    $username,
    $email
);

mysqli_stmt_execute($check_stmt);

$result = mysqli_stmt_get_result($check_stmt);

if (mysqli_num_rows($result) > 0) {

    $existing_user = mysqli_fetch_assoc($result);

    if ($existing_user) {
        die("Username or email is already registered.");
    }
}

mysqli_stmt_close($check_stmt);


/*
|--------------------------------------------------------------------------
| Hash password
|--------------------------------------------------------------------------
*/

$hashed_password = password_hash(
    $password,
    PASSWORD_DEFAULT
);


/*
|--------------------------------------------------------------------------
| Insert user
|--------------------------------------------------------------------------
*/

$sql = "
    INSERT INTO users
    (
        first_name,
        last_name,
        username,
        email,
        password,
        role
    )
    VALUES (?, ?, ?, ?, ?, 'customer')
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "sssss",
    $first_name,
    $last_name,
    $username,
    $email,
    $hashed_password
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: ../login.php?registered=1");
    exit;

}


mysqli_stmt_close($stmt);

die("Registration failed. Please try again.");

?>