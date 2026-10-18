(function () {
const authText = (key, fallback) => window.authTranslations?.['auth.js.' + key] || fallback;
const showAuthToast = (type, message) => window.showAuthToast ? window.showAuthToast(type, message) : window.alert(message);
function passwordStrength(password) {
  const criteria = {
    upper: /[A-Z]/.test(password),
    lower: /[a-z]/.test(password),
    number: /[0-9]/.test(password),
    special: /[!@#$%^&*()\-_+=\[\]{}|;:,.<>?\/~]/.test(password),
    length: password.length > 8,
  };
  return { criteria, score: Object.values(criteria).filter(Boolean).length };
}

function setupPasswordStrength(inputId) {
  const input = document.getElementById(inputId);
  const container = document.querySelector(`[data-password-strength="${inputId}"]`);
  if (!input || !container) return;
  const update = () => {
    const result = passwordStrength(input.value);
    const hue = result.score <= 1 ? 0 : (result.score - 1) * 30;
    const labels = [
      authText('strength_very_weak', 'بسیار ضعیف'),
      authText('strength_very_weak', 'بسیار ضعیف'),
      authText('strength_very_weak', 'بسیار ضعیف'),
      authText('strength_medium', 'متوسط'),
      authText('strength_strong', 'قوی'),
      authText('strength_very_strong', 'بسیار قوی'),
    ];
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

window.validateRegisterForm = function (form) {
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
    showAuthToast(
      'error',
      method === 'email' ? 'ایمیل را وارد کنید.' : 'شماره موبایل را وارد کنید.'
    );
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
  if (passwordStrength(pass).score < 3) {
    showAuthToast(
      'error',
      'رمز عبور بسیار ضعیف است؛ حداقل ۳ مورد از معیارهای قدرت رمز را رعایت کنید.'
    );
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

window.setRegisterMethod = function (method) {
  document.getElementById('regMethod').value = method;
  document.getElementById('regEmailBox')?.classList.toggle('hidden', method !== 'email');
  document.getElementById('regPhoneBox')?.classList.toggle('hidden', method !== 'phone');
  document.querySelectorAll('.reg-method').forEach((btn) => {
    btn.classList.remove('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
    btn.classList.add('border-gray-200', 'text-gray-600');
  });
  const active = document.getElementById(method === 'email' ? 'regMethodEmail' : 'regMethodPhone');
  active?.classList.add('border-indigo-600', 'bg-indigo-50', 'text-indigo-700');
  active?.classList.remove('border-gray-200', 'text-gray-600');
};

window.handleRegisterSubmit = function (form) {
  if (form.dataset.otpRequired === '0') return validateRegisterForm(form);
  if (registerStep === 1) {
    if (!validateRegisterForm(form)) return false;
    sendRegistrationOtp();
    return false;
  }
  const code = Array.from(document.querySelectorAll('.reg-otp'))
    .map((input) => input.value)
    .join('');
  if (!/^\d{6}$/.test(code)) {
    showRegisterError('regOtpError', 'کد ۶ رقمی را کامل وارد کنید.');
    return false;
  }
  document.getElementById('regOtp').value = code;
  return true;
};

window.sendRegistrationOtp = async function () {
  const form = document.getElementById('registerForm');
  if (!form || !validateRegisterForm(form)) return;
  const button = document.getElementById('regSendOtpBtn');
  if (button) button.disabled = true;
  showRegisterError('regFormError', '');
  try {
    const response = await (form.dataset.staffRegistration === '1' ? window.staffRegistrationOtpRequest(form) : fetch('/register/send-otp', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'application/json' },
      body: new FormData(form),
    }));
    const rawResponse = await response.text();
    if (!form.isConnected) return;
    if (!form.isConnected) return;
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
    document.querySelectorAll('.reg-otp').forEach((input) => (input.value = ''));
    registerStep = 2;
    setupRegisterOtpInputs();
    startRegisterTimer(data.expires_in || 120);
  } catch (error) {
    showRegisterError(registerStep === 1 ? 'regFormError' : 'regOtpError', error.message);
  } finally {
    if (button) button.disabled = false;
  }
};

window.showRegisterDetails = function () {
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
    input.onkeydown = (event) => {
      if (event.key === 'Backspace' && !input.value && index > 0) inputs[index - 1].focus();
    };
    input.onpaste = (event) => {
      event.preventDefault();
      const code = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6);
      code.split('').forEach((digit, i) => {
        if (inputs[i]) inputs[i].value = digit;
      });
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


window.setupPasswordStrength = setupPasswordStrength;
window.showRegisterError = showRegisterError;
window.initializeRegistrationForm = function (form) {
  registerStep = 1;
  if (registerTimerInterval) clearInterval(registerTimerInterval);
  setRegisterMethod(form.querySelector('[name="register_method"]').value);
  setupPasswordStrength('regPassword');
};
})();
