<style>
    .error-container {
        max-width: 700px;
        padding: 4rem 2rem;
        margin: 5rem auto;
    }

    .error-code {
        font-size: 12rem;
        font-weight: 900;
        color: var(--primary);
        line-height: 1;
        opacity: 0.9;
        margin-bottom: 1rem;
    }

    .error-title {
        font-size: 3.2rem;
        color: #1a3c6d;
        margin-bottom: 1.5rem;
    }

    .error-message {
        font-size: 1.85rem;
        color: #555;
        margin-bottom: 3rem;
        line-height: 1.9;
    }

    .illustration {
        font-size: 9rem;
        margin: 2rem 0 3rem;
        opacity: 0.85;
    }

    .suggestions {
        margin-top: 4rem;
        font-size: 1.6rem;
        color: #666;
    }

    .suggestions a {
        color: var(--primary);
        text-decoration: none;
    }

    .suggestions a:hover {
        text-decoration: underline;
    }

    @media (max-width: 768px) {
        .error-code { font-size: 8rem; }
        .error-title { font-size: 2.6rem; }
        .illustration { font-size: 7rem; }
        .btn { padding: 1.1rem 2.5rem; font-size: 1.6rem; }
    }
</style>

<div class="error-container">
    <!-- انیمیشن / تصویر خطا -->
    <div class="illustration">🎵</div>
    
    <div class="error-code">404</div>
    <h1 class="error-title">صفحه مورد نظر پیدا نشد!</h1>
    
    <p class="error-message">
        متأسفانه صفحه‌ای که به دنبال آن بودید وجود ندارد یا حذف شده است.<br>
        ممکن است آدرس را اشتباه وارد کرده باشید.
    </p>

    <!-- دکمه‌های اقدام -->
    <div>
        <a href="/" class="btn-outline">بازگشت به صفحه اصلی</a>
    </div>
</div>
