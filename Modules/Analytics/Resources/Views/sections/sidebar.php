<div class="w-72 bg-indigo-900 text-white sidebar flex flex-col">
    <div class="p-6 border-b border-indigo-800">
        <div class="flex items-center gap-3">
            <i class="fas fa-music text-3xl"></i>
            <div>
                <a href="/analytics/home">
                    <h1 class="text-2xl font-bold">برنامه سرناز</h1>
                </a>
                <p class="text-indigo-300 text-sm">پنل مدیریت آموزشگاه</p>
            </div>
        </div>
    </div>
    
    <nav class="flex-1 p-4 overflow-y-auto">
        <ul class="space-y-2">
            <li><a href="#" onclick="showSection('dashboard')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-home w-5"></i> داشبورد</a></li>
            <li><a href="#" onclick="showSection('account')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-user-cog w-5"></i> حساب کاربری</a></li>
            <li><a href="#" onclick="showSection('branches')" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-building w-5"></i> شعبه‌ها</a></li>
            <li><a href="#" onclick="showSection('profiles')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-id-card w-5"></i> پروفایل‌ها</a></li>
            
            <li><a href="#" onclick="showSection('gallery')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-images w-5"></i> گالری</a></li>
            <li><a href="#" onclick="showSection('classrooms')" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-door-open w-5"></i> کلاس‌ها</a></li>
            <li><a href="#" onclick="showSection('teachers')" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-chalkboard-teacher w-5"></i> پرسنل</a></li>
            <li><a href="#" onclick="showSection('students')" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-users w-5"></i> هنرجویان</a></li>
            <li><a href="#" onclick="showSection('courses')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-book-open w-5"></i> دوره‌ها</a></li>
            <li><a href="#" onclick="showSection('terms')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-calendar-check w-5"></i> ترم‌ها</a></li>
            <li><a href="#" onclick="showSection('schedules')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-calendar-alt w-5"></i> برنامه زمانی</a></li>

            <li><a href="#" onclick="showSection('reports')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-chart-bar w-5"></i> گزارش‌ها</a></li>
            <li><a href="#" onclick="showSection('finance')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-money-bill-wave w-5"></i> امور مالی</a></li>
            <li><a href="#" onclick="showSection('instruments')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-guitar w-5"></i> ابزارها</a></li>
            <li><a href="#" onclick="showSection('lessons')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-book w-5"></i> درس‌ها</a></li>
            <!-- Rev 2
                <li><a href="#" onclick="showSection('awards')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-trophy w-5"></i> جایزه‌ها</a></li>
                <li><a href="#" onclick="showSection('certificates')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-certificate w-5"></i> تأییدیه‌ها</a></li>
                <li><a href="#" onclick="showSection('educations')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-graduation-cap w-5"></i> تحصیلات</a></li>
                <li><a href="#" onclick="showSection('events')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-calendar-star w-5"></i> رویدادها</a></li>
                <li><a href="#" onclick="showSection('polls')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-poll w-5"></i> نظرسنجی‌ها</a></li>
                <li><a href="#" onclick="showSection('publications')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-book-open w-5"></i> تألیف‌ها</a></li>
                <li><a href="#" onclick="showSection('badges')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-medal w-5"></i> نشان‌ها</a></li>
                <li><a href="#" onclick="showSection('approvals')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-check-double w-5"></i> تأییدها</a></li>
                
                <li><a href="#" onclick="showSection('ratings')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-star w-5"></i> رتبه‌بندی‌ها</a></li>
                <li><a href="#" onclick="showSection('rating-summaries')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-chart-bar w-5"></i> خلاصه رتبه‌بندی‌ها</a></li>
                <li><a href="#" onclick="showSection('experiences')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-briefcase w-5"></i> تجربه‌ها</a></li>
                <li><a href="#" onclick="showSection('posts')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-newspaper w-5"></i> مقاله‌ها</a></li>
                <li><a href="#" onclick="showSection('about-us')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-info-circle w-5"></i> درباره ما</a></li>
                <li><a href="#" onclick="showSection('contact-us')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-envelope w-5"></i> تماس با ما</a></li>
                <li><a href="#" onclick="showSection('academy-requests')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-file-signature w-5"></i> درخواست ثبت آموزشگاه</a></li>
            -->
            
            <!-- <li><a href="#" onclick="showSection('articles')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-graduation-cap w-5"></i> مقاله‌های آموزشی</a></li> -->
            <!-- <li><a href="#" onclick="showSection('academies')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-university w-5"></i> آموزشگاه‌ها</a></li> -->
            <!-- <li><a href="#" onclick="showSection('academy-enroll')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-user-plus w-5"></i> ثبت‌نام در آموزشگاه</a></li> -->
            <!-- <li><a href="#" onclick="showSection('home')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-home w-5"></i> صفحه اصلی</a></li> -->


            <!-- Remove
                <li><a href="#" onclick="showSection('users')" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-users-cog w-5"></i> پرسنل</a></li>
                <li><a href="#" onclick="showSection('contracts')" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800"><i class="fas fa-file-contract w-5"></i> قراردادها</a></li>
            -->


        </ul>
    </nav>

    <!-- Rev 2
        <div class="p-4 border-t border-indigo-800 mt-auto">
            <div class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-indigo-800 cursor-pointer">
                <div class="w-10 h-10 bg-indigo-700 rounded-full flex items-center justify-center">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <p class="font-medium">مدیر آموزشگاه</p>
                    <p class="text-xs text-indigo-300">خروج</p>
                </div>
            </div>
        </div>
    -->
</div>