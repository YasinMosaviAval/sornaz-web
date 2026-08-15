<?php $isSiteAdmin = \Modules\System\Services\SiteAdminAccess::allows(auth()->user()); ?>
<div id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 bg-indigo-900 text-white flex flex-col shadow-2xl transform -translate-x-full transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:shadow-none">
    <!-- Header -->
    <div class="flex items-center justify-between p-6 border-b border-indigo-800">
        <div class="flex items-center gap-3">
            <img src="/assets/images/logo/white_logo_transparent.png" alt="لوگوی سرناز" class="w-11 h-11 object-contain shrink-0">
            <div class="sidebar-text">
                <a href="/"><h1 class="text-xl font-bold">برنامه سرناز</h1></a>
                <p class="text-xs text-indigo-300"><?= $isSiteAdmin ? 'پنل مدیریت سایت' : 'پنل مدیریت آموزشگاه' ?></p>
            </div>
        </div>
        <!-- فقط موبایل -->
        <button class="lg:hidden text-xl" onclick="closeMobileSidebar()"><i class="fas fa-times"></i></button>
    </div>
    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto overflow-x-hidden p-4">
        <ul class="space-y-2">
            <?php if ($isSiteAdmin && env('APP_ENV', 'production') === 'local'): ?>
                <li><a href="#tests" onclick="showSection('tests')" class="nav-link flex items-center gap-3 rounded-xl bg-indigo-950/40 px-4 py-3 transition hover:bg-indigo-800"><i class="fas fa-vials w-5 text-center"></i> مرکز تست‌ها</a></li>
            <?php endif; ?>
            <li><a href="#" onclick="showSection('dashboard')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-home w-5 text-center"></i> داشبورد</a></li>
            <li><a href="#" onclick="showSection('account')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-user-cog w-5 text-center"></i> حساب کاربری</a></li>
            <li><button type="button" onclick="toggleSidebarSubmenu('branchesSubmenu',this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><span class="flex gap-3"><i class="fas fa-building w-5"></i>شعبه‌ها</span><i class="fas fa-chevron-down text-xs submenu-chevron"></i></button><ul id="branchesSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2"><li><a href="#" onclick="showSection('branches')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-building w-4"></i>شعبه‌ها</a></li><?php if($isSiteAdmin):?><li><a href="#" onclick="showSection('branch-types')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-layer-group w-4"></i>انواع آموزشی</a></li><?php endif;?></ul></li>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('profilesSubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-right text-white">
                    <span class="flex items-center gap-3"><i class="fas fa-id-card w-5 text-center"></i> نقش‌ها و دسترسی‌ها</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="profilesSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('users')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white"><i class="fas fa-users w-4"></i>کاربران</a></li>
                    <li><a href="#" onclick="showSection('roles')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white"><i class="fas fa-user-tag w-4"></i>نقش‌ها</a></li>
                    <li><a href="#" onclick="showSection('permissions')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-white"><i class="fas fa-key w-4"></i>دسترسی‌ها</a></li>
                </ul>
            </li>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('gallerySubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-right text-yellow-300 hover:text-yellow-200">
                    <span class="flex items-center gap-3"><i class="fas fa-images w-5 text-center"></i> گالری</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="gallerySubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('gallery-cover')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-yellow-300 hover:text-yellow-200"><i class="fas fa-image w-4"></i>کاور</a></li>
                    <li><a href="#" onclick="showSection('gallery-logo')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-yellow-300 hover:text-yellow-200"><i class="fas fa-certificate w-4"></i>لوگو</a></li>
                    <li><a href="#" onclick="showSection('gallery-intro-video')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-yellow-300 hover:text-yellow-200"><i class="fas fa-video w-4"></i>ویدیو معرفی</a></li>
                    <li><a href="#" onclick="showSection('gallery-collection')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-yellow-300 hover:text-yellow-200"><i class="fas fa-photo-video w-4"></i>مجموعه عکس‌ها و ویدیوها</a></li>
                </ul>
            </li>
            <li><a href="#" onclick="showSection('teachers')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-chalkboard-teacher w-5 text-center"></i> پرسنل</a></li>
            <li><a href="#" onclick="showSection('students')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-users w-5 text-center"></i> هنرجویان</a></li>
            <li><button type="button" onclick="toggleSidebarSubmenu('classroomsSubmenu',this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><span class="flex gap-3"><i class="fas fa-door-open w-5"></i>کلاس‌ها</span><i class="fas fa-chevron-down text-xs submenu-chevron"></i></button><ul id="classroomsSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2"><li><a href="#" onclick="showSection('classrooms')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-door-open w-4"></i>کلاس‌ها</a></li><?php if($isSiteAdmin):?><li><a href="#" onclick="showSection('classroom-types')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-shapes w-4"></i>انواع کلاس</a></li><?php endif;?></ul></li>
            <li><a href="#" onclick="showSection('instruments')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-guitar w-5 text-center"></i> سازها</a></li>
            <li><a href="#" onclick="showSection('lessons')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-book w-5 text-center"></i> درس‌ها</a></li>
            <li><button type="button" onclick="toggleSidebarSubmenu('coursesSubmenu',this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><span class="flex gap-3"><i class="fas fa-book-open w-5"></i>دوره‌ها</span><i class="fas fa-chevron-down text-xs submenu-chevron"></i></button><ul id="coursesSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2"><li><a href="#" onclick="showSection('courses')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-book-open w-4"></i>دوره‌ها</a></li><li><a href="#" onclick="showSection('course-levels')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-signal w-4"></i>سطح دوره‌ها</a></li><li><a href="#" onclick="showSection('terms')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm"><i class="fas fa-calendar-check w-4"></i>ترم‌ها</a></li></ul></li>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('scheduleSubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-right">
                    <span class="flex items-center gap-3"><i class="fas fa-calendar-alt w-5 text-center"></i> برنامه زمانی</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="scheduleSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('scheduling-rules')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm"><i class="fas fa-gavel w-4"></i>قوانین زمانبندی</a></li>
                    <li><a href="#" onclick="showSection('availabilities')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-indigo-100"><i class="fas fa-clock w-4"></i>برنامه زمانی شعبه‌ها</a></li>
                    <li><a href="#" onclick="showSection('member-schedules')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-indigo-100"><i class="fas fa-user-clock w-4"></i>برنامه زمانی اعضا</a></li>
                    <li><a href="#" onclick="showSection('availability-exceptions')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm text-indigo-100"><i class="fas fa-umbrella-beach w-4"></i>تعطیلات و مرخصی‌ها</a></li>
                    <li><a href="#" onclick="showSection('schedules')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 transition text-sm"><i class="fas fa-chalkboard w-4"></i>برنامه زمانی کلاس‌ها</a></li>
                </ul>
            </li>
            <li><a href="#" onclick="showSection('finance')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-money-bill-wave w-5 text-center"></i> امور مالی</a></li>
            <li><a href="#" onclick="showSection('reports')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> گزارش‌ها</a></li>
            
            
            
            
            
            <li>--------------------------------</li>
            <li><a href="#" onclick="showSection('posts')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-file-alt w-5 text-center"></i> نوشته‌ها</a></li>
            <li><a href="#" onclick="showSection('pages')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-copy w-5 text-center"></i> برگه‌ها</a></li>
            <li><a href="#" onclick="showSection('media')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-photo-video w-5 text-center"></i> رسانه‌ها</a></li>
            <li><a href="#" onclick="showSection('comments')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-comments w-5 text-center"></i> دیدگاه‌ها</a></li>
            <li><a href="#" onclick="showSection('settings')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition"><i class="fas fa-cog w-5 text-center"></i> تنظیمات</a></li>
            <li><a href="#" onclick="showSection('articles')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> articles</a></li>
            <li><a href="#" onclick="showSection('about-us')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> about-us</a></li>
            <li><a href="#" onclick="showSection('contact-us')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> contact-us</a></li>
            <li><a href="#" onclick="showSection('academies')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> academies</a></li>
            <li><a href="#" onclick="showSection('academy-enroll')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> academy-enroll</a></li>
            <li><a href="#" onclick="showSection('academy-requests')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 transition text-yellow-300 hover:text-yellow-200"><i class="fas fa-chart-bar w-5 text-center"></i> academy-requests</a></li>

            <!--
                component('home');
                component('login');
                component('register');
            -->
        </ul>
    </nav>
</div>
