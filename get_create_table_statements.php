<?php
// explore-tables-full.php
header('Content-Type: text/plain'); // Plain text for readability

// Database connection
$mysqli = new mysqli("localhost", "root", "admin12345", "asset_management");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Get all table names
$result = $mysqli->query("SHOW TABLES");
if (!$result) {
    die("Error fetching tables: " . $mysqli->error);
}

$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0]; // First column is table name
}
$result->free();

// Get CREATE TABLE statements
foreach ($tables as $table) {
    $createResult = $mysqli->query("SHOW CREATE TABLE `$table`");
    if ($createResult) {
        $row = $createResult->fetch_assoc();
        echo "Table: $table\n";
        echo $row['Create Table'] . ";\n\n";
        $createResult->free();
    } else {
        echo "Error for $table: " . $mysqli->error . "\n";
    }
}

$mysqli->close();
?>