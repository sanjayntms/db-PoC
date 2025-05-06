<?php
// db.php (Database Connection)

function getConnection() {
    $serverName = "ntmssql11.database.windows.net"; // Replace with your SQL Server instance name or address
    $databaseName = "db1"; // Replace with your database name
    $username = "sqladmin"; // Replace with your SQL Server username
    $password = "123#ntms123#"; // Replace with your SQL Server password
    $port = 1433;

    $connectionInfo = array(
        "Database" => $databaseName,
        "Uid" => $username,
        "PWD" => $password
    );
     if ($port) {
        $connectionInfo["Port"] = $port;
    }


    $conn = sqlsrv_connect($serverName, $connectionInfo);
    if ($conn === false) {
        $errors = sqlsrv_errors();
        foreach ($errors as $error) {
            error_log("SQLSTATE: " . $error['SQLSTATE'] . "\n");
            error_log("Code: " . $error['code'] . "\n");
            error_log("Message: " . $error['message'] . "\n", 0);
        }
        return false; // Return false, and handle the error in the caller.  Do NOT die here.
    }
    return $conn;
}
?>
```

**To get this code working:**

1.  **Replace placeholders:** In `db.php`, `api.php` and `product.php`, update the `$serverName`, `$databaseName`, `$username`, and `$password` variables with your actual SQL Server credentials.
2.  **Create the files:** Create the `index.html`, `api.php`, `product.php`, and `db.php` files on your server.  Make sure  `index.html` is in a publicly accessible directory (like  `public_html` or a folder named  `public`) and the PHP files are outside of the web root for security.
3.  **Deploy the files:** Upload the files to your web server.
4.  **Test:** Access  `index.html` in your web browser.  You should be able to view the products and add new on
