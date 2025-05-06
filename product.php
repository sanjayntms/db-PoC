<?php
// product.php (Model - interacts with the database)

require_once('db.php'); // Include the database connection file

function getProducts() {
    $conn = getConnection();
    if (!$conn) {
        return false; // getConnection() already handles error
    }

    $sql = "SELECT ProductID, Name, ListPrice FROM SalesLT.Product";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        log_error("getProducts Query failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    $products = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $products[] = $row;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $products;
}

function getProductById($productId) {
    $conn = getConnection();
    if (!$conn) {
        return false; // getConnection() already handles error
    }

    $sql = "SELECT ProductID, Name, ListPrice FROM SalesLT.Product WHERE ProductID = ?";
    $params = array($productId);
    $stmt = sqlsrv_prepare($conn, $sql, $params);
    if ($stmt === false) {
        log_error("getProductById prepare failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    if (sqlsrv_execute($stmt) === false) {
        log_error("getProductById execute failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }
    $product = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $product;
}

function createProduct($productData) {
    $conn = getConnection();
    if (!$conn) {
        return false; // getConnection() already handles error
    }

    // Generate new ProductID manually
    $newProductId = generateNewProductId($conn);
    if ($newProductId === false) {
        log_error("Failed to generate new ProductID", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    $sql = "INSERT INTO SalesLT.Product (ProductID, Name, ProductNumber, ListPrice, SellStartDate, StandardCost)
            VALUES (?, ?, ?, ?, ?, ?)";

    // Handle nulls/defaults
    $productNumber = $productData['productNumber'] ?? null;
    $standardCost = $productData['standardCost'] ?? 0;
    $sellStartDate = $productData['sellStartDate'] ?? date('Y-m-d H:i:s');

    $params = array(
        $newProductId,
        $productData['name'],
        $productNumber,
        $productData['listPrice'],
        $sellStartDate,
        $standardCost
    );

    $stmt = sqlsrv_prepare($conn, $sql, $params);
    if ($stmt === false) {
        log_error("createProduct prepare failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    if (sqlsrv_execute($stmt) === false) {
        log_error("createProduct execute failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    $rowsAffected = sqlsrv_rows_affected($stmt);
    if ($rowsAffected === false || $rowsAffected < 1) {
        log_error("createProduct rowsAffected failed or no rows inserted", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $newProductId;
}

// Generate new ProductID (MAX(ProductID) + 1)
function generateNewProductId($conn) {
    $sql = "SELECT MAX(ProductID) AS MaxID FROM SalesLT.Product";
    $stmt = sqlsrv_query($conn, $sql);
    if ($stmt === false) {
        log_error("generateNewProductId query failed", sqlsrv_errors());
        return false;
    }

    $maxId = 0; // fallback if no rows yet
    if (sqlsrv_fetch($stmt) === true) {
        $maxId = sqlsrv_get_field($stmt, 0);
        if ($maxId === null) {
            $maxId = 0; // handle null result
        }
    }

    sqlsrv_free_stmt($stmt);
    return $maxId + 1;
}

function log_error($message, $errors) {
    // Improved error logging
    error_log($message . ":\n" . print_r($errors, true) . "\n", 0);
    // You can enhance this to log into a custom file or database if needed.
}

?>
