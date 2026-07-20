document.addEventListener("DOMContentLoaded",()=>{
    const hero=document.querySelector(".hero");
    if(!hero) return;
        window.addEventListener("mousemove",(e)=>{
        const x=(e.clientX/window.innerWidth-.5)*15;
        const y=(e.clientY/window.innerHeight-.5)*15;
        hero.style.backgroundPosition=`${50+x}% ${50+y}%`;
    });
});