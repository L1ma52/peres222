<?php
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $age = intval($_POST['age']);
    $competitions = isset($_POST['competitions']) ? implode(', ', $_POST['competitions']) : '';
    
    // Создаем папки для загрузок если их нет
    if (!file_exists('uploads/photos')) {
        mkdir('uploads/photos', 0777, true);
    }
    if (!file_exists('uploads/music')) {
        mkdir('uploads/music', 0777, true);
    }
    
    // Обработка загрузки фото
    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $photo_ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_filename = uniqid() . '.' . $photo_ext;
        $photo_path = 'uploads/photos/' . $photo_filename;
        move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path);
    }
    
    // Обработка загрузки музыки
    $music_path = '';
    if (isset($_FILES['music']) && $_FILES['music']['error'] === UPLOAD_ERR_OK) {
        $music_ext = pathinfo($_FILES['music']['name'], PATHINFO_EXTENSION);
        $music_filename = uniqid() . '.' . $music_ext;
        $music_path = 'uploads/music/' . $music_filename;
        move_uploaded_file($_FILES['music']['tmp_name'], $music_path);
    }
    
    // Сохранение в базу данных
    $db = getDB();
    $stmt = $db->prepare("INSERT INTO registrations (fullname, phone, age, competitions, photo_path, music_path) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bindValue(1, $fullname, SQLITE3_TEXT);
    $stmt->bindValue(2, $phone, SQLITE3_TEXT);
    $stmt->bindValue(3, $age, SQLITE3_INTEGER);
    $stmt->bindValue(4, $competitions, SQLITE3_TEXT);
    $stmt->bindValue(5, $photo_path, SQLITE3_TEXT);
    $stmt->bindValue(6, $music_path, SQLITE3_TEXT);
    
    if ($stmt->execute()) {
        header('Location: index.php');
        exit;
    }
    
    $db->close();
}

header('Location: index.php');
exit;
?>