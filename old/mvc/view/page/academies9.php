<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<title>صورتحساب آموزشگاه</title>

<script src="https://cdn.jsdelivr.net/npm/html2pdf.js@0.10.1/dist/html2pdf.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>

<style>

body{
font-family:tahoma;
background:#f5f7fb;
margin:0;
padding:20px;
}

.container{
max-width:900px;
margin:auto;
}

.card{
background:#fff;
padding:20px;
border-radius:14px;
box-shadow:0 8px 20px rgba(0,0,0,.05);
}

h2{margin-top:0}

table{
width:100%;
border-collapse:collapse;
margin-top:15px;
}

th,td{
border:1px solid #ddd;
padding:10px;
text-align:center;
}

th{
background:#f3f4f6;
}

.total{
text-align:left;
margin-top:15px;
font-size:18px;
font-weight:bold;
}

.actions{
margin-top:15px;
display:flex;
gap:10px;
}

button{
padding:8px 12px;
border:none;
border-radius:8px;
cursor:pointer;
color:#fff;
}

.pdf{background:#4f46e5}
.excel{background:#10b981}

</style>

</head>
<body>

<div class="container">

<div class="card" id="invoice">

<h2>📄 صورتحساب آموزشگاه موسیقی</h2>

<p>تاریخ: <span id="date"></span></p>

<table id="table">

<thead>
<tr>
<th>کلاس</th>
<th>هنرجو</th>
<th>تعداد جلسات</th>
<th>مبلغ (تومان)</th>
</tr>
</thead>

<tbody>
<tr>
<td پیانو</td>
<td علی رضایی</td>
<td>8</td>
<td class="price">1200000</td>
</tr>

<tr>
<td گیتار</td>
<td سارا محمدی</td>
<td>6</td>
<td class="price">900000</td>
</tr>

<tr>
<td ویولن</td>
<td محمد کریمی</td>
<td>10</td>
<td class="price">1500000</td>
</tr>

</tbody>

</table>

<div class="total">
جمع کل: <span id="total"></span> تومان
</div>

</div>

<div class="actions">

<button class="pdf" onclick="exportPDF()">📄 خروجی PDF</button>
<button class="excel" onclick="exportExcel()">📊 خروجی Excel</button>

</div>

</div>

<script>

// 📅 date
document.getElementById('date').innerText=new Date().toLocaleDateString('fa-IR')

// 💰 calc total
function calcTotal(){
let sum=0
document.querySelectorAll('.price').forEach(p=>{
sum+=parseInt(p.innerText)
})
document.getElementById('total').innerText=sum.toLocaleString()
}
calcTotal()

// 📄 PDF Export
function exportPDF(){

const element=document.getElementById('invoice')

html2pdf().set({
margin:0.5,
filename:'invoice.pdf',
html2canvas:{scale:2},
jsPDF:{unit:'in',format:'a4',orientation:'portrait'}
}).from(element).save()

}

// 📊 Excel Export
function exportExcel(){

let rows=[]

document.querySelectorAll('#table tbody tr').forEach(tr=>{
let cells=tr.querySelectorAll('td')

rows.push({
کلاس:cells[0].innerText,
هنرجو:cells[1].innerText,
جلسات:cells[2].innerText,
مبلغ:cells[3].innerText
})

})

const ws=XLSX.utils.json_to_sheet(rows)
const wb=XLSX.utils.book_new()
XLSX.utils.book_append_sheet(wb,ws,'Invoice')

XLSX.writeFile(wb,'invoice.xlsx')

}

</script>

</body>
</html>