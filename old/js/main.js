document.querySelectorAll('.accordion-header').forEach(header => {
    header.addEventListener('click', () => {
        const content = header.nextElementSibling;
        const icon = header.querySelector('.accordion-icon');
        const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';

        // بستن همه دراپ‌داون‌های دیگر (اختیاری - فقط یکی باز باشد)
        document.querySelectorAll('.accordion-content').forEach(c => {
            c.style.maxHeight = '0px';
        });
        document.querySelectorAll('.accordion-icon').forEach(i => {
            i.textContent = '+';
        });

        // باز کردن فعلی
        if (!isOpen) {
            content.style.maxHeight = content.scrollHeight + 'px';
            icon.textContent = '−';
        }
    });
});
