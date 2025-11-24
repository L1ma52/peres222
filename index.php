<?php
require_once 'config.php';

// Загрузка данных из БД
$db = getDB();

// Загрузка одобренных отзывов
$reviews = [];
$result = $db->query("SELECT * FROM reviews WHERE status = 'approved' ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $reviews[] = $row;
}

// Загрузка категорий
$categories = [];
$result = $db->query("SELECT * FROM categories ORDER BY created_at DESC");
while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
    $categories[] = $row;
}

// Загрузка галереи
$gallery = [];
$result = $db->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 6");
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
    <title>Конкурс Прожектор - Конкурс дефиле и фото</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" type="image/x-icon" href="logo.ico">
</head>
<body>
    <!-- Хедер -->
    <header class="main-header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <span class="logo-text">КонкурсПрожектор</span>
                </div>
                <nav class="main-nav">
                    <a href="#home">Главная</a>
                    <a href="#registration">Регистрация</a>
                    <a href="#categories">Конкурсы</a>
                    <a href="#contacts">Контакты</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Главный баннер -->
    <section id="home" class="banner">
        <div class="container">
            <div class="banner-content">
                <div class="banner-text">
                    <h1>В центре внимания</h1>
                    <p class="subtitle"><br>Присоединяйтесь к конкурсу дефиле и фото.<br>Покажите свой стиль и талант на подиуме!</p>
                    <button class="btn-primary" onclick="scrollToRegister()">Зарегистрироваться</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Преимущества -->
    <section class="benefits">
        <div class="container">
            <h2>Зачем участвовать в «Прожекторе»?</h2>
            <div class="benefits-grid">
                <div class="benefit-card">
                    <h3>Признание и награды</h3>
                    <p>Получайте заслуженное признание своего таланта и профессиональные награды от жюри.</p>
                </div>
                <div class="benefit-card">
                    <h3>Профессиональная съёмка</h3>
                    <p>Работайте с опытными фотографами и получите качественное портфолио для карьеры.</p>
                </div>
                <div class="benefit-card">
                    <h3>Новые возможности</h3>
                    <p>Откройте двери к новым проектам, контрактам и сотрудничеству в индустрии моды.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Категории конкурса -->
    <section id="categories" class="categories">
        <div class="container">
            <h2>Категории конкурса</h2>
            <div class="categories-grid">
                <?php foreach (array_chunk($categories, 2) as $categoryRow): ?>
                <div class="categories-row">
                    <?php foreach ($categoryRow as $category): ?>
                    <div class="category-card">
                        <img src="<?php echo $category['image_path']; ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                        <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Процесс регистрации -->
    <section class="registration-process">
        <div class="container">
            <h2>Как зарегистрироваться</h2>
            <div class="steps-vertical">
                <div class="step-vertical">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h3>Заполните форму</h3>
                        <p>Укажите ваше ФИО, номер телефона и возраст для регистрации на конкурс.</p>
                    </div>
                </div>
                <div class="step-vertical">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h3>Выберите конкурсы</h3>
                        <p>Отметьте галочками конкурсы для участия.</p>
                    </div>
                </div>
                <div class="step-vertical">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h3>Загрузите материалы</h3>
                        <p>Добавьте ваши фото и музыкальные файлы.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Форма регистрации -->
    <section id="registration" class="registration-form">
        <div class="container">
            <h2>Регистрация на конкурс</h2>
            <form action="register.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="fullname">ФИО *</label>
                    <input type="text" id="fullname" name="fullname" required>
                </div>
                <div class="form-group">
                    <label for="phone">Номер телефона *</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
                <div class="form-group">
                    <label for="age">Возраст *</label>
                    <input type="number" id="age" name="age" min="16" max="60" required>
                </div>
                <div class="form-group">
                    <label>Выберите конкурсы для участия:</label>
                    <div class="checkbox-group">
                        <label><input type="checkbox" name="competitions[]" value="defile"> Дефиле</label>
                        <label><input type="checkbox" name="competitions[]" value="photo"> Фотоконкурс</label>
                        <label><input type="checkbox" name="competitions[]" value="art"> Фотоискусство</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="photo">Загрузите фото *</label>
                    <input type="file" id="photo" name="photo" accept="image/*" required class="file-input">
                </div>
                <div class="form-group">
                    <label for="music">Загрузите музыку</label>
                    <input type="file" id="music" name="music" accept="audio/*" class="file-input">
                </div>
                <button type="submit" class="btn-primary btn-register">Зарегистрироваться</button>
            </form>
        </div>
    </section>

    <!-- Галерея звезд -->
    <section class="stars-gallery">
        <div class="container">
            <h2>Галерея звезд</h2>
            <div class="gallery-grid">
                <?php if (empty($gallery)): ?>
                    <div class="no-gallery">
                        <p>Скоро здесь появятся фотографии наших звезд!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($gallery as $image): ?>
                    <div class="gallery-item">
                        <img src="<?php echo $image['image_path']; ?>" alt="Звезда конкурса">
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Отзывы -->
    <section class="reviews">
        <div class="container">
            <h2>Отзывы участников</h2>
            <div class="reviews-grid">
                <?php if (empty($reviews)): ?>
                    <div class="no-reviews">
                        <p>Пока нет отзывов. Будьте первым!</p>
                    </div>
                <?php else: ?>
                    <?php 
                    $colors = ['mint', 'pink', 'gray'];
                    $color_index = 0;
                    foreach ($reviews as $review): 
                    ?>
                    <div class="review-card <?php echo $colors[$color_index % 3]; ?> fade-in-up">
                        <p class="review-text">"<?php echo htmlspecialchars($review['review']); ?>"</p>
                        <div class="review-author"><?php echo htmlspecialchars($review['name']); ?></div>
                        <?php if (!empty($review['role'])): ?>
                        <div class="review-role"><?php echo htmlspecialchars($review['role']); ?></div>
                        <?php endif; ?>
                    </div>
                    <?php 
                    $color_index++;
                    endforeach; 
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Форма отзыва -->
    <section class="add-review">
        <div class="container">
            <h2>Оставьте свой отзыв</h2>
            <form action="submit_review.php" method="POST">
                <div class="form-group">
                    <label for="review_name">Ваше имя *</label>
                    <input type="text" id="review_name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="review_text">Ваш отзыв *</label>
                    <textarea id="review_text" name="review" rows="5" required placeholder="Расскажите о своих впечатлениях..."></textarea>
                </div>
                <button type="submit" class="btn-primary btn-fullwidth">Отправить отзыв</button>
            </form>
        </div>
    </section>

    <!-- Контакты -->
    <section id="contacts" class="contacts">
        <div class="container">
            <h2>Свяжитесь с нами</h2>
            <div class="contact-content">
                <form action="contact.php" method="POST" class="contact-form">
                    <div class="form-group">
                        <label for="contact_name">Имя</label>
                        <input type="text" id="contact_name" name="name">
                    </div>
                    <div class="form-group">
                        <label for="contact_email">Email</label>
                        <input type="email" id="contact_email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="contact_message">Сообщение</label>
                        <textarea id="contact_message" name="message" rows="5"></textarea>
                    </div>
                    <button type="submit" class="btn-primary">Отправить сообщение</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Футер -->
    <footer>
        <div class="container">
            <div class="social-links">
                <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
            </div>
            <nav class="footer-nav">
                <a href="#home">Главная</a>
                <a href="#registration">Регистрация</a>
                <a href="#categories">Конкурсы</a>
                <a href="#contacts">Контакты</a>
            </nav>
            <div class="footer-info">
                <p>&copy; 2025 Конкурс Прожектор. Все права защищены. Москва, ул. Примерная, д. 10</p>
                <div class="admin-link">
                    <a href="login.php">Админ-панель</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>
</html>