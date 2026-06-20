
<style>
  body{font-family:tahoma;background:#f4f6fb; padding:20px; }
  .container{max-width:1000px;margin:auto}
  .filters{display:flex;gap:10px;margin-bottom:25px;background:#fff;padding:15px;border-radius:14px;box-shadow:0 4px 12px rgba(0,0,0,.05)}
  .filters input,.filters select{padding:10px;border:1px solid #ddd;border-radius:8px;flex:1}
  
  .section{margin-bottom:30px}
  .cards{display:grid;gap:15px}
  .card{ background:#fff; border-radius:14px; padding:16px; border:1px solid #e5e7eb; box-shadow:0 8px 20px rgba(0,0,0,.05);overflow:hidden;position:relative}
  .card img{width:100%;height:180px;object-fit:cover}
  
  .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:20px}
  .favorite{position:absolute;top:10px;left:10px;background:#fff;border-radius:50%;width:35px;height:35px;display:flex;align-items:center;justify-content:center;cursor:pointer;box-shadow:0 2px 6px rgba(0,0,0,.15)}
  .favorite.active{color:red}
  .fav-count{position:absolute;top:10px;right:10px;background:#fff;padding:4px 8px;border-radius:10px;font-size:12px;box-shadow:0 2px 6px rgba(0,0,0,.15)}
  .card-content{padding:15px}
  .card-title{font-size:15px;font-weight:bold;margin-bottom:8px}
  .card-meta{font-size:12px;color:#777;margin-bottom:8px}
  .stars{color:#ffc107;font-size:14px;margin-bottom:8px}
  .tags{display:flex;flex-wrap:wrap;gap:5px;margin-bottom:10px}
  .tag{background:#eef2ff;color:#3f51b5;padding:4px 8px;border-radius:6px;font-size:11px}
  .btn{display:block;text-align:center;padding:8px;background:#3f51b5;color:#fff;border-radius:10px;text-decoration:none;font-size:13px;margin-top:5px;cursor:pointer}
  .btn:hover{background:#2f3fa3}
  .special-btn{background:#ff9800}
  .special-btn:hover{background:#e68900}
  /* MODAL */
  .modal{ position:fixed;top:0;left:0;width:100%;height:100%; background:rgba(0,0,0,.4); display:flex;align-items:center;justify-content:center; opacity:0;pointer-events:none; transition:opacity .35s ease; }
  .modal.active{ opacity:1;pointer-events:auto; }
  .modal.closing{ opacity:0; }
  .modal-content{ background:#fff;padding:20px;border-radius:14px;width:320px; transform:scale(.85); opacity:0; transition:all .35s cubic-bezier(.25,.8,.25,1); }
  .modal.active .modal-content{ transform:scale(1); opacity:1; }
  .modal.closing .modal-content{ transform:scale(.85); opacity:0; }
  .close-btn{margin-top:8px;background:#999 !important}
  /* آموزشگاه من */
  .my-academy-card{ padding:16px; border-radius:14px; margin-bottom:12px; background:linear-gradient(135deg,#eef2ff,#f8fafc); border:2px solid #6366f1; }
  .my-academy-card .role{ background:#6366f1; color:#fff; padding:4px 10px; border-radius:999px; font-size:12px; }
  .my-academy-card.role-student{ border-color:#10b981; background:linear-gradient(135deg,#ecfdf5,#f0fdf4); }
  .my-academy-card.role-admin{ border-color:#f59e0b; background:linear-gradient(135deg,#fffbeb,#fefce8); }
  .section-divider{ margin:30px 0; border-top:1px solid #e5e7eb; }
  /* ========================= */
  /* MY ACADEMY SPECIAL */
  .my-card{ border:2px solid #6366f1; background:linear-gradient(135deg,#eef2ff,#f8fafc); }
  .role{ display:inline-block; padding:4px 10px; border-radius:999px; font-size:12px; color:#fff; margin-right:5px; }
  .teacher{background:#6366f1}
  .student{background:#10b981}
  .staff{background:#f59e0b}
  .meta{font-size:13px;color:#555;margin:8px 0}
  .actions{margin-top:10px}
  .primary{background:#4f46e5;color:#fff}
  .secondary{background:#e5e7eb}
  /* STATS */
  .stats{ display:grid; grid-template-columns:repeat(3,1fr); gap:10px; margin-top:10px; }
  .stat{ background:#fff; padding:12px; border-radius:10px; text-align:center; border:1px solid #eee; }
  .stat span{font-size:12px;color:#666}
  .divider{margin:30px 0;border-top:1px solid #ddd}
</style>

<div class="container">
        <!-- MY ACADEMIES -->
  <div class="section">
    <h2>آموزشگاه‌های من</h2>
    <div class="cards">
      <div class="card my-card">
          <h3>آوای هنر</h3>
          <span class="role teacher">مدرس</span>

          <div class="meta">📍 تهران | ⭐ 4.8</div>

          <div class="stats">
            <div class="stat"><span>کلاس‌ها</span><h4>12</h4></div>
            <div class="stat"><span>هنرجوها</span><h4>58</h4></div>
            <div class="stat"><span>درآمد</span><h4>18M</h4></div>
          </div>

          <div class="actions">
            <button class="primary">ورود به پنل</button>
            <button class="secondary">مشاهده</button>
          </div>
      </div>
      <div class="card my-card">
        <h3>نوای شرق</h3>
        <span class="role student">هنرجو</span>

        <div class="meta">📍 شیراز | ⭐ 4.5</div>

        <div class="stats">
          <div class="stat"><span>کلاس‌ها</span><h4>3</h4></div>
          <div class="stat"><span>هنرجوها</span><h4>-</h4></div>
          <div class="stat"><span>هزینه</span><h4>2M</h4></div>
        </div>

        <div class="actions">
          <button class="primary">مشاهده کلاس‌ها</button>
        </div>
      </div>
    </div>
  </div>

  <div class="section-divider"></div>

  <h1>لیست آموزشگاه‌ها</h1>

  <div class="filters">
    <input type="text" id="search" placeholder="جستجو آموزشگاه...">
    <select id="city">
      <option value="">همه شهرها</option>
      <option value="تهران">تهران</option>
      <option value="اصفهان">اصفهان</option>
    </select>
    <select id="category">
      <option value="">همه دسته‌ها</option>
      <option value="موسیقی">موسیقی</option>
      <option value="هنر">هنر</option>
    </select>
  </div>

  <div class="grid" id="list"></div>
</div>

<!-- MODAL FORM -->
<div class="modal" id="modal">
  <div class="modal-content" id="modalContent">
  <h3 id="modalTitle">ثبت نام</h3>
  <input type="text" id="nameInput" placeholder="نام شما">
  <input type="tel" placeholder="شماره تماس">
  <button onclick="submitForm()">ثبت درخواست</button>
  <button class="close-btn" onclick="closeModal()">بستن</button>
  </div>
</div>

<script>
  const data=[
  {name:'آموزشگاه موسیقی آوا',city:'تهران',category:'موسیقی',rating:4.5,fav:12,tags:['گیتار','پیانو'],img:'https://via.placeholder.com/400x200'},
  {name:'آموزشگاه هنر نو',city:'اصفهان',category:'هنر',rating:3.8,fav:5,tags:['نقاشی','طراحی'],img:'https://via.placeholder.com/400x200'}
  ]

  let currentItem=''
  const list=document.getElementById('list')
  const modal=document.getElementById('modal')
  const modalContent=document.getElementById('modalContent')

  function render(items){
  list.innerHTML=''
  items.forEach((item,i)=>{
  let stars=''
  for(let s=1;s<=5;s++){stars+= s<=Math.round(item.rating)?'★':'☆'}

  list.innerHTML+=`\
  <div class="card">
  <div class="favorite" onclick="toggleFav(${i},this)">❤</div>
  <div class="fav-count" id="fav-${i}">${item.fav}</div>
  <img src="${item.img}">
  <div class="card-content">
  <div class="card-title">${item.name}</div>
  <div class="card-meta">${item.city}</div>
  <div class="meta">📍 اصفهان</div>
  <div class="stars">${stars} (${item.rating})</div>
  <div class="meta">⭐ 4.7</div>
  <div class="tags">${item.tags.map(t=>`<span class='tag'>${t}</span>`).join('')}</div>
  <a class="btn" href="<?= baseUrl() ?>/page/profileAcademy/1">مشاهده</a>
  <a class="btn special-btn" onclick="openModal('${item.name}')">ثبت نام</a>
  </div>
  </div>`
  })
  }

  function toggleFav(i,el){
  el.classList.toggle('active')
  if(el.classList.contains('active')){data[i].fav++}else{data[i].fav--}
  document.getElementById('fav-'+i).innerText=data[i].fav
  }

  function openModal(name){
  currentItem=name
  modal.classList.remove('closing')
  modal.classList.add('active')
  document.getElementById('modalTitle').innerText='ثبت نام در '+name

  // focus first input
  setTimeout(()=>{
    document.getElementById('nameInput').focus()
  },100)
  }

  function closeModal(){
  modal.classList.add('closing')
  setTimeout(()=>{
    modal.classList.remove('active','closing')
  },300)
  }

  function submitForm(){
  alert('درخواست شما برای '+currentItem+' ثبت شد ✅')
  closeModal()
  }

  // prevent closing when clicking inside modal
  modalContent.addEventListener('click',function(e){
    e.stopPropagation()
  })

  // close on outside click
  modal.addEventListener('click',function(){
    closeModal()
  })

  function filter(){
  let s=document.getElementById('search').value
  let c=document.getElementById('city').value
  let cat=document.getElementById('category').value

  let filtered=data.filter(item=>{
  return (!s || item.name.includes(s)) && (!c || item.city==c) && (!cat || item.category==cat)
  })

  render(filtered)
  }

  document.getElementById('search').addEventListener('input',filter)
  document.getElementById('city').addEventListener('change',filter)
  document.getElementById('category').addEventListener('change',filter)

  render(data)
</script>
















