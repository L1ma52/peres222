<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);
    
    if (!empty($message)) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO contacts (name, email, message) VALUES (?, ?, ?)");
        $stmt->bindValue(1, $name, SQLITE3_TEXT);
        $stmt->bindValue(2, $email, SQLITE3_TEXT);
        $stmt->bindValue(3, $message, SQLITE3_TEXT);
        
        $stmt->execute();
        $db->close();
        
        header('Location: index.php?contact=success');
        exit;
    }
}

header('Location: index.php?contact=error');
?>