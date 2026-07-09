<?php
// exit("Don't Run Whitout Permission !!");
// Database connection parameters
$servername = "localhost";
$username = "root";
$password = '';
$dbname = "crm";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get all table names in the database
$tablesResult = $conn->query("SHOW TABLES");
$tables = [];

if ($tablesResult->num_rows > 0) {
    while ($row = $tablesResult->fetch_row()) {
        $tables[] = $row[0];
    }
} else {
    die("No tables found in the database.");
}

// Truncate columns query for each table
foreach ($tables as $tableName) {
    $sql = "UPDATE $tableName SET modified_by = NULL, modified_by_type = NULL, modified_date = NULL";

    if ($conn->query($sql) === TRUE) {
        echo "Columns truncated successfully for table: $tableName<br>";
    } else {
        echo "Error truncating columns for table $tableName: " . $conn->error . "<br>";
    }
}

// Close connection
$conn->close();
