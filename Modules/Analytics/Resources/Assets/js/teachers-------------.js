document.addEventListener(
    "DOMContentLoaded",
    async () => {
        await TemplateLoader.loadAll();
        initStaffPage();
    }
);


StaffTable.render(StaffApp.filteredData, StaffApp.currentPage, StaffApp.pageSize);

// /*=====================================================
//     Staff Controller
// ======================================================*/

// const StaffApp = {
//     data: [],
//     filteredData: [],
//     currentPage: 1,
//     pageSize: 10,
//     selectedStaff: null,
//     filters: {
//         search: "",
//         role: "",
//         branch: "",
//         status: ""
//     }
// };

// document.addEventListener("DOMContentLoaded", initStaffPage);

// function initStaffPage() {
//     loadTemplates();
//     loadStaffData();
//     registerEvents();
//     renderTable();
// }

// const Templates = {};

// async function loadTemplates() {
//     const files = {
//         row: "templates/staff-row.html",
//         inline: "templates/inline-edit-row.html",
//         addModal: "templates/modal-add-staff.html",
//         editModal: "templates/modal-edit-staff.html",
//         viewModal: "templates/modal-view-staff.html",
//         pdfModal: "templates/modal-pdf-options.html",
//         pagination: "templates/pagination-button.html",
//         empty: "templates/empty-table.html"
//     };
//     for (const key in files) {
//         const html = await fetch(files[key]).then(r => r.text());
//         const div = document.createElement("div");
//         div.innerHTML = html;
//         Templates[key] = div.firstElementChild;
//     }
// }

// function loadStaffData() {
//     StaffApp.data = staffData;
//     StaffApp.filteredData = [...staffData];
// }

// function renderTable() {
//     StaffTable.render(StaffApp.filteredData, StaffApp.currentPage, StaffApp.pageSize);
// }

// function registerEvents() {
//     document.getElementById("btnAddStaff").addEventListener("click", StaffModal.openAdd);
//     document.getElementById("btnExportPDF").addEventListener("click", StaffPDF.openOptions);
//     document.getElementById("searchInput").addEventListener("keyup", StaffFilter.search);
// }


// function refreshData() {
//     StaffApp.filteredData = StaffFilter.apply(StaffApp.data, StaffApp.filters);
//     renderTable();
// }

// function changePage(page) {
//     StaffApp.currentPage = page;
//     renderTable();
// }

// function removeStaff(id) {
//     StaffApp.data = StaffApp.data.filter(item => item.id !== id);
//     refreshData();
// }

// function addStaff(staff) {
//     StaffApp.data.push(staff);
//     refreshData();
// }

// function updateStaff(staff) {
//     const index = StaffApp.data.findIndex(x => x.id === staff.id);
//     if (index !== -1) StaffApp.data[index] = staff;
//     refreshData();
// }

// function viewStaff(id) {
//     const staff = StaffApp.data.find(x => x.id === id);
//     StaffModal.openView(staff);
// }