// ==================== داده کلاس‌ها ====================
let allClassrooms = [
    { id: 1, name: "کلاس پیانو ۱", branchId: 1, branchName: "شعبه مرکزی", capacity: 8, equipment: "۲ پیانو، صندلی، تخته", status: "فعال" },
    { id: 2, name: "کلاس گیتار A", branchId: 1, branchName: "شعبه مرکزی", capacity: 10, equipment: "گیتار، آمپلی‌فایر، میکروفون", status: "فعال" },
    { id: 3, name: "سالن تمرین گروهی", branchId: 1, branchName: "شعبه مرکزی", capacity: 20, equipment: "پیانو، درام، سیستم صوتی", status: "فعال" },
    { id: 4, name: "کلاس ویولن", branchId: 2, branchName: "شعبه ونک", capacity: 6, equipment: "ویولن، پایه نت", status: "فعال" },
    { id: 5, name: "کلاس آواز", branchId: 2, branchName: "شعبه ونک", capacity: 8, equipment: "پیانو، میکروفون، آینه", status: "فعال" },
    { id: 6, name: "کلاس پیانو ۲", branchId: 3, branchName: "شعبه سعادت‌آباد", capacity: 6, equipment: "پیانو دیجیتال، هدفون", status: "فعال" },
    { id: 7, name: "کلاس درام", branchId: 3, branchName: "شعبه سعادت‌آباد", capacity: 4, equipment: "درام ست، پد تمرین", status: "تعمیر" },
    { id: 8, name: "کلاس عمومی", branchId: 4, branchName: "شعبه کرج", capacity: 12, equipment: "پیانو، وایت‌برد", status: "فعال" }
];

let currentBranchFilter = 'all';

// ==================== ساخت تب‌های شعبه ====================
window.renderBranchTabs = function() {
    const container = document.getElementById('branchTabs');
    if (!container) return;

    // دکمه همه شعبه‌ها از قبل در HTML هست، فقط شعبه‌ها را اضافه می‌کنیم
    const existingTabs = container.querySelectorAll('.branch-tab:not(:first-child)');
    existingTabs.forEach(t => t.remove());

    allBranches.forEach(b => {
        const btn = document.createElement('button');
        btn.className = `branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border border-gray-200 hover:bg-gray-50 ${currentBranchFilter == b.id ? 'bg-indigo-600 text-white border-indigo-600' : ''}`;
        btn.textContent = b.name;
        btn.onclick = () => filterClassroomsByBranch(b.id);
        container.appendChild(btn);
    });
};

// ==================== فیلتر بر اساس شعبه ====================
window.filterClassroomsByBranch = function(branchId) {
    currentBranchFilter = branchId;

    // به‌روزرسانی استایل تب‌ها
    document.querySelectorAll('.branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });

    // فعال کردن تب انتخاب‌شده
    const tabs = document.querySelectorAll('.branch-tab');
    if (branchId === 'all') {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        // پیدا کردن تب مربوطه
        tabs.forEach(tab => {
            if (tab.textContent === allBranches.find(b => b.id == branchId)?.name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }

    renderClassroomsTable();
};

// ==================== رندر جدول کلاس‌ها ====================
window.renderClassroomsTable = function() {
    const tbody = document.querySelector('#classroomsTable tbody');
    if (!tbody) return;

    let list = allClassrooms;
    if (currentBranchFilter !== 'all') {
        list = allClassrooms.filter(c => c.branchId == currentBranchFilter);
    }

    tbody.innerHTML = '';

    if (list.length === 0) {
        tbody.innerHTML = `<tr><td colspan="6" class="py-12 text-center text-gray-400">کلاسی یافت نشد</td></tr>`;
        return;
    }

    list.forEach(c => {
        const statusClass = c.status === 'فعال' ? 'bg-green-100 text-green-700' : 'bg-orange-100 text-orange-700';
        const tr = document.createElement('tr');
        tr.className = "hover:bg-gray-50";
        tr.innerHTML = `
            <td class="py-4 px-5 font-medium">${c.name}</td>
            <td class="py-4 px-5">${c.branchName}</td>
            <td class="py-4 px-5">${c.capacity} نفر</td>
            <td class="py-4 px-5 text-sm text-gray-600">${c.equipment}</td>
            <td class="py-4 px-5"><span class="px-3 py-1 rounded-full text-xs ${statusClass}">${c.status}</span></td>
            <td class="py-4 px-5 text-left">
                <button onclick="editClassroom(${c.id})" class="text-indigo-600 hover:underline text-sm ml-3">ویرایش</button>
                <button onclick="deleteClassroom(${c.id})" class="text-red-500 hover:underline text-sm">حذف</button>
            </td>
        `;
        tbody.appendChild(tr);
    });
};

// ==================== افزودن کلاس ====================
window.openAddClassroomModal = function() {
    const branchOptions = allBranches.map(b => `<option value="${b.id}">${b.name}</option>`).join('');
    
    const html = `
    <div class="fixed inset-0 bg-black/60 flex items-center justify-center z-50 p-4" onclick="if(event.target===this) closeModal()">
        <div class="bg-white rounded-3xl w-full max-w-lg shadow-2xl" onclick="event.stopPropagation()">
            <div class="px-8 py-5 border-b flex justify-between items-center">
                <h2 class="text-2xl font-bold">افزودن کلاس جدید</h2>
                <button onclick="closeModal()" class="text-3xl text-gray-300">×</button>
            </div>
            <div class="p-8 space-y-5">
                <div>
                    <label class="block text-sm font-medium mb-2">نام کلاس *</label>
                    <input id="classroomName" type="text" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">شعبه *</label>
                    <select id="classroomBranch" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                        ${branchOptions}
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-2">ظرفیت</label>
                        <input id="classroomCapacity" type="number" value="8" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">وضعیت</label>
                        <select id="classroomStatus" class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                            <option value="فعال">فعال</option>
                            <option value="تعمیر">تعمیر</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-2">تجهیزات</label>
                    <input id="classroomEquipment" type="text" placeholder="پیانو، صندلی، ..." class="w-full border border-gray-300 rounded-2xl py-3.5 px-5">
                </div>
                <div class="flex gap-4 pt-4">
                    <button onclick="saveClassroom()" class="flex-1 bg-indigo-600 text-white py-3.5 rounded-2xl">ذخیره</button>
                    <button onclick="closeModal()" class="flex-1 border py-3.5 rounded-2xl">انصراف</button>
                </div>
            </div>
        </div>
    </div>`;
    document.getElementById('modalContainer').innerHTML = html;
};

window.saveClassroom = function() {
    const name = document.getElementById('classroomName')?.value.trim();
    const branchId = parseInt(document.getElementById('classroomBranch').value);
    if (!name) return alert('نام کلاس الزامی است');

    const branch = allBranches.find(b => b.id === branchId);
    
    allClassrooms.push({
        id: Date.now(),
        name,
        branchId,
        branchName: branch ? branch.name : '',
        capacity: parseInt(document.getElementById('classroomCapacity').value) || 8,
        equipment: document.getElementById('classroomEquipment').value || '—',
        status: document.getElementById('classroomStatus').value
    });

    // افزایش تعداد کلاس شعبه
    if (branch) branch.classrooms++;

    renderClassroomsTable();
    closeModal();
    alert('✅ کلاس اضافه شد');
};

window.deleteClassroom = function(id) {
    if (!confirm('آیا از حذف این کلاس مطمئن هستید؟')) return;
    allClassrooms = allClassrooms.filter(c => c.id !== id);
    renderClassroomsTable();
};

window.editClassroom = function(id) {
    alert('ویرایش کلاس (مشابه بقیه بخش‌ها قابل پیاده‌سازی است)');
};

// Init
(function() {
    setTimeout(() => {
        if (document.getElementById('classroomsTable')) {
            renderBranchTabs();
            renderClassroomsTable();
        }
    }, 150);
})();