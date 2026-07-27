// ==================== داده نمونه قوانین زمان‌بندی ====================
let allRules = [
    { id: 1, title: "حداقل زمان لغو کلاس", branchId: 1, branchName: "شعبه مرکزی", type: "لغو", value: "۲۴ ساعت قبل", status: "فعال" },
    { id: 2, title: "حداکثر جلسات جبرانی در ماه", branchId: 1, branchName: "شعبه مرکزی", type: "جبرانی", value: "۲ جلسه", status: "فعال" },
    { id: 3, title: "ساعت شروع رزرو آنلاین", branchId: 2, branchName: "شعبه ونک", type: "رزرو", value: "۰۸:۰۰", status: "فعال" },
    { id: 4, title: "ساعت پایان رزرو آنلاین", branchId: 2, branchName: "شعبه ونک", type: "رزرو", value: "۲۲:۰۰", status: "فعال" },
    { id: 5, title: "حداقل فاصله بین دو کلاس استاد", branchId: 1, branchName: "شعبه مرکزی", type: "زمان‌بندی", value: "۱۵ دقیقه", status: "فعال" },
    { id: 6, title: "حداکثر کلاس روزانه استاد", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "زمان‌بندی", value: "۶ جلسه", status: "فعال" },
    { id: 7, title: "مهلت رزرو کلاس گروهی", branchId: 4, branchName: "شعبه کرج", type: "رزرو", value: "۴۸ ساعت قبل", status: "فعال" },
    { id: 8, title: "قفل خودکار کلاس پس از غیبت", branchId: 1, branchName: "شعبه مرکزی", type: "لغو", value: "۳ غیبت متوالی", status: "غیرفعال" }
];

let currentRuleBranch = 'all';

window.renderRulesBranchTabs = function() {
    const container = document.getElementById('rulesBranchTabs');
    if (!container) return;
    container.querySelectorAll('.rule-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'rule-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterRulesByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterRulesByBranch = function(branchId) {
    currentRuleBranch = branchId;

    document.querySelectorAll('.rule-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.rule-branch-tab');
    if (branchId === 'all') {
        if (tabs[0]) {
            tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
            tabs[0].classList.remove('border-gray-200');
        }
    } else {
        tabs.forEach(tab => {
            const branch = allBranches?.find(b => b.id == branchId);
            if (branch && tab.textContent === branch.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }

    renderRulesTable();
};

window.renderRulesTable = function() {
    const tbody = document.querySelector('#rulesTable tbody');
    if (!tbody) return;

    const list = currentRuleBranch === 'all' 
        ? allRules 
        : allRules.filter(r => r.branchId == currentRuleBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="6" class="py-12 text-center text-gray-400">قانونی یافت نشد</td></tr>`
        : list.map(r => {
            const statusClass = r.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${r.title}</td>
                <td class="py-4 px-5">${r.branchName}</td>
                <td class="py-4 px-5">${r.type}</td>
                <td class="py-4 px-5">${r.value}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${r.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="editRule(${r.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                    <button onclick="deleteRule(${r.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddRuleModal = function() {
    if (!document.getElementById('modalContainer')) {
        alert('modalContainer پیدا نشد!');
        return;
    }

    const branchOptions = (typeof allBranches !== 'undefined' ? allBranches : [])
        .map(b => `<option value="${b.id}">${b.name}</option>`).join('');

    document.getElementById('modalContainer').innerHTML = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target === this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن قانون زمان‌بندی</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان قانون *</label>
                    <input id="ruleTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="ruleBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">نوع قانون</label>
                    <select id="ruleType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="لغو">لغو</option>
                        <option value="جبرانی">جبرانی</option>
                        <option value="رزرو">رزرو</option>
                        <option value="زمان‌بندی">زمان‌بندی</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">مقدار</label>
                    <input id="ruleValue" type="text" placeholder="مثال: ۲۴ ساعت قبل" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">وضعیت</label>
                    <select id="ruleStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        <option value="فعال">فعال</option>
                        <option value="غیرفعال">غیرفعال</option>
                    </select>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveRule()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveRule = function() {
    const title = document.getElementById('ruleTitle')?.value.trim();
    if (!title) return alert('عنوان قانون الزامی است');

    const branchId = parseInt(document.getElementById('ruleBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allRules.unshift({
        id: Date.now(),
        title,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        type: document.getElementById('ruleType').value,
        value: document.getElementById('ruleValue').value || '—',
        status: document.getElementById('ruleStatus').value
    });

    filterRulesByBranch(currentRuleBranch);
    closeModal();
    alert('✅ قانون ثبت شد');
};

window.editRule = function(id) {
    alert('ویرایش قانون');
};

window.deleteRule = function(id) {
    if (confirm('حذف این قانون؟')) {
        allRules = allRules.filter(r => r.id !== id);
        filterRulesByBranch(currentRuleBranch);
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('rulesTable')) {
        renderRulesBranchTabs();
        filterRulesByBranch('all');
    }
}, 200);