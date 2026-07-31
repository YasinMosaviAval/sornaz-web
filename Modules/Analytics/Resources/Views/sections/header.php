<header class="bg-white border-b px-8 py-4 flex items-center justify-between shadow-sm">
    <div class="flex items-center gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden text-2xl"><i class="fas fa-bars"></i></button>
        <div class="relative w-80">
            <!-- <input type="text" placeholder="جستجو هنرجو، کلاس..." class="w-full bg-gray-100 border border-gray-300 rounded-2xl py-3 px-5 pl-12 focus:outline-none focus:border-indigo-500"> -->
            <!-- <i class="fas fa-search absolute left-5 top-3.5 text-gray-400"></i> -->
        </div>
    </div>
    
    <div class="flex items-center gap-6">
        <button onclick="showSection('points')" class="relative"><i class="fas fa-coins text-2xl text-gray-600"></i></button>
        <!-- <button onclick="showSection('favorites')" class="relative"><i class="fas fa-heart text-2xl text-gray-600"></i></button> -->
        <button onclick="showSection('messages')" class="relative">
            <i class="fas fa-envelope text-2xl text-gray-600"></i>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">۳</span>
        </button>
        <button onclick="showSection('notifications')" class="relative">
            <i class="fas fa-bell text-2xl text-gray-600"></i>
            <span onclick="showNotifications()" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">۳</span>
        </button>
        <div class="flex items-center gap-3">
            <div class="text-right">
                <p class="font-medium">علی رضایی</p>
                <p class="text-xs text-gray-500">مدیر ارشد</p>
            </div>
            <?//= base_path() . '/Modules/Analytics/Resources/Assets/images/photo1.jpg' ?>
            <!-- <img src="<?//= base_path() ?>/Modules/Analytics/Resources/Assets/images/photo1.jpg" alt="Admin" class="w-10 h-10 rounded-full"> -->
            <i class="fas fa-user"></i>
            
        </div>
    </div>
</header>