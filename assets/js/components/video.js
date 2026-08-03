document.addEventListener("change",function(e){
    if(e.target.type!=="file"){
        return;
    }
    if(!e.target.accept.includes("video")){
        return;
    }
    if(!e.target.files.length){
        return;
    }
    const preview=e.target.parentNode.querySelector(".sn-video-preview");
    preview.innerHTML="";
    const video=document.createElement("video");
    video.controls=true;
    video.src=URL.createObjectURL(e.target.files[0]);
    preview.appendChild(video);
});