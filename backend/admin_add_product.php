<?php

require_once "auth.php";

require_once "config.php";



// =========================================================
// ONLY POST REQUEST
// =========================================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../admin/add_product.php");
    exit;
}


// =========================================================
// GET FORM DATA
// =========================================================

$name = trim($_POST["name"] ?? "");

$category_id = (int) ($_POST["category_id"] ?? 0);

$price = (float) ($_POST["price"] ?? 0);

$stock = (int) ($_POST["stock"] ?? 0);

$description = trim(
    $_POST["description"] ?? ""
);


// =========================================================
// VALIDATION
// =========================================================

if (
    $name === "" ||
    $category_id <= 0 ||
    $price < 0 ||
    $stock < 0
) {
    header(
        "Location: ../admin/add_product.php?error=invalid"
    );

    exit;
}


// =========================================================
// IMAGE UPLOAD
// =========================================================

$image_name = "";


if (
    isset($_FILES["image"]) &&
    $_FILES["image"]["error"] === UPLOAD_ERR_OK
) {

    $image = $_FILES["image"];

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
            "Location: ../admin/add_product.php?error=image"
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


    $image_name =
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
        . $image_name;


    if (
        !move_uploaded_file(
            $image["tmp_name"],
            $upload_path
        )
    ) {

        header(
            "Location: ../admin/add_product.php?error=upload"
        );

        exit;
    }
}


// =========================================================
// INSERT PRODUCT
// =========================================================

$sql = "
    INSERT INTO products
    (
        name,
        category_id,
        price,
        stock,
        description,
        image
    )

    VALUES
    (
        ?,
        ?,
        ?,
        ?,
        ?,
        ?
    )
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


if (!$stmt) {

    header(
        "Location: ../admin/add_product.php?error=database"
    );

    exit;
}


mysqli_stmt_bind_param(
    $stmt,
    "sidiss",
    $name,
    $category_id,
    $price,
    $stock,
    $description,
    $image_name
);


if (
    mysqli_stmt_execute($stmt)
) {

    mysqli_stmt_close($stmt);

    header(
        "Location: ../admin/products.php?success=added"
    );

    exit;

}


mysqli_stmt_close($stmt);


header(
    "Location: ../admin/add_product.php?error=database"
);

exit;

?>