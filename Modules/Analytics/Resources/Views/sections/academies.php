<div id="academies" class="section hidden">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold">آموزشگاه‌ها</h1>
            <p class="text-gray-500 mt-1">لیست آموزشگاه‌های ثبت‌شده و نقش شما در آن‌ها</p>
        </div>
        <div class="flex flex-wrap gap-3">
            <button onclick="showSection('academy-requests')"
                    class="border border-indigo-300 text-indigo-600 hover:bg-indigo-50 px-5 py-3 rounded-2xl text-sm">
                ثبت آموزشگاه
            </button>
            <button onclick="openAddAcademyModal()"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-2xl flex items-center gap-2">
                <i class="fas fa-plus"></i> افزودن
            </button>
        </div>
    </div>

    <h2 class="text-xl font-bold mb-4">آموزشگاه‌های من</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-10" id="myAcademiesCards"></div>

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
        <h2 class="text-xl font-bold">لیست آموزشگاه‌ها</h2>
        <input type="text" id="academySearch" placeholder="جستجو نام / شهر..."
               onkeyup="filterAcademiesList()"
               class="w-full md:w-72 border border-gray-300 rounded-2xl py-2.5 px-4 text-sm focus:outline-none focus:border-indigo-500">
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="allAcademiesCards"></div>
</div>
