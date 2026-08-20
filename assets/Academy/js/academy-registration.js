let academyRegistrationStep = 1;
let academyRegistrationTimer = null;
const academyText = (key, fallback = '') => window.academyTranslations?.[`academy.${key}`] || fallback;

window.togglePassword = function(inputId, button) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.type = input.type === 'password' ? 'text' : 'password';
    const icon = button?.querySelector('i');
    icon?.classList.toggle('fa-eye', input.type === 'password');
    icon?.classList.toggle('fa-eye-slash', input.type !== 'password');
};
window.closeModal = function() { const container = document.getElementById('modalContainer'); if (container) container.innerHTML = ''; };
window.toggleMobileMenu = function() { document.getElementById('mobileMenu')?.classList.toggle('hidden'); };
window.closeMobileMenu = function() { document.getElementById('mobileMenu')?.classList.add('hidden'); };

function academyPasswordStrength(password) {
    const criteria = {
        upper: /[A-Z]/.test(password), lower: /[a-z]/.test(password),
        number: /[0-9]/.test(password), special: /[!@#$%^&*()\-_+=\[\]{}|;:,.<>?\/~]/.test(password),
        length: password.length > 8
    };
    return {criteria, score: Object.values(criteria).filter(Boolean).length};
}

function setupAcademyPasswordStrength() {
    const input = document.getElementById('academyPassword');
    const container = document.querySelector('[data-password-strength="academyPassword"]');
    if (!input || !container) return;
    const update = () => {
        const result = academyPasswordStrength(input.value);
        const hue = result.score <= 1 ? 0 : (result.score - 1) * 30;
        const labels = [academyText('js.strength_very_weak'), academyText('js.strength_very_weak'), academyText('js.strength_very_weak'), academyText('js.strength_medium'), academyText('js.strength_strong'), academyText('js.strength_very_strong')];
        const bar = container.querySelector('[data-strength-bar]');
        const label = container.querySelector('[data-strength-label]');
        bar.style.width = `${result.score * 20}%`;
        bar.style.backgroundColor = `hsl(${hue} 75% 42%)`;
        label.textContent = labels[result.score];
        label.style.color = `hsl(${hue} 75% 35%)`;
        Object.entries(result.criteria).forEach(([key, met]) => {
            const item = container.querySelector(`[data-criterion="${key}"]`);
            if (!item) return;
            item.classList.toggle('text-green-600', met);
            item.classList.toggle('text-gray-400', !met);
            item.textContent = `${met ? '✓' : '○'} ${item.textContent.slice(2)}`;
        });
    };
    input.addEventListener('input', update);
    update();
}

function academyToast(message) {
    const el = document.getElementById(academyRegistrationStep === 1 ? 'academyFormError' : 'academyOtpError');
    if (el) { el.textContent = message; el.classList.toggle('hidden', !message); }
    if (message && typeof Swal !== 'undefined') Swal.fire({toast: true, position: 'top', icon: 'error', title: message, showConfirmButton: false, timer: 6000});
}

window.setAcademyRegistrationMethod = function(method) {
    document.getElementById('academyRegMethod').value = method;
    document.getElementById('academyEmailBox')?.classList.toggle('hidden', method !== 'email');
    document.getElementById('academyPhoneBox')?.classList.toggle('hidden', method !== 'phone');
    document.querySelectorAll('.academy-method').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
        btn.classList.add('border-gray-200', 'text-gray-600');
    });
    const active = document.getElementById(method === 'email' ? 'academyMethodEmail' : 'academyMethodPhone');
    active?.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
    active?.classList.remove('border-gray-200', 'text-gray-600');
};

function validateAcademyRegistration(form) {
    const method = form.elements.register_method.value;
    const identifier = form.elements[method];
    const value = identifier?.value.trim() || '';
    if (!value) { identifier?.focus(); academyToast(method === 'email' ? academyText('error.email_required') : academyText('error.phone_required')); return false; }
    if (method === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { identifier.focus(); academyToast(academyText('error.email_invalid')); return false; }
    if (method === 'phone' && !/^09\d{9}$/.test(value.replace(/\D/g, ''))) { identifier.focus(); academyToast(academyText('error.phone_invalid')); return false; }
    for (const [name, message] of [['username', academyText('error.username_required')], ['academy_name', academyText('error.name_required')], ['password', academyText('error.password_required')]]) {
        if (!form.elements[name]?.value.trim()) { form.elements[name]?.focus(); academyToast(message); return false; }
    }
    if (form.elements.password.value.length < 8) { academyToast(academyText('error.password_short')); return false; }
    if (academyPasswordStrength(form.elements.password.value).score < 3) { form.elements.password.focus(); academyToast(academyText('error.password_weak')); return false; }
    if (form.elements.password.value !== form.elements.password2.value) { academyToast(academyText('error.password_mismatch')); return false; }
    if (!form.elements.terms.checked) { academyToast(academyText('error.terms_required')); return false; }
    academyToast('');
    return true;
}

window.handleAcademyRegistrationSubmit = function(form) {
    if (academyRegistrationStep === 1) { if (validateAcademyRegistration(form)) sendAcademyRegistrationOtp(); return false; }
    const code = Array.from(document.querySelectorAll('.academy-otp')).map(input => input.value).join('');
    if (!/^\d{6}$/.test(code)) { academyToast(academyText('error.otp_incomplete')); return false; }
    document.getElementById('academyRegOtp').value = code;
    return true;
};

window.sendAcademyRegistrationOtp = async function() {
    const form = document.getElementById('academyRegistrationForm');
    if (!form || !validateAcademyRegistration(form)) return;
    const button = document.getElementById('academySendOtpBtn');
    if (button) button.disabled = true;
    try {
        const otpEndpoint = form.action.includes('register-main-branch') ? '/academy/register-main-branch/send-otp' : '/academy/send-academy-request/send-otp';
        const response = await fetch(otpEndpoint, {method: 'POST', headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: new FormData(form)});
        const payload = await response.json();
        const data = payload.data || {};
        if (!response.ok || !data.success) throw new Error(data.errors ? Object.values(data.errors).flat()[0] : (data.message || academyText('error.otp_send_failed')));
        const method = form.elements.register_method.value;
        document.getElementById('academySentTo').textContent = form.elements[method].value.trim();
        document.getElementById('academyDetailsStep').classList.add('hidden');
        document.getElementById('academyOtpStep').classList.remove('hidden');
        academyRegistrationStep = 2;
        setupAcademyOtpInputs();
        startAcademyTimer(data.expires_in || 120);
    } catch (error) { academyToast(error.message || academyText('error.invalid_response')); }
    finally { if (button) button.disabled = false; }
};

window.showAcademyRegistrationDetails = function() {
    academyRegistrationStep = 1;
    document.getElementById('academyDetailsStep').classList.remove('hidden');
    document.getElementById('academyOtpStep').classList.add('hidden');
    document.getElementById('academyRegOtp').value = '';
    if (academyRegistrationTimer) clearInterval(academyRegistrationTimer);
};

function setupAcademyOtpInputs() {
    const inputs = document.querySelectorAll('.academy-otp');
    inputs.forEach((input, index) => {
        input.value = '';
        input.oninput = () => { input.value = input.value.replace(/\D/g, '').slice(0, 1); if (input.value) inputs[index + 1]?.focus(); };
        input.onkeydown = event => { if (event.key === 'Backspace' && !input.value) inputs[index - 1]?.focus(); };
        input.onpaste = event => { event.preventDefault(); event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6).split('').forEach((digit, i) => { if (inputs[i]) inputs[i].value = digit; }); };
    });
    inputs[0]?.focus();
}

function startAcademyTimer(seconds) {
    if (academyRegistrationTimer) clearInterval(academyRegistrationTimer);
    const resend = document.getElementById('academyResendBtn');
    if (resend) resend.disabled = true;
    let remaining = seconds;
    const tick = () => {
        const timer = document.getElementById('academyTimer');
        if (timer) timer.textContent = String(Math.floor(remaining / 60)).padStart(2, '0') + ':' + String(remaining % 60).padStart(2, '0');
        if (remaining-- <= 0) { clearInterval(academyRegistrationTimer); academyRegistrationTimer = null; if (resend) resend.disabled = false; }
    };
    tick(); academyRegistrationTimer = setInterval(tick, 1000);
}

const defaultAcademyTerms = `
<h3 class="font-bold text-lg mb-2">۱. صحت اطلاعات و مجوزها</h3><p class="mb-4 text-gray-600 leading-relaxed">مدیر آموزشگاه مسئول صحت مشخصات، مجوزهای فعالیت، اطلاعات شعب و هویت عوامل معرفی‌شده است.</p>
<h3 class="font-bold text-lg mb-2">۲. مدیریت محتوا و دوره‌ها</h3><p class="mb-4 text-gray-600 leading-relaxed">اطلاعات دوره‌ها، شهریه، ظرفیت و زمان‌بندی باید شفاف و به‌روز باشد و حقوق مالکیت فکری دیگران رعایت شود.</p>
<h3 class="font-bold text-lg mb-2">۳. تعهد در برابر هنرجویان</h3><p class="mb-4 text-gray-600 leading-relaxed">آموزشگاه متعهد است درخواست‌ها و پرداخت‌های هنرجویان را مطابق شرایط اعلام‌شده و قوانین جاری پاسخ‌گو باشد.</p>
<h3 class="font-bold text-lg mb-2">۴. حریم خصوصی</h3><p class="mb-4 text-gray-600 leading-relaxed">اطلاعات هنرجویان و استادان فقط برای ارائه خدمات مجاز استفاده می‌شود و افشای آن بدون مجوز ممنوع است.</p>
<h3 class="font-bold text-lg mb-2">۵. بررسی و تعلیق</h3><p class="text-gray-600 leading-relaxed">سُرناز می‌تواند مدارک را بررسی کند و در صورت اطلاعات نادرست، تخلف یا شکایت معتبر، انتشار صفحه آموزشگاه را متوقف کند.</p>`;

const defaultBranchTerms = `
<h3 class="font-bold text-lg mb-2">۱. صحت اطلاعات شعبه</h3><p class="mb-4 text-gray-600 leading-relaxed">مدیر شعبه مسئول صحت اطلاعات تماس، نشانی، ساعات فعالیت و مجوزهای مربوط به شعبه است.</p>
<h3 class="font-bold text-lg mb-2">۲. رعایت مقررات آموزشگاه</h3><p class="mb-4 text-gray-600 leading-relaxed">تمام فعالیت‌های شعبه باید مطابق قوانین آموزشگاه مادر و دستورالعمل‌های اعلام‌شده انجام شود.</p>
<h3 class="font-bold text-lg mb-2">۳. مسئولیت خدمات شعبه</h3><p class="mb-4 text-gray-600 leading-relaxed">مدیر شعبه مسئول کیفیت خدمات، پاسخ‌گویی به هنرجویان و رعایت برنامه‌های ثبت‌شده در شعبه است.</p>
<h3 class="font-bold text-lg mb-2">۴. حفظ حریم خصوصی</h3><p class="mb-4 text-gray-600 leading-relaxed">اطلاعات هنرجویان، استادان و کارکنان شعبه باید محرمانه نگه داشته شود و فقط برای ارائه خدمات مجاز استفاده شود.</p>
<h3 class="font-bold text-lg mb-2">۵. بررسی و تعلیق شعبه</h3><p class="text-gray-600 leading-relaxed">سُرناز و مدیر آموزشگاه می‌توانند در صورت تخلف، اطلاعات نادرست یا شکایت معتبر، فعالیت شعبه را بررسی یا محدود کنند.</p>`;

window.openAcademyTermsModal = function() {
    const container = document.getElementById('modalContainer');
    if (!container) return;
    const isBranch = document.getElementById('academyRegistrationForm')?.action.includes('register-main-branch');
    const terms = isBranch ? defaultBranchTerms : academyText('terms.content', defaultAcademyTerms);
    const termsTitle = isBranch ? 'قوانین ثبت و فعالیت شعبه' : academyText('terms.title', 'قوانین ثبت و فعالیت آموزشگاه');
    container.innerHTML = `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()"><div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()"><div class="px-8 py-5 border-b flex justify-between items-center"><h2 class="text-xl font-bold">${termsTitle}</h2><button type="button" aria-label="${academyText('terms.close')}" onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="p-8 max-h-[60vh] overflow-y-auto text-sm">${terms}</div><div class="px-8 py-5 border-t flex gap-3"><button type="button" onclick="acceptAcademyTerms()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">${academyText('terms.accept')}</button><button type="button" onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl">${academyText('terms.close')}</button></div></div></div>`;
};

window.acceptAcademyTerms = function() { const terms = document.getElementById('academyTerms'); if (terms) terms.checked = true; closeModal(); };

document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('academyRegistrationForm')) return;
    setAcademyRegistrationMethod(document.getElementById('academyRegMethod').value || 'email');
    setupAcademyPasswordStrength();
    const message = document.getElementById('academyFlashMessage')?.dataset.error;
    if (message) academyToast(message);
});
