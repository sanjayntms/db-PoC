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

// CREATE
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $price = $_POST['price'];
    $sql = "INSERT INTO Production.Product (Name, ProductNumber, StandardCost, ListPrice, SellStartDate)
            VALUES (?, ?, 0, ?, GETDATE())";
    $params = [$name, $number, $price];
    sqlsrv_query($conn, $sql, $params);
}

// READ
$sql = "SELECT ProductID, Name, ProductNumber, ListPrice FROM Production.Product";
$result = sqlsrv_query($conn, $sql);

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price = $_POST['price'];
    $sql = "UPDATE Production.Product SET ListPrice = ? WHERE ProductID = ?";
    $params = [$price, $id];
    sqlsrv_query($conn, $sql, $params);
}

// DELETE
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM Production.Product WHERE ProductID = ?";
    $params = [$id];
    sqlsrv_query($conn, $sql, $params);
}
?>

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
            <td><?= $row['ProductID'] ?></td>
            <td><?= $row['Name'] ?></td>
            <td><?= $row['ProductNumber'] ?></td>
            <td><?= $row['ListPrice'] ?></td>
        </tr>
    <?php } ?>
</table>
