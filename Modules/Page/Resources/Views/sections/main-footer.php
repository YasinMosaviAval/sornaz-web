<footer class="bg-gray-900 text-gray-300 mt-auto">
    <div class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10">
            <!-- درباره -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <img src="/assets/images/logo/white_logo_transparent.png" alt="<?= e(trans('public.logo_alt', 'لوگوی سرناز')) ?>" class="w-10 h-10 object-contain">
                    <span class="font-bold text-white text-lg"><?= e(trans('public.brand', 'برنامه موسیقی سُرناز')) ?></span>
                </div>
                <p class="text-sm text-gray-400 leading-relaxed">
                    <?= e(trans('public.footer.description')) ?>
                </p>
            </div>

            <!-- لینک‌های مهم -->
            <div>
                <h4 class="font-bold text-white mb-4"><?= e(trans('public.footer.important_links', 'لینک‌های مهم')) ?></h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/analytics/articles" class="hover:text-white transition"><?= e(trans('public.nav.articles', 'مقاله‌های آموزشی')) ?></a></li>
                    <li><a href="/page/about-us" class="hover:text-white transition"><?= e(trans('public.nav.about', 'درباره ما')) ?></a></li>
                    <li><a href="/page/contact-us" class="hover:text-white transition"><?= e(trans('public.nav.contact', 'تماس با ما')) ?></a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-white mb-4"><?= e(trans('public.footer.community', 'جامعه سُرناز')) ?></h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="/academy/academies" class="hover:text-white transition"><?= e(trans('public.nav.academies', 'آموزشگاه‌ها')) ?></a></li>
                    <li><a href="/users" class="hover:text-white transition"><?= e(trans('public.nav.users', 'کاربران')) ?></a></li>
                    <li><a href="/academy/send-academy-request" class="hover:text-white transition"><?= e(trans('public.footer.register_academy', 'ثبت آموزشگاه')) ?></a></li>
                </ul>
            </div>

            <!-- لینک‌های آموزشگاه -->
            <div>
                <h4 class="font-bold text-white mb-4"><?= e(trans('public.footer.app_links', 'لینک‌های برنامه')) ?></h4>
                <ul class="space-y-2 text-sm">
                    <?php if (auth()->check()): ?>
                        <li>
                            <form method="POST" action="/logout" class="inline">
                                <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\Csrf\Csrf::class)->token() ?>">
                                <button type="submit" class="hover:text-white transition text-red-600"><?= e(trans('public.action.logout', 'خروج')) ?></button>
                            </form>
                        </li>
                    <?php else: ?>
                        <li><a href="/system/login" class="hover:text-white transition"><?= e(trans('public.action.login', 'ورود')) ?></a></li>
                        <li><a href="/system/register" class="hover:text-white transition"><?= e(trans('public.action.register', 'ثبت نام')) ?></a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>





        <div class="border-t border-gray-800 mt-10 pt-6 text-center text-sm text-gray-500">
            <?= e(trans('public.footer.copyright', '© ۱۴۰۳ تمامی حقوق برای برنامه موسیقی سُرناز محفوظ می‌باشد.')) ?>
        </div>
    </div>
</footer>
