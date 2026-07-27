const itemTypeLabels = {
    user: 'کاربر', academy: 'آموزشگاه', branch: 'شعبه', course: 'دوره',
    post: 'پست', lesson: 'درس', instrument: 'ساز'
};

let allFavorites = [
    { id: 1, title: "دوره پیانو مبتدی", summary: "دوره محبوب هنرجو", description: "علاقه‌مندی به دوره پیانو سطح مبتدی شعبه مرکزی.", item_type: "course", item_id: 101, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "استاد محمد موسوی", summary: "استاد مورد علاقه", description: "دنبال کردن کلاس‌های استاد موسوی.", item_type: "user", item_id: 55, user_id: 2, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "شعبه ونک", summary: "شعبه نزدیک", description: "علاقه‌مندی به شعبه ونک برای کلاس‌های گیتار.", item_type: "branch", item_id: 2, user_id: 3, branchId: 2, branchName: "شعبه ونک" },
    { id: 4, title: "درس تئوری موسیقی", summary: "درس پایه", description: "ذخیره درس تئوری برای مرور.", item_type: "lesson", item_id: 12, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" }
];
let currentFavBranch = 'all';

window.renderFavoritesBranchTabs = function() {
    const container = document.getElementById('favoritesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.fav-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'fav-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterFavoritesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterFavoritesByBranch = function(branchId) {
    currentFavBranch = branchId;
    document.querySelectorAll('.fav-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.fav-branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    renderFavoritesTable();
};

window.renderFavoritesTable = function() {
    const tbody = document.querySelector('#favoritesTable tbody');
    if (!tbody) return;
    const list = currentFavBranch === 'all' ? allFavorites : allFavorites.filter(f => f.branchId == currentFavBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="5" class="py-12 text-center text-gray-400">موردی یافت نشد</td></tr>`
        : list.map(f => `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${f.title}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs bg-pink-100 text-pink-700">${itemTypeLabels[f.item_type] || f.item_type}</span></td>
                <td class="py-4 px-5 text-gray-600 max-w-xs truncate">${f.summary || '—'}</td>
                <td class="py-4 px-5">${f.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewFavorite(${f.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editFavorite(${f.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteFavorite(${f.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`).join('');
};

window.openAddFavoriteModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const typeOptions = Object.entries(itemTypeLabels).map(([k, v]) => `<option value="${k}">${v}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن علاقه‌مندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="favTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="favSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="favDesc" rows="3" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="favItemType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                    <input id="favItemId" type="number" placeholder="شناسه آیتم (item_id)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="favBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveFavorite()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveFavorite = function() {
    const title = document.getElementById('favTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('favBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allFavorites.unshift({
        id: Date.now(), title,
        summary: document.getElementById('favSummary').value.trim(),
        description: document.getElementById('favDesc').value.trim(),
        item_type: document.getElementById('favItemType').value,
        item_id: parseInt(document.getElementById('favItemId').value) || 0,
        user_id: 1,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterFavoritesByBranch(currentFavBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewFavorite = function(id) {
    const f = allFavorites.find(x => x.id === id);
    if (!f) return;
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${f.title}</h2>
                    <p class="text-sm text-gray-500">${itemTypeLabels[f.item_type] || f.item_type}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editFavorite(${f.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${f.summary ? `<p class="text-indigo-600 font-medium">${f.summary}</p>` : ''}
                ${f.description ? `<p class="text-gray-600">${f.description}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span>${itemTypeLabels[f.item_type]}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شناسه آیتم</span><span>${f.item_id}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${f.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editFavorite = function(id) {
    const f = allFavorites.find(x => x.id === id);
    if (!f) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === f.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const typeOptions = Object.entries(itemTypeLabels).map(([k, v]) =>
        `<option value="${k}" ${f.item_type === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش علاقه‌مندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editFavTitle" type="text" value="${f.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editFavSummary" type="text" value="${f.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editFavDesc" rows="3" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${f.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editFavItemType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                    <input id="editFavItemId" type="number" value="${f.item_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editFavBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedFavorite(${f.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedFavorite = function(id) {
    const title = document.getElementById('editFavTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allFavorites.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editFavBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allFavorites[index] = {
        ...allFavorites[index], title,
        summary: document.getElementById('editFavSummary').value.trim(),
        description: document.getElementById('editFavDesc').value.trim(),
        item_type: document.getElementById('editFavItemType').value,
        item_id: parseInt(document.getElementById('editFavItemId').value) || 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterFavoritesByBranch(currentFavBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteFavorite = function(id) {
    if (confirm('حذف این علاقه‌مندی؟')) {
        allFavorites = allFavorites.filter(f => f.id !== id);
        filterFavoritesByBranch(currentFavBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#favoritesTable tbody')) {
            renderFavoritesBranchTabs();
            filterFavoritesByBranch('all');
        }
    }, 200);
})();