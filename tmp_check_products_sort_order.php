<?php
try {
    $db = new PDO('mysql:host=127.0.0.1;port=3308;dbname=gut_reset', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $stmt = $db->query("SHOW COLUMNS FROM products LIKE 'sort_order'");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $row ? "exists" : "missing";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage();
}
