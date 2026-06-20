<style>
    :root {
        --primary: #0066cc;
        --primary-dark: #004080;
        --bg: #f8f9fa;
        --white: #ffffff;
        --shadow: 0 10px 30px rgba(0,0,0,0.12);
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: 'Vazirmatn', sans-serif;
        background: var(--bg);
        color: #333;
        line-height: 1.8;
    }

    .page-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 6rem 2rem 4rem;
        text-align: center;
    }

    .page-title {
        font-size: 3.6rem;
        font-weight: 900;
        margin-bottom: 1rem;
    }

    .page-subtitle {
        font-size: 1.9rem;
        opacity: 0.95;
        max-width: 800px;
        margin: 0 auto;
    }

    .main-content {
        max-width: 1300px;
        margin: -3rem auto 0;
        padding: 0 2rem;
    }

    .users-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 2.5rem;
        margin-top: 2rem;
    }

    .user-card {
        background: var(--white);
        border-radius: 20px;
        box-shadow: var(--shadow);
        overflow: hidden;
        transition: all 0.4s ease;
    }

    .user-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.15);
    }

    .user-photo {
        width: 100%;
        height: 280px;
        object-fit: cover;
    }

    .user-info {
        padding: 2rem 2rem 2.5rem;
        text-align: center;
    }

    .user-name {
        font-size: 2.1rem;
        font-weight: 700;
        color: #1a3c6d;
        margin-bottom: 0.5rem;
    }

    .user-role {
        font-size: 1.55rem;
        color: var(--primary);
        font-weight: 600;
        margin-bottom: 1.5rem;
    }

    .user-bio {
        font-size: 1.55rem;
        color: #555;
        line-height: 1.9;
        margin-bottom: 2rem;
        min-height: 120px;
    }

    .user-social {
        display: flex;
        justify-content: center;
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .user-social a {
        font-size: 2rem;
        color: #777;
        transition: color 0.3s;
    }

    .user-social a:hover {
        color: var(--primary);
    }

    .role-badge {
        display: inline-block;
        padding: 0.5rem 1.5rem;
        background: var(--light);
        color: var(--primary);
        border-radius: 30px;
        font-size: 1.4rem;
        margin-top: 1rem;
    }

    @media (max-width: 768px) {
        .users-grid {
            grid-template-columns: 1fr;
            gap: 2rem;
        }
        .page-title { font-size: 2.8rem; }
    }
</style>


<!-- هدر صفحه -->
<header class="page-header">
    <h1 class="page-title">تیم سُرناز</h1>
    <p class="page-subtitle">
        آشنایی با اعضای تیم آموزشی و پشتیبانی آموزشگاه موسیقی سُرناز
    </p>
</header>

<main class="main-content">

    <div class="users-grid">

        <!-- کارت ۱: مدیر سایت -->
        <div class="user-card">
            <img src="https://via.placeholder.com/600x280/0066cc/ffffff?text=علی+رضایی" alt="علی رضایی" class="user-photo">
            <div class="user-info">
                <h3 class="user-name">علی رضایی</h3>
                <p class="user-role">مدیر و بنیان‌گذار</p>
                <p class="user-bio">
                    پژوهشگر و مدرس موسیقی ایرانی با بیش از ۱۲ سال تجربه. علاقه‌مند به ساده‌سازی مفاهیم پیچیده موسیقی برای همه سطوح.
                </p>
                <div class="role-badge">مدیر سایت</div>
                <div class="user-social">
                    <a href="<?= baseUrl() ?>/page/profile/1">view profile</a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
        </div>

        <!-- کارت ۲: مدرس ویولن -->
        <div class="user-card">
            <img src="https://via.placeholder.com/600x280/28a745/ffffff?text=محمد+احمدی" alt="محمد احمدی" class="user-photo">
            <div class="user-info">
                <h3 class="user-name">استاد محمد احمدی</h3>
                <p class="user-role">مدرس ویولن</p>
                <p class="user-bio">
                    فارغ‌التحصیل کارشناسی ارشد موسیقی از دانشگاه تهران. متخصص ردیف و تکنیک ویولن ایرانی با ۱۲ سال سابقه تدریس.
                </p>
                <div class="role-badge">مدرس</div>
                <div class="user-social">
                    <a href="<?= baseUrl() ?>/page/profile/2">view profile</a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>

        <!-- کارت ۳: نویسنده -->
        <div class="user-card">
            <img src="https://via.placeholder.com/600x280/ffc107/333?text=سارا+محمدی" alt="سارا محمدی" class="user-photo">
            <div class="user-info">
                <h3 class="user-name">سارا محمدی</h3>
                <p class="user-role">نویسنده و پژوهشگر</p>
                <p class="user-bio">
                    نویسنده مقالات تخصصی موسیقی ایرانی. تمرکز اصلی بر مقایسه موسیقی ایرانی با موسیقی ملل شرقی.
                </p>
                <div class="role-badge">نویسنده</div>
                <div class="user-social">
                    <a href="<?= baseUrl() ?>/page/profile/4">view profile</a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
        </div>

        <!-- کارت ۴: طراح -->
        <div class="user-card">
            <img src="https://via.placeholder.com/600x280/17a2b8/ffffff?text=رضا+کریمی" alt="رضا کریمی" class="user-photo">
            <div class="user-info">
                <h3 class="user-name">رضا کریمی</h3>
                <p class="user-role">طراح گرافیک و UI/UX</p>
                <p class="user-bio">
                    مسئول طراحی بصری سایت و تجربه کاربری. علاقه‌مند به ترکیب هنر سنتی ایرانی با طراحی مدرن.
                </p>
                <div class="role-badge">طراح</div>
                <div class="user-social">
                    <a href="<?= baseUrl() ?>/page/profile/4">view profile</a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
        </div>

    </div>

</main>
