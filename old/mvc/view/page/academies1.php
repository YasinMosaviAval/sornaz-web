<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>Chart.js Playground + PDF Export</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>

<style>
body{
font-family:tahoma;
background:#f5f7fb;
margin:0;
padding:20px;
}

.container{
max-width:1200px;
margin:auto;
display:grid;
grid-template-columns:repeat(2,1fr);
gap:15px;
}

.card{
background:#fff;
padding:15px;
border-radius:12px;
box-shadow:0 6px 20px rgba(0,0,0,.05);
}

h3{margin-top:0}

button{
margin-top:10px;
padding:6px 12px;
border:none;
background:#4f46e5;
color:#fff;
border-radius:8px;
cursor:pointer;
}
</style>

</head>
<body>

<h2>📊 Chart.js Playground + Export PDF</h2>

<div class="container">

<div class="card" id="lineBox">
<h3>Line Chart</h3>
<canvas id="line"></canvas>
<button onclick="exportPDF('lineBox')">PDF</button>
</div>

<div class="card" id="barBox">
<h3>Bar Chart</h3>
<canvas id="bar"></canvas>
<button onclick="exportPDF('barBox')">PDF</button>
</div>

<div class="card" id="pieBox">
<h3>Pie Chart</h3>
<canvas id="pie"></canvas>
<button onclick="exportPDF('pieBox')">PDF</button>
</div>

<div class="card" id="doughnutBox">
<h3>Doughnut Chart</h3>
<canvas id="doughnut"></canvas>
<button onclick="exportPDF('doughnutBox')">PDF</button>
</div>

<div class="card" id="radarBox">
<h3>Radar Chart</h3>
<canvas id="radar"></canvas>
<button onclick="exportPDF('radarBox')">PDF</button>
</div>

<div class="card" id="polarBox">
<h3>Polar Area</h3>
<canvas id="polar"></canvas>
<button onclick="exportPDF('polarBox')">PDF</button>
</div>

<div class="card" id="scatterBox">
<h3>Scatter</h3>
<canvas id="scatter"></canvas>
<button onclick="exportPDF('scatterBox')">PDF</button>
</div>

<div class="card" id="bubbleBox">
<h3>Bubble</h3>
<canvas id="bubble"></canvas>
<button onclick="exportPDF('bubbleBox')">PDF</button>
</div>

</div>

<script>

// 📊 LINE
new Chart(document.getElementById('line'),{
type:'line',
data:{
labels:['A','B','C','D'],
datasets:[{label:'Data',data:[10,20,15,30],borderColor:'#4f46e5'}]
}
})

// 📊 BAR
new Chart(document.getElementById('bar'),{
type:'bar',
data:{
labels:['A','B','C','D'],
datasets:[{label:'Data',data:[5,15,10,25],backgroundColor:'#10b981'}]
}
})

// 📊 PIE
new Chart(document.getElementById('pie'),{
type:'pie',
data:{
labels:['A','B','C'],
datasets:[{data:[30,40,30],backgroundColor:['#4f46e5','#10b981','#f59e0b']}]
}
})

// 📊 DOUGHNUT
new Chart(document.getElementById('doughnut'),{
type:'doughnut',
data:{
labels:['A','B','C'],
datasets:[{data:[20,50,30],backgroundColor:['#4f46e5','#10b981','#f43f5e']}]
}
})

// 📊 RADAR
new Chart(document.getElementById('radar'),{
type:'radar',
data:{
labels:['A','B','C','D'],
datasets:[{label:'Score',data:[3,5,2,4],borderColor:'#4f46e5'}]
}
})

// 📊 POLAR
new Chart(document.getElementById('polar'),{
type:'polarArea',
data:{
labels:['A','B','C'],
datasets:[{data:[11,16,7],backgroundColor:['#4f46e5','#10b981','#f59e0b']}]
}
})

// 📊 SCATTER
new Chart(document.getElementById('scatter'),{
type:'scatter',
data:{
datasets:[{
label:'Scatter',
data:[
{x:1,y:2},
{x:2,y:5},
{x:3,y:3},
{x:4,y:7}
],
backgroundColor:'#4f46e5'
}]
}
})

// 📊 BUBBLE
new Chart(document.getElementById('bubble'),{
type:'bubble',
data:{
datasets:[{
label:'Bubble',
data:[
{x:10,y:20,r:10},
{x:15,y:10,r:15},
{x:25,y:30,r:20}
],
backgroundColor:'#10b981'
}]
}
})


// 📄 EXPORT PDF (each chart separately)
function exportPDF(id){

const element=document.getElementById(id)

html2pdf().set({
margin:0.5,
filename:id+'.pdf',
html2canvas:{scale:2},
jsPDF:{unit:'in',format:'a4',orientation:'portrait'}
}).from(element).save()

}

</script>

</body>
</html>