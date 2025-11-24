<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $review = trim($_POST['review']);
    
    if (!empty($name) && !empty($review)) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO reviews (name, review, status) VALUES (?, ?, 'pending')");
        $stmt->bindValue(1, $name, SQLITE3_TEXT);
        $stmt->bindValue(2, $review, SQLITE3_TEXT);
        
        if ($stmt->execute()) {
            header('Location: index.php');
        } else {
            header('Location: index.php');
        }
        
        $db->close();
    } else {
        header('Location: index.php');
    }
    exit;
}
?>