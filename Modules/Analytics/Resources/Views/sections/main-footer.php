<footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <!-- درباره -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="/assets/images/logo/white_logo_transparent.png" alt="لوگوی سرناز" class="w-10 h-10 object-contain">
                    <span class="font-bold text-white text-lg">برنامه موسیقی سُرناز</span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    برنامه موسیقی سُرناز، بستری برای آموزش تئوری موسیقی ایرانی و جهانی، ردیف، دستگاه‌ها و همراهی هنرجویان و اساتید است.
                </p>
            </div>

            <!-- لینک‌های مهم -->
            <div>
                <h4 class="font-bold text-white mb-4">لینک‌های مهم</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/analytics/articles" class="hover:text-white transition">مقاله‌های آموزشی</a></li>
                    <li><a href="/page/about-us" class="hover:text-white transition">درباره ما</a></li>
                    <li><a href="/page/contact-us" class="hover:text-white transition">تماس با ما</a></li>
                </ul>
            </div>

            <!-- لینک‌های آموزشگاه -->
            <div>
                <h4 class="font-bold text-white mb-4">لینک‌های برنامه</h4>
                <ul class="space-y-2 text-sm">
                    <?php if (auth()->check()): ?>
                        <li>
                            <form method="POST" action="/logout" class="inline">
                                <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\Csrf\Csrf::class)->token() ?>">
                                <button type="submit" class="hover:text-white transition text-red-600">خروج</button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="/system/login" class="hover:text-white transition">ورود</a></li>
                        <li><a href="/system/register" class="hover:text-white transition">ثبت نام</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm text-gray-500">
            © ۱۴۰۳ تمامی حقوق برای برنامه موسیقی سُرناز محفوظ می‌باشد.
        </div>
    </div>
</footer>
