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



window.validateRegisterForm = function(form) {
    const pass = form.querySelector('[name="password"]').value;
    const pass2 = form.querySelector('[name="password2"]').value;
    const terms = form.querySelector('[name="terms"]').checked;

    if (pass.length < 8) {
        alert('رمز عبور حداقل ۸ کاراکتر باشد');
        return false;
    }
    if (pass !== pass2) {
        alert('رمز عبور و تکرار آن یکسان نیست');
        return false;
    }
    if (!terms) {
        alert('پذیرش قوانین الزامی است');
        return false;
    }
    return true;
};

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
let fpDemoOtp = '123456'; // فقط برای دمو — در سرور تولید می‌شود

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

window.sendFpOtp = function() {
    if (fpMethod === 'email') {
        const email = document.getElementById('fpEmail')?.value.trim();
        if (!email) return alert('ایمیل را وارد کنید');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) return alert('ایمیل معتبر نیست');
        document.getElementById('fpSentTo').textContent = email;
    } else {
        const phone = document.getElementById('fpPhone')?.value.trim();
        if (!phone) return alert('شماره موبایل را وارد کنید');
        if (!/^09\d{9}$/.test(phone.replace(/\s/g, ''))) return alert('شماره موبایل معتبر نیست (مثال: 09123456789)');
        document.getElementById('fpSentTo').textContent = phone;
    }

    // در نسخه واقعی: fetch('/api/auth/send-otp', { method, email/phone })
    fpDemoOtp = String(Math.floor(100000 + Math.random() * 900000));
    console.log('OTP دمو:', fpDemoOtp); // فقط توسعه

    // پاک کردن اینپوت‌های OTP
    document.querySelectorAll('.fp-otp').forEach(inp => { inp.value = ''; });
    setupOtpInputs();

    fpGoStep(2);
    startFpTimer(120);
    alert(fpMethod === 'email'
        ? 'کد تأیید به ایمیل ارسال شد (در دمو در Console ببینید)'
        : 'کد تأیید پیامک شد (در دمو در Console ببینید)');
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

window.verifyFpOtp = function() {
    const code = Array.from(document.querySelectorAll('.fp-otp')).map(i => i.value).join('');
    if (code.length !== 6) return alert('کد ۶ رقمی را کامل وارد کنید');

    // در نسخه واقعی: verify با سرور
    if (code !== fpDemoOtp) return alert('کد نادرست است');

    if (fpTimerInterval) clearInterval(fpTimerInterval);
    fpGoStep(3);
};

window.resetPassword = function() {
    const p1 = document.getElementById('fpNewPass')?.value;
    const p2 = document.getElementById('fpNewPass2')?.value;
    if (!p1 || p1.length < 8) return alert('رمز عبور حداقل ۸ کاراکتر باشد');
    if (p1 !== p2) return alert('رمز عبور و تکرار آن یکسان نیست');

    // در نسخه واقعی: POST رمز جدید + توکن OTP
    alert('✅ رمز عبور با موفقیت تغییر کرد. اکنون وارد شوید.');
    document.getElementById('fpNewPass').value = '';
    document.getElementById('fpNewPass2').value = '';
    fpGoStep(1);
    if (typeof showSection === 'function') showSection('login');
};

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