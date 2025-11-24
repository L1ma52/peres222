<?php
// Конфигурация базы данных SQLite
define('DB_PATH', __DIR__ . '/database.db');

/**
 * Инициализация базы данных и таблиц
 */
function initDatabase() {
    if (!file_exists(DB_PATH)) {
        $db = new SQLite3(DB_PATH);
        
        // Таблица заявок
        $db->exec("CREATE TABLE IF NOT EXISTS registrations (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            fullname TEXT NOT NULL,
            phone TEXT NOT NULL,
            age INTEGER NOT NULL,
            competitions TEXT NOT NULL,
            photo_path TEXT NOT NULL,
            music_path TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Таблица отзывов с модерацией
        $db->exec("CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            review TEXT NOT NULL,
            role TEXT DEFAULT '',
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Таблица контактов
        $db->exec("CREATE TABLE IF NOT EXISTS contacts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT,
            email TEXT,
            message TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Администраторы
        $db->exec("CREATE TABLE IF NOT EXISTS admins (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL
        )");
        
        // Категории конкурса
        $db->exec("CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            image_path TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Галерея звезд
        $db->exec("CREATE TABLE IF NOT EXISTS gallery (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            image_path TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Тестовый администратор
        $password_hash = password_hash('admin123', PASSWORD_DEFAULT);
        $db->exec("INSERT OR IGNORE INTO admins (username, password) VALUES ('admin', '$password_hash')");
        
        // Тестовые категории
        $db->exec("INSERT OR IGNORE INTO categories (name, image_path) VALUES 
            ('Лучший дефиле', 'images/category1.jpg'),
            ('Фотоконкурс', 'images/category2.jpg'),
            ('Фотоискусство', 'images/category3.jpg'),
            ('Творческий подход', 'images/category4.jpg')");
        
        $db->close();
    }
}

// Инициализация БД
initDatabase();

/**
 * Получение подключения к БД
 */
function getDB() {
    return new SQLite3(DB_PATH);
}
?>