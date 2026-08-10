let academyRegistrationStep = 1;
let academyRegistrationTimer = null;

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
    if (!value) { identifier?.focus(); academyToast(method === 'email' ? 'ایمیل را وارد کنید.' : 'شماره موبایل را وارد کنید.'); return false; }
    if (method === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) { identifier.focus(); academyToast('ایمیل واردشده معتبر نیست.'); return false; }
    if (method === 'phone' && !/^09\d{9}$/.test(value.replace(/\D/g, ''))) { identifier.focus(); academyToast('شماره موبایل معتبر نیست.'); return false; }
    for (const [name, message] of [['username', 'نام کاربری را وارد کنید.'], ['academy_name', 'نام آموزشگاه را وارد کنید.'], ['password', 'رمز عبور را وارد کنید.']]) {
        if (!form.elements[name]?.value.trim()) { form.elements[name]?.focus(); academyToast(message); return false; }
    }
    if (form.elements.password.value.length < 8) { academyToast('رمز عبور باید حداقل ۸ کاراکتر باشد.'); return false; }
    if (form.elements.password.value !== form.elements.password2.value) { academyToast('رمز عبور و تکرار آن یکسان نیست.'); return false; }
    if (!form.elements.terms.checked) { academyToast('پذیرش قوانین ثبت آموزشگاه الزامی است.'); return false; }
    academyToast('');
    return true;
}

window.handleAcademyRegistrationSubmit = function(form) {
    if (academyRegistrationStep === 1) { if (validateAcademyRegistration(form)) sendAcademyRegistrationOtp(); return false; }
    const code = Array.from(document.querySelectorAll('.academy-otp')).map(input => input.value).join('');
    if (!/^\d{6}$/.test(code)) { academyToast('کد ۶ رقمی را کامل وارد کنید.'); return false; }
    document.getElementById('academyRegOtp').value = code;
    return true;
};

window.sendAcademyRegistrationOtp = async function() {
    const form = document.getElementById('academyRegistrationForm');
    if (!form || !validateAcademyRegistration(form)) return;
    const button = document.getElementById('academySendOtpBtn');
    if (button) button.disabled = true;
    try {
        const response = await fetch('/analytics/send-academy-request/send-otp', {method: 'POST', headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body: new FormData(form)});
        const payload = await response.json();
        const data = payload.data || {};
        if (!response.ok || !data.success) throw new Error(data.errors ? Object.values(data.errors).flat()[0] : (data.message || 'ارسال کد انجام نشد.'));
        const method = form.elements.register_method.value;
        document.getElementById('academySentTo').textContent = form.elements[method].value.trim();
        document.getElementById('academyDetailsStep').classList.add('hidden');
        document.getElementById('academyOtpStep').classList.remove('hidden');
        academyRegistrationStep = 2;
        setupAcademyOtpInputs();
        startAcademyTimer(data.expires_in || 120);
    } catch (error) { academyToast(error.message || 'پاسخ نامعتبر از سرور دریافت شد.'); }
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

const academyTerms = `
<h3 class="font-bold text-lg mb-2">۱. صحت اطلاعات و مجوزها</h3><p class="mb-4 text-gray-600 leading-relaxed">مدیر آموزشگاه مسئول صحت مشخصات، مجوزهای فعالیت، اطلاعات شعب و هویت عوامل معرفی‌شده است.</p>
<h3 class="font-bold text-lg mb-2">۲. مدیریت محتوا و دوره‌ها</h3><p class="mb-4 text-gray-600 leading-relaxed">اطلاعات دوره‌ها، شهریه، ظرفیت و زمان‌بندی باید شفاف و به‌روز باشد و حقوق مالکیت فکری دیگران رعایت شود.</p>
<h3 class="font-bold text-lg mb-2">۳. تعهد در برابر هنرجویان</h3><p class="mb-4 text-gray-600 leading-relaxed">آموزشگاه متعهد است درخواست‌ها و پرداخت‌های هنرجویان را مطابق شرایط اعلام‌شده و قوانین جاری پاسخ‌گو باشد.</p>
<h3 class="font-bold text-lg mb-2">۴. حریم خصوصی</h3><p class="mb-4 text-gray-600 leading-relaxed">اطلاعات هنرجویان و استادان فقط برای ارائه خدمات مجاز استفاده می‌شود و افشای آن بدون مجوز ممنوع است.</p>
<h3 class="font-bold text-lg mb-2">۵. بررسی و تعلیق</h3><p class="text-gray-600 leading-relaxed">سُرناز می‌تواند مدارک را بررسی کند و در صورت اطلاعات نادرست، تخلف یا شکایت معتبر، انتشار صفحه آموزشگاه را متوقف کند.</p>`;

window.openAcademyTermsModal = function() {
    const container = document.getElementById('modalContainer');
    if (!container) return;
    container.innerHTML = `<div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto" onclick="if(event.target===this) closeModal()"><div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()"><div class="px-8 py-5 border-b flex justify-between items-center"><h2 class="text-xl font-bold">قوانین ثبت و فعالیت آموزشگاه</h2><button type="button" onclick="closeModal()" class="text-3xl text-gray-300">×</button></div><div class="p-8 max-h-[60vh] overflow-y-auto text-sm">${academyTerms}</div><div class="px-8 py-5 border-t flex gap-3"><button type="button" onclick="acceptAcademyTerms()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">می‌پذیرم</button><button type="button" onclick="closeModal()" class="flex-1 border border-gray-300 py-3.5 rounded-2xl">بستن</button></div></div></div>`;
};

window.acceptAcademyTerms = function() { const terms = document.getElementById('academyTerms'); if (terms) terms.checked = true; closeModal(); };

document.addEventListener('DOMContentLoaded', () => {
    if (!document.getElementById('academyRegistrationForm')) return;
    setAcademyRegistrationMethod(document.getElementById('academyRegMethod').value || 'email');
    const message = document.getElementById('academyFlashMessage')?.dataset.error;
    if (message) academyToast(message);
});
