<?php
$serverName = "tcp:ntmsdb11.database.windows.net,1433";
$connectionOptions = [
    "Database" => "db",
    "Uid" => "sqladmin",
    "PWD" => "123#ntms123#",
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die("<pre>Connection failed:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}

$message = "";

// Seed
if (isset($_POST['seed'])) {
    $standardCost = 10;
    $productCategoryID = 1;
    $productModelID = 1;

    for ($i = 1; $i <= 10; $i++) {
        $uniqueSuffix = time() . rand(1000, 9999);
        $name = "TestProduct_" . $uniqueSuffix . "_$i";
        $number = "TP_" . $uniqueSuffix . "_$i";
        $price = rand(50, 500);

        $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) 
                VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
        $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if (!$stmt) {
            $message .= "<div class='alert alert-danger'>❌ Failed inserting $name: " . print_r(sqlsrv_errors(), true) . "</div>";
        }
    }
    $message .= "<div class='alert alert-success'>🎉 10 New test products seeded successfully.</div>";
}

// Create
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $price = $_POST['price'];
    $standardCost = 0;
    $productCategoryID = 1;
    $productModelID = 1;

    $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) 
            VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
    $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $message .= "<div class='alert alert-success'>✅ Product created successfully.</div>";
    } else {
        $message .= "<div class='alert alert-danger'>❌ Create failed: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}

// Update
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price = $_POST['price'];
    $sql = "UPDATE SalesLT.Product SET ListPrice = ? WHERE ProductID = ?";
    $params = [$price, $id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $message .= "<div class='alert alert-success'>✅ Product updated successfully.</div>";
    } else {
        $message .= "<div class='alert alert-danger'>❌ Update failed: " . print_r(sqlsrv_errors(), true) . "</div>";
    }
}

// Delete
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM SalesLT.Product WHERE ProductID = ?";
    $params = [$id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if ($stmt) {
        $message .= "<div class='alert alert-success'>✅ Product deleted successfully.</div>";
    } else {
        $
