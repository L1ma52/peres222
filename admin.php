<?php
session_start();
require_once 'config.php';

// Проверка авторизации
if (!isset($_SESSION['admin_logged_in']) || !$_SESSION['admin_logged_in']) {
    header('Location: login.php');
    exit;
}

// Обработка действий
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    
    if (isset($_POST['approve_review'])) {
        $review_id = intval($_POST['review_id']);
        $role = trim($_POST['role']);
        $stmt = $db->prepare("UPDATE reviews SET status = 'approved', role = ? WHERE id = ?");
        $stmt->bindValue(1, $role, SQLITE3_TEXT);
        $stmt->bindValue(2, $review_id, SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['reject_review'])) {
        $review_id = intval($_POST['review_id']);
        $stmt = $db->prepare("UPDATE reviews SET status = 'rejected' WHERE id = ?");
        $stmt->bindValue(1, $review_id, SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['add_category'])) {
        $name = trim($_POST['category_name']);
        if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/categories/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $filepath = $upload_dir . $filename;
            move_uploaded_file($_FILES['category_image']['tmp_name'], $filepath);
            
            $stmt = $db->prepare("INSERT INTO categories (name, image_path) VALUES (?, ?)");
            $stmt->bindValue(1, $name, SQLITE3_TEXT);
            $stmt->bindValue(2, $filepath, SQLITE3_TEXT);
            $stmt->execute();
        }
    } elseif (isset($_POST['add_gallery_image'])) {
        if (isset($_FILES['gallery_image']) && $_FILES['gallery_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/gallery/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            $ext = pathinfo($_FILES['gallery_image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $ext;
            $filepath = $upload_dir . $filename;
            move_uploaded_file($_FILES['gallery_image']['tmp_name'], $filepath);
            
            $stmt = $db->prepare("INSERT INTO gallery (image_path) VALUES (?)");
            $stmt->bindValue(1, $filepath, SQLITE3_TEXT);
            $stmt->execute();
        }
    } elseif (isset($_POST['delete_category'])) {
        $category_id = intval($_POST['category_id']);
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        $stmt->bindValue(1, $category_id, SQLITE3_INTEGER);
        $stmt->execute();
    } elseif (isset($_POST['delete_gallery_image'])) {
        $image_id = intval($_POST['image_id']);
        $stmt = $db->prepare("DELETE FROM gallery WHERE id = ?");
        $stmt->bindValue(1, $image_id, SQLITE3_INTEGER);
        $stmt->execute();
    }
    
    $db->close();
    header('Location: admin.php');
    exit;
}

// Получение данных из базы
$db = getDB();

// Получение заявок
$registrations = [];
$result = $db->query("SELECT * FROM registrations ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $registrations[] = $row;
}

// Получение отзывов для модерации
$pending_reviews = [];
$result = $db->query("SELECT * FROM reviews WHERE status = 'pending' ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $pending_reviews[] = $row;
}

// Получение одобренных отзывов
$approved_reviews = [];
$result = $db->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $approved_reviews[] = $row;
}

// Получение контактов
$contacts = [];
$result = $db->query("SELECT * FROM contacts ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $contacts[] = $row;
}

// Получение категорий
$categories = [];
$result = $db->query("SELECT * FROM categories ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

// Получение галереи
$gallery = [];
$result = $db->query("SELECT * FROM gallery ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $gallery[] = $row;
}

$db->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель - Конкурс Прожектор</title>
    <link rel="stylesheet" href="css/admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="logo.ico">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>Админ-панель конкурса "Прожектор"</h1>
            <div class="admin-user">
                <span>Добро пожаловать, <?php echo $_SESSION['admin_username']; ?>!</span>
                <a href="logout.php" class="logout-btn">🚪 Выйти</a>
            </div>
        </div>

        <div class="admin-grid">
            <!-- Статистика -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3>Заявки</h3>
                    <p class="stat-number"><?php echo count($registrations); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Отзывы на модерации</h3>
                    <p class="stat-number"><?php echo count($pending_reviews); ?></p>
                </div>
                <div class="stat-card">
                    <h3>Контакты</h3>
                    <p class="stat-number"><?php echo count($contacts); ?></p>
                </div>
            </div>

            <!-- Модерация отзывов -->
            <div class="admin-section">
                <h2>Модерация отзывов (<?php echo count($pending_reviews); ?> на проверку)</h2>
                <?php if (empty($pending_reviews)): ?>
                    <p class="no-data">Нет отзывов для модерации</p>
                <?php else: ?>
                    <div class="reviews-moderation">
                        <?php foreach ($pending_reviews as $review): ?>
                        <div class="moderation-item">
                            <div class="review-content">
                                <h4><?php echo htmlspecialchars($review['name']); ?></h4>
                                <p>"<?php echo htmlspecialchars($review['review']); ?>"</p>
                                <small><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></small>
                            </div>
                            <div class="moderation-actions">
                                <form method="POST" class="moderation-form">
                                    <input type="hidden" name="review_id" value="<?php echo $review['id']; ?>">
                                    <select name="role" required class="role-select">
                                        <option value="">Выберите роль</option>
                                        <option value="Участник конкурса">Участник конкурса</option>
                                        <option value="Директор компании">Директор компании</option>
                                        <option value="Фотограф">Фотограф</option>
                                        <option value="Член жюри">Член жюри</option>
                                        <option value="Зритель">Зритель</option>
                                    </select>
                                    <div class="action-buttons">
                                        <button type="submit" name="approve_review" class="btn-approve">Одобрить</button>
                                        <button type="submit" name="reject_review" class="btn-reject">Отклонить</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Управление категориями -->
            <div class="admin-section">
                <h2>Управление категориями</h2>
                <form method="POST" enctype="multipart/form-data" class="add-form">
                    <div class="form-row">
                        <input type="text" name="category_name" placeholder="Название категории" required>
                        <input type="file" name="category_image" accept="image/*" required>
                        <button type="submit" name="add_category" class="btn-add">Добавить категорию</button>
                    </div>
                </form>
                
                <div class="categories-grid">
                    <?php foreach ($categories as $category): ?>
                    <div class="category-admin-item">
                        <img src="<?php echo $category['image_path']; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                        <h4><?php echo htmlspecialchars($category['name']); ?></h4>
                        <form method="POST" class="delete-form">
                            <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                            <button type="submit" name="delete_category" class="btn-delete">Удалить</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Управление галереей -->
            <div class="admin-section">
                <h2>Управление галереей</h2>
                <form method="POST" enctype="multipart/form-data" class="add-form">
                    <div class="form-row">
                        <input type="file" name="gallery_image" accept="image/*" required>
                        <button type="submit" name="add_gallery_image" class="btn-add">Добавить в галерею</button>
                    </div>
                </form>
                
                <div class="gallery-grid">
                    <?php foreach ($gallery as $image): ?>
                    <div class="gallery-admin-item">
                        <img src="<?php echo $image['image_path']; ?>" alt="Галерея">
                        <form method="POST" class="delete-form">
                            <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                            <button type="submit" name="delete_gallery_image" class="btn-delete">Удалить</button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Одобренные отзывы -->
            <div class="admin-section">
                <h2>Одобренные отзывы (<?php echo count($approved_reviews); ?>)</h2>
                <?php if (empty($approved_reviews)): ?>
                    <p class="no-data">Нет одобренных отзывов</p>
                <?php else: ?>
                    <div class="approved-reviews">
                        <?php foreach ($approved_reviews as $review): ?>
                        <div class="approved-review">
                            <div class="review-header">
                                <strong><?php echo htmlspecialchars($review['name']); ?></strong>
                                <span class="review-role"><?php echo htmlspecialchars($review['role']); ?></span>
                            </div>
                            <p>"<?php echo htmlspecialchars($review['review']); ?>"</p>
                            <small><?php echo date('d.m.Y H:i', strtotime($review['created_at'])); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Заявки на конкурс -->
            <div class="admin-section">
                <h2>Заявки на конкурс (<?php echo count($registrations); ?>)</h2>
                <?php if (empty($registrations)): ?>
                    <p class="no-data">Заявок пока нет</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ФИО</th>
                                    <th>Телефон</th>
                                    <th>Возраст</th>
                                    <th>Конкурсы</th>
                                    <th>Фото</th>
                                    <th>Музыка</th>
                                    <th>Дата</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($registrations as $reg): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reg['fullname']); ?></td>
                                    <td><?php echo htmlspecialchars($reg['phone']); ?></td>
                                    <td><?php echo $reg['age']; ?></td>
                                    <td><?php echo htmlspecialchars($reg['competitions']); ?></td>
                                    <td>
                                        <?php if ($reg['photo_path']): ?>
                                            <a href="<?php echo $reg['photo_path']; ?>" target="_blank" class="file-link">Просмотр</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($reg['music_path']): ?>
                                            <a href="<?php echo $reg['music_path']; ?>" target="_blank" class="file-link">Скачать</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('d.m.Y H:i', strtotime($reg['created_at'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Сообщения из формы контактов -->
            <div class="admin-section">
                <h2>Сообщения от пользователей (<?php echo count($contacts); ?>)</h2>
                <?php if (empty($contacts)): ?>
                    <p class="no-data">Сообщений пока нет</p>
                <?php else: ?>
                    <div class="contacts-list">
                        <?php foreach ($contacts as $contact): ?>
                        <div class="contact-item">
                            <div class="contact-header">
                                <strong><?php echo htmlspecialchars($contact['name']); ?></strong>
                                <span class="contact-email"><?php echo htmlspecialchars($contact['email']); ?></span>
                            </div>
                            <p><?php echo htmlspecialchars($contact['message']); ?></p>
                            <small><?php echo date('d.m.Y H:i', strtotime($contact['created_at'])); ?></small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>