<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Advanced Dashboard</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
body{font-family:tahoma;margin:0;background:#f5f7fb;padding:20px}
.container{max-width:1100px;margin:auto}
.card{background:#fff;border-radius:14px;padding:16px;border:1px solid #e5e7eb;box-shadow:0 8px 20px rgba(0,0,0,.05);margin-bottom:20px}

/* KPI */
.kpis{display:grid;grid-template-columns:repeat(3,1fr);gap:10px}
.kpi{padding:15px;border-radius:10px;background:#fff;border:1px solid #eee;text-align:center}
.kpi h3{margin:5px 0}
.kpi span{font-size:12px;color:#666}

/* FILTER */
.filters button{padding:6px 10px;margin-left:5px;border:none;border-radius:8px;background:#e5e7eb;cursor:pointer}
.filters .active{background:#4f46e5;color:#fff}

.details{font-size:12px;margin-top:10px;color:#444}
</style>
</head>
<body>

<div class="container">

<h2>📊 داشبورد پیشرفته</h2>

<div class="kpis" id="kpis"></div>

<div class="filters">
  <button onclick="setRange('month')" class="active">ماهانه</button>
  <button onclick="setRange('year')">سالانه</button>
</div>

<div class="card">
  <canvas id="chart"></canvas>
  <div class="details" id="details">روی نمودار کلیک کنید</div>
</div>

</div>

<script>

const dataSets={
  month:{labels:['فر','ار','خر','تی'],income:[5,8,6,10],students:[30,45,40,60]},
  year:{labels:['1399','1400','1401','1402'],income:[50,80,65,110],students:[300,450,400,600]}
}

let chart
let range='month'

function movingAverage(data){
  let avg=[]
  for(let i=0;i<data.length;i++){
    let prev=data[i-1]||data[i]
    avg.push(((prev+data[i])/2).toFixed(1))
  }
  return avg
}

function renderKPIs(d){
  let total=d.income.reduce((a,b)=>a+b,0)
  let growth=((d.income.at(-1)-d.income[0])/d.income[0]*100).toFixed(1)
  let best=Math.max(...d.income)

  document.getElementById('kpis').innerHTML=`
    <div class="kpi"><span>درآمد کل</span><h3>${total}</h3></div>
    <div class="kpi"><span>رشد</span><h3>${growth}%</h3></div>
    <div class="kpi"><span>بهترین دوره</span><h3>${best}</h3></div>
  `
}

function renderChart(){
  const d=dataSets[range]
  renderKPIs(d)

  const ctx=document.getElementById('chart').getContext('2d')
  if(chart) chart.destroy()

  const grad1=ctx.createLinearGradient(0,0,0,300)
  grad1.addColorStop(0,'rgba(79,70,229,.5)')
  grad1.addColorStop(1,'rgba(79,70,229,0)')

  const grad2=ctx.createLinearGradient(0,0,0,300)
  grad2.addColorStop(0,'rgba(16,185,129,.4)')
  grad2.addColorStop(1,'rgba(16,185,129,0)')

  chart=new Chart(ctx,{
    type:'line',
    data:{
      labels:d.labels,
      datasets:[
        {
          label:'درآمد',
          data:d.income,
          fill:true,
          backgroundColor:grad1,
          borderColor:'#4f46e5',
          tension:.4
        },
        {
          label:'هنرجو',
          data:d.students,
          fill:true,
          backgroundColor:grad2,
          borderColor:'#10b981',
          tension:.4
        },
        {
          label:'پیش‌بینی',
          data:movingAverage(d.income),
          borderDash:[5,5],
          borderColor:'#f59e0b',
          tension:.4
        }
      ]
    },
    options:{
      onClick:(e,el)=>{
        if(el.length){
          let i=el[0].index
          document.getElementById('details').innerText=
            'جزئیات '+d.labels[i]+': درآمد '+d.income[i]+' | هنرجو '+d.students[i]
        }
      },
      plugins:{tooltip:{backgroundColor:'#111',titleColor:'#fff',bodyColor:'#fff'}}
    }
  })
}

function setRange(r){
  range=r
  document.querySelectorAll('.filters button').forEach(b=>b.classList.remove('active'))
  event.target.classList.add('active')
  renderChart()
}

renderChart()

</script>

</body>
</html>

