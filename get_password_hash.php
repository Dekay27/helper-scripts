<?php
// hash-password.php

// Handle both web and CLI usage
if (PHP_SAPI === 'cli') {
    // Command-line mode
    if ($argc < 2) {
        echo "Usage: php hash-password.php <value>\n";
        exit(1);
    }
    $input = $argv[1];
} else {
    // Web mode
    header('Content-Type: text/html');
    $input = $_POST['value'] ?? '';
}

// Function to generate and display hash
function generateHash(string $value): void {
    // Use PASSWORD_DEFAULT (bcrypt) with default cost (10)
    $hash = password_hash($value, PASSWORD_DEFAULT);

    if ($hash === false) {
        echo "Error: Failed to generate hash.<br/>";
    } else {
        echo "Input: " . htmlspecialchars($value) . "<br/>";
        echo "Hash: " . $hash . "<br/>";
    }
}

// Simple HTML form for web usage
if (PHP_SAPI !== 'cli') {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Password Hash Generator</title>
    </head>
    <body>
    <h1>Generate Password Hash</h1>
    <form method="post" action="">
        <label for="value">Enter value to hash:</label>
        <input type="text" id="value" name="value" value="<?php echo htmlspecialchars($input); ?>">
        <button type="submit">Hash It</button>
    </form>
    </body>
    </html>
    <?php
}


// Process input
if (!empty($input)) {
    echo "<br/>";
    generateHash($input);
}

?>