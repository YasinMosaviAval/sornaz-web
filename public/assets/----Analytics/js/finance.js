// ==================== داده نمونه تراکنش‌ها ====================
let allTransactions = [
    { id: 1, title: "پرداخت شهریه سارا احمدی", branchId: 1, branchName: "شعبه مرکزی", type: "درآمد", amount: 1200000, date: "۱۴۰۴/۰۵/۱۲", status: "تأیید شده" },
    { id: 2, title: "حقوق استاد موسوی", branchId: 1, branchName: "شعبه مرکزی", type: "هزینه", amount: 8500000, date: "۱۴۰۴/۰۵/۰۵", status: "تأیید شده" },
    { id: 3, title: "پرداخت شهریه امیر حسینی", branchId: 2, branchName: "شعبه ونک", type: "درآمد", amount: 950000, date: "۱۴۰۴/۰۵/۱۰", status: "تأیید شده" },
    { id: 4, title: "خرید تجهیزات صوتی", branchId: 1, branchName: "شعبه مرکزی", type: "هزینه", amount: 3200000, date: "۱۴۰۴/۰۴/۲۸", status: "تأیید شده" },
    { id: 5, title: "پرداخت شهریه زهرا کریمی", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "درآمد", amount: 1100000, date: "۱۴۰۴/۰۵/۰۸", status: "در انتظار" },
    { id: 6, title: "اجاره ماهیانه شعبه", branchId: 4, branchName: "شعبه کرج", type: "هزینه", amount: 15000000, date: "۱۴۰۴/۰۵/۰۱", status: "تأیید شده" },
    { id: 7, title: "پرداخت شهریه گروهی گیتار", branchId: 2, branchName: "شعبه ونک", type: "درآمد", amount: 4500000, date: "۱۴۰۴/۰۵/۰۳", status: "تأیید شده" },
    { id: 8, title: "حقوق استاد رضایی", branchId: 3, branchName: "شعبه سعادت‌آباد", type: "هزینه", amount: 7200000, date: "۱۴۰۴/۰۵/۰۵", status: "تأیید شده" }
];

let currentFinanceBranch = 'all';

window.renderFinanceBranchTabs = function() {
    const container = document.getElementById('financeBranchTabs');
    if (!container) return;
    container.querySelectorAll('.finance-branch-tab:not(:first-child)').forEach(t => t.remove());

    if (typeof allBranches !== 'undefined') {
        allBranches.forEach(b => {
            const btn = document.createElement('button');
            btn.className = 'finance-branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 transition';
            btn.textContent = b.name;
            btn.onclick = () => filterFinanceByBranch(b.id);
            container.appendChild(btn);
        });
    }
};

window.filterFinanceByBranch = function(branchId) {
    currentFinanceBranch = branchId;

    document.querySelectorAll('.finance-branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    const tabs = document.querySelectorAll('.finance-branch-tab');
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

    renderFinanceTable();
    renderFinanceSummary();
};

window.renderFinanceSummary = function() {
    const container = document.getElementById('financeSummaryCards');
    if (!container) return;

    const list = currentFinanceBranch === 'all' 
        ? allTransactions 
        : allTransactions.filter(t => t.branchId == currentFinanceBranch);

    const income = list.filter(t => t.type === 'درآمد').reduce((sum, t) => sum + t.amount, 0);
    const expense = list.filter(t => t.type === 'هزینه').reduce((sum, t) => sum + t.amount, 0);
    const balance = income - expense;

    container.innerHTML = `
        <div class="bg-white rounded-3xl p-6 shadow">
            <p class="text-gray-500 text-sm">کل درآمد</p>
            <p class="text-2xl font-bold text-green-600 mt-2">${income.toLocaleString('fa-IR')} تومان</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow">
            <p class="text-gray-500 text-sm">کل هزینه</p>
            <p class="text-2xl font-bold text-red-600 mt-2">${expense.toLocaleString('fa-IR')} تومان</p>
        </div>
        <div class="bg-white rounded-3xl p-6 shadow">
            <p class="text-gray-500 text-sm">مانده</p>
            <p class="text-2xl font-bold ${balance >= 0 ? 'text-indigo-600' : 'text-red-600'} mt-2">${balance.toLocaleString('fa-IR')} تومان</p>
        </div>
    `;
};

window.renderFinanceTable = function() {
    const tbody = document.querySelector('#financeTable tbody');
    if (!tbody) return;

    const list = currentFinanceBranch === 'all' 
        ? allTransactions 
        : allTransactions.filter(t => t.branchId == currentFinanceBranch);

    tbody.innerHTML = list.length === 0 
        ? `<tr><td colspan="7" class="py-12 text-center text-gray-400">تراکنشی یافت نشد</td></tr>`
        : list.map(t => {
            const typeClass = t.type === 'درآمد' ? 'text-green-600' : 'text-red-600';
            const statusClass = t.status === 'تأیید شده' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700';
            return `
            <tr class="hover:bg-gray-50">
                <td class="py-4 px-5 font-medium">${t.title}</td>
                <td class="py-4 px-5">${t.branchName}</td>
                <td class="py-4 px-5"><span class="${typeClass} font-medium">${t.type}</span></td>
                <td class="py-4 px-5 font-medium">${t.amount.toLocaleString('fa-IR')} تومان</td>
                <td class="py-4 px-5">${t.date}</td>
                <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${t.status}</span></td>
                <td class="py-4 px-5 text-left">
                    <button onclick="viewTransaction(${t.id})" class="text-indigo-600 hover:underline text-sm">جزئیات</button>
                </td>
            </tr>`;
        }).join('');
};

window.openAddTransactionModal = function() {
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
                <h2 class="text-2xl font-bold">ثبت تراکنش جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">شرح تراکنش *</label>
                    <input id="transTitle" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه</label>
                    <select id="transBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">${branchOptions}</select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">نوع</label>
                        <select id="transType" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="درآمد">درآمد</option>
                            <option value="هزینه">هزینه</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">مبلغ (تومان)</label>
                        <input id="transAmount" type="number" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تاریخ</label>
                    <input id="transDate" type="text" placeholder="۱۴۰۴/۰۵/۱۵" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="flex gap-4 pt-2">
                    <button onclick="saveTransaction()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
};

window.saveTransaction = function() {
    const title = document.getElementById('transTitle')?.value.trim();
    const amount = parseInt(document.getElementById('transAmount')?.value);
    if (!title || !amount) return alert('شرح و مبلغ الزامی است');

    const branchId = parseInt(document.getElementById('transBranch').value);
    const branch = allBranches?.find(b => b.id === branchId);

    allTransactions.unshift({
        id: Date.now(),
        title,
        branchId,
        branchName: branch ? branch.name : 'نامشخص',
        type: document.getElementById('transType').value,
        amount,
        date: document.getElementById('transDate').value || '—',
        status: "تأیید شده"
    });

    filterFinanceByBranch(currentFinanceBranch);
    closeModal();
    alert('✅ تراکنش ثبت شد');
};

window.viewTransaction = function(id) {
    const t = allTransactions.find(x => x.id === id);
    if (t) alert(`${t.title}\nمبلغ: ${t.amount.toLocaleString('fa-IR')} تومان\nنوع: ${t.type}`);
};

window.exportFinance = function() {
    alert('خروجی اکسل امور مالی');
};

// Init
setTimeout(() => {
    if (document.getElementById('financeTable')) {
        renderFinanceBranchTabs();
        filterFinanceByBranch('all');
    }
}, 200);

