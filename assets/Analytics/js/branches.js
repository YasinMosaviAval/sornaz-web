let allBranchTypes = [];
let allBranches = [];
let filteredBranches = [];
let iranProvinces = [];
let iranCounties = [];
let allBranchAcademies = [];
let allBranchManagerCandidates = [];
window.branchReadOnly = false;
window.branchSiteAdmin = false;
window.branchAccount = false;
window.applyFixedBranchScope = function () {
    if (!window.branchAccount) return;
    document.documentElement.classList.add('branch-scope-fixed');
    document.querySelectorAll('[id]').forEach(function (element) {
        const id = element.id.toLowerCase();
        const isTabs = id.includes('branch') && id.endsWith('tabs');
        const isBranchFilter = id.endsWith('branchfilter') || id === 'filterbranchacademy';
        const isAcademyFilter = id.includes('academy') && id.includes('filter');
        const isAccessBranchFilter = id === 'accessuserbranch';
        if (!isTabs && !isBranchFilter && !isAcademyFilter && !isAccessBranchFilter) return;
        if (isTabs) {
            const shell = element.parentElement && element.parentElement.children.length === 1 ? element.parentElement : element;
            shell.classList.add('hidden');
            shell.setAttribute('aria-hidden', 'true');
            return;
        }
        const field = element.closest('select, [data-branch-filter]') || element;
        const wrapper = field.parentElement && field.parentElement.children.length <= 2 ? field.parentElement : field;
        wrapper.classList.add('hidden');
        wrapper.setAttribute('aria-hidden', 'true');
    });
    if (!window.branchScopeObserver && document.body) {
        window.branchScopeObserver = new MutationObserver(function () { window.applyFixedBranchScope(); });
        window.branchScopeObserver.observe(document.body, {childList:true, subtree:true});
    }
};
let branchesView = 'cards';
let branchSortField = '';
let branchSortDirection = 'asc';
let editingBranchRowId = null;
let branchCsrfToken = '';
let branchesCurrentPage = 1;
let branchesPageSize = 20;

const branchPhysicalTypes = [{value:'physical',label:'فیزیکی'},{value:'online',label:'آنلاین'},{value:'hybrid',label:'هیبرید'}];
const platformOptions = [{value:'instagram',label:'اینستاگرام'},{value:'whats-app',label:'واتساپ'},{value:'youtube',label:'یوتیوب'},{value:'telegram',label:'تلگرام'},{value:'website',label:'وب‌سایت'},{value:'zoom',label:'زوم'},{value:'google-meet',label:'گوگل میت'},{value:'custom',label:'سفارشی'},{value:'other',label:'سایر'}];
const priorityOptions = [{value:'primary',label:'اصلی'},{value:'secondary',label:'فرعی'},{value:'emergency',label:'اضطراری'},{value:'ledger',label:'دفتر'},{value:'support',label:'پشتیبانی'},{value:'other',label:'سایر'}];
const branchPdfColumns = [{field:'index',label:'ردیف'},{field:'academy',label:'آموزشگاه'},{field:'name',label:'نام شعبه'},{field:'type',label:'نوع آموزشی'},{field:'physicalType',label:'نوع ارائه'},{field:'main',label:'اصلی'},{field:'manager',label:'مدیر'},{field:'status',label:'وضعیت'},{field:'classrooms',label:'تعداد کلاس'}];

function renderOptions(items,selected,valueKey='value',labelKey='label'){return items.map(item=>`<option value="${item[valueKey]}" ${String(item[valueKey])===String(selected)?'selected':''}>${item[labelKey]}</option>`).join('');}
function getBranchTypeOptions(selected=''){return renderOptions(allBranchTypes,selected,'id','name');}
function getBranchManagerOptions(selected='',academyId=0){const members=allBranchManagerCandidates.filter(item=>!academyId||String(item.academy_id)===String(academyId));return '<option value="">انتخاب مدیر شعبه</option>'+members.map(item=>`<option value="${item.user_id}" ${String(item.user_id)===String(selected)?'selected':''}>${item.name}</option>`).join('');}
window.getBranchManagerOptions=getBranchManagerOptions;
window.updateBranchManagerOptions=function(academySelect,managerSelectId){const manager=document.getElementById(managerSelectId);if(manager)manager.innerHTML=getBranchManagerOptions(manager.value,academySelect?.value||0);};
window.addEventListener('academy-data-loaded',event=>{allBranchManagerCandidates=event.detail?.manager_candidates||[];});
function hydrateBranchManagerSelect(inputId,academySelectId,academyId,selected=''){const input=document.getElementById(inputId);if(!input)return;const select=document.createElement('select');select.id=inputId;select.className=input.className;select.innerHTML=getBranchManagerOptions(selected,document.getElementById(academySelectId)?.value||academyId||0);input.replaceWith(select);}
function enhanceBranchDialog(){/* فیلدهای ویرایش مستقیماً از قالب مشترک تولید می‌شوند. */}
function enhanceBranchDetails(){}
function syncBranchMainCheckbox(checkboxId,academyId,isEdit=false,branch=null){const checkbox=document.getElementById(checkboxId);if(!checkbox)return;checkbox.checked=isEdit?Boolean(branch?.is_main):!allBranches.some(item=>String(item.academy_id)===String(academyId));checkbox.disabled=true;checkbox.closest('label')?.classList.add('cursor-not-allowed','opacity-70');}
function hideMainBranchDeleteButtons(){allBranches.filter(branch=>branch.is_main).forEach(branch=>document.querySelectorAll(`button[onclick="deleteBranch(${branch.id})"]`).forEach(button=>button.remove()));document.getElementById('branchesPagination')?.classList.toggle('hidden',allBranches.length<11);}
function getPriorityOptions(selected='primary'){return renderOptions(priorityOptions,selected);}
function getPlatformOptions(selected='website'){return renderOptions(platformOptions,selected);}
function getProvinceOptions(selected='تهران'){return iranProvinces.map(item=>`<option value="${item.name}" ${item.name===selected?'selected':''}>${item.name}</option>`).join('');}
function getCityOptions(selected='',provinceName=''){const province=iranProvinces.find(item=>item.name===provinceName);return iranCounties.filter(item=>!province||String(item.province_id)===String(province.id)).map(item=>`<option value="${item.name}" ${item.name===selected?'selected':''}>${item.name}</option>`).join('');}
window.getBranchPhysicalTypeOptions=selected=>renderOptions(branchPhysicalTypes,selected||'physical');
window.getBranchPhysicalTypeLabel=value=>branchPhysicalTypes.find(item=>item.value===value)?.label||'فیزیکی';

window.renderBranchFilters = async function (){const type=document.getElementById('filterBranchType'),physical=document.getElementById('filterBranchPhysicalType'),academy=document.getElementById('filterBranchAcademy');if(type)type.innerHTML='<option value="">همه انواع آموزشی</option>'+renderOptions(allBranchTypes,type.value,'id','name');if(physical)physical.innerHTML='<option value="">همه انواع ارائه</option>'+window.getBranchPhysicalTypeOptions(physical.value);if(academy){academy.innerHTML='<option value="">همه آموزشگاه‌ها</option>'+renderOptions(allBranchAcademies,academy.value,'id','name');academy.classList.toggle('hidden',!window.branchSiteAdmin);}};
window.renderBranchTypeFilter=window.renderBranchFilters;
window.renderBranches = async function (list=filteredBranches){const total=list.length,pages=Math.max(1,Math.ceil(total/branchesPageSize));branchesCurrentPage=Math.min(branchesCurrentPage,pages);const start=(branchesCurrentPage-1)*branchesPageSize;list=list.slice(start,start+branchesPageSize);const box=document.getElementById('branchesCards'),body=document.getElementById('branchesTableBody');if(box)box.innerHTML=list.length?list.map(window.getBranchCardHTML).join(''):window.getBranchEmptyHTML();if(body){body.innerHTML='';if(!list.length)body.innerHTML='<tr><td colspan="7" class="p-12 text-center text-gray-400">شعبه‌ای یافت نشد</td></tr>';list.forEach(branch=>{body.insertAdjacentHTML('beforeend',window.getBranchTableRowHTML(branch));if(editingBranchRowId===branch.id)body.insertAdjacentHTML('beforeend',window.getBranchInlineEditRowHTML(branch));});}document.getElementById('branchesCount').textContent=localizeBranchNumber(allBranches.length);const summary=document.getElementById('branchesPaginationSummary'),label=document.getElementById('branchesPageLabel');if(summary)summary.textContent=total?`نمایش ${localizeBranchNumber(start+1)} تا ${localizeBranchNumber(Math.min(start+branchesPageSize,total))} از ${localizeBranchNumber(total)}`:'موردی یافت نشد';if(label)label.textContent=`${localizeBranchNumber(branchesCurrentPage)} / ${localizeBranchNumber(pages)}`;const prev=document.getElementById('branchesPrevPage'),next=document.getElementById('branchesNextPage');if(prev)prev.disabled=branchesCurrentPage<=1;if(next)next.disabled=branchesCurrentPage>=pages;};
window.changeBranchesPage=delta=>{branchesCurrentPage+=Number(delta);window.renderBranches();};
window.changeBranchesPageSize=value=>{branchesPageSize=Math.max(1,Number(value)||20);branchesCurrentPage=1;window.renderBranches();};
window.sortBranchesBy=function(field){if(branchSortField===field)branchSortDirection=branchSortDirection==='asc'?'desc':'asc';else{branchSortField=field;branchSortDirection='asc';}filteredBranches.sort((a,b)=>{let av=field==='physical_type'?window.getBranchPhysicalTypeLabel(a[field]):a[field],bv=field==='physical_type'?window.getBranchPhysicalTypeLabel(b[field]):b[field];if(field==='id'){av=Number(av);bv=Number(bv);}else{av=String(av??'').toLowerCase();bv=String(bv??'').toLowerCase();}return av<bv?(branchSortDirection==='asc'?-1:1):av>bv?(branchSortDirection==='asc'?1:-1):0;});document.querySelectorAll('[id^="branchSortIcon-"]').forEach(icon=>icon.textContent='↕');const icon=document.getElementById(`branchSortIcon-${field}`);if(icon)icon.textContent=branchSortDirection==='asc'?'↑':'↓';window.renderBranches();};
window.filterBranches = async function (){const q=(document.getElementById('branchSearch')?.value||'').trim().toLowerCase(),typeId=document.getElementById('filterBranchType')?.value||'',mode=document.getElementById('filterBranchPhysicalType')?.value||'',academy=document.getElementById('filterBranchAcademy')?.value||'',status=document.getElementById('filterBranchStatus')?.value||'';filteredBranches=allBranches.filter(branch=>(!q||branch.name.toLowerCase().includes(q)||(branch.manager||'').toLowerCase().includes(q)||(branch.academy_name||'').toLowerCase().includes(q))&&(!typeId||String(branch.type_id)===String(typeId))&&(!mode||branch.physical_type===mode)&&(!academy||String(branch.academy_id)===String(academy))&&(!status||branch.status===status));window.renderBranches();};

async function branchRequest(url,fields=null,method='POST'){const options={method,credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}};if(fields!==null){const token=branchCsrfToken||document.querySelector('[data-branches-root]')?.dataset.csrf||'';const body=new URLSearchParams({_token:token});Object.entries(fields).forEach(([key,value])=>body.set(key,String(value)));options.headers['Content-Type']='application/x-www-form-urlencoded;charset=UTF-8';options.headers['X-CSRF-TOKEN']=token;options.body=body.toString();}const response=await fetch(url,options),raw=await response.text();let payload;try{payload=JSON.parse(raw);}catch(error){console.error('Invalid branches API response:',response.status,raw);throw new Error('سرور پاسخ نامعتبر برگرداند؛ صفحه را تازه‌سازی و دوباره تلاش کنید.');}const envelope=payload.data??payload;if(envelope.csrf_token)branchCsrfToken=envelope.csrf_token;if(!response.ok||envelope.success===false)throw new Error(envelope.message||'عملیات شعبه انجام نشد.');return envelope.data??envelope;}
function localizeBranchNumber(value){return document.documentElement.lang==='fa'?String(value).replace(/\d/g,d=>'۰۱۲۳۴۵۶۷۸۹'[d]):String(value);}
window.setBranchesView=function(view){branchesView=view;document.getElementById('branchesCards')?.classList.toggle('hidden',view!=='cards');document.getElementById('branchesTableWrap')?.classList.toggle('hidden',view!=='table');document.getElementById('branchesTableViewButton')?.classList.toggle('bg-indigo-600',view==='table');document.getElementById('branchesTableViewButton')?.classList.toggle('text-white',view==='table');document.getElementById('branchesCardViewButton')?.classList.toggle('bg-indigo-600',view==='cards');document.getElementById('branchesCardViewButton')?.classList.toggle('text-white',view==='cards');};
async function loadBranches(){try{const data=await branchRequest('/academy/admin/branches',null,'GET');branchCsrfToken=data.csrf_token||branchCsrfToken;allBranches=data.branches||[];filteredBranches=[...allBranches];allBranchTypes=data.types||[];allBranchAcademies=data.academies||[];window.branchReadOnly=Boolean(data.read_only);window.branchSiteAdmin=Boolean(data.site_admin);window.branchAccount=Boolean(data.branch_account);window.branchCanCreate=data.can_create_branch!==false;window.branchCanDelete=data.can_delete_branch!==false;window.applyFixedBranchScope();const addButton=document.getElementById('addBranchButton');if(addButton)addButton.classList.toggle('hidden',!window.branchCanCreate);iranProvinces=(data.provinces||[]).map(item=>({id:item.province_id,name:item.province_name}));iranCounties=(data.counties||[]).map(item=>({id:item.county_id,province_id:item.province_id,name:item.county_name}));const description=document.getElementById('branchesScopeDescription');if(description)description.textContent=window.branchSiteAdmin?'مدیریت سراسری تمام آموزشگاه‌ها و شعب ثبت‌شده':(window.branchAccount?'اطلاعات و مدیریت همین شعبه':'مدیریت شعب آموزشگاه');document.getElementById('branchesAcademiesCount').textContent=localizeBranchNumber(allBranchAcademies.length||new Set(allBranches.map(item=>item.academy_id)).size);window.renderBranchFilters();window.filterBranches();window.setBranchesView(window.branchSiteAdmin?'table':'cards');window.dispatchEvent(new CustomEvent('academy-data-loaded',{detail:data}));window.applyFixedBranchScope();}catch(error){alert(error.message);}}

window.updateBranchCountySelect=function(provinceSelect){const block=provinceSelect.closest('.address-block');const city=block?.querySelector('.addr-city');if(!city)return;const province=iranProvinces.find(item=>item.name===provinceSelect.value);const counties=province?iranCounties.filter(item=>String(item.province_id)===String(province.id)):[];city.innerHTML='<option value="">'+(province?'انتخاب شهر':'ابتدا استان را انتخاب کنید')+'</option>'+counties.map(item=>`<option value="${item.name}">${item.name}</option>`).join('');city.disabled=!province;};

function encodeBranchPayload(data){const bytes=new TextEncoder().encode(JSON.stringify(data)),chunks=[];for(let i=0;i<bytes.length;i+=8192)chunks.push(String.fromCharCode(...bytes.subarray(i,i+8192)));return btoa(chunks.join(''));}
window.promptAddBranchType=function(){document.getElementById('branchTypeModal')?.remove();document.body.insertAdjacentHTML('beforeend',window.getBranchTypeModalHTML());};
window.closeBranchTypeModal=function(){document.getElementById('branchTypeModal')?.remove();};
window.saveBranchType=async function(button){const data={title:document.getElementById('newBranchTypeTitle')?.value.trim()||'',summary:document.getElementById('newBranchTypeSummary')?.value.trim()||'',description:document.getElementById('newBranchTypeDescription')?.value.trim()||''};if(!data.title||!data.summary||!data.description)return alert('عنوان، خلاصه و شرح نوع آموزشی را کامل کنید.');if(allBranchTypes.some(item=>item.name===data.title))return alert('این نوع آموزشی قبلاً وجود دارد.');await withBranchSaveButton(button,async()=>{try{const created=await branchRequest('/academy/admin/branches/types',{payload_b64:encodeBranchPayload(data)});allBranchTypes.push(created);document.querySelectorAll('#branchTypeModal').forEach(modal=>modal.remove());document.querySelectorAll('#branchType, #editBranchType, #inlineBranchType, [id^="inlineBranchType-"]').forEach(select=>{select.innerHTML=getBranchTypeOptions(created.id);select.value=String(created.id);select.dispatchEvent(new Event('change',{bubbles:true}));});window.renderBranchFilters();alert('نوع آموزشی با موفقیت در دیتابیس ثبت شد.');}catch(error){alert(error.message);}});};
window.openAddBranchModal = async function (){const academyId=Number(document.getElementById('filterBranchAcademy')?.value||allBranchAcademies[0]?.id||allBranchManagerCandidates[0]?.academy_id||allBranches[0]?.academy_id||0);if(!academyId)return alert('آموزشگاه مقصد برای ثبت شعبه مشخص نیست.');const container=document.getElementById('modalContainer');container.innerHTML='<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60"><div class="rounded-2xl bg-white px-8 py-6">در حال بارگذاری فرم ثبت شعبه…</div></div>';try{const response=await fetch(`/academy/admin/branches/registration-form?academy_id=${academyId}`,{credentials:'same-origin',headers:{Accept:'text/html','X-Requested-With':'XMLHttpRequest'}});if(!response.ok)throw new Error('بارگذاری فرم ثبت شعبه ناموفق بود.');container.innerHTML=await response.text();window.initAcademyRegistrationForm?.();}catch(error){container.innerHTML='';alert(error.message);}};
window.viewBranch = async function (id){editingBranchRowId=null;window.renderBranches();const branch=allBranches.find(item=>item.id===id);if(branch){document.getElementById('modalContainer').innerHTML=window.getBranchViewModalHTML(branch);enhanceBranchDetails(branch);}};
window.editBranch = async function (id){if(branchesView==='cards')return window.editBranchDialog(id);editingBranchRowId=editingBranchRowId===id?null:id;window.renderBranches();};
window.editBranchDialog = async function (id){const branch=allBranches.find(item=>item.id===id);if(branch){document.getElementById('modalContainer').innerHTML=window.getBranchEditModalHTML(branch);enhanceBranchDialog(branch,true);hydrateBranchManagerSelect('editBranchManager','editBranchAcademy',branch.academy_id,branch.manager_user_id||'');document.getElementById('editBranchIsMain')?.closest('label')?.remove();if(branch.is_main){const modal=document.querySelector('#modalContainer .fixed > div');modal?.classList.remove('bg-white');modal?.classList.add('bg-amber-50','border','border-amber-300','ring-1','ring-amber-200');const header=modal?.querySelector('.sticky');header?.classList.remove('bg-white');header?.classList.add('bg-amber-100');}}};
window.deleteBranch=async function(id){const branch=allBranches.find(item=>item.id===id);if(branch?.is_main)return alert('حذف شعبه اصلی آموزشگاه امکان‌پذیر نیست.');if (!(await AppDialog.confirmDelete(allBranches,id,'شعبه')))return;try{await branchRequest(`/academy/admin/branches/${id}/delete`);await loadBranches();}catch(error){alert(error.message);}};

function readBranchCollection(containerId,mapper){return[...document.querySelectorAll(`#${containerId} > div`)].map(mapper).filter(Boolean);}
function normalizeBranchPrimary(items){let found=false;return items.map(item=>{const main=Boolean(item.is_main)&&!found;if(main)found=true;return{...item,is_main:main};});}
function readBranchForm(prefix=''){const field=name=>document.getElementById(prefix?`${prefix}${name}`:`${name.charAt(0).toLowerCase()}${name.slice(1)}`);const phones=readBranchCollection(`${prefix}PhonesContainer`,div=>{const number=div.querySelector('.phone-number')?.value.trim();return number?{number,priority:div.querySelector('.phone-priority')?.value||'primary',is_main:Boolean(div.querySelector('.phone-is-main')?.checked)}:null;});const links=readBranchCollection(`${prefix}LinksContainer`,div=>{const title=div.querySelector('.link-title')?.value.trim(),url=div.querySelector('.link-url')?.value.trim();return title||url?{title:title||'لینک',url:url||'#',mode:div.querySelector('.link-mode')?.value||'social',platform:div.querySelector('.link-platform')?.value||'other',priority:div.querySelector('.link-priority')?.value||'secondary',is_main:Boolean(div.querySelector('.link-is-main')?.checked)}:null;});const addresses=readBranchCollection(`${prefix}AddressesContainer`,div=>({province:div.querySelector('.addr-province')?.value||'',city:div.querySelector('.addr-city')?.value||'',address:div.querySelector('.addr-address')?.value.trim()||'',postal_code:div.querySelector('.addr-postal')?.value.trim()||'',lat:div.querySelector('.addr-lat')?.value.trim()||'',lng:div.querySelector('.addr-lng')?.value.trim()||'',is_main:Boolean(div.querySelector('.addr-is-main')?.checked)}));const managerSelect=field('BranchManager'),managerOption=managerSelect?.selectedOptions?.[0];return{academy_id:Number(field('BranchAcademy')?.value||0),name:field('BranchName')?.value.trim(),username:field('BranchUsername')?.value.trim()||'',email:field('BranchEmail')?.value.trim()||'',phone:field('BranchPhone')?.value.trim()||'',password:field('BranchPassword')?.value||'',password2:field('BranchPassword2')?.value||'',type_id:Number(field('BranchType')?.value),physical_type:field('BranchPhysicalType')?.value,is_main:Boolean(field('BranchIsMain')?.checked),manager_user_id:Number(managerSelect?.value||0),manager:managerOption?.value?managerOption.textContent.trim():'',status:field('BranchStatus')?.value==='فعال'?'active':'inactive',slogan:field('BranchSlogan')?.value.trim(),short_description:field('BranchShortDescription')?.value.trim()||'',bio:field('BranchBio')?.value.trim(),phones:normalizeBranchPrimary(phones),links:normalizeBranchPrimary(links),addresses:normalizeBranchPrimary(addresses)};}
async function withBranchSaveButton(button,callback){if(button?.dataset.saving==='1')return;const label=button?.textContent;if(button){button.dataset.saving='1';button.disabled=true;button.classList.add('opacity-70');button.textContent='در حال ذخیره...';}try{await callback();}finally{if(button?.isConnected){button.dataset.saving='0';button.disabled=false;button.classList.remove('opacity-70');button.textContent=label;}}}
window.saveBranch=async function(button=null){const branch=readBranchForm();if(!branch.name)return alert('نام شعبه الزامی است');await withBranchSaveButton(button,async()=>{try{await branchRequest('/academy/admin/branches',{payload_b64:encodeBranchPayload(branch)});closeModal();await loadBranches();}catch(error){alert(error.message);}});};
window.saveEditedBranch=async function(id,button=null){const branch=readBranchForm('edit'),current=allBranches.find(item=>item.id===id);branch.username=current?.username||'';if(!branch.name)return alert('نام شعبه الزامی است');await withBranchSaveButton(button,async()=>{try{await branchRequest(`/academy/admin/branches/${id}/update`,{payload_b64:encodeBranchPayload(branch)});closeModal();await loadBranches();}catch(error){alert(error.message);}});};
window.saveInlineBranch=async function(id,button=null){const branch=readBranchForm('inline'),current=allBranches.find(item=>item.id===id);branch.username=current?.username||'';if(!branch.name)return alert('نام شعبه الزامی است');await withBranchSaveButton(button,async()=>{try{await branchRequest(`/academy/admin/branches/${id}/update`,{payload_b64:encodeBranchPayload(branch)});editingBranchRowId=null;await loadBranches();}catch(error){alert(error.message);}});};

window.addPhoneField=(id='phonesContainer')=>document.getElementById(id)?.insertAdjacentHTML('beforeend',window.getBranchPhoneFieldHTML());
window.addLinkField=(id='linksContainer')=>document.getElementById(id)?.insertAdjacentHTML('beforeend',window.getBranchLinkFieldHTML());
window.addAddressField=(id='addressesContainer')=>document.getElementById(id)?.insertAdjacentHTML('beforeend',window.getBranchAddressFieldHTML());
window.enforceSingleBranchPrimary = async function (checkbox,className){if(checkbox.checked)checkbox.closest('.fixed')?.querySelectorAll(`.${className}`).forEach(item=>{if(item!==checkbox)item.checked=false;});};
window.openGoogleMapsPicker = async function (button){const block=button.closest('.address-block'),lat=block?.querySelector('.addr-lat')?.value||'35.6892',lng=block?.querySelector('.addr-lng')?.value||'51.3890';window.open(`https://www.google.com/maps/@${lat},${lng},15z`,'_blank');};

window.exportBranchesToExcel = async function (){const rows=filteredBranches.length?filteredBranches:allBranches,csv='\uFEFFردیف,آموزشگاه,نام,نوع آموزشی,نوع ارائه,شعبه اصلی,مدیر,وضعیت,تعداد کلاس\n'+rows.map((branch,index)=>`${index+1},"${branch.academy_name||''}","${branch.name}","${branch.type}","${window.getBranchPhysicalTypeLabel(branch.physical_type)}","${branch.is_main?'بله':'خیر'}","${branch.manager||''}","${branch.status}",${branch.classrooms||0}`).join('\n'),link=document.createElement('a');link.href=URL.createObjectURL(new Blob([csv],{type:'text/csv;charset=utf-8;'}));link.download=`شعبه‌ها_${new Date().toLocaleDateString('fa-IR')}.csv`;link.click();};
window.openBranchesPDFOptionsModal = async function (){document.getElementById('modalContainer').innerHTML=window.getBranchPDFModalHTML(branchPdfColumns);};
window.generateBranchesPDF=async function(){
    if(!window.html2canvas||!window.jspdf)return alert('ابزار تولید PDF بارگذاری نشده است. لطفاً صفحه را مجدداً بارگذاری کنید.');
    const title=document.getElementById('branchPdfTitle')?.value||'گزارش شعبه‌های آموزشگاه';
    const subtitle=document.getElementById('branchPdfSubtitle')?.value||'';
    const footer=document.getElementById('branchPdfFooter')?.value||'';
    const format=document.getElementById('branchPdfFormat')?.value||'a4';
    const orientation=document.getElementById('branchPdfOrientation')?.value||'landscape';
    const includeDate=Boolean(document.getElementById('branchPdfIncludeDate')?.checked);
    const headerColor=document.getElementById('branchPdfHeaderColor')?.value||'#eff6ff';
    const evenRowColor=document.getElementById('branchPdfEvenRowColor')?.value||'#ffffff';
    const oddRowColor=document.getElementById('branchPdfOddRowColor')?.value||'#f8fafc';
    const columns=branchPdfColumns.filter(column=>document.getElementById(`branchPdfCol-${column.field}`)?.checked);
    if(!columns.length)return alert('حداقل یک ستون را برای خروجی PDF انتخاب کنید.');
    const source=filteredBranches.length?filteredBranches:allBranches;
    const rows=source.map((branch,index)=>({index:index+1,academy:branch.academy_name||'—',name:branch.name||'—',type:branch.type||'—',physicalType:window.getBranchPhysicalTypeLabel(branch.physical_type),main:branch.is_main?'بله':'خیر',manager:branch.manager||'—',status:branch.status||'—',classrooms:branch.classrooms||0}));
    const date=new Date().toLocaleDateString('fa-IR');
    const rowsPerPage=orientation==='portrait'?18:15;
    const totalPages=Math.max(1,Math.ceil(rows.length/rowsPerPage));
    const canvases=[];
    if(document.fonts?.ready)await document.fonts.ready;
    for(let pageIndex=0;pageIndex<totalPages;pageIndex++){
        const wrapper=document.createElement('div');
        wrapper.style.cssText=`direction:rtl;position:fixed;top:-9999px;left:-9999px;width:${orientation==='portrait'?'900':'1400'}px;padding:30px;background:#fff;color:#111827;font-family:Vazirmatn,Tahoma,sans-serif;`;
        wrapper.innerHTML=window.getBranchPDFPageHTML(pageIndex+1,rows.slice(pageIndex*rowsPerPage,(pageIndex+1)*rowsPerPage),pageIndex===0,{title,subtitle,footer,includeDate,date,headerColor,evenRowColor,oddRowColor,columns,rowsPerPage,totalPages});
        document.body.appendChild(wrapper);
        try{canvases.push(await window.html2canvas(wrapper,{scale:2,useCORS:true,backgroundColor:'#ffffff',scrollY:-window.scrollY}));}finally{wrapper.remove();}
    }
    const pdf=new window.jspdf.jsPDF({orientation,unit:'pt',format});
    const margin=20,pageWidth=pdf.internal.pageSize.getWidth(),imageWidth=pageWidth-margin*2;
    canvases.forEach((canvas,index)=>{if(index)pdf.addPage();pdf.addImage(canvas.toDataURL('image/png'),'PNG',margin,margin,imageWidth,(canvas.height*imageWidth)/canvas.width);});
    pdf.save(`شعبه‌ها_${date}.pdf`);
    closeModal();
};

document.addEventListener('change',event=>{if(event.target?.id==='branchAcademy'){window.updateBranchManagerOptions(event.target,'branchManager');syncBranchMainCheckbox('branchIsMain',event.target.value);}else if(event.target?.id==='editBranchAcademy')window.updateBranchManagerOptions(event.target,'editBranchManager');});
document.addEventListener('click',event=>{const button=event.target.closest('[data-branch-save]');if(!button)return;event.preventDefault();event.stopPropagation();const id=Number(button.dataset.branchId||0);if(button.dataset.branchSave==='save-add')window.saveBranch(button);else if(button.dataset.branchSave==='save-edit')window.saveEditedBranch(id,button);else if(button.dataset.branchSave==='save-inline')window.saveInlineBranch(id,button);},true);
document.addEventListener('DOMContentLoaded',()=>{const cards=document.getElementById('branchesCards'),table=document.getElementById('branchesTableBody');if(cards||table){const observer=new MutationObserver(hideMainBranchDeleteButtons);if(cards)observer.observe(cards,{childList:true,subtree:true});if(table)observer.observe(table,{childList:true,subtree:true});loadBranches();}});
window.branchOfferingDelete=async function(type,id){const token=window.adminCsrfToken||branchCsrfToken||'';const r=await fetch(`/academy/admin/branch-offerings/${type}/${id}/delete`,{method:'POST',credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest','Content-Type':'application/x-www-form-urlencoded;charset=UTF-8','X-CSRF-TOKEN':token},body:new URLSearchParams({_token:token}).toString()});const p=await r.json(),envelope=p.data??p;if(!r.ok||envelope.success===false)throw new Error(envelope.message||'حذف ناموفق بود.');};
window.loadBranchOfferings=function(){
    if(window.branchOfferingData)return Promise.resolve(window.branchOfferingData);
    if(window.branchOfferingLoadPromise)return window.branchOfferingLoadPromise;
    window.branchOfferingLoadPromise=fetch('/academy/admin/branch-offerings',{credentials:'same-origin',headers:{Accept:'application/json','X-Requested-With':'XMLHttpRequest'}})
        .then(async r=>{const text=await r.text();let p;try{p=JSON.parse(text);}catch(e){throw new Error('پاسخ اطلاعات شعب از سرور معتبر نیست.');}const envelope=p.data??p;if(!r.ok||envelope.success===false)throw new Error(envelope.message||'بارگذاری اطلاعات شعب ناموفق بود.');return envelope.data??envelope;})
        .then(d=>{window.branchOfferingData=d;window.branchOfferingBranches=d.branches||[];window.dispatchEvent(new CustomEvent('branch-offerings-loaded',{detail:d}));return d;})
        .finally(()=>{window.branchOfferingLoadPromise=null;});
    return window.branchOfferingLoadPromise;
};
document.addEventListener('DOMContentLoaded',async()=>{if(!document.getElementById('instrumentsTable')&&!document.getElementById('lessonsTable')&&!document.getElementById('branchSchedulesTable'))return;try{await window.loadBranchOfferings();}catch(e){alert(e.message);}});
