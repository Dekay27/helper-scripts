<?php
// backup-db.php
header('Content-Type: text/plain');

// Database connection
$mysqli = include("config.php");

$result = $mysqli->query("SHOW TABLES");
if (!$result) {
    die("Error listing tables: " . $mysqli->error);
}

$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

$backup = "-- Backup generated on " . date('Y-m-d H:i:s') . "\n\n";
foreach ($tables as $table) {
    $create = $mysqli->query("SHOW CREATE TABLE `$table`")->fetch_assoc()['Create Table'];
    $backup .= "$create;\n\n";

    $rows = $mysqli->query("SELECT * FROM `$table`");
    if ($rows && $rows->num_rows > 0) {
        while ($row = $rows->fetch_assoc()) {
            // Handle NULL values explicitly
            $values = array_map(function ($value) use ($mysqli) {
                return $value === null ? 'NULL' : "'" . $mysqli->real_escape_string($value) . "'";
            }, array_values($row));
            $backup .= "INSERT INTO `$table` VALUES (" . implode(", ", $values) . ");\n";
        }
    }
    $backup .= "\n";
}

// Save to backups/ folder
$backupDir = 'backups';
$backupFile = $backupDir . '/backup-' . date('YmdHis') . '.sql';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true); // Create backups/ if it doesn’t exist
}
file_put_contents($backupFile, $backup);
echo "Backup saved as $backupFile\n";

$result->free();
$mysqli->close();
?>