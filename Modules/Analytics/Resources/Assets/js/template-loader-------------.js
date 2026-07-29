"use strict";

/*=========================================================
    Template Loader
    --------------------------------------------
    تمام Template های پروژه فقط یک بار لود می‌شوند
=========================================================*/

const TemplateLoader = (() => {
    const cache = {};
    const files = {
        row: "templates/staff-row.html",
        inlineEdit: "templates/inline-edit-row.html",
        addModal: "templates/modal-add-staff.html",
        editModal: "templates/modal-edit-staff.html",
        viewModal: "templates/modal-view-staff.html",
        pdfModal: "templates/modal-pdf-options.html",
        pagination: "templates/pagination-button.html",
        empty: "templates/empty-table.html"
    };

    async function load(name, url) {
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Template not found : ${url}`);
        }
        const html = await response.text();
        const wrapper = document.createElement("div");
        wrapper.innerHTML = html.trim();
        cache[name] = wrapper.querySelector("template");
    }

    async function loadAll() {
        const jobs = [];
        for (const [key, value] of Object.entries(files)) {
            jobs.push(load(key, value));
        }
        await Promise.all(jobs);
    }

    function get(name) {
        if (!cache[name]) {
            throw new Error(`Template '${name}' not loaded.`);
        }
        return cache[name];
    }

    function clone(name) {
        return get(name)
            .content
            .firstElementChild
            .cloneNode(true);
    }

    function exists(name) {
        return cache[name] !== undefined;
    }

    function clear() {
        Object.keys(cache)
            .forEach(key => delete cache[key]);
    }

    return {
        loadAll,
        get,
        clone,
        exists,
        clear
    };
})();