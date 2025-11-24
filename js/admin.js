/**
 * Навигация и функционал админ-панели
 */
document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Автоматическое скрытие файловых input'ов
     */
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const fileName = this.files[0]?.name || 'Файл не выбран';
            const label = this.previousElementSibling;
            if (label && label.tagName === 'LABEL') {
                label.textContent = fileName;
            }
        });
    });
    
    /**
     * Подтверждение удаления
     */
    const deleteForms = document.querySelectorAll('.delete-form');
    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!confirm('Вы уверены, что хотите удалить этот элемент?')) {
                e.preventDefault();
            }
        });
    });
    
    /**
     * Анимация появления элементов
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
    
    // Наблюдаем за карточками
    const cards = document.querySelectorAll('.stat-card, .review-card, .category-card, .gallery-card, .message-card');
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    /**
     * Адаптивность таблиц
     */
    function handleTableResponsive() {
        const tables = document.querySelectorAll('.admin-table');
        tables.forEach(table => {
            if (window.innerWidth <= 768) {
                table.parentElement.classList.add('table-responsive');
            } else {
                table.parentElement.classList.remove('table-responsive');
            }
        });
    }

    // Инициализация
    handleTableResponsive();
    window.addEventListener('resize', handleTableResponsive);
});