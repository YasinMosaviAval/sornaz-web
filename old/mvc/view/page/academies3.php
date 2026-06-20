
<style>
.section{margin-bottom:30px}
.cards{display:grid;gap:15px}
.card{background:#fff;border-radius:14px;padding:16px;border:1px solid #e5e7eb;box-shadow:0 8px 20px rgba(0,0,0,.05);position:relative}
.my-card{border:2px solid #6366f1;background:linear-gradient(135deg,#eef2ff,#f8fafc)}
.role{display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;color:#fff;margin-right:5px}
.teacher{background:#6366f1}.student{background:#10b981}.staff{background:#f59e0b}
.meta{font-size:13px;color:#555;margin:8px 0}
.actions{margin-top:10px}
.primary{background:#4f46e5;color:#fff}.secondary{background:#e5e7eb}
.stats{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:10px}
.stat{background:#fff;padding:12px;border-radius:10px;text-align:center;border:1px solid #eee}
.stat h4{margin:5px 0 0;font-size:16px}
.stat span{font-size:12px;color:#666}
.divider{margin:30px 0;border-top:1px solid #ddd}

/* NEW FEATURES */
.badge{
  position:absolute;
  top:12px;
  left:12px;
  background:#ef4444;
  color:#fff;
  font-size:11px;
  padding:4px 8px;
  border-radius:999px;
}

.activity{
  font-size:12px;
  color:#64748b;
  margin-top:6px;
}

.chart-box{
  margin-top:15px;
  background:#fff;
  padding:10px;
  border-radius:10px;
  border:1px solid #eee;
}
</style>

<body>

<div class="container">

<div class="section">
<h2>آموزشگاه‌های من</h2>

<div class="cards">

<div class="card my-card">
  <div class="badge">3 پیام جدید</div>

  <h3>آوای هنر</h3>
  <span class="role teacher">مدرس</span>

  <div class="meta">📍 تهران | ⭐ 4.8</div>
  <div class="activity">🕒 آخرین کلاس: دیروز ساعت 18</div>

  <div class="stats">
    <div class="stat"><span>کلاس‌ها</span><h4>12</h4></div>
    <div class="stat"><span>هنرجوها</span><h4>58</h4></div>
    <div class="stat"><span>درآمد</span><h4>18M</h4></div>
  </div>

  <div class="chart-box">
    <canvas id="chart1"></canvas>
  </div>

  <div class="actions">
    <button class="primary">ورود به پنل</button>
    <button class="secondary">مشاهده</button>
  </div>
</div>

<div class="card my-card">
  <div class="badge">1 پیام</div>

  <h3>نوای شرق</h3>
  <span class="role student">هنرجو</span>

  <div class="meta">📍 شیراز | ⭐ 4.5</div>
  <div class="activity">🕒 آخرین کلاس: امروز ساعت 14</div>

  <div class="stats">
    <div class="stat"><span>کلاس‌ها</span><h4>3</h4></div>
    <div class="stat"><span>هنرجوها</span><h4>-</h4></div>
    <div class="stat"><span>هزینه</span><h4>2M</h4></div>
  </div>

  <div class="chart-box">
    <canvas id="chart2"></canvas>
  </div>

  <div class="actions">
    <button class="primary">مشاهده کلاس‌ها</button>
  </div>
</div>

</div>
</div>



</div>

<script>
// Charts
new Chart(document.getElementById('chart1'),{
  type:'line',
  data:{
    labels:['فروردین','اردیبهشت','خرداد','تیر'],
    datasets:[{label:'درآمد',data:[5,8,6,10]}]
  }
})

new Chart(document.getElementById('chart2'),{
  type:'line',
  data:{
    labels:['فروردین','اردیبهشت','خرداد','تیر'],
    datasets:[{label:'هزینه',data:[1,2,1.5,2]}]
  }
})
</script>

</body>











