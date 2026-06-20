/** ================================================
    ==== Open Drop down Menu in Admin Panel Sidebar
    ================================================ */

document.querySelectorAll('.has-submenu > a').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        const submenu = link.nextElementSibling;
        submenu.classList.toggle('show');
        link.parentElement.classList.toggle('open');
    });
});

