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
    die(print_r(sqlsrv_errors(), true));
}

// Seed test data if requested
if (isset($_POST['seed'])) {
    $standardCost = 10;
    $productCategoryID = 1;
    $productModelID = 1;

    for ($i = 1; $i <= 1000; $i++) {
        $name = "TestProduct$i";
        $number = "TP$i";
        $price = rand(50, 500);

        $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
        $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if (!$stmt) {
            die(print_r(sqlsrv_errors(), true));
        }
    }
    echo "<p>Test data seeded successfully.</p>";
}

// CREATE
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $price = $_POST['price'];
    $standardCost = 0;
    $productCategoryID = 1;
    $productModelID = 1;

    $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
    $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
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
        die(print_r(sqlsrv_errors(), true));
    }
}

// DELETE
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM SalesLT.Product WHERE ProductID = ?";
    $params = [$id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die(print_r(sqlsrv_errors(), true));
    }
}

// READ
$sql = "SELECT ProductID, Name, ProductNumber, ListPrice FROM SalesLT.Product";
$result = sqlsrv_query($conn, $sql);
if (!$result) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<h2>Seed Test Data</h2>
<form method="post">
    <button type="submit" name="seed">Seed 10 Test Products</button>
</form>

<h2>Create Product</h2>
<form method="post">
    Name: <input type="text" name="name" required>
    Number: <input type="text" name="number" required>
    Price: <input type="text" name="price" required>
    <button type="submit" name="create">Create</button>
</form>

<h2>Update Product Price</h2>
<form method="post">
    Product ID: <input type="text" name="id" required>
    New Price: <input type="text" name="price" required>
    <button type="submit" name="update">Update</button>
</form>

<h2>Delete Product</h2>
<form method="post">
    Product ID: <input type="text" name="id" required>
    <button type="submit" name="delete">Delete</button>
</form>

<h2>Product List</h2>
<table border="1">
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
