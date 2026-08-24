<?php

session_start();

require_once "config.php";


// =========================================================
// ADMIN ACCESS CHECK
// =========================================================

if (
    !isset($_SESSION["user_id"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: ../admin/login.php");
    exit;
}


// =========================================================
// REQUEST CHECK
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/products.php");
    exit;
}


// =========================================================
// GET DATA
// =========================================================

$id = (int) ($_POST["id"] ?? 0);

$name = trim(
    $_POST["name"] ?? ""
);

$category_id = (int) (
    $_POST["category_id"] ?? 0
);

$price = (float) (
    $_POST["price"] ?? 0
);

$stock = (int) (
    $_POST["stock"] ?? 0
);

$description = trim(
    $_POST["description"] ?? ""
);


// =========================================================
// VALIDATION
// =========================================================

if (
    $id <= 0 ||
    $name === "" ||
    $category_id <= 0 ||
    $price < 0 ||
    $stock < 0
) {

    header(
        "Location: ../admin/edit_product.php?id="
        . $id
        . "&error=invalid"
    );

    exit;
}


// =========================================================
// GET CURRENT IMAGE
// =========================================================

$sql = "
    SELECT image
    FROM products
    WHERE id = ?
    LIMIT 1
";

$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$current_product =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$current_product) {

    header(
        "Location: ../admin/products.php"
    );

    exit;
}


$current_image =
    $current_product["image"] ?? "";

$new_image =
    $current_image;


// =========================================================
// IMAGE UPLOAD
// =========================================================

if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] === UPLOAD_ERR_OK
) {

    $image =
        $_FILES["image"];


    $allowed_types = [
        "image/jpeg",
        "image/png",
        "image/webp",
        "image/gif"
    ];


    if (
        !in_array(
            $image["type"],
            $allowed_types,
            true
        )
    ) {

        header(
            "Location: ../admin/edit_product.php?id="
            . $id
            . "&error=image"
        );

        exit;
    }


    $extension =
        strtolower(
            pathinfo(
                $image["name"],
                PATHINFO_EXTENSION
            )
        );


    $new_image =
        uniqid("product_", true)
        . "."
        . $extension;


    $upload_directory =
        "../assets/images/";


    if (
        !is_dir($upload_directory)
    ) {

        mkdir(
            $upload_directory,
            0755,
            true
        );
    }


    $upload_path =
        $upload_directory
        . $new_image;


    if (
        !move_uploaded_file(
            $image["tmp_name"],
            $upload_path
        )
    ) {

        header(
            "Location: ../admin/edit_product.php?id="
            . $id
            . "&error=upload"
        );

        exit;
    }


    // DELETE OLD IMAGE

    if (
        !empty($current_image)
    ) {

        $old_image =
            $upload_directory
            . $current_image;


        if (
            file_exists($old_image)
        ) {

            unlink($old_image);

        }
    }
}


// =========================================================
// UPDATE PRODUCT
// =========================================================

$sql = "
    UPDATE products

    SET
        name = ?,
        category_id = ?,
        price = ?,
        stock = ?,
        description = ?,
        image = ?

    WHERE id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!$stmt) {

    header(
        "Location: ../admin/edit_product.php?id="
        . $id
        . "&error=database"
    );

    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    "sidissi",
    $name,
    $category_id,
    $price,
    $stock,
    $description,
    $new_image,
    $id
);


if (
    mysqli_stmt_execute($stmt)
) {

    mysqli_stmt_close($stmt);

    header(
        "Location: ../admin/products.php?success=updated"
    );

    exit;
}


mysqli_stmt_close($stmt);


header(
    "Location: ../admin/edit_product.php?id="
    . $id
    . "&error=database"
);

exit;

?>