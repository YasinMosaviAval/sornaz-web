<style>
    .card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 420px;
        padding: 2.5rem 2rem;
        text-align: center;
    }
    .back {
        position: absolute;
        top: 25px;
        right: 25px;
        color: #666;
        font-size: 1.4rem;
        cursor: pointer;
    }

    /* تایمر دایره‌ای */
    .timer-container {
        margin: 2rem auto 2.5rem;
        position: relative;
        width: 140px;
        height: 140px;
    }
    .timer-circle {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 8px solid #e0e0e0;
        border-top: 8px solid var(--primary);
        animation: spin 120s linear infinite;
        position: relative;
    }
    .timer-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 2.2rem;
        font-weight: 700;
        color: #333;
    }

    h2 {
        font-size: 2.1rem;
        color: #1a3c6d;
        margin-bottom: 1rem;
    }
    .subtitle {
        color: #555;
        font-size: 1.45rem;
        line-height: 1.8;
        margin-bottom: 2.5rem;
    }

    /* ورودی‌های کد */
    .otp-inputs {
        display: flex;
        justify-content: center;
        gap: 12px;
        margin-bottom: 2.5rem;
    }
    .otp-input {
        width: 52px;
        height: 62px;
        text-align: center;
        font-size: 1.8rem;
        font-weight: 700;
        border: 2px solid #ddd;
        border-radius: 12px;
        outline: none;
    }
    .otp-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(0,196,140,0.2);
    }

    /* دکمه تایید */
    .btn-confirm {
        width: 100%;
        padding: 1.3rem;
        background: #5e9cff;
        color: white;
        border: none;
        border-radius: 12px;
        font-size: 1.7rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }
    .btn-confirm:hover {
        background: #4a8ae8;
    }

    .help-link {
        color: var(--primary);
        text-decoration: none;
        font-size: 1.45rem;
        margin-top: 2rem;
        display: inline-block;
    }

    .footer {
        margin-top: 3rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 1.35rem;
        color: #666;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<div class="card">
    <!-- <div class="back">بازگشت ←</div> -->

    <!-- تایمر دایره‌ای -->
    <div class="timer-container">
        <div class="timer-circle" id="timerCircle"></div>
        <div class="timer-text" id="timerText">02:00</div>
    </div>

    <h2>کد امنیتی را وارد کنید</h2>
    <p class="subtitle">
        کد تایید به شماره موبایل <strong>۰۹۱۶***۸۸</strong> ارسال شد.
    </p>

    <!-- ورودی‌های کد -->
    <div class="otp-inputs" id="otpInputs" dir="ltr">
        <input type="text" maxlength="1" class="otp-input" autofocus>
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
        <input type="text" maxlength="1" class="otp-input">
    </div>

    <button class="btn-confirm" onclick="verifyCode()">تایید</button>

    <a href="#" class="help-link">راهنمای بازیابی حساب کاربری</a>

    <div class="footer">
        <span>support@sornaz.com</span>
        <span>ارتباط با کاوه نگار</span>
    </div>
</div>

<script>
    // تایمر ۲ دقیقه‌ای
    let timeLeft = 120; // 2 minutes in seconds
    const timerText = document.getElementById('timerText');
    const timerCircle = document.getElementById('timerCircle');

    const countdown = setInterval(() => {
        timeLeft--;
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerText.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        // پیشرفت دایره
        const progress = (timeLeft / 120) * 360;
        timerCircle.style.background = `conic-gradient(var(--primary) ${progress}deg, #e0e0e0 ${progress}deg)`;

        if (timeLeft <= 0) {
            clearInterval(countdown);
            timerText.textContent = "00:00";
            timerText.style.color = "#d32f2f";
        }
    }, 1000);

    // حرکت خودکار بین فیلدهای OTP
    const inputs = document.querySelectorAll('.otp-input');
    inputs.forEach((input, index) => {
        input.addEventListener('input', () => {
            if (input.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener('keydown', (e) => {
            if (e.key === "Backspace" && input.value.length === 0 && index > 0) {
                inputs[index - 1].focus();
            }
        });
    });

    function verifyCode() {
        alert("کد با موفقیت تایید شد! (این فقط دمو است)");
    }
</script>
