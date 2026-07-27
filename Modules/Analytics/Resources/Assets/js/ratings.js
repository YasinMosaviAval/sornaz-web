const ratingItemTypes = {
    user: 'کاربر', academy: 'آموزشگاه', branch: 'شعبه', course: 'دوره',
    post: 'پست', lesson: 'درس', instrument: 'ساز'
};

let allRatings = [
    { id: 1, title: "نظر درباره دوره پیانو", summary: "رضایت بالا", description: "تدریس عالی و منظم.", item_type: "course", item_id: 101, rating: 5, review: "بسیار راضی هستم از کیفیت کلاس‌ها.", is_private: 0, is_anonymous: 0, user_id: 1, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 2, title: "امتیاز به استاد موسوی", summary: "استاد حرفه‌ای", description: "", item_type: "user", item_id: 55, rating: 4, review: "صبور و دقیق.", is_private: 0, is_anonymous: 1, user_id: 2, branchId: 1, branchName: "شعبه مرکزی" },
    { id: 3, title: "نظر درباره شعبه ونک", summary: "محیط خوب", description: "فضای مناسب برای تمرین.", item_type: "branch", item_id: 2, rating: 5, review: "عالی.", is_private: 1, is_anonymous: 0, user_id: 3, branchId: 2, branchName: "شعبه ونک" }
];
let currentRatingBranch = 'all';

window.renderRatingsBranchTabs = function() {
    const container = document.getElementById('ratingsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.rating-branch-tab:not(:first-child)').forEach(t => t.remove());
    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'rating-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50';
            btn.textContent = b.name;
            btn.onclick = () => filterRatingsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterRatingsByBranch = function(branchId) {
    currentRatingBranch = branchId;
    document.querySelectorAll('.rating-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.rating-branch-tab');
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
    renderRatingsTable();
};

window.renderRatingsTable = function() {
    const tbody = document.querySelector('#ratingsTable tbody');
    if (!tbody) return;
    const list = currentRatingBranch === 'all' ? allRatings : allRatings.filter(r => r.branchId == currentRatingBranch);
    tbody.innerHTML = list.length === 0
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">رتبه‌بندی‌ای یافت نشد</td></tr>`
        : list.map(r => {
            const stars = '★'.repeat(r.rating || 0) + '☆'.repeat(5 - (r.rating || 0));
            const flags = [r.is_anonymous ? 'ناشناس' : '', r.is_private ? 'خصوصی' : ''].filter(Boolean).join(' / ') || '—';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${r.title}</td>
                <td class="py-4 px-5">${ratingItemTypes[r.item_type] || r.item_type}</td>
                <td class="py-4 px-5 text-amber-500">${stars} (${r.rating})</td>
                <td class="py-4 px-5 text-sm text-gray-500">${flags}</td>
                <td class="py-4 px-5">${r.branchName}</td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewRating(${r.id})" class="text-indigo-600 text-sm ml-3">جزئیات</button>
                    <button onclick="editRating(${r.id})" class="text-indigo-600 text-sm ml-3">ویرایش</button>
                    <button onclick="deleteRating(${r.id})" class="text-red-500 text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddRatingModal = function() {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد!');
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    const typeOptions = Object.entries(ratingItemTypes).map(([k, v]) => `<option value="${k}">${v}</option>`).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن رتبه‌بندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="ratingTitle" type="text" placeholder="عنوان *" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="ratingSummary" type="text" placeholder="خلاصه" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="ratingDesc" rows="2" placeholder="توضیحات" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="ratingItemType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                    <input id="ratingItemId" type="number" placeholder="شناسه آیتم" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">امتیاز (۱ تا ۵)</label>
                    <select id="ratingValue" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="5">۵</option><option value="4">۴</option><option value="3">۳</option><option value="2">۲</option><option value="1">۱</option>
                    </select>
                </div>
                <textarea id="ratingReview" rows="2" placeholder="متن نظر (review)" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5"></textarea>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="ratingPrivate"> خصوصی</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="ratingAnonymous"> ناشناس</label>
                </div>
                <select id="ratingBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveRating()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveRating = function() {
    const title = document.getElementById('ratingTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const branchId = parseInt(document.getElementById('ratingBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allRatings.unshift({
        id: Date.now(), title,
        summary: document.getElementById('ratingSummary').value.trim(),
        description: document.getElementById('ratingDesc').value.trim(),
        item_type: document.getElementById('ratingItemType').value,
        item_id: parseInt(document.getElementById('ratingItemId').value) || 0,
        rating: parseInt(document.getElementById('ratingValue').value) || 5,
        review: document.getElementById('ratingReview').value.trim(),
        is_private: document.getElementById('ratingPrivate').checked ? 1 : 0,
        is_anonymous: document.getElementById('ratingAnonymous').checked ? 1 : 0,
        user_id: 1, branchId, branchName: branch ? branch.name : 'نامشخص'
    });
    filterRatingsByBranch(currentRatingBranch);
    closeModal();
    alert('✅ ثبت شد');
};

window.viewRating = function(id) {
    const r = allRatings.find(x => x.id === id);
    if (!r) return;
    const stars = '★'.repeat(r.rating || 0) + '☆'.repeat(5 - (r.rating || 0));
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold">${r.title}</h2>
                    <p class="text-amber-500 text-lg mt-1">${stars}</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="editRating(${r.id})" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm">ویرایش</button>
                    <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
                </div>
            </div>
            <div class="p-8 space-y-4">
                ${r.summary ? `<p class="text-indigo-600 font-medium">${r.summary}</p>` : ''}
                ${r.description ? `<p class="text-gray-600">${r.description}</p>` : ''}
                ${r.review ? `<p class="text-sm bg-gray-50 p-4 rounded-2xl">${r.review}</p>` : ''}
                <div class="text-sm space-y-2">
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">نوع</span><span>${ratingItemTypes[r.item_type]}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شناسه آیتم</span><span>${r.item_id}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">خصوصی</span><span>${r.is_private ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">ناشناس</span><span>${r.is_anonymous ? 'بله' : 'خیر'}</span></div>
                    <div class="flex justify-between border-b pb-2"><span class="text-gray-500">شعبه</span><span>${r.branchName}</span></div>
                </div>
            </div>
        </div>
    </div>`;
};

window.editRating = function(id) {
    const r = allRatings.find(x => x.id === id);
    if (!r) return;
    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : []).map(b =>
        `<option value="${b.id}" ${b.id === r.branchId ? 'selected' : ''}>${b.name}</option>`
    ).join('');
    const typeOptions = Object.entries(ratingItemTypes).map(([k, v]) =>
        `<option value="${k}" ${r.item_type === k ? 'selected' : ''}>${v}</option>`
    ).join('');
    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">ویرایش رتبه‌بندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <input id="editRatingTitle" type="text" value="${r.title}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <input id="editRatingSummary" type="text" value="${r.summary || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                <textarea id="editRatingDesc" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${r.description || ''}</textarea>
                <div class="grid grid-cols-2 gap-4">
                    <select id="editRatingItemType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${typeOptions}</select>
                    <input id="editRatingItemId" type="number" value="${r.item_id || ''}" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <select id="editRatingValue" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    ${[5,4,3,2,1].map(n => `<option value="${n}" ${r.rating===n?'selected':''}>${n}</option>`).join('')}
                </select>
                <textarea id="editRatingReview" rows="2" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${r.review || ''}</textarea>
                <div class="flex gap-6">
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editRatingPrivate" ${r.is_private?'checked':''}> خصوصی</label>
                    <label class="flex items-center gap-2 text-sm"><input type="checkbox" id="editRatingAnonymous" ${r.is_anonymous?'checked':''}> ناشناس</label>
                </div>
                <select id="editRatingBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                <div class="flex gap-4">
                    <button onclick="saveEditedRating(${r.id})" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveEditedRating = function(id) {
    const title = document.getElementById('editRatingTitle')?.value.trim();
    if (!title) return alert('عنوان الزامی است');
    const index = allRatings.findIndex(x => x.id === id);
    if (index === -1) return;
    const branchId = parseInt(document.getElementById('editRatingBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);
    allRatings[index] = {
        ...allRatings[index], title,
        summary: document.getElementById('editRatingSummary').value.trim(),
        description: document.getElementById('editRatingDesc').value.trim(),
        item_type: document.getElementById('editRatingItemType').value,
        item_id: parseInt(document.getElementById('editRatingItemId').value) || 0,
        rating: parseInt(document.getElementById('editRatingValue').value) || 5,
        review: document.getElementById('editRatingReview').value.trim(),
        is_private: document.getElementById('editRatingPrivate').checked ? 1 : 0,
        is_anonymous: document.getElementById('editRatingAnonymous').checked ? 1 : 0,
        branchId, branchName: branch ? branch.name : 'نامشخص'
    };
    filterRatingsByBranch(currentRatingBranch);
    closeModal();
    alert('✅ ذخیره شد');
};

window.deleteRating = function(id) {
    if (confirm('حذف این رتبه‌بندی؟')) {
        allRatings = allRatings.filter(r => r.id !== id);
        filterRatingsByBranch(currentRatingBranch);
    }
};

(function() {
    setTimeout(() => {
        if (document.querySelector('#ratingsTable tbody')) {
            renderRatingsBranchTabs();
            filterRatingsByBranch('all');
        }
    }, 200);
})();