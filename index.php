<?php
// Read from Azure App Service environment variables
$serverName = getenv('DB_SERVER');
$database = getenv('DB_NAME');
$username = getenv('DB_USER');
$password = getenv('DB_PASSWORD');

$connectionOptions = [
    "Database" => $database,
    "Uid" => $username,
    "PWD" => $password,
    "Encrypt" => 1,
    "TrustServerCertificate" => 0
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if (!$conn) {
    die("<p style='color:red;'>Database connection failed: " . print_r(sqlsrv_errors(), true) . "</p>");
}

// Get total number of records
$sqlCount = "SELECT COUNT(*) AS total_records FROM SalesLT.Product";
$resultCount = sqlsrv_query($conn, $sqlCount);
if (!$resultCount) {
    die("<p style='color:red;'>Failed to fetch record count: " . print_r(sqlsrv_errors(), true) . "</p>");
}
$countRow = sqlsrv_fetch_array($resultCount, SQLSRV_FETCH_ASSOC);
$totalRecords = $countRow['total_records'];

// Seed test data if requested
if (isset($_POST['seed'])) {
    $numRecords = $_POST['numRecords'] ?? 10; // Default to 10 if not provided
    $standardCost = 10;
    $productCategoryID = 1;
    $productModelID = 1;

    $startTime = microtime(true);

    for ($i = 1; $i <= $numRecords; $i++) {
        $uniqueSuffix = uniqid();
        $name = "TestProduct$uniqueSuffix";
        $number = "TP$uniqueSuffix";
        $price = rand(50, 500);

        $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
        $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if (!$stmt) {
            die("<p style='color:red;'>Failed to insert seed data: " . print_r(sqlsrv_errors(), true) . "</p>");
        }
    }

    $endTime = microtime(true);
    $executionTime = $endTime - $startTime;

    echo "<p style='color:green;'>$numRecords new test products seeded successfully. Insertion took $executionTime seconds.</p>";
}

// CREATE
if (isset($_POST['create'])) {
    $name = $_POST['name'];
    $number = $_POST['number'];
    $price = $_POST['price'];
    $standardCost = 0;
    $productCategoryID = 1;
    $productModelID = 1;

    echo "<p style='color:blue;'>Creating product...</p>";

    $sql = "INSERT INTO SalesLT.Product (Name, ProductNumber, StandardCost, ListPrice, ProductCategoryID, ProductModelID, SellStartDate) VALUES (?, ?, ?, ?, ?, ?, GETDATE())";
    $params = [$name, $number, $standardCost, $price, $productCategoryID, $productModelID];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die("<p style='color:red;'>Failed to create product: " . print_r(sqlsrv_errors(), true) . "</p>");
    }
    echo "<p style='color:green;'>Product created successfully.</p>";
}

// UPDATE
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $price = $_POST['price'];
    $sql = "UPDATE SalesLT.Product SET ListPrice = ? WHERE ProductID = ?";
    $params = [$price, $id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die("<p style='color:red;'>Failed to update product: " . print_r(sqlsrv_errors(), true) . "</p>");
    }
    echo "<p style='color:green;'>Product price updated.</p>";
}

// DELETE
if (isset($_POST['delete'])) {
    $id = $_POST['id'];
    $sql = "DELETE FROM SalesLT.Product WHERE ProductID = ?";
    $params = [$id];
    $stmt = sqlsrv_query($conn, $sql, $params);
    if (!$stmt) {
        die("<p style='color:red;'>Failed to delete product: " . print_r(sqlsrv_errors(), true) . "</p>");
    }
    echo "<p style='color:green;'>Product deleted.</p>";
}

// READ latest 10 products
$sql = "SELECT TOP 10 ProductID, Name, ProductNumber, ListPrice FROM SalesLT.Product ORDER BY ProductID DESC";
$result = sqlsrv_query($conn, $sql);
if (!$result) {
    die("<p style='color:red;'>Failed to fetch products: " . print_r(sqlsrv_errors(), true) . "</p>");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>🌟 😊 NTMS database PoC Product Management</title>
    <style>
        body {
            background: linear-gradient(to right, #74ebd5, #ACB6E5);
            font-family: Arial, sans-serif;
            margin: 40px;
            color: #333;
        }
        h2 {
            color: #444;
        }
        form {
            background: #fff;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        input, button {
            padding: 8px;
            margin: 5px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            background: #4CAF50;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
        }
        th {
            background: #6a11cb;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        #loadingMessage {
            display: none;
            color: blue;
            font-weight: bold;
            margin-left: 10px;
        }
    </style>
    <script>
        function showLoadingMessage() {
            document.getElementById("loadingMessage").style.display = "inline";
        }
    </script>
</head>
<body>

<h1>🌟 NTMS database PoC Product Management Portal</h1>

<h2>Seed Test Data (Total Records: <?= $totalRecords ?>)</h2>
<form method="post" onsubmit="showLoadingMessage();">
    Number of Records: <input type="number" name="numRecords" min="1" required>
    <button type="submit" name="seed">Seed Records</button>
    <span id="loadingMessage">Creating...</span>
</form>

<h2>Create Product</h2>
<form method="post">
    Name: <input type="text" name="name" required>
    Number: <input type="text" name="number" required>
    Price: <input type="number" step="0.01" name="price" required>
    <button type="submit" name="create">Create</button>
</form>

<h2>Update Product Price</h2>
<form method="post">
    Product ID: <input type="number" name="id" required>
    New Price: <input type="number" step="0.01" name="price" required>
    <button type="submit" name="update">Update</button>
</form>

<h2>Delete Product</h2>
<form method="post">
    Product ID: <input type="number" name="id" required>
    <button type="submit" name="delete">Delete</button>
</form>

<h2>Latest 10 Products</h2>
<table>
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

</body>
</html>
