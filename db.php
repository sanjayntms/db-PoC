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


