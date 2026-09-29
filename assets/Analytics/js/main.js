window.showSitePage = function (page) {
  document.querySelectorAll('.site-page').forEach((el) => el.classList.remove('active'));
  const target = document.getElementById('page-' + page);
  if (target) target.classList.add('active');

  document.querySelectorAll('.nav-link-site').forEach((a) => {
    a.classList.toggle('active', a.getAttribute('data-page') === page);
  });

  window.scrollTo({ top: 0, behavior: 'smooth' });
  closeMobileMenu();
};

window.toggleMobileMenu = function () {
  const menu = document.getElementById('mobileMenu');
  const icon = document.getElementById('mobileMenuIcon');
  if (!menu) return;
  menu.classList.toggle('hidden');
  if (icon) {
    icon.classList.toggle('fa-bars');
    icon.classList.toggle('fa-times');
  }
};

window.closeMobileMenu = function () {
  document.getElementById('mobileMenu')?.classList.add('hidden');
  const icon = document.getElementById('mobileMenuIcon');
  if (icon) {
    icon.classList.add('fa-bars');
    icon.classList.remove('fa-times');
  }
};

window.toggleAccordion = function (btn) {
  const body = btn.nextElementSibling;
  const icon = btn.querySelector('.accordion-icon');
  const isOpen = body.classList.contains('open');

  // بستن بقیه (اختیاری — مثل آکاردئون تک‌باز)
  document.querySelectorAll('.accordion-body.open').forEach((b) => {
    if (b !== body) {
      b.classList.remove('open');
      const i = b.previousElementSibling?.querySelector('.accordion-icon');
      if (i) {
        i.classList.remove('open', 'fa-minus');
        i.classList.add('fa-plus');
      }
    }
  });

  body.classList.toggle('open', !isOpen);
  if (icon) {
    icon.classList.toggle('open', !isOpen);
    icon.classList.toggle('fa-plus', isOpen);
    icon.classList.toggle('fa-minus', !isOpen);
  }
};

window.submitPublicContact = async function (e) {
  e.preventDefault();
  const form = document.getElementById('contactPublicForm');
  if (!form || form.dataset.sending === '1') return;
  const english = document.documentElement.lang === 'en';
  const notify = (kind, text) =>
    typeof showAuthToast === 'function' ? showAuthToast(kind, text) : alert(text);
  const button = form.querySelector('[type="submit"]');
  const body = new URLSearchParams({ _token: window.siteCsrfToken || window.adminCsrfToken || '' });
  for (const [field, id] of Object.entries({
    name: 'cName',
    email: 'cEmail',
    subject: 'cSubject',
    message: 'cMessage',
  }))
    body.set(field, document.getElementById(id)?.value.trim() || '');
  form.dataset.sending = '1';
  if (button) button.disabled = true;
  try {
    const response = await fetch('/contact', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { Accept: 'application/json' },
      body,
    });
    const payload = await response.json(),
      result = payload.data || payload;
    if (!response.ok || !result.success)
      throw new Error(
        result.message || (english ? 'Message could not be saved.' : 'ثبت پیام انجام نشد.')
      );
    form.reset();
    notify('success', english ? 'Your message has been received.' : 'پیام شما ثبت شد.');
  } catch (error) {
    notify('error', error.message || (english ? 'Please try again.' : 'دوباره تلاش کنید.'));
  } finally {
    delete form.dataset.sending;
    if (button) button.disabled = false;
  }
};

// شروع از خانه
document.addEventListener('DOMContentLoaded', () => showSitePage('home'));

// این تابع را در main.js یا یک فایل مشترک بگذار
window.closeModal = function () {
  const container = document.getElementById('modalContainer');
  if (container) {
    container.innerHTML = '';
  } else {
    console.warn('modalContainer پیدا نشد');
  }
};
