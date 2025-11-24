/**
 * Прокрутка к форме регистрации
 */
function scrollToRegister() {
    document.getElementById('registration').scrollIntoView({ 
        behavior: 'smooth' 
    });
}

/**
 * Плавная прокрутка для навигации
 */
document.addEventListener('DOMContentLoaded', function() {
    // Плавная прокрутка для якорных ссылок
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    /**
     * Анимация появления элементов при скролле
     */
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Наблюдаем за элементами с анимацией
    const animatedElements = document.querySelectorAll('.benefit-card, .category-card, .step-vertical, .review-card, .gallery-item');
    
    animatedElements.forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
    });

    /**
     * Валидация формы регистрации
     */
    const registrationForm = document.querySelector('.registration-form form');
    if (registrationForm) {
        registrationForm.addEventListener('submit', function(e) {
            const age = parseInt(document.getElementById('age').value);
            const competitions = document.querySelectorAll('input[name="competitions[]"]:checked');
            
            if (age < 16 || age > 60) {
                e.preventDefault();
                alert('Возраст должен быть от 16 до 60 лет');
                return;
            }
            
            if (competitions.length === 0) {
                e.preventDefault();
                alert('Пожалуйста, выберите хотя бы один конкурс');
                return;
            }
        });
    }

    /**
     * Фикс для мобильного меню
     */
    function handleMobileMenu() {
        const header = document.querySelector('.main-header');
        if (window.innerWidth <= 768) {
            header.classList.add('mobile-header');
        } else {
            header.classList.remove('mobile-header');
        }
    }

    // Инициализация при загрузке и изменении размера
    handleMobileMenu();
    window.addEventListener('resize', handleMobileMenu);
});