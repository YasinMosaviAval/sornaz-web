// ==================== انواع کلاس و وضعیت‌ها ====================
let allClassroomTypes = [];
let allClassroomBranches = [];
const classroomStatuses = ['فعال', 'تعمیر', 'غیرفعال'];

function getBranchesList() {
    return allClassroomBranches;
}

// ==================== نمونه داده — ۴۰ کلاس ====================
let allClassrooms = [];

window.classroomApi=async function(url,data=null,method='POST'){const o={method,credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}};if(data!==null){const token=window.adminCsrfToken||'';o.headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';o.headers['X-CSRF-TOKEN']=token;o.body=new URLSearchParams({_token:token,payload_b64:encodeBranchPayload(data)}).toString();}const r=await fetch(url,o),raw=await r.text();let p;try{p=JSON.parse(raw)}catch(e){throw new Error('پاسخ معتبر JSON دریافت نشد.')}const x=p.data??p;if(!r.ok||x.success===false)throw new Error(x.message||'عملیات ناموفق بود');return x.data??x;};
async function loadClassrooms(){try{const d=await classroomApi('/academy/admin/classrooms',null,'GET');allClassroomBranches=d.branches||[];allClassroomTypes=d.types||[];allClassrooms=d.classrooms||[];filteredClassrooms=allClassrooms.slice();renderClassroomBranchTabs();renderClassroomEquipmentFilter();renderClassroomTypeFilter();renderClassroomCapacityFilter();filterClassrooms();}catch(e){alert(e.message);}}
window.syncClassroomTypeState=function(item,selectNew=false){if(!item?.id)return;const index=allClassroomTypes.findIndex(type=>Number(type.id)===Number(item.id));const normalized={...item,name:item.title||item.name};if(index>=0)allClassroomTypes[index]={...allClassroomTypes[index],...normalized};else allClassroomTypes.push(normalized);document.querySelectorAll('#classroomType,#editClassroomType,[id^="inlineClassroom"][id$="Type"]').forEach(select=>{const selected=selectNew?item.id:select.value;select.innerHTML=allClassroomTypes.map(type=>`<option value="${type.id}" ${String(type.id)===String(selected)?'selected':''}>${type.name}</option>`).join('');if(selectNew)select.value=String(item.id);select.dispatchEvent(new Event('change',{bubbles:true}));});renderClassroomTypeFilter();};
window.addEventListener('classroom-type-saved',event=>window.syncClassroomTypeState(event.detail,true));
window.addEventListener('classroom-type-deleted',event=>{allClassroomTypes=allClassroomTypes.filter(type=>Number(type.id)!==Number(event.detail.id));document.querySelectorAll('#classroomType,#editClassroomType,[id^="inlineClassroom"][id$="Type"]').forEach(select=>{const selected=select.value;select.innerHTML=allClassroomTypes.map(type=>`<option value="${type.id}" ${String(type.id)===String(selected)?'selected':''}>${type.name}</option>`).join('');});renderClassroomTypeFilter();});

// ==================== صفحه‌بندی / مرتب‌سازی / فیلتر ====================
let classroomCurrentPage = 1;
const classroomPerPage = 10;
let filteredClassrooms = [...allClassrooms];
let currentClassroomBranchFilter = 'all';
let editingClassroomRowId = null;
let classroomSortField = '';
let classroomSortDirection = 'asc';

const classroomPdfColumns = [
    { field: 'index', label: 'ردیف' },
    { field: 'name', label: 'نام کلاس' },
    { field: 'typeLabel', label: 'نوع کلاس' },
    { field: 'branchName', label: 'شعبه' },
    { field: 'capacity', label: 'ظرفیت' },
    { field: 'equipment', label: 'تجهیزات' },
    { field: 'status', label: 'وضعیت' }
];

function sortClassroomItems() {
    if (!classroomSortField) return;
    filteredClassrooms.sort((a, b) => {
        let aValue = a[classroomSortField];
        let bValue = b[classroomSortField];
        if (classroomSortField === 'capacity') {
            aValue = Number(aValue);
            bValue = Number(bValue);
        } else if (classroomSortField === 'equipment') {
            aValue = (a.equipment || []).map(e => e.name).join('، ').toLowerCase();
            bValue = (b.equipment || []).map(e => e.name).join('، ').toLowerCase();
        } else {
            aValue = String(aValue || '').toLowerCase();
            bValue = String(bValue || '').toLowerCase();
        }
        if (aValue < bValue) return classroomSortDirection === 'asc' ? -1 : 1;
        if (aValue > bValue) return classroomSortDirection === 'asc' ? 1 : -1;
        return 0;
    });
}

window.updateClassroomSortIcons = async function () {
    const fields = ['name', 'typeLabel', 'branchName', 'capacity', 'equipment', 'status'];
    fields.forEach(field => {
        const icon = document.getElementById(`classroomSortIcon-${field}`);
        if (!icon) return;
        icon.textContent = classroomSortField === field
            ? (classroomSortDirection === 'asc' ? '↑' : '↓')
            : '↕';
    });
};

window.sortClassroomsBy = async function (field) {
    if (classroomSortField === field) {
        classroomSortDirection = classroomSortDirection === 'asc' ? 'desc' : 'asc';
    } else {
        classroomSortField = field;
        classroomSortDirection = 'asc';
    }
    sortClassroomItems();
    renderClassroomsTable(filteredClassrooms);
    updateClassroomSortIcons();
};

// ==================== تب شعبه‌ها ====================
window.renderClassroomBranchTabs = async function () {
    const container = document.getElementById('classroomBranchTabs') || document.getElementById('branchTabs');
    if (!container) return;
    container.querySelectorAll('.branch-tab:not(:first-child)').forEach(t => t.remove());
    getBranchesList().forEach(b => {
        const btn = document.createElement('button');
        const active = currentClassroomBranchFilter == b.id;
        btn.className = `branch-tab px-5 py-2.5 rounded-2xl text-sm font-medium border ${active ? 'bg-indigo-600 text-white border-indigo-600' : 'border-gray-200 hover:bg-gray-50'}`;
        btn.textContent = b.name;
        btn.onclick = () => filterClassroomsByBranch(b.id);
        container.appendChild(btn);
    });
};
window.renderBranchTabs = window.renderClassroomBranchTabs;

window.filterClassroomsByBranch = async function (branchId) {
    currentClassroomBranchFilter = branchId;
    document.querySelectorAll('.branch-tab').forEach(tab => {
        tab.classList.remove('bg-indigo-600', 'text-white', 'border-indigo-600');
        tab.classList.add('border', 'border-gray-200');
    });
    const tabs = document.querySelectorAll('.branch-tab');
    if (branchId === 'all' && tabs[0]) {
        tabs[0].classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
        tabs[0].classList.remove('border-gray-200');
    } else {
        const name = getBranchesList().find(b => b.id == branchId)?.name;
        tabs.forEach(tab => {
            if (tab.textContent === name) {
                tab.classList.add('bg-indigo-600', 'text-white', 'border-indigo-600');
                tab.classList.remove('border-gray-200');
            }
        });
    }
    filterClassrooms();
};

// ==================== فیلتر تجهیزات ====================
window.renderClassroomEquipmentFilter = async function () {
    const select = document.getElementById('filterClassroomEquipment');
    if (!select) return;
    const names = new Set();
    allClassrooms.forEach(c => (c.equipment || []).forEach(e => names.add(e.name)));
    const current = select.value;
    select.innerHTML = '<option value="">همه تجهیزات</option>' +
        [...names].sort().map(n => `<option value="${n}" ${n === current ? 'selected' : ''}>${n}</option>`).join('');
};

window.renderClassroomTypeFilter = async function () {
    const select = document.getElementById('filterClassroomType');
    if (!select) return;
    const current = select.value;
    const types = [...new Set(allClassrooms.map(c => c.typeLabel || c.type).filter(Boolean))].sort();
    select.innerHTML = '<option value="">همه انواع</option>' +
        types.map(t => `<option value="${t}" ${t === current ? 'selected' : ''}>${t}</option>`).join('');
};

window.renderClassroomCapacityFilter = async function () {
    const select = document.getElementById('filterClassroomCapacity');
    if (!select) return;
    const current = select.value;
    const capacities = [...new Set(allClassrooms.map(c => Number(c.capacity || 0)).filter(Boolean))].sort((a, b) => a - b);
    select.innerHTML = '<option value="">همه ظرفیت‌ها</option>' +
        capacities.map(cap => `<option value="${cap}" ${cap === Number(current) ? 'selected' : ''}>${cap} نفر</option>`).join('');
};

window.filterClassrooms = async function () {
    const search = (document.getElementById('classroomSearch')?.value || '').trim().toLowerCase();
    const status = document.getElementById('filterClassroomStatus')?.value || '';
    const equipment = document.getElementById('filterClassroomEquipment')?.value || '';
    const type = document.getElementById('filterClassroomType')?.value || '';
    const capacity = document.getElementById('filterClassroomCapacity')?.value || '';

    filteredClassrooms = allClassrooms.filter(item => {
        const matchBranch = currentClassroomBranchFilter === 'all' || item.branchId == currentClassroomBranchFilter;
        const matchSearch = !search || (item.name || '').toLowerCase().includes(search);
        const matchStatus = !status || item.status === status;
        const matchEquip = !equipment || (item.equipment || []).some(e => e.name === equipment);
        const matchType = !type || (item.typeLabel || item.type) === type;
        const matchCapacity = !capacity || Number(item.capacity || 0) === Number(capacity);
        return matchBranch && matchSearch && matchStatus && matchEquip && matchType && matchCapacity;
    });

    classroomCurrentPage = 1;
    sortClassroomItems();
    renderClassroomsTable(filteredClassrooms);
};

// ==================== رندر جدول ====================
window.renderClassroomsTable = async function (list = filteredClassrooms) {
    const tbody = document.querySelector('#classroomsTable tbody');
    if (!tbody) return;

    const totalPages = Math.ceil(list.length / classroomPerPage) || 1;
    if (classroomCurrentPage > totalPages) classroomCurrentPage = totalPages;

    const start = (classroomCurrentPage - 1) * classroomPerPage;
    const end = start + classroomPerPage;
    const pageItems = list.slice(start, end);

    tbody.innerHTML = '';

    if (!pageItems.length) {
        tbody.innerHTML = window.getClassroomEmptyRowHTML ? window.getClassroomEmptyRowHTML() : '';
    } else {
        pageItems.forEach(item => {
            const statusClass = item.status === 'فعال'
                ? 'bg-green-100 text-green-700'
                : item.status === 'تعمیر'
                    ? 'bg-orange-100 text-orange-700'
                    : 'bg-red-100 text-red-700';
            const tr = document.createElement('tr');
            tr.className = 'hover:bg-gray-50 transition';
            tr.innerHTML = window.getClassroomRowHTML ? window.getClassroomRowHTML(item, statusClass) : '';
            tbody.appendChild(tr);

            if (editingClassroomRowId === item.id) {
                const expandRow = document.createElement('tr');
                expandRow.className = 'bg-gray-50 classroom-inline-expand';
                expandRow.innerHTML = window.getClassroomInlineExpandRowHTML
                    ? window.getClassroomInlineExpandRowHTML(item) : '';
                tbody.appendChild(expandRow);
            }
        });
    }

    updateClassroomPagination(list.length, start, end, totalPages);
    updateClassroomSortIcons();
};

function updateClassroomPagination(total, start, end, totalPages) {
    const info = document.getElementById('classroomPaginationInfo');
    if (info) {
        const from = total === 0 ? 0 : start + 1;
        const to = Math.min(end, total);
        info.textContent = `نمایش ${from} تا ${to} از ${total} کلاس`;
    }

    const pagination = document.getElementById('classroomPaginationButtons');
    if (!pagination) return;

    let html = `
        <button onclick="changeClassroomPage(1)" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${classroomCurrentPage === 1 ? 'disabled' : ''}>اول</button>
        <button onclick="changeClassroomPage(${classroomCurrentPage - 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${classroomCurrentPage === 1 ? 'disabled' : ''}>قبلی</button>
    `;

    let startPage = Math.max(1, classroomCurrentPage - 2);
    let endPage = Math.min(totalPages, startPage + 4);
    if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

    for (let i = startPage; i <= endPage; i++) {
        html += `<button onclick="changeClassroomPage(${i})" class="px-3 py-1.5 rounded-lg ${i === classroomCurrentPage ? 'bg-indigo-600 text-white' : 'border hover:bg-gray-50'}">${i}</button>`;
    }

    html += `
        <button onclick="changeClassroomPage(${classroomCurrentPage + 1})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${classroomCurrentPage === totalPages ? 'disabled' : ''}>بعدی</button>
        <button onclick="changeClassroomPage(${totalPages})" class="px-3 py-1.5 rounded-lg border hover:bg-gray-50 disabled:opacity-40" ${classroomCurrentPage === totalPages ? 'disabled' : ''}>آخر</button>
    `;
    pagination.innerHTML = html;
}

window.changeClassroomPage = async function (page) {
    const totalPages = Math.ceil(filteredClassrooms.length / classroomPerPage) || 1;
    if (page < 1 || page > totalPages) return;
    classroomCurrentPage = page;
    renderClassroomsTable(filteredClassrooms);
};

// ==================== نوع کلاس ====================
window.promptAddClassroomType=()=>window.openClassroomTypeAdmin?.();

// ==================== تجهیزات ====================
window.addClassroomEquipmentField = async function (containerId) {
    const el = document.getElementById(containerId);
    if (!el || !window.getClassroomEquipmentFieldHTML) return;
    el.insertAdjacentHTML('beforeend', window.getClassroomEquipmentFieldHTML());
};

window.removeClassroomEquipmentField = async function (button) {
    const item = button?.closest('.equipment-item');
    if (!item) return;
    const container = item.parentElement;
    if (!container) return;
    item.remove();
};

function readEquipmentFromContainer(containerId) {
    const container = document.getElementById(containerId);
    if (!container) return [];
    return [...container.querySelectorAll('.equipment-item')].map(div => {
        const name = div.querySelector('.equip-name')?.value.trim();
        const qty = parseInt(div.querySelector('.equip-qty')?.value || '1', 10) || 1;
        return name ? { name, qty } : null;
    }).filter(Boolean);
}

function readClassroomForm(prefix) {
    // prefix: '' | 'editClassroom' | 'inlineClassroom{id}'
    const field = (suffix) => document.getElementById(prefix ? `${prefix}${suffix}` : `classroom${suffix}`);
    const name = field('Name')?.value.trim();
    const typeId = parseInt(field('Type')?.value,10);
    const typeObj=allClassroomTypes.find(x=>x.id===typeId);
    const branchId = parseInt(field('Branch')?.value, 10);
    const capacity = parseInt(field('Capacity')?.value || '8', 10) || 8;
    const status = field('Status')?.value || 'فعال';
    const equipContainerId = prefix
        ? (prefix.startsWith('inline') ? `${prefix}EquipmentContainer` : `${prefix}EquipmentContainer`)
        : 'classroomEquipmentContainer';
    // fix for add form
    const equipment = readEquipmentFromContainer(
        prefix === '' ? 'classroomEquipmentContainer' : `${prefix}EquipmentContainer`
    );
    const branch = getBranchesList().find(b => b.id === branchId);
    return {
        name,
        typeId,type:typeObj?.name||'',typeLabel:typeObj?.name||'',
        branchId,
        branchName: branch ? branch.name : '',
        capacity,
        status,
        equipment,summary:field('Summary')?.value.trim()||'',description:field('Description')?.value.trim()||''
    };
}

// ==================== CRUD ====================
window.openAddClassroomModal = async function () {
    if (!document.getElementById('modalContainer')) return alert('modalContainer پیدا نشد');
    document.getElementById('modalContainer').innerHTML = window.getClassroomAddModalHTML
        ? window.getClassroomAddModalHTML() : '';
};

window.saveClassroom = async function () {
    const data = readClassroomForm('');
    if (!data.name) return alert('نام کلاس الزامی است');
    if (!data.branchId) return alert('شعبه الزامی است');

    const created=await classroomApi('/academy/admin/classrooms',data);allClassrooms.unshift(created);
    const branch = getBranchesList().find(b => b.id === data.branchId);
    if (branch) branch.classrooms = (branch.classrooms || 0) + 1;

    renderClassroomEquipmentFilter();
    filterClassrooms();
    closeModal();
    alert('✅ کلاس اضافه شد');
};

window.viewClassroom = async function (id) {
    const item = allClassrooms.find(x => x.id === id);
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getClassroomDetailsModalHTML
        ? window.getClassroomDetailsModalHTML(item) : '';
};

window.editClassroom = async function (id) {
    const item = allClassrooms.find(x => x.id === id);
    if (!item) return;
    document.getElementById('modalContainer').innerHTML = window.getClassroomEditModalHTML
        ? window.getClassroomEditModalHTML(item) : '';
};

window.saveEditedClassroom = async function (id) {
    const data = readClassroomForm('editClassroom');
    if (!data.name) return alert('نام کلاس الزامی است');
    const index = allClassrooms.findIndex(x => x.id === id);
    if (index === -1) return;
    allClassrooms[index] = await classroomApi(`/academy/admin/classrooms/${id}/update`,data);
    editingClassroomRowId = null;
    renderClassroomEquipmentFilter();
    filterClassrooms();
    closeModal();
    alert('✅ تغییرات ذخیره شد');
};

window.toggleClassroomInlineEdit = async function (id) {
    editingClassroomRowId = editingClassroomRowId === id ? null : id;
    renderClassroomsTable(filteredClassrooms);
};

window.saveInlineClassroom = async function (id) {
    const data = readClassroomForm(`inlineClassroom${id}`);
    if (!data.name) return alert('نام کلاس الزامی است');
    const index = allClassrooms.findIndex(x => x.id === id);
    if (index === -1) return;
    allClassrooms[index] = await classroomApi(`/academy/admin/classrooms/${id}/update`,data);
    editingClassroomRowId = null;
    renderClassroomEquipmentFilter();
    filterClassrooms();
    alert('✅ تغییرات با موفقیت ذخیره شد');
};

window.deleteClassroom = async function (id) {
    if (!(await AppDialog.confirmDelete(allClassrooms, id, 'کلاس'))) return;
    await classroomApi(`/academy/admin/classrooms/${id}/delete`,{});
    allClassrooms = allClassrooms.filter(c => c.id !== id);
    if (editingClassroomRowId === id) editingClassroomRowId = null;
    renderClassroomEquipmentFilter();
    filterClassrooms();
};

// ==================== خروجی اکسل ====================
document.addEventListener('DOMContentLoaded',()=>{if(document.getElementById('classroomsTable'))loadClassrooms();});
window.exportClassroomsToExcel = async function () {
    const data = filteredClassrooms.length ? filteredClassrooms : allClassrooms;
    let csv = '\uFEFF';
    csv += 'ردیف,نام کلاس,نوع کلاس,شعبه,ظرفیت,تجهیزات,وضعیت\n';
    data.forEach((item, index) => {
        const equip = (item.equipment || []).map(e => `${e.name}(${e.qty})`).join('؛ ');
        csv += `${index + 1},"${item.name}","${item.typeLabel || item.type}","${item.branchName}",${item.capacity},"${equip}","${item.status}"\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = `کلاس‌ها_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

// ==================== خروجی PDF ====================
window.exportClassroomsToPDF = async function () {
    openClassroomsPDFOptionsModal();
};

window.openClassroomsPDFOptionsModal = async function () {
    document.getElementById('modalContainer').innerHTML = window.getClassroomPDFModalHTML
        ? window.getClassroomPDFModalHTML(classroomPdfColumns) : '';
};

window.generateClassroomsPDF = async function () {
    if (!window.html2canvas) {
        alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
        return;
    }

    const title = document.getElementById('classroomPdfTitle')?.value || 'گزارش کلاس‌های فیزیکی';
    const subtitle = document.getElementById('classroomPdfSubtitle')?.value || 'لیست کلاس‌ها، تجهیزات و وضعیت';
    const footer = document.getElementById('classroomPdfFooter')?.value || '';
    const format = document.getElementById('classroomPdfFormat')?.value || 'a4';
    const orientation = document.getElementById('classroomPdfOrientation')?.value || 'landscape';
    const includeDate = document.getElementById('classroomPdfIncludeDate')?.checked;
    const headerColor = document.getElementById('classroomPdfHeaderColor')?.value || '#eff6ff';
    const evenRowColor = document.getElementById('classroomPdfEvenRowColor')?.value || '#ffffff';
    const oddRowColor = document.getElementById('classroomPdfOddRowColor')?.value || '#f8fafc';
    const selectedColumns = classroomPdfColumns.filter(col =>
        document.getElementById(`classroomPdfCol-${col.field}`)?.checked
    );
    const date = new Date().toLocaleDateString('fa-IR');
    const data = filteredClassrooms.length ? filteredClassrooms : allClassrooms;

    if (!selectedColumns.length) {
        alert('لطفاً حداقل یک ستون برای خروجی PDF انتخاب کنید.');
        return;
    }

    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(data.length / rowsPerPage));
    const canvasPages = [];

    for (let pageIndex = 0; pageIndex < totalPages; pageIndex++) {
        const pageRows = data.slice(pageIndex * rowsPerPage, (pageIndex + 1) * rowsPerPage);
        const pageWrapper = document.createElement('div');
        pageWrapper.style.direction = 'rtl';
        pageWrapper.style.position = 'fixed';
        pageWrapper.style.top = '-9999px';
        pageWrapper.style.left = '-9999px';
        pageWrapper.style.width = orientation === 'portrait' ? '900px' : '1400px';
        pageWrapper.style.padding = pageIndex === 0 ? '20px 30px 30px' : '30px';
        pageWrapper.style.backgroundColor = '#ffffff';
        pageWrapper.style.fontFamily = 'Vazirmatn, Tahoma, sans-serif';
        pageWrapper.innerHTML = window.getClassroomPDFPageHTML
            ? window.getClassroomPDFPageHTML(pageIndex + 1, pageRows, pageIndex === 0, {
                title, subtitle, footer, includeDate, date,
                headerColor, evenRowColor, oddRowColor,
                selectedColumns, rowsPerPage, totalPages
            }) : '';
        document.body.appendChild(pageWrapper);
        const canvas = await html2canvas(pageWrapper, {
            scale: 2, useCORS: true, backgroundColor: '#ffffff', scrollY: -window.scrollY
        });
        canvasPages.push(canvas);
        pageWrapper.remove();
    }

    const doc = new window.jspdf.jsPDF({ orientation, unit: 'pt', format });
    const pageWidth = doc.internal.pageSize.getWidth();
    const margin = 20;
    const imgWidth = pageWidth - margin * 2;

    canvasPages.forEach((canvas, index) => {
        if (index > 0) doc.addPage();
        const imgHeight = (canvas.height * imgWidth) / canvas.width;
        doc.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, imgWidth, imgHeight);
    });

    doc.save(`کلاس‌ها_${date}.pdf`);
    closeModal();
};

// ==================== Init ====================
(function initClassrooms() {
    setTimeout(() => {
        if (document.querySelector('#classroomsTable tbody')) {
            renderClassroomBranchTabs();
            renderClassroomEquipmentFilter();
            renderClassroomTypeFilter();
            renderClassroomCapacityFilter();
            filterClassrooms();
        }
    }, 150);
})();
