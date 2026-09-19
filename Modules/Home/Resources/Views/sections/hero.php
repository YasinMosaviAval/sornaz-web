<section class="hero">
    <div class="container hero-wrapper">
        <div class="hero-content">
            <span data-fixed-copy class="hero-tag"><?= locale()==='en'?'With you on your music learning journey':'همراه شما در مسیر یادگیری موسیقی' ?></span>
            <h1 data-fixed-copy><?= locale()==='en'?'Find the best music academy':'بهترین آموزشگاه موسیقی را پیدا کنید' ?></h1>
            <p data-fixed-copy><?= locale()==='en'?'Search and compare music academies, teachers, classes and courses across Iran.':'آموزشگاه‌ها، اساتید، کلاس‌ها و دوره‌های موسیقی سراسر ایران را جستجو و مقایسه کنید.' ?></p>
            <form class="hero-search" action="/academy" method="GET">
                <input type="text" name="q" placeholder="نام آموزشگاه">
                <select name="instrument">
                    <option value="">ساز</option>
                    <option>پیانو</option>
                    <option>گیتار</option>
                    <option>ویولن</option>
                    <option>دف</option>
                </select>
                <input type="text" name="city" placeholder="شهر">
                <button>جستجو</button>
            </form>
            <div class="hero-stats">
                <div>
                    <strong>+۱۲۵۰</strong>
                    <span>آموزشگاه</span>
                </div>
                <div>
                    <strong>+۸۰۰۰</strong>
                    <span>مدرس</span>
                </div>
                <div>
                    <strong>+۵۰۰</strong>
                    <span>دوره</span>
                </div>
            </div>
        </div>
        <div class="hero-image">
            <img src="/assets/images/hero.png" alt="hero">
        </div>
    </div>
</section>
