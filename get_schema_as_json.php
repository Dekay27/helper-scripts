<?php
// explore-tables-simple.php
header('Content-Type: application/json');

// Database connection
$mysqli = new mysqli("localhost", "root", "admin12345", "asset_management");

if ($mysqli->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $mysqli->connect_error]);
    exit;
}

// Verify database (replace 'student_db' with your actual DB name if different)
$dbName = $mysqli->real_escape_string("asset_management"); // Adjust if needed

// Query INFORMATION_SCHEMA
$query = "
    SELECT TABLE_NAME, COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_KEY
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = '$dbName'
    ORDER BY TABLE_NAME, ORDINAL_POSITION
";

$result = $mysqli->query($query);
if (!$result) {
    http_response_code(500);
    echo json_encode(["error" => "Query failed: " . $mysqli->error]);
    exit;
}

// Check if rows exist
if ($result->num_rows === 0) {
    echo json_encode(["message" => "No tables found in database '$dbName'"]);
    exit;
}

// Build schema array
$schema = [];
while ($row = $result->fetch_assoc()) {
    $table = $row['TABLE_NAME'];
    if (!isset($schema[$table])) {
        $schema[$table] = [];
    }
    $schema[$table][] = [
        'column' => $row['COLUMN_NAME'],
        'type' => $row['DATA_TYPE'],
        'nullable' => $row['IS_NULLABLE'],
        'key' => $row['COLUMN_KEY']
    ];
}

// Output JSON
echo json_encode($schema, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

$result->free();
$mysqli->close();