<div class="w-72 bg-indigo-900 text-white sidebar flex flex-col">
    <div class="p-6 border-b border-indigo-800">
        <div class="flex items-center gap-3">
            <i class="fas fa-music text-3xl"></i>
            <div>
                <a href="/analytics/home"><h1 class="text-2xl font-bold">برنامه سرناز</h1></a>
                <p class="text-indigo-300 text-sm">پنل مدیریت آموزشگاه</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            <li><a href="#" onclick="showSection('dashboard')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-home w-5"></i> داشبورد</a></li>
            <li><a href="#" onclick="showSection('account')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-user-cog w-5"></i> حساب کاربری</a></li>
            <li><a href="#" onclick="showSection('branches')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-building w-5"></i> شعبه‌ها</a></li>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('profilesSubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 text-right">
                    <span class="flex items-center gap-3"><i class="fas fa-id-card w-5"></i> پروفایل‌ها</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="profilesSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('profiles')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-users w-4"></i>کاربران</a></li>
                    <li><a href="#" onclick="showSection('roles')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-user-tag w-4"></i>نقش‌ها</a></li>
                    <li><a href="#" onclick="showSection('permissions')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-key w-4"></i>دسترسی‌ها</a></li>
                </ul>
            </li>
            <li><a href="#" onclick="showSection('gallery')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-images w-5"></i> گالری</a></li>
            <li><a href="#" onclick="showSection('teachers')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-chalkboard-teacher w-5"></i> پرسنل</a></li>
            <li><a href="#" onclick="showSection('students')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-users w-5"></i> هنرجویان</a></li>
            <li><a href="#" onclick="showSection('classrooms')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-door-open w-5"></i> کلاس‌ها</a></li>
            <li><a href="#" onclick="showSection('instruments')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-guitar w-5"></i> سازها</a></li>
            <li><a href="#" onclick="showSection('lessons')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-book w-5"></i> درس‌ها</a></li>
            <li><a href="#" onclick="showSection('courses')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-book-open w-5"></i> دوره‌ها</a></li>
            <li><a href="#" onclick="showSection('terms')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-calendar-check w-5"></i> ترم‌ها</a></li>
            <li>
                <button type="button" onclick="toggleSidebarSubmenu('scheduleSubmenu', this)" class="w-full flex items-center justify-between gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 text-right">
                    <span class="flex items-center gap-3"><i class="fas fa-calendar-alt w-5"></i> برنامه زمانی</span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200 submenu-chevron"></i>
                </button>
                <ul id="scheduleSubmenu" class="mt-1 mr-4 space-y-1 hidden border-r border-indigo-700/60 pr-2">
                    <li><a href="#" onclick="showSection('scheduling-rules')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-gavel w-4"></i>قوانین زمانبندی</a></li>
                    <li><a href="#" onclick="showSection('availabilities')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-clock w-4"></i>برنامه زمانی شعبه‌ها</a></li>
                    <li><a href="#" onclick="showSection('member-schedules')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-user-clock w-4"></i>برنامه زمانی اعضا</a></li>
                    <li><a href="#" onclick="showSection('availability-exceptions')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-umbrella-beach w-4"></i>تعطیلات و مرخصی‌ها</a></li>
                    <li><a href="#" onclick="showSection('schedules')" class="nav-link flex items-center gap-3 px-4 py-2.5 rounded-xl hover:bg-indigo-800 text-sm text-indigo-100"><i class="fas fa-chalkboard w-4"></i>برنامه زمانی کلاس‌ها</a></li>
                </ul>
            </li>
            <li><a href="#" onclick="showSection('finance')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-money-bill-wave w-5"></i> امور مالی</a></li>
            <li><a href="#" onclick="showSection('reports')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-chart-bar w-5"></i> گزارش‌ها</a></li>
        </ul>
    </nav>
</div>
