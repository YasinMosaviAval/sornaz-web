"use strict";

/*=========================================================
    Staff Table
=========================================================*/

const StaffTable = (() => {
    const tbody = document.querySelector("#staffTable tbody");
    const pagination = document.getElementById("pagination");

    /*===========================================
        Render Table
    ===========================================*/

    function render(data, page = 1, pageSize = 10) {
        tbody.replaceChildren();
        if (!data.length) {
            tbody.appendChild(
                TemplateLoader.clone("empty")
            );
            renderPagination(0);
            return;
        }
        const start = (page - 1) * pageSize;
        const end = start + pageSize;
        const rows = data.slice(start, end);
        rows.forEach(staff => {
            tbody.appendChild(
                createRow(staff)
            );
        });
        renderPagination(
            data.length,
            page,
            pageSize
        );
    }
    /*===========================================
        Create Row
    ===========================================*/

    function createRow(staff) {
        const row =
            TemplateLoader.clone("row");
        row.dataset.id = staff.id;
        setText(
            row,
            ".staff-id",
            staff.id
        );
        setText(
            row,
            ".staff-name",
            staff.name
        );
        setText(
            row,
            ".staff-code",
            staff.code
        );
        setText(
            row,
            ".staff-role",
            staff.role
        );
        setText(
            row,
            ".staff-phone",
            staff.phone
        );
        setText(
            row,
            ".staff-branch",
            staff.branch
        );
        const avatar =
            row.querySelector(
                ".staff-avatar"
            );
        avatar.src =
            staff.avatar ||
            "assets/images/avatar.png";
        avatar.alt =
            staff.name;
        const badge =
            row.querySelector(
                ".staff-status"
            );
        badge.textContent =
            staff.status;
        badge.classList.add(
            staff.status
        );
        bindEvents(
            row,
            staff
        );
        return row;
    }

    /*===========================================
        Set Text
    ===========================================*/

    function setText(
        row,
        selector,
        value
    ) {
        const element =
            row.querySelector(selector);
        if (!element) return;
        element.textContent = value ?? "";
    }

    /*===========================================
        Events
    ===========================================*/

    function bindEvents(
        row,
        staff
    ) {
        row
            .querySelector(".btn-view")
            .addEventListener(
                "click",
                () => {
                    StaffModal.view(
                        staff.id
                    );
                }
            );
        row
            .querySelector(".btn-edit")
            .addEventListener(
                "click",
                () => {
                    StaffModal.edit(
                        staff.id
                    );
                }
            );
        row
            .querySelector(".btn-delete")
            .addEventListener(
                "click",
                () => {
                    deleteStaff(
                        staff.id
                    );
                }
            );
        row
            .addEventListener(
                "dblclick",
                () => {
                    StaffModal.edit(
                        staff.id
                    );
                }
            );
    }

    /*===========================================
        Pagination
    ===========================================*/

    function renderPagination(
        total,
        page = 1,
        pageSize = 10
    ) {
        pagination.replaceChildren();
        if (total === 0)
            return;
        const pages =
            Math.ceil(
                total / pageSize
            );
        for (
            let i = 1;
            i <= pages;
            i++
        ) {
            const button =
                TemplateLoader.clone(
                    "pagination"
                );
            button.textContent = i;
            if (i === page)
                button.classList.add(
                    "active"
                );
            button.addEventListener(
                "click",
                () => {
                    StaffApp.currentPage = i;
                    refresh();
                }
            );
            pagination.appendChild(
                button
            );
        }
    }

    /*===========================================
        Refresh
    ===========================================*/

    function refresh() {
        render(
            StaffApp.filteredData,
            StaffApp.currentPage,
            StaffApp.pageSize
        );
    }

    return {
        render,
        refresh
    };
})();