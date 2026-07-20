document.addEventListener("DOMContentLoaded",()=>{
    document.querySelectorAll(".category-card")
    .forEach(card=>{
        card.addEventListener("mouseenter",()=>{
            card.style.transition=".25s";
        });
    });
});