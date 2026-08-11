window.showSitePage = function(page) {
    document.querySelectorAll('.site-page').forEach(el => el.classList.remove('active'));
    const target = document.getElementById('page-' + page);
    if (target) target.classList.add('active');

    document.querySelectorAll('.nav-link-site').forEach(a => {
        a.classList.toggle('active', a.getAttribute('data-page') === page);
    });

    window.scrollTo({ top: 0, behavior: 'smooth' });
    closeMobileMenu();
};

window.toggleMobileMenu = function() {
    const menu = document.getElementById('mobileMenu');
    const icon = document.getElementById('mobileMenuIcon');
    if (!menu) return;
    menu.classList.toggle('hidden');
    if (icon) {
        icon.classList.toggle('fa-bars');
        icon.classList.toggle('fa-times');
    }
};

window.closeMobileMenu = function() {
    document.getElementById('mobileMenu')?.classList.add('hidden');
    const icon = document.getElementById('mobileMenuIcon');
    if (icon) {
        icon.classList.add('fa-bars');
        icon.classList.remove('fa-times');
    }
};

window.toggleAccordion = function(btn) {
    const body = btn.nextElementSibling;
    const icon = btn.querySelector('.accordion-icon');
    const isOpen = body.classList.contains('open');

    // بستن بقیه (اختیاری — مثل آکاردئون تک‌باز)
    document.querySelectorAll('.accordion-body.open').forEach(b => {
        if (b !== body) {
            b.classList.remove('open');
            const i = b.previousElementSibling?.querySelector('.accordion-icon');
            if (i) { i.classList.remove('open', 'fa-minus'); i.classList.add('fa-plus'); }
        }
    });

    body.classList.toggle('open', !isOpen);
    if (icon) {
        icon.classList.toggle('open', !isOpen);
        icon.classList.toggle('fa-plus', isOpen);
        icon.classList.toggle('fa-minus', !isOpen);
    }
};

window.submitPublicContact = function(e) {
    e.preventDefault();
    const name = document.getElementById('cName')?.value.trim();
    const email = document.getElementById('cEmail')?.value.trim();
    const message = document.getElementById('cMessage')?.value.trim();
    const publicText = (key, fallback) => window.publicTranslations?.[`public.js.${key}`] || fallback;
    if (!name || !email || !message) {
        return typeof showAuthToast === 'function'
            ? showAuthToast('error', publicText('required_fields', 'لطفاً فیلدهای الزامی را پر کنید'))
            : alert(publicText('required_fields', 'لطفاً فیلدهای الزامی را پر کنید'));
    }

    // در نسخه واقعی: ارسال به API
    const successMessage = publicText('contact_success', '✅ پیام شما ارسال شد. در اولین فرصت پاسخ می‌دهیم.');
    if (typeof showAuthToast === 'function') showAuthToast('success', successMessage);
    else alert(successMessage);
    document.getElementById('contactPublicForm')?.reset();
};

// شروع از خانه
document.addEventListener('DOMContentLoaded', () => showSitePage('home'));


// این تابع را در main.js یا یک فایل مشترک بگذار
window.closeModal = function() {
    const container = document.getElementById('modalContainer');
    if (container) {
        container.innerHTML = '';
    } else {
        console.warn('modalContainer پیدا نشد');
    }
};
