<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3308;dbname=gut_reset', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "TABLES:\n";
    foreach ($db->query('SHOW TABLES') as $row) {
        echo $row[0] . "\n";
    }
    echo "\nPRODUCTS COLUMNS:\n";
    foreach ($db->query('SHOW COLUMNS FROM products') as $row) {
        echo $row['Field'] . ' ' . $row['Type'] . "\n";
    }
    echo "\nMIGRATIONS TABLE EXISTS?\n";
    $tables = $db->query("SHOW TABLES LIKE 'migrations'")->fetchAll(PDO::FETCH_NUM);
    if ($tables) {
        echo "yes\n";
        echo "\nMIGRATIONS TABLE CONTENT:\n";
        foreach ($db->query('SELECT migration, batch FROM migrations ORDER BY id') as $row) {
            echo $row['migration'] . ' | ' . $row['batch'] . "\n";
        }
    } else {
        echo "no\n";
    }
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
}
