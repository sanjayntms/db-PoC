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

// Seed test data if requested
if (isset($_POST['seed'])) {
    $standardCost = 10;
    $productCategoryID = 1;
    $productModelID = 1;

    echo "<h3>Seeding Test Data...</h3>";

    for ($i = 1; $i <= 100000; $i++) {
        // Add unique suffix using timestamp + random
        $uniqueSuffix = time() . rand(1000, 9999);
        $name = "TestProduct_" . $uniqueSuffix . "_$i";
        $number = "TP_" . $uniqueSuffix . "_$i";
        $price = rand(50, 500);

        $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) 
                VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
        $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
        $stmt = sqlsrv_query($conn, $sql, $params);

        if (!$stmt) {
            echo "<pre>❌ Failed inserting $name:\n" . print_r(sqlsrv_errors(), true) . "</pre>";
            die();
        } else {
            echo "<p>✅ Inserted $name successfully.</p>";
        }
    }
    echo "<p>🎉 10 New test products seeded successfully.</p>";
}

// CREATE
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
    if (!$stmt) {
        die("<pre>❌ Create failed:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
    } else {
        echo "<p>✅ Product created successfully.</p>";
    }
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price = $_POST['price'];
    $sql = "UPDATE SalesLT.Product SET ListPrice = ? WHERE ProductID = ?";
    $params = [$price, $id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die("<pre>❌ Update failed:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
    } else {
        echo "<p>✅ Product updated successfully.</p>";
    }
}

// DELETE
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM SalesLT.Product WHERE ProductID = ?";
    $params = [$id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die("<pre>❌ Delete failed:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
    } else {
        echo "<p>✅ Product deleted successfully.</p>";
    }
}

// READ
$sql = "SELECT TOP 10 ProductID, Name, ProductNumber, ListPrice FROM SalesLT.Product ORDER BY ProductID DESC";
$result = sqlsrv_query($conn, $sql);
if (!$result) {
    die("<pre>❌ Read failed:\n" . print_r(sqlsrv_errors(), true) . "</pre>");
}
?>

<h2>Seed Test Data</h2>
<form method="post">
    <button type="submit" name="seed" value="1">Seed 100000 New Test Products</button>
</form>

<h2>Create Product</h2>
<form method="post">
    Name: <input type="text" name="name" required>
    Number: <input type="text" name="number" required>
    Price: <input type="number" name="price" required>
    <button type="submit" name="create" value="1">Create</button>
</form>

<h2>Update Product Price</h2>
<form method="post">
    Product ID: <input type="number" name="id" required>
    New Price: <input type="number" name="price" required>
    <button type="submit" name="update" value="1">Update</button>
</form>

<h2>Delete Product</h2>
<form method="post">
    Product ID: <input type="number" name="id" required>
    <button type="submit" name="delete" value="1">Delete</button>
</form>

<h2>Latest 10 Products</h2>
<table border="1" cellpadding="5" cellspacing="0">
    <tr><th>ID</th><th>Name</th><th>Number</th><th>Price</th></tr>
    <?php while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) { ?>
        <tr>
            <td><?= htmlspecialchars($row['ProductID']) ?></td>
            <td><?= htmlspecialchars($row['Name']) ?></td>
            <td><?= htmlspecialchars($row['ProductNumber']) ?></td>
            <td><?= htmlspecialchars($row['ListPrice']) ?></td>
        </tr>
    <?php } ?>
</table>
