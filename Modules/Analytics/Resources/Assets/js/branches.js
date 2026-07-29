// ==================== داده‌ها ====================
let allBranchTypes = [
    { id: 1, name: 'موسیقی' }, { id: 2, name: 'ادبیات' }, { id: 3, name: 'شعر' },
    { id: 4, name: 'نقاشی' }, { id: 5, name: 'خیاطی' }, { id: 6, name: 'سایر' }
];

const branchPhysicalTypes = [
    { value: 'physical', label: 'فیزیکی' },
    { value: 'online', label: 'آنلاین' },
    { value: 'hybrid', label: 'هیبرید' }
];
const iranProvinces = ['تهران', 'البرز', 'اصفهان', 'فارس', 'خراسان رضوی', 'آذربایجان شرقی', 'آذربایجان غربی', 'خوزستان', 'مازندران', 'گیلان', 'کرمان', 'سیستان و بلوچستان', 'همدان', 'کرمانشاه', 'لرستان', 'کردستان', 'یزد', 'مرکزی', 'قم', 'قزوین', 'زنجان', 'اردبیل', 'بوشهر', 'هرمزگان', 'چهارمحال و بختیاری', 'کهگیلویه و بویراحمد', 'ایلام', 'سمنان', 'گلستان', 'خراسان شمالی', 'خراسان جنوبی'];
const tehranCities = ['تهران', 'شمیرانات', 'ری', 'اسلامشهر', 'شهریار', 'قدس', 'ملارد', 'پردیس', 'دماوند', 'فیروزکوه', 'ورامین', 'پاکدشت', 'رباط‌کریم'];
const platformOptions = [{ value: 'instagram', label: 'اینستاگرام' }, { value: 'whats-app', label: 'واتساپ' }, { value: 'youtube', label: 'یوتیوب' }, { value: 'spotify', label: 'اسپاتیفای' }, { value: 'soundcloud', label: 'ساوندکلود' }, { value: 'telegram', label: 'تلگرام' }, { value: 'linkedin', label: 'لینکدین' }, { value: 'x', label: 'ایکس (توییتر)' }, { value: 'website', label: 'وب‌سایت' }, { value: 'zoom', label: 'زوم' }, { value: 'google-meet', label: 'گوگل میت' }, { value: 'skype', label: 'اسکایپ' }, { value: 'custom', label: 'سفارشی' }, { value: 'other', label: 'سایر' }];
const priorityOptions = [{ value: 'primary', label: 'اصلی' }, { value: 'secondary', label: 'فرعی' }, { value: 'emergency', label: 'اضطراری' }, { value: 'ledger', label: 'دفتر' }, { value: 'support', label: 'پشتیبانی' }, { value: 'other', label: 'سایر' }];

let allBranches = [
    { id: 1, name: 'شعبه مرکزی', type: 'موسیقی', physical_type: 'hybrid', is_main: true, slogan: 'آموزش با عشق، اجرا با افتخار', bio: 'شعبه اصلی آموزشگاه با تمرکز بر سازهای کلاسیک و ایرانی.', manager: 'آقای رضایی', classrooms: 8, status: 'فعال', phones: [{ number: '۰۲۱-۸۸۷۷۶۶۵۵', priority: 'primary', is_main: true }, { number: '۰۹۱۲۱۲۳۴۵۶۷', priority: 'secondary', is_main: false }], links: [{ title: 'کلاس آنلاین', url: 'https://meet.example.com/central', mode: 'social', platform: 'google-meet', priority: 'primary', is_main: true }], addresses: [{ province: 'تهران', city: 'تهران', address: 'خیابان ولیعصر، پلاک ۱۲۳', postal_code: '۱۴۱۵۷۴۳۴۵۶', lat: '35.7219', lng: '51.3347', is_main: true }] },
    { id: 2, name: 'شعبه ونک', type: 'موسیقی', physical_type: 'physical', is_main: false, slogan: 'صدای آینده از اینجا شروع می‌شود', bio: 'شعبه تخصصی گیتار و آواز.', manager: 'خانم موسوی', classrooms: 5, status: 'فعال', phones: [{ number: '۰۲۱-۸۸۶۶۵۵۴۴', priority: 'primary', is_main: true }], links: [{ title: 'کلاس آنلاین', url: 'https://meet.example.com/vanak', mode: 'social', platform: 'zoom', priority: 'primary', is_main: true }], addresses: [{ province: 'تهران', city: 'تهران', address: 'میدان ونک، برج آسمان، طبقه ۳', postal_code: '۱۹۹۴۷۶۳۴۵۶', lat: '35.7575', lng: '51.4100', is_main: true }] },
    { id: 3, name: 'شعبه سعادت‌آباد', type: 'نقاشی', physical_type: 'physical', is_main: false, slogan: 'رنگ‌ها را زندگی کنید', bio: 'شعبه هنرهای تجسمی.', manager: 'آقای بهرامی', classrooms: 6, status: 'فعال', phones: [{ number: '۰۲۱-۲۲۱۱۰۰۳۳', priority: 'primary', is_main: true }], links: [], addresses: [{ province: 'تهران', city: 'تهران', address: 'سعادت‌آباد، میدان کاج', postal_code: '', lat: '', lng: '', is_main: true }] },
    { id: 4, name: 'شعبه کرج', type: 'خیاطی', physical_type: 'online', is_main: false, slogan: 'طراحی و دوخت حرفه‌ای', bio: 'آموزش خیاطی در کرج.', manager: 'خانم کریمی', classrooms: 4, status: 'فعال', phones: [{ number: '۰۲۶-۳۴۵۶۷۸۹۰', priority: 'primary', is_main: true }], links: [{ title: 'واتساپ پشتیبانی', url: 'https://wa.me/98912xxxxxxx', mode: 'social', platform: 'whats-app', priority: 'support', is_main: true }], addresses: [{ province: 'البرز', city: 'کرج', address: 'مهرویلا، خیابان شهید بهشتی', postal_code: '', lat: '35.8400', lng: '50.9391', is_main: true }] }
];
let filteredBranches = [...allBranches];
const branchPdfColumns = [
    { field: 'index', label: 'ردیف' }, { field: 'name', label: 'نام شعبه' },
    { field: 'type', label: 'نوع آموزشی' }, { field: 'physicalType', label: 'نوع ارائه' },
    { field: 'main', label: 'اصلی' }, { field: 'manager', label: 'مدیر' },
    { field: 'status', label: 'وضعیت' }, { field: 'classrooms', label: 'تعداد کلاس' }
];

// ==================== گزینه‌ها و فیلترها ====================
function renderOptions(items, selected, valueKey = 'value', labelKey = 'label') {
    return items.map(item => `<option value="${item[valueKey]}" ${item[valueKey] === selected ? 'selected' : ''}>${item[labelKey]}</option>`).join('');
}
function getBranchTypeOptions(selected = '') { return renderOptions(allBranchTypes, selected, 'name', 'name'); }
function getPriorityOptions(selected = 'primary') { return renderOptions(priorityOptions, selected); }
function getPlatformOptions(selected = 'website') { return renderOptions(platformOptions, selected); }
function getProvinceOptions(selected = 'تهران') { return iranProvinces.map(value => `<option value="${value}" ${value === selected ? 'selected' : ''}>${value}</option>`).join(''); }
function getCityOptions(selected = 'تهران') { return tehranCities.map(value => `<option value="${value}" ${value === selected ? 'selected' : ''}>${value}</option>`).join(''); }
window.getBranchPhysicalTypeOptions = selected => renderOptions(branchPhysicalTypes, selected || 'physical');
window.getBranchPhysicalTypeLabel = value => branchPhysicalTypes.find(item => item.value === value)?.label || 'فیزیکی';

window.renderBranchFilters = function () {
    const typeSelect = document.getElementById('filterBranchType');
    const physicalSelect = document.getElementById('filterBranchPhysicalType');
    if (typeSelect) typeSelect.innerHTML = '<option value="">همه انواع آموزشی</option>' + getBranchTypeOptions(typeSelect.value);
    if (physicalSelect) physicalSelect.innerHTML = '<option value="">همه انواع ارائه</option>' + window.getBranchPhysicalTypeOptions(physicalSelect.value);
};
window.renderBranchTypeFilter = window.renderBranchFilters;
window.promptAddBranchType = function () {
    const name = prompt('نام نوع شعبه جدید را وارد کنید:')?.trim();
    if (!name) return;
    if (allBranchTypes.some(item => item.name === name)) return alert('این نوع قبلاً وجود دارد');
    allBranchTypes.push({ id: Date.now(), name });
    window.renderBranchFilters();
    ['branchType', 'editBranchType'].forEach(id => { const select = document.getElementById(id); if (select) select.innerHTML = getBranchTypeOptions(select.value); });
};

// ==================== رندر و عملیات ====================
window.renderBranches = function (list = filteredBranches) {
    const container = document.getElementById('branchesCards');
    if (!container) return;
    container.innerHTML = list.length ? list.map(branch => window.getBranchCardHTML(branch)).join('') : window.getBranchEmptyHTML();
};
window.filterBranches = function () {
    const search = (document.getElementById('branchSearch')?.value || '').trim().toLowerCase();
    const type = document.getElementById('filterBranchType')?.value || '';
    const physicalType = document.getElementById('filterBranchPhysicalType')?.value || '';
    filteredBranches = allBranches.filter(branch => (!search || branch.name.toLowerCase().includes(search) || (branch.manager || '').toLowerCase().includes(search)) && (!type || branch.type === type) && (!physicalType || branch.physical_type === physicalType));
    window.renderBranches(filteredBranches);
};
window.exportBranchesToExcel = function () {
    const rows = filteredBranches.length ? filteredBranches : allBranches;
    const csv = '\uFEFFردیف,نام,نوع آموزشی,نوع ارائه,شعبه اصلی,مدیر,وضعیت,تعداد کلاس\n' + rows.map((branch, index) => `${index + 1},"${branch.name}","${branch.type}","${window.getBranchPhysicalTypeLabel(branch.physical_type)}","${branch.is_main ? 'بله' : 'خیر'}","${branch.manager || ''}","${branch.status}",${branch.classrooms || 0}`).join('\n');
    const link = document.createElement('a');
    link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
    link.download = `شعبه‌ها_${new Date().toLocaleDateString('fa-IR')}.csv`;
    link.click();
};

window.openBranchesPDFOptionsModal = function () {
    document.getElementById('modalContainer').innerHTML = window.getBranchPDFModalHTML(branchPdfColumns);
};
window.generateBranchesPDF = async function () {
    if (!window.html2canvas || !window.jspdf) return alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
    const getValue = (id, fallback = '') => document.getElementById(id)?.value || fallback;
    const title = getValue('branchPdfTitle', 'گزارش شعبه‌های آموزشگاه');
    const subtitle = getValue('branchPdfSubtitle', 'فهرست شعبه‌ها و اطلاعات پایه');
    const footer = getValue('branchPdfFooter');
    const format = getValue('branchPdfFormat', 'a4');
    const orientation = getValue('branchPdfOrientation', 'landscape');
    const columns = branchPdfColumns.filter(column => document.getElementById(`branchPdfCol-${column.field}`)?.checked);
    if (!columns.length) return alert('لطفاً حداقل یک ستون برای خروجی PDF انتخاب کنید.');
    const rows = (filteredBranches.length ? filteredBranches : allBranches).map(branch => ({ ...branch, physicalType: window.getBranchPhysicalTypeLabel(branch.physical_type), main: branch.is_main ? 'بله' : 'خیر' }));
    const rowsPerPage = orientation === 'portrait' ? 18 : 15;
    const totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
    const options = { title, subtitle, footer, format, orientation, columns, rowsPerPage, totalPages, date: new Date().toLocaleDateString('fa-IR'), includeDate: Boolean(document.getElementById('branchPdfIncludeDate')?.checked), headerColor: getValue('branchPdfHeaderColor', '#eff6ff'), evenRowColor: getValue('branchPdfEvenRowColor', '#ffffff'), oddRowColor: getValue('branchPdfOddRowColor', '#f8fafc') };
    const canvases = [];
    for (let page = 0; page < totalPages; page++) {
        const wrapper = document.createElement('div');
        wrapper.style.cssText = `direction:rtl;position:fixed;top:-9999px;left:-9999px;width:${orientation === 'portrait' ? '900px' : '1400px'};padding:30px;background:#fff;font-family:Vazirmatn, Tahoma, sans-serif`;
        wrapper.innerHTML = window.getBranchPDFPageHTML(page + 1, rows.slice(page * rowsPerPage, (page + 1) * rowsPerPage), page === 0, options);
        document.body.appendChild(wrapper);
        canvases.push(await window.html2canvas(wrapper, { scale: 2, useCORS: true, backgroundColor: '#ffffff', scrollY: -window.scrollY }));
        wrapper.remove();
    }
    const documentPdf = new window.jspdf.jsPDF({ orientation, unit: 'pt', format });
    const margin = 20;
    canvases.forEach((canvas, index) => {
        if (index) documentPdf.addPage();
        const width = documentPdf.internal.pageSize.getWidth() - margin * 2;
        documentPdf.addImage(canvas.toDataURL('image/png'), 'PNG', margin, margin, width, canvas.height * width / canvas.width);
    });
    documentPdf.save(`شعبه‌ها_${options.date}.pdf`);
    closeModal();
};

window.addPhoneField = function (containerId = 'phonesContainer') { document.getElementById(containerId)?.insertAdjacentHTML('beforeend', window.getBranchPhoneFieldHTML()); };
window.addLinkField = function (containerId = 'linksContainer') { document.getElementById(containerId)?.insertAdjacentHTML('beforeend', window.getBranchLinkFieldHTML()); };
window.addAddressField = function (containerId = 'addressesContainer') { document.getElementById(containerId)?.insertAdjacentHTML('beforeend', window.getBranchAddressFieldHTML()); };
window.enforceSingleBranchPrimary = function (checkbox, className) {
    if (!checkbox.checked) return;
    checkbox.closest('.fixed')?.querySelectorAll(`.${className}`).forEach(item => { if (item !== checkbox) item.checked = false; });
};
window.openGoogleMapsPicker = function (button) {
    const block = button.closest('.address-block');
    const lat = block?.querySelector('.addr-lat')?.value || '35.6892';
    const lng = block?.querySelector('.addr-lng')?.value || '51.3890';
    window.open(`https://www.google.com/maps/@${lat},${lng},15z`, '_blank');
};

function readCollection(containerId, selector, mapper) {
    return [...document.querySelectorAll(`#${containerId} > ${selector}`)].map(mapper).filter(Boolean);
}
function normalizePrimary(items) {
    let hasMain = false;
    return items.map(item => {
        if (!item.is_main || hasMain) return { ...item, is_main: false };
        hasMain = true;
        return item;
    });
}
function readBranchForm(prefix = '') {
    const field = name => document.getElementById(prefix ? `${prefix}${name}` : `${name.charAt(0).toLowerCase()}${name.slice(1)}`);
    const phones = readCollection(`${prefix}PhonesContainer`, 'div', div => {
        const number = div.querySelector('.phone-number')?.value.trim();
        return number ? { number, priority: div.querySelector('.phone-priority')?.value || 'primary', is_main: Boolean(div.querySelector('.phone-is-main')?.checked) } : null;
    });
    const links = readCollection(`${prefix}LinksContainer`, 'div', div => {
        const title = div.querySelector('.link-title')?.value.trim(); const url = div.querySelector('.link-url')?.value.trim();
        return title || url ? { title: title || 'لینک', url: url || '#', mode: div.querySelector('.link-mode')?.value || 'social', platform: div.querySelector('.link-platform')?.value || 'other', priority: div.querySelector('.link-priority')?.value || 'secondary', is_main: Boolean(div.querySelector('.link-is-main')?.checked) } : null;
    });
    const addresses = readCollection(`${prefix}AddressesContainer`, 'div', div => ({ province: div.querySelector('.addr-province')?.value || '', city: div.querySelector('.addr-city')?.value || '', address: div.querySelector('.addr-address')?.value.trim() || '', postal_code: div.querySelector('.addr-postal')?.value.trim() || '', lat: div.querySelector('.addr-lat')?.value.trim() || '', lng: div.querySelector('.addr-lng')?.value.trim() || '', is_main: Boolean(div.querySelector('.addr-is-main')?.checked) }));
    return { name: field('BranchName')?.value.trim(), type: field('BranchType')?.value, physical_type: field('BranchPhysicalType')?.value, is_main: Boolean(field('BranchIsMain')?.checked), manager: field('BranchManager')?.value.trim(), status: field('BranchStatus')?.value, slogan: field('BranchSlogan')?.value.trim(), bio: field('BranchBio')?.value.trim(), phones: normalizePrimary(phones), links: normalizePrimary(links), addresses: normalizePrimary(addresses) };
}
function makeOnlyMain(id) { allBranches.forEach(branch => { branch.is_main = branch.id === id; }); }

window.openAddBranchModal = function () { document.getElementById('modalContainer').innerHTML = window.getBranchAddModalHTML(); };
window.saveBranch = function () {
    const branch = readBranchForm();
    if (!branch.name) return alert('نام شعبه الزامی است');
    branch.id = Date.now(); branch.classrooms = 0;
    if (branch.is_main) makeOnlyMain(branch.id);
    allBranches.unshift(branch);
    window.filterBranches(); closeModal(); alert('✅ شعبه اضافه شد');
};
window.viewBranch = function (id) { const branch = allBranches.find(item => item.id === id); if (branch) document.getElementById('modalContainer').innerHTML = window.getBranchViewModalHTML(branch); };
window.editBranch = function (id) { const branch = allBranches.find(item => item.id === id); if (branch) document.getElementById('modalContainer').innerHTML = window.getBranchEditModalHTML(branch); };
window.saveEditedBranch = function (id) {
    const branch = readBranchForm('edit');
    if (!branch.name) return alert('نام شعبه الزامی است');
    const index = allBranches.findIndex(item => item.id === id); if (index === -1) return;
    allBranches[index] = { ...allBranches[index], ...branch };
    if (branch.is_main) makeOnlyMain(id);
    window.filterBranches(); closeModal(); alert('✅ تغییرات ذخیره شد');
};

(function () { setTimeout(() => { window.renderBranchFilters(); if (document.getElementById('branchesCards')) window.filterBranches(); }, 150); })();
