window.showAuthToast = function(type, message) {
    if (!message) return;
    if (typeof Swal === 'undefined') {
        console[type === 'error' ? 'error' : 'log'](message);
        return;
    }
    Swal.fire({
        toast: true, position: 'top', icon: type === 'success' ? 'success' : 'error',
        title: message, showConfirmButton: false, showCloseButton: true,
        timer: type === 'success' ? 4500 : 6000, timerProgressBar: true,
        customClass: {popup: type === 'success' ? 'auth-toast-success' : 'auth-toast-error'}
    });
};

window.alert = function(message) {
    if (String(message).includes('موفق')) return;
    showAuthToast('error', String(message));
};

window.togglePassword = function(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
    } else {
        input.type = 'password';
        if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
    }
};



window.validateLoginForm = function(form) {
    const identifierInput = form.querySelector('[name="identifier"]');
    const passwordInput = form.querySelector('[name="password"]');
    if (!identifierInput?.value.trim()) {
        showAuthToast('error', 'نام کاربری، ایمیل یا شماره موبایل را وارد کنید.');
        identifierInput?.focus();
        return false;
    }
    if (!passwordInput?.value) {
        showAuthToast('error', 'رمز عبور را وارد کنید.');
        passwordInput?.focus();
        return false;
    }
    return true;
};

window.validateRegisterForm = function(form) {
    const method = form.querySelector('[name="register_method"]')?.value || 'email';
    const identifierInput = form.querySelector(`[name="${method}"]`);
    const identifier = identifierInput?.value.trim() || '';
    const usernameInput = form.querySelector('[name="username"]');
    const passwordInput = form.querySelector('[name="password"]');
    const confirmationInput = form.querySelector('[name="password2"]');
    const pass = passwordInput?.value || '';
    const pass2 = confirmationInput?.value || '';
    const terms = form.querySelector('[name="terms"]')?.checked;

    if (!identifier) {
        showAuthToast('error', method === 'email' ? 'ایمیل را وارد کنید.' : 'شماره موبایل را وارد کنید.');
        identifierInput?.focus();
        return false;
    }
    if (method === 'email' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(identifier)) {
        showAuthToast('error', 'ایمیل واردشده معتبر نیست.');
        identifierInput?.focus();
        return false;
    }
    if (method === 'phone' && !/^09\d{9}$/.test(identifier.replace(/\s/g, ''))) {
        showAuthToast('error', 'شماره موبایل معتبر نیست.');
        identifierInput?.focus();
        return false;
    }
    if (!usernameInput?.value.trim()) {
        showAuthToast('error', 'نام کاربری را وارد کنید.');
        usernameInput?.focus();
        return false;
    }
    if (!pass) {
        showAuthToast('error', 'رمز عبور را وارد کنید.');
        passwordInput?.focus();
        return false;
    }
    if (pass.length < 8) {
        showAuthToast('error', 'رمز عبور حداقل ۸ کاراکتر باشد.');
        passwordInput?.focus();
        return false;
    }
    if (!pass2) {
        showAuthToast('error', 'تکرار رمز عبور را وارد کنید.');
        confirmationInput?.focus();
        return false;
    }
    if (pass !== pass2) {
        showAuthToast('error', 'رمز عبور و تکرار آن یکسان نیست.');
        confirmationInput?.focus();
        return false;
    }
    if (!terms) {
        showAuthToast('error', 'پذیرش قوانین الزامی است.');
        return false;
    }
    return true;
};

let registerStep = 1;
let registerTimerInterval = null;

window.setRegisterMethod = function(method) {
    document.getElementById('regMethod').value = method;
    document.getElementById('regEmailBox')?.classList.toggle('hidden', method !== 'email');
    document.getElementById('regPhoneBox')?.classList.toggle('hidden', method !== 'phone');
    document.querySelectorAll('.reg-method').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
        btn.classList.add('border-gray-200', 'text-gray-600');
    });
    const active = document.getElementById(method === 'email' ? 'regMethodEmail' : 'regMethodPhone');
    active?.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
    active?.classList.remove('border-gray-200', 'text-gray-600');
};

window.handleRegisterSubmit = function(form) {
    if (registerStep === 1) {
        if (!validateRegisterForm(form)) return false;
        sendRegistrationOtp();
        return false;
    }
    const code = Array.from(document.querySelectorAll('.reg-otp')).map(input => input.value).join('');
    if (!/^\d{6}$/.test(code)) {
        showRegisterError('regOtpError', 'کد ۶ رقمی را کامل وارد کنید.');
        return false;
    }
    document.getElementById('regOtp').value = code;
    return true;
};

window.sendRegistrationOtp = async function() {
    const form = document.getElementById('registerForm');
    if (!form || !validateRegisterForm(form)) return;
    const button = document.getElementById('regSendOtpBtn');
    if (button) button.disabled = true;
    showRegisterError('regFormError', '');
    try {
        const response = await fetch('/register/send-otp', {
            method: 'POST',
            headers: {'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json'},
            body: new FormData(form)
        });
        const rawResponse = await response.text();
        let payload;
        try {
            payload = JSON.parse(rawResponse);
        } catch (parseError) {
            console.error('Invalid registration OTP response:', rawResponse);
            throw new Error('پاسخ نامعتبر از سرور دریافت شد. لطفاً گزارش خطای سرور را بررسی کنید.');
        }
        const data = payload.data || {};
        if (!response.ok || !data.success) {
            const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
            throw new Error(firstError || data.message || 'ارسال کد انجام نشد.');
        }
        const method = document.getElementById('regMethod').value;
        const destination = form.querySelector(`[name="${method}"]`).value.trim();
        document.getElementById('regSentTo').textContent = destination;
        document.getElementById('regDetailsStep').classList.add('hidden');
        document.getElementById('regOtpStep').classList.remove('hidden');
        document.querySelectorAll('.reg-otp').forEach(input => input.value = '');
        registerStep = 2;
        setupRegisterOtpInputs();
        startRegisterTimer(data.expires_in || 120);
    } catch (error) {
        showRegisterError(registerStep === 1 ? 'regFormError' : 'regOtpError', error.message);
    } finally {
        if (button) button.disabled = false;
    }
};

window.showRegisterDetails = function() {
    registerStep = 1;
    document.getElementById('regDetailsStep')?.classList.remove('hidden');
    document.getElementById('regOtpStep')?.classList.add('hidden');
    document.getElementById('regOtp').value = '';
    if (registerTimerInterval) clearInterval(registerTimerInterval);
};

function showRegisterError(id, message) {
    const element = document.getElementById(id);
    if (!element) return;
    element.textContent = message;
    element.classList.toggle('hidden', !message);
    if (message) showAuthToast('error', message);
}

function setupRegisterOtpInputs() {
    const inputs = document.querySelectorAll('.reg-otp');
    inputs.forEach((input, index) => {
        input.oninput = () => {
            input.value = input.value.replace(/\D/g, '').slice(0, 1);
            if (input.value && index < inputs.length - 1) inputs[index + 1].focus();
        };
        input.onkeydown = event => {
            if (event.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
        };
        input.onpaste = event => {
            event.preventDefault();
            const code = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
            code.split('').forEach((digit, i) => { if (inputs[i]) inputs[i].value = digit; });
        };
    });
    inputs[0]?.focus();
}

function startRegisterTimer(seconds) {
    if (registerTimerInterval) clearInterval(registerTimerInterval);
    const timer = document.getElementById('regTimer');
    const resend = document.getElementById('regResendBtn');
    if (resend) resend.disabled = true;
    let remaining = seconds;
    const tick = () => {
        const minutes = String(Math.floor(remaining / 60)).padStart(2, '0');
        const secs = String(remaining % 60).padStart(2, '0');
        if (timer) timer.textContent = minutes + ':' + secs;
        if (remaining-- <= 0) {
            clearInterval(registerTimerInterval);
            registerTimerInterval = null;
            if (resend) resend.disabled = false;
        }
    };
    tick();
    registerTimerInterval = setInterval(tick, 1000);
}

document.addEventListener('DOMContentLoaded', () => {
    const method = document.getElementById('regMethod')?.value || 'email';
    if (document.getElementById('registerForm')) setRegisterMethod(method);
    const flash = document.getElementById('authFlashMessage');
    if (flash?.dataset.success) showAuthToast('success', flash.dataset.success);
    if (flash?.dataset.error) showAuthToast('error', flash.dataset.error);
});

window.showForgotPassword = function() {
    if (!document.getElementById('modalContainer')) {
        return alert('برای بازیابی رمز، ایمیل خود را به پشتیبانی ارسال کنید.');
    }
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold">بازیابی رمز عبور</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <p class="text-sm text-gray-500">ایمیل حساب خود را وارد کنید تا لینک بازیابی ارسال شود.</p>
                <input id="forgotEmail" type="email" placeholder="email@example.com"
                       class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <button onclick="submitForgotPassword()" class="w-full bg-indigo-600 text-white py-3.5 rounded-2xl">
                    ارسال لینک بازیابی
                </button>
            </div>
        </div>
    </div>`;
};

window.submitForgotPassword = function() {
    const email = document.getElementById('forgotEmail')?.value.trim();
    if (!email) return alert('ایمیل الزامی است');
    closeModal();
    alert('اگر این ایمیل ثبت شده باشد، لینک بازیابی ارسال می‌شود.');
};

const termsContent = `
<h3 class="font-bold text-lg mb-3">۱. پذیرش شرایط</h3>
<p class="mb-4 text-gray-600 leading-relaxed">با ثبت‌نام در این سامانه، تمامی قوانین و مقررات استفاده از خدمات آموزشگاه و پلتفرم آموزشی را می‌پذیرید.</p>

<h3 class="font-bold text-lg mb-3">۲. حساب کاربری</h3>
<p class="mb-4 text-gray-600 leading-relaxed">کاربر موظف است اطلاعات صحیح وارد کند و مسئولیت حفظ محرمانگی رمز عبور بر عهده خود اوست. هرگونه فعالیت انجام‌شده با حساب کاربری به نام همان کاربر محسوب می‌شود.</p>

<h3 class="font-bold text-lg mb-3">۳. محتوای آموزشی</h3>
<p class="mb-4 text-gray-600 leading-relaxed">محتوای دوره‌ها، مقالات و فایل‌های آموزشی صرفاً برای استفاده شخصی هنرجو است و انتشار، فروش یا کپی‌برداری بدون مجوز کتبی ممنوع است.</p>

<h3 class="font-bold text-lg mb-3">۴. پرداخت و انصراف</h3>
<p class="mb-4 text-gray-600 leading-relaxed">شهریه دوره‌ها طبق تعرفه‌های اعلام‌شده دریافت می‌شود. شرایط استرداد وجه مطابق آیین‌نامه مالی آموزشگاه خواهد بود.</p>

<h3 class="font-bold text-lg mb-3">۵. حریم خصوصی</h3>
<p class="mb-4 text-gray-600 leading-relaxed">اطلاعات شخصی کاربران محرمانه نگهداری می‌شود و جز در موارد قانونی یا با رضایت کاربر در اختیار شخص ثالث قرار نمی‌گیرد.</p>

<h3 class="font-bold text-lg mb-3">۶. رفتار کاربران</h3>
<p class="mb-4 text-gray-600 leading-relaxed">هرگونه محتوای توهین‌آمیز، اسپم یا نقض حقوق دیگران در پیام‌ها، نظرات و پروفایل ممنوع است و می‌تواند به تعلیق حساب منجر شود.</p>

<h3 class="font-bold text-lg mb-3">۷. تغییرات قوانین</h3>
<p class="text-gray-600 leading-relaxed">آموزشگاه می‌تواند این قوانین را به‌روزرسانی کند. ادامه استفاده از خدمات پس از اعلام تغییرات به منزله پذیرش نسخه جدید است.</p>
`;

window.openTermsModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4 overflow-y-auto"
         onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg my-8 shadow-2xl" onclick="event.stopPropagation()">
            <div class="sticky top-0 bg-white px-8 py-5 border-b flex justify-between items-center rounded-t-3xl z-10">
                <h2 class="text-xl font-bold">قوانین و شرایط استفاده</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300 leading-none">×</button>
            </div>
            <div class="p-8 max-h-[60vh] overflow-y-auto text-sm">
                ${termsContent}
            </div>
            <div class="px-8 py-5 border-t flex gap-3">
                <button onclick="acceptTerms()"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white py-3.5 rounded-2xl font-medium">
                    می‌پذیرم
                </button>
                <button onclick="closeModal()"
                        class="flex-1 border border-gray-300 py-3.5 rounded-2xl hover:bg-gray-50">
                    بستن
                </button>
            </div>
        </div>
    </div>`;
};

window.acceptTerms = function() {
    const cb = document.getElementById('regTerms');
    if (cb) cb.checked = true;
    closeModal();
};


let fpMethod = 'email'; // 'email' | 'phone'
let fpTimerInterval = null;

window.setFpMethod = function(method) {
    fpMethod = method;
    document.querySelectorAll('.fp-method').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
        btn.classList.add('border-gray-200', 'text-gray-600');
    });
    const active = document.getElementById(method === 'email' ? 'fpMethodEmail' : 'fpMethodPhone');
    if (active) {
        active.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
        active.classList.remove('border-gray-200', 'text-gray-600');
    }
    document.getElementById('fpEmailBox')?.classList.toggle('hidden', method !== 'email');
    document.getElementById('fpPhoneBox')?.classList.toggle('hidden', method !== 'phone');
};

window.fpGoStep = function(step) {
    [1, 2, 3].forEach(n => {
        document.getElementById('fpStep' + n)?.classList.toggle('hidden', n !== step);
    });
    if (step === 1 && fpTimerInterval) {
        clearInterval(fpTimerInterval);
        fpTimerInterval = null;
    }
};

window.sendFpOtp = async function() {
    let destination;
    if (fpMethod === 'email') {
        const email = document.getElementById('fpEmail')?.value.trim();
        if (!email) return alert('ایمیل را وارد کنید');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return alert('ایمیل معتبر نیست');
        document.getElementById('fpSentTo').textContent = email;
        destination = email;
    } else {
        const phone = document.getElementById('fpPhone')?.value.trim();
        if (!phone) return alert('شماره موبایل را وارد کنید');
        if (!/^09\d{9}$/.test(phone.replace(/\s/g, ''))) return alert('شماره موبایل معتبر نیست (مثال: 09123456789)');
        document.getElementById('fpSentTo').textContent = phone;
        destination = phone.replace(/\s/g, '');
    }
    try {
        const data = await fpRequest('/forgot-password/send-otp', {method: fpMethod, destination});
        document.querySelectorAll('.fp-otp').forEach(inp => { inp.value = ''; });
        setupOtpInputs();
        fpGoStep(2);
        startFpTimer(data.expires_in || 120);
    } catch (error) { showFpError(error.message); }
};

function startFpTimer(seconds) {
    if (fpTimerInterval) clearInterval(fpTimerInterval);
    const timerEl = document.getElementById('fpTimer');
    const resendBtn = document.getElementById('fpResendBtn');
    if (resendBtn) resendBtn.disabled = true;
    let left = seconds;

    const tick = () => {
        const m = String(Math.floor(left / 60)).padStart(2, '0');
        const s = String(left % 60).padStart(2, '0');
        if (timerEl) timerEl.textContent = m + ':' + s;
        if (left <= 0) {
            clearInterval(fpTimerInterval);
            fpTimerInterval = null;
            if (resendBtn) resendBtn.disabled = false;
            if (timerEl) timerEl.textContent = '۰۰:۰۰';
            return;
        }
        left--;
    };
    tick();
    fpTimerInterval = setInterval(tick, 1000);
}

function setupOtpInputs() {
    const inputs = document.querySelectorAll('.fp-otp');
    inputs.forEach((inp, i) => {
        inp.oninput = () => {
            inp.value = inp.value.replace(/\D/g, '').slice(0, 1);
            if (inp.value && i < inputs.length - 1) inputs[i + 1].focus();
        };
        inp.onkeydown = (e) => {
            if (e.key === 'Backspace' && !inp.value && i > 0) inputs[i - 1].focus();
        };
        inp.onpaste = (e) => {
            e.preventDefault();
            const text = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, 6);
            text.split('').forEach((ch, j) => { if (inputs[j]) inputs[j].value = ch; });
            if (text.length) inputs[Math.min(text.length, inputs.length) - 1].focus();
        };
    });
    if (inputs[0]) inputs[0].focus();
}

window.verifyFpOtp = async function() {
    const code = Array.from(document.querySelectorAll('.fp-otp')).map(i => i.value).join('');
    if (code.length !== 6) return alert('کد ۶ رقمی را کامل وارد کنید');
    try {
        await fpRequest('/forgot-password/verify-otp', {code});
        if (fpTimerInterval) clearInterval(fpTimerInterval);
        fpGoStep(3);
    } catch (error) { showFpError(error.message); }
};

window.resetPassword = async function() {
    const p1 = document.getElementById('fpNewPass')?.value;
    const p2 = document.getElementById('fpNewPass2')?.value;
    if (!p1 || p1.length < 8) return alert('رمز عبور حداقل ۸ کاراکتر باشد');
    if (p1 !== p2) return alert('رمز عبور و تکرار آن یکسان نیست');

    try {
        await fpRequest('/forgot-password/reset', {password: p1, password_confirmation: p2});
        alert('رمز عبور با موفقیت تغییر کرد. اکنون وارد شوید.');
        window.location.href = '/system/login';
    } catch (error) { showFpError(error.message); }
};

async function fpRequest(url, fields) {
    showFpError('');
    const body = new FormData();
    body.append('_token', document.getElementById('fpCsrf')?.value || '');
    Object.entries(fields).forEach(([key, value]) => body.append(key, value));
    const response = await fetch(url, {
        method: 'POST', headers: {'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest'}, body
    });
    const raw = await response.text();
    let payload;
    try { payload = JSON.parse(raw); }
    catch (error) { console.error('Invalid password reset response:', raw); throw new Error('پاسخ نامعتبر از سرور دریافت شد.'); }
    const data = payload.data || {};
    if (!response.ok || !data.success) throw new Error(data.message || 'انجام درخواست ناموفق بود.');
    return data;
}

function showFpError(message) {
    const element = document.getElementById('fpError');
    if (!element) return;
    element.textContent = message;
    element.classList.toggle('hidden', !message);
    if (message) showAuthToast('error', message);
}

// لینک فراموشی در صفحه ورود
window.showForgotPassword = function() {
    if (typeof showSection === 'function') showSection('forgot-password');
    else openForgotModalFallback();
};



// خروج
window.handleLogout = function() {
    try {
        localStorage.removeItem('academyAuth');
        sessionStorage.removeItem('academyAuth');
    } catch (e) {}
    if (typeof showSection === 'function') showSection('login');
};
