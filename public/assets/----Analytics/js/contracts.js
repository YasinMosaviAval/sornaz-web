// ==================== داده نمونه قراردادها ====================
let allContracts = [
    { id: 1, title: "قرارداد شهریه سارا احمدی", party: "سارا احمدی", branchId: 1, branchName: "شعبه مرکزی", type: "هنرجو", start: "۱۴۰۴/۰۴/۰۱", status: "فعال" },
    { id: 2, title: "قرارداد همکاری استاد موسوی", party: "استاد محمد موسوی", branchId: 1, branchName: "شعبه مرکزی", type: "استاد", start: "۱۴۰۳/۰۷/۱۵", status: "فعال" },
    { id: 3, title: "قرارداد شهریه امیر حسینی", party: "امیر حسینی", branchId: 2, branchName: "شعبه ونک", type: "هنرجو", start: "۱۴۰۴/۰۵/۰۱", status: "فعال" },
    { id: 4, title: "قرارداد اجاره تجهیزات صوتی", party: "شرکت صوت برتر", branchId: 1, branchName: "شعبه مرکزی", type: "پیمانکار", start: "۱۴۰۴/۰۱/۲۰", status: "فعال" },
    { id: 5, title: "قرارداد استاد رضایی", party: "استاد علی رضایی", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "استاد", start: "۱۴۰۳/۱۰/۰۱", status: "منقضی" },
    { id: 6, title: "قرارداد شهریه گروهی گیتار", party: "گروه ۸ نفره", branchId: 2, branchName: "شعبه ونک", type: "هنرجو", start: "۱۴۰۴/۰۴/۱۵", status: "فعال" },
    { id: 7, title: "قرارداد خدمات نظافت", party: "شرکت پاک‌رو", branchId: 4, branchName: "شعبه کرج", type: "پیمانکار", start: "۱۴۰۴/۰۲/۰۱", status: "فعال" }
];

let currentContractBranch = 'all';

window.renderContractsBranchTabs = function() {
    const container = document.getElementById('contractsBranchTabs');
    if (!container) return;
    container.querySelectorAll('.contract-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'contract-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterContractsByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterContractsByBranch = function(branchId) {
    currentContractBranch = branchId;

    document.querySelectorAll('.contract-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.contract-branch-tab');
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

    renderContractsTable();
};

window.renderContractsTable = function() {
    const tbody = document.querySelector('#contractsTable tbody');
    if (!tbody) return;

    const list = currentContractBranch === 'all' 
        ? allContracts 
        : allContracts.filter(c => c.branchId == currentContractBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="7" class="py-12 text-center text-gray-400">قراردادی یافت نشد</td></tr>`
        : list.map(c => {
            const statusClass = c.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${c.title}</td>
                <td class="py-4 px-5">${c.party}</td>
                <td class="py-4 px-5">${c.branchName}</td>
                <td class="py-4 px-5">${c.type}</td>
                <td class="py-4 px-5">${c.start}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${c.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewContract(${c.id})" class="text-indigo-600 hover:underline text-sm ml-3">مشاهده</button>
                    <button onclick="deleteContract(${c.id})" class="text-red-500 hover:underline text-sm">حذف</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddContractModal = function() {
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
                <h2 class="text-2xl font-bold">ثبت قرارداد جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">عنوان قرارداد *</label>
                    <input id="contractTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">طرف قرارداد *</label>
                    <input id="contractParty" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="contractBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع قرارداد</label>
                        <select id="contractType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="هنرجو">هنرجو</option>
                            <option value="استاد">استاد</option>
                            <option value="پیمانکار">پیمانکار</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">تاریخ شروع</label>
                        <input id="contractStart" type="text" placeholder="۱۴۰۴/۰۵/۱۵" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveContract()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ثبت قرارداد</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveContract = function() {
    const title = document.getElementById('contractTitle')?.value.trim();
    const party = document.getElementById('contractParty')?.value.trim();
    if (!title || !party) return alert('عنوان و طرف قرارداد الزامی است');

    const branchId = parseInt(document.getElementById('contractBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allContracts.unshift({
        id: Date.now(),
        title,
        party,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        type: document.getElementById('contractType').value,
        start: document.getElementById('contractStart').value || '—',
        status: "فعال"
    });

    filterContractsByBranch(currentContractBranch);
    closeModal();
    alert('✅ قرارداد ثبت شد');
};

window.viewContract = function(id) {
    const c = allContracts.find(x => x.id === id);
    if (c) alert(`قرارداد: ${c.title}\nطرف: ${c.party}\nنوع: ${c.type}\nوضعیت: ${c.status}`);
};

window.deleteContract = function(id) {
    if (confirm('حذف این قرارداد؟')) {
        allContracts = allContracts.filter(c => c.id !== id);
        filterContractsByBranch(currentContractBranch);
    }
};

// Init
setTimeout(() => {
    if (document.getElementById('contractsTable')) {
        renderContractsBranchTabs();
        filterContractsByBranch('all');
    }
}, 200);