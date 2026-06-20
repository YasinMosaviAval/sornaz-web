<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>پروفایل آموزشگاه</title>
<style>
body{font-family:tahoma;background:#f4f6fb;margin:0}
.container{max-width:1000px;margin:auto;padding:20px}

.header{background:#fff;border-radius:16px;padding:20px;box-shadow:0 6px 15px rgba(0,0,0,.05);margin-bottom:20px}
.header img{width:100%;height:250px;object-fit:cover;border-radius:12px;margin-bottom:15px}
.title{font-size:20px;font-weight:bold;margin-bottom:10px}
.meta{color:#666;font-size:13px;margin-bottom:10px}
.desc{line-height:1.8;color:#444}

.section{background:#fff;border-radius:16px;padding:20px;margin-bottom:20px;box-shadow:0 6px 15px rgba(0,0,0,.05)}
.section h3{margin-bottom:15px}

.gallery{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:10px}
.gallery img{width:100%;height:120px;object-fit:cover;border-radius:10px;cursor:pointer}

.lightbox{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.8);display:flex;align-items:center;justify-content:center;opacity:0;pointer-events:none;transition:.3s}
.lightbox.active{opacity:1;pointer-events:auto}
.lightbox img{max-width:90%;max-height:90%;border-radius:10px}

.rating-input span{font-size:22px;cursor:pointer;color:#ccc}
.rating-input span.active{color:#ffc107}

.comment{border-bottom:1px solid #eee;padding:10px 0}
.comment:last-child{border:none}
.comment-name{font-weight:bold}
.comment-text{font-size:13px;color:#555}
.admin-reply{background:#f1f3ff;padding:8px;border-radius:8px;margin-top:5px;font-size:12px}

.form input,.form textarea{width:100%;padding:10px;margin-bottom:10px;border:1px solid #ddd;border-radius:8px}
.btn{background:#3f51b5;color:#fff;border:none;padding:10px;border-radius:8px;cursor:pointer}

.map iframe{width:100%;height:300px;border:none;border-radius:12px}

</style>
</head>
<body>

<div class="container">

<div class="header">
<img src="https://via.placeholder.com/800x250">
<div class="title">آموزشگاه موسیقی آوا</div>
<div class="meta" id="avgRating">⭐ 0</div>
<div class="desc">توضیحات آموزشگاه...</div>
</div>

<div class="section">
<h3>گالری تصاویر</h3>
<input type="file" id="upload" multiple>
<div class="gallery" id="gallery"></div>
</div>

<div class="lightbox" id="lightbox"><img id="lightImg"></div>

<div class="section map">
<h3>نقشه</h3>
<iframe src="https://maps.google.com/maps?q=Tehran&output=embed"></iframe>
</div>

<div class="section">
<h3>نظرات</h3>
<div id="comments"></div>
</div>

<div class="section">
<h3>ارسال نظر</h3>
<div class="rating-input" id="ratingInput">
<span data-val="1">★</span>
<span data-val="2">★</span>
<span data-val="3">★</span>
<span data-val="4">★</span>
<span data-val="5">★</span>
</div>
<input id="name" placeholder="نام">
<textarea id="text" placeholder="نظر"></textarea>
<button class="btn" onclick="addComment()">ثبت</button>
</div>

</div>

<script>
let selectedRating=0
let ratings=[]
const commentsEl=document.getElementById('comments')
const gallery=document.getElementById('gallery')

// rating select
const stars=document.querySelectorAll('#ratingInput span')
stars.forEach(star=>{
star.onclick=()=>{
selectedRating=+star.dataset.val
stars.forEach(s=>s.classList.remove('active'))
for(let i=0;i<selectedRating;i++)stars[i].classList.add('active')
}
})

function updateAvg(){
if(ratings.length==0)return
let avg=(ratings.reduce((a,b)=>a+b,0)/ratings.length).toFixed(1)
document.getElementById('avgRating').innerText='⭐ '+avg
}

function addComment(){
const name=document.getElementById('name').value
const text=document.getElementById('text').value
if(!name||!text||!selectedRating)return alert('همه موارد را وارد کنید')

ratings.push(selectedRating)
updateAvg()

const div=document.createElement('div')
div.className='comment'
div.innerHTML=`
<div class='comment-name'>${name} - ⭐ ${selectedRating}</div>
<div class='comment-text'>${text}</div>
<div class='admin-reply'>پاسخ ادمین: ممنون از نظر شما 🙏</div>`

commentsEl.appendChild(div)

// reset
selectedRating=0
stars.forEach(s=>s.classList.remove('active'))
document.getElementById('name').value=''
document.getElementById('text').value=''
}

// upload image
const upload=document.getElementById('upload')
upload.addEventListener('change',e=>{
[...e.target.files].forEach(file=>{
const reader=new FileReader()
reader.onload=()=>{
const img=document.createElement('img')
img.src=reader.result
img.onclick=()=>openLightbox(img.src)
gallery.appendChild(img)
}
reader.readAsDataURL(file)
})
})

// lightbox
const lightbox=document.getElementById('lightbox')
const lightImg=document.getElementById('lightImg')
function openLightbox(src){
lightImg.src=src
lightbox.classList.add('active')
}
lightbox.onclick=()=>lightbox.classList.remove('active')

</script>

</body>
</html>