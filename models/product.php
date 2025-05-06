<?php
// product.php (Model - interacts with the database)

require_once('db.php'); // Include the database connection file

function getProducts() {
    $conn = getConnection();
    if (!$conn) {
        return false; //getConnection() already handles error
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
        return false; //getConnection() already handles error
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
        return false; //getConnection() already handles error
    }

    $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, ListPrice, SellStartDate, StandardCost)
            VALUES (?, ?, ?, ?, ?)";

    //handle nulls
    $productNumber = $productData['productNumber'] ?? null;
    $standardCost = $productData['standardCost'] ?? 0;
    $sellStartDate = $productData['sellStartDate'] ?? date('Y-m-d H:i:s');


    $params = array(
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
    if ($rowsAffected === false) {
        log_error("createProduct rowsAffected failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }


    $lastId = get_last_identity($conn); //User defined function
    if ($lastId === false){
         log_error("createProduct lastId failed", sqlsrv_errors());
        sqlsrv_close($conn);
        return false;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    return $lastId;
}



//User-defined function to get last inserted ID.
function get_last_identity($conn){
     $sql = "SELECT SCOPE_IDENTITY() AS LastID";  //SCOPE_IDENTITY() is the correct function.
     $stmt = sqlsrv_query($conn, $sql);
     if ($stmt === false) {
          return false;
     }

     if (sqlsrv_fetch($stmt) === true) {
        $lastId = sqlsrv_get_field($stmt, 0);  // 0 is the index of the first field.
     }
     else{
        $lastId = false;
     }

     sqlsrv_free_stmt($stmt);
     return $lastId;
}

function log_error($message, $errors) {
    // Improved error logging
    error_log($message . ":\n" . print_r($errors, true) . "\n", 0);
    // Consider logging to a file or database instead of the error log.
}

?>
