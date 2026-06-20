<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Advanced Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
<style>
body{font-family:tahoma;margin:0;background:#f5f7fb;padding:20px}
.container{max-width:1100px;margin:auto}
.card{background:#fff;border-radius:14px;padding:16px;border:1px solid #e5e7eb;box-shadow:0 8px 20px rgba(0,0,0,.05);margin-bottom:20px}
.kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.kpi{padding:15px;border-radius:10px;background:#fff;border:1px solid #eee;text-align:center}
.filters button{padding:6px 10px;margin-left:5px;border:none;border-radius:8px;background:#e5e7eb;cursor:pointer}
.filters .active{background:#4f46e5;color:#fff}
.export-btns button{margin-left:5px;padding:6px 10px;border:none;border-radius:8px;background:#10b981;color:#fff;cursor:pointer}
</style>
</head>
<body>

<div class="container" id="report">
<h2>📊 سیستم گزارش‌گیری آموزشگاه</h2>

<div class="kpis" id="kpis"></div>

<div class="filters">
  <button onclick="setRange('month')" class="active">ماهانه</button>
  <button onclick="setRange('year')">سالانه</button>
</div>

<div class="export-btns">
  <button onclick="exportPDF()">📄 خروجی PDF دقیق</button>
  <button onclick="exportExcel()">📊 Excel</button>
</div>

<div class="card">
  <canvas id="chart"></canvas>
</div>

</div>

<script>
const dataSets={
  month:{labels:['فر','ار','خر','تی'],income:[5,8,6,10],students:[30,45,40,60]},
  year:{labels:['1399','1400','1401','1402'],income:[50,80,65,110],students:[300,450,400,600]}
}

let chart,range='month'

function renderKPIs(d){
  let total=d.income.reduce((a,b)=>a+b,0)
  let growth=((d.income.at(-1)-d.income[0])/d.income[0]*100).toFixed(1)
  let best=Math.max(...d.income)
  document.getElementById('kpis').innerHTML=`
    <div class="kpi"><span>درآمد کل</span><h3>${total}</h3></div>
    <div class="kpi"><span>رشد</span><h3>${growth}%</h3></div>
    <div class="kpi"><span>بهترین دوره</span><h3>${best}</h3></div>`
}

function renderChart(){
  const d=dataSets[range]
  renderKPIs(d)
  const ctx=document.getElementById('chart').getContext('2d')
  if(chart) chart.destroy()
  chart=new Chart(ctx,{type:'line',data:{labels:d.labels,datasets:[
    {label:'درآمد',data:d.income,borderColor:'#4f46e5'},
    {label:'هنرجو',data:d.students,borderColor:'#10b981'}]}})
}

function setRange(r){
  range=r
  document.querySelectorAll('.filters button').forEach(b=>b.classList.remove('active'))
  event.target.classList.add('active')
  renderChart()
}

// ✅ HTML2PDF (بدون مشکل فارسی)
function exportPDF(){
  const element = document.getElementById('report')

  const opt = {
    margin:       0.5,
    filename:     'dashboard.pdf',
    image:        { type: 'jpeg', quality: 1 },
    html2canvas:  { scale: 2, useCORS: true },
    jsPDF:        { unit: 'in', format: 'a4', orientation: 'portrait' }
  }

  html2pdf().set(opt).from(element).save()
}

// Excel
function exportExcel(){
  const d=dataSets[range]
  const data=d.labels.map((l,i)=>({دوره:l,درآمد:d.income[i],هنرجو:d.students[i]}))
  const ws=XLSX.utils.json_to_sheet(data)
  const wb=XLSX.utils.book_new()
  XLSX.utils.book_append_sheet(wb,ws,'Report')
  XLSX.writeFile(wb,'report.xlsx')
}

renderChart()
</script>

</body>
</html>