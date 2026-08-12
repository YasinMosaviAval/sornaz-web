<header class="bg-white border-b border-gray-100 sticky top-0 z-40 shadow-sm">
    <?php $headerUser = auth()->user(); $isSiteAdmin = \Modules\System\Services\SiteAdminAccess::allows($headerUser); ?>
    <div class="max-w-7xl mx-auto px-4">
        <div class="hidden lg:flex items-center gap-4 min-h-20" dir="ltr">
            <div class="flex flex-1 min-w-0 items-center justify-between gap-3" dir="<?= e(direction()) ?>">
                <a href="/" class="flex items-center gap-2 shrink-0">
                    <img src="/assets/images/logo/cropped-favicon_512x512.jpg" alt="<?= e(trans('public.logo_alt', 'لوگوی سرناز')) ?>" class="w-11 h-11 rounded-xl object-cover">
                    <span class="font-bold text-lg hidden xl:block"><?= e(trans('public.brand', 'برنامه موسیقی سُرناز')) ?></span>
                </a>

                <nav class="flex items-center gap-1 text-sm">
                <!-- <a href="/page/home" data-page="home" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50">خانه</a> -->
                <a href="/analytics/articles" data-page="articles" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.articles', 'مقاله‌های آموزشی')) ?></a>
                <a href="/academy/academies" data-page="academies" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.academies', 'آموزشگاه‌ها')) ?></a>
                <a href="/users" data-page="users" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.users', 'کاربران')) ?></a>
                <?php if (($headerUser['type'] ?? null) === 'academy' || $isSiteAdmin): ?>
                    <a href="/analytics/admin-panel" data-page="contact" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50"><?= e($isSiteAdmin ? trans('public.nav.admin_panel', 'پنل ادمین') : trans('public.nav.academy_panel', 'پنل آموزشگاه')) ?></a>
                <?php endif; ?>

                <a href="/page/about-us" data-page="about" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.about', 'درباره ما')) ?></a>
                <a href="/page/contact-us" data-page="contact" class="nav-link-site px-3 py-2 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.contact', 'تماس با ما')) ?></a>
                </nav>

                <div class="flex items-center gap-2 shrink-0">
                <?php if (auth()->check()): ?>
                    <form method="POST" action="/logout" class="inline">
                        <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\Csrf\Csrf::class)->token() ?>">
                        <button type="submit" class="text-sm px-4 py-2 rounded-xl text-red-600 hover:bg-red-50"><?= e(trans('public.action.logout', 'خروج')) ?></button>
                    </form>
                <?php else: ?>
                    <a href="/system/login" class="text-sm px-4 py-2 rounded-xl text-indigo-600 hover:bg-indigo-50"><?= e(trans('public.action.login', 'ورود')) ?></a>
                    <a href="/system/register" class="text-sm px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700"><?= e(trans('public.action.register', 'ثبت نام')) ?></a>
                <?php endif; ?>
                </div>
            </div>

            <div class="flex shrink-0 items-center gap-2 border-l border-gray-200 pl-4" dir="ltr">
                <? component('inline-edit-switch'); ?>
                <? component('theme-switcher'); ?>
                <? component('language-switcher'); ?>
            </div>
        </div>

        <div class="flex lg:hidden items-center justify-between h-16">
            <a href="/" class="flex items-center gap-2 shrink-0">
                <img src="/assets/images/logo/cropped-favicon_512x512.jpg" alt="<?= e(trans('public.logo_alt', 'لوگوی سرناز')) ?>" class="w-11 h-11 rounded-xl object-cover">
                <span class="font-bold text-base"><?= e(trans('public.brand', 'برنامه موسیقی سُرناز')) ?></span>
            </a>
            <button type="button" onclick="toggleMobileMenu()" class="lg:hidden p-2 text-gray-600">
                <i class="fas fa-bars text-xl" id="mobileMenuIcon"></i>
            </button>
        </div>

        <!-- منوی کشویی موبایل -->
        <div id="mobileMenu" class="hidden lg:hidden pb-4 border-t border-gray-100 pt-3 space-y-1">
            <div class="mobile-header-switchers grid grid-cols-2 gap-2 px-3 pb-3 mb-2 border-b border-gray-100" dir="ltr">
                <? component('inline-edit-switch'); ?>
                <? component('theme-switcher'); ?>
                <? component('language-switcher'); ?>
            </div>
            <!-- <a href="/page/home" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50">خانه</a> -->
            <a href="/analytics/articles" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.articles', 'مقاله‌های آموزشی')) ?></a>
            <a href="/academy/academies" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.academies', 'آموزشگاه‌ها')) ?></a>
            <a href="/users" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.users', 'کاربران')) ?></a>
            <?php if (($headerUser['type'] ?? null) === 'academy' || $isSiteAdmin): ?>
                <a href="/analytics/admin-panel" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50"><?= e($isSiteAdmin ? trans('public.nav.admin_panel', 'پنل ادمین') : trans('public.nav.academy_panel', 'پنل آموزشگاه')) ?></a>
            <?php endif; ?>
            <a href="/page/about-us" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.about', 'درباره ما')) ?></a>
            <a href="/page/contact-us" onclick="closeMobileMenu();" class="block px-3 py-2.5 rounded-lg hover:bg-gray-50"><?= e(trans('public.nav.contact', 'تماس با ما')) ?></a>
            <div class="flex gap-2 pt-3 border-t border-gray-100 mt-2">
                <?php if (auth()->check()): ?>
                    <form method="POST" action="/logout" class="flex-1">
                        <input type="hidden" name="_token" value="<?= app()->container()->make(\Core\Csrf\Csrf::class)->token() ?>">
                        <button type="submit" class="w-full text-center text-sm py-2.5 rounded-xl border border-red-200 text-red-600"><?= e(trans('public.action.logout', 'خروج')) ?></button>
                    </form>
                <?php else: ?>
                    <a href="/system/login" onclick="closeMobileMenu();" class="flex-1 text-center text-sm py-2.5 rounded-xl border border-indigo-200 text-indigo-600"><?= e(trans('public.action.login', 'ورود')) ?></a>
                    <a href="/system/register" onclick="closeMobileMenu();" class="flex-1 text-center text-sm py-2.5 rounded-xl bg-indigo-600 text-white"><?= e(trans('public.action.register', 'ثبت نام')) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
