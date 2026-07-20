document.addEventListener("DOMContentLoaded",()=>{
    const btn=document.querySelector(".mobile-menu-btn");
    if(!btn) return;
    btn.addEventListener("click",()=>{
        document.body.classList.toggle("mobile-menu-open");
    });
});