<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>بازیابی رمز عبور</title>
<style>

/* =========================
   DANSIM-STYLE CLEAN UI
========================= */

body{
  font-family:Tahoma, sans-serif;
  margin:0;
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:16px;
  background:#f5f7fb;
}

body::before{
  content:"";
  position:absolute;
  width:420px;
  height:420px;
  background:#4f46e5;
  opacity:.08;
  filter:blur(90px);
  top:-120px;
  left:-120px;
  border-radius:50%;
}

body::after{
  content:"";
  position:absolute;
  width:420px;
  height:420px;
  background:#06b6d4;
  opacity:.06;
  filter:blur(100px);
  bottom:-140px;
  right:-120px;
  border-radius:50%;
}

.card{
  width:100%;
  max-width:420px;
  background:#fff;
  border-radius:18px;
  padding:26px;
  box-shadow:0 20px 50px rgba(15,23,42,.08);
  border:1px solid #eef2f7;
}

h2{
  text-align:center;
  margin:0 0 6px;
  font-size:18px;
  color:#0f172a;
}

.sub{
  text-align:center;
  font-size:12px;
  color:#64748b;
  margin-bottom:14px;
}

.error{
  text-align:center;
  font-size:12px;
  color:#ef4444;
  min-height:18px;
  margin-bottom:10px;
}

.steps{position:relative;height:340px}

.step{
  position:absolute;
  width:100%;
  top:0;left:0;
  opacity:0;
  transform:translateY(10px);
  transition:.4s ease;
  pointer-events:none;
}

.step.active{
  opacity:1;
  transform:translateY(0);
  pointer-events:auto;
}

.step.exit-left{
  opacity:0;
  transform:translateY(-10px);
}

.input{
  width:100%;
  padding:13px;
  border-radius:12px;
  border:1px solid #e2e8f0;
  background:#f8fafc;
  margin-bottom:10px;
  outline:none;
}

.input:focus{
  border-color:#4f46e5;
  box-shadow:0 0 0 3px rgba(79,70,229,.15);
  background:#fff;
}

.btn{
  width:100%;
  padding:13px;
  border:none;
  border-radius:12px;
  background:#4f46e5;
  color:#fff;
  font-weight:600;
  cursor:pointer;
  margin-top:10px;
}

.btn.secondary{
  background:#f1f5f9;
  color:#0f172a;
}

.switch{
  text-align:center;
  font-size:12px;
  color:#64748b;
  margin-bottom:8px;
}

.link{
  color:#4f46e5;
  cursor:pointer;
  font-weight:600;
}

.otp{
  display:flex;
  justify-content:center;
  gap:8px;
}

.otp input{
  width:46px;
  height:46px;
  border-radius:10px;
  border:1px solid #e2e8f0;
  text-align:center;
  font-size:16px;
}

.timer{
  text-align:center;
  font-size:12px;
  color:#64748b;
  margin-top:8px;
}

.pass-wrap{position:relative;margin-bottom:10px}
.eye{
  position:absolute;
  left:12px;
  top:50%;
  transform:translateY(-50%);
  cursor:pointer;
  color:#64748b;
}

</style>
</head>
<body>

<div class="card">

<h2>بازیابی رمز عبور</h2>
<div class="sub">UI مشابه داشبورد مدرن</div>
<div class="error" id="error"></div>

<div class="steps">

<div class="step active" id="step1">
<input class="input" id="identifier" placeholder="ایمیل یا شماره موبایل">
<div class="switch">
ارسال از طریق:
<span class="link" onclick="setMethod('email')">ایمیل</span> |
<span class="link" onclick="setMethod('sms')">پیامک</span>
</div>
<button class="btn" onclick="goStep(2)">ارسال کد</button>
</div>

<div class="step" id="step2">
<p style="text-align:center;font-size:13px;color:#64748b">کد ارسال شد</p>
<div class="otp">
<input maxlength="1"><input maxlength="1"><input maxlength="1"><input maxlength="1"><input maxlength="1">
</div>
<div class="timer" id="timer">60 ثانیه</div>
<button class="btn secondary" id="resend" disabled onclick="sendOTP(true)">ارسال مجدد</button>
<button class="btn" onclick="goStep(3)">تایید</button>
</div>

<div class="step" id="step3">
<div class="pass-wrap">
<input class="input" type="password" id="p1" placeholder="رمز جدید">
<span class="eye" onclick="toggle('p1')">👁</span>
</div>
<div class="pass-wrap">
<input class="input" type="password" id="p2" placeholder="تکرار رمز">
<span class="eye" onclick="toggle('p2')">👁</span>
</div>
<button class="btn" onclick="resetPass()">تغییر رمز</button>
</div>

</div>
</div>

<script>
let method='email'
let currentStep=1
let countdown=60
let interval

function setMethod(m){method=m}
function showError(msg){document.getElementById('error').innerText=msg}
function validate(){
const v=document.getElementById('identifier').value
return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)||/^09\d{9}$/.test(v)
}

function goStep(step){
if(step===2){if(!validate())return showError('نامعتبر')
sendOTP()}
if(step===3){const code=[...document.querySelectorAll('.otp input')].map(i=>i.value).join('')
if(code!=='12345')return showError('کد اشتباه')}
switchStep(step)}

function switchStep(step){
const prev=document.getElementById('step'+currentStep)
const next=document.getElementById('step'+step)
prev.classList.remove('active')
prev.classList.add('exit-left')
setTimeout(()=>prev.classList.remove('exit-left'),400)
next.classList.add('active')
currentStep=step}

function sendOTP(){startTimer()}
function startTimer(){
countdown=60
const btn=document.getElementById('resend')
btn.disabled=true
clearInterval(interval)
interval=setInterval(()=>{
countdown--
document.getElementById('timer').innerText=countdown+' ثانیه'
if(countdown<=0){clearInterval(interval);btn.disabled=false}
},1000)}

const inputs=document.querySelectorAll('.otp input')
inputs.forEach((i,idx)=>i.addEventListener('input',()=>{
if(i.value&&inputs[idx+1])inputs[idx+1].focus()
}))

function toggle(id){const el=document.getElementById(id);el.type=el.type==='password'?'text':'password'}
function resetPass(){alert('تغییر موفق');location.reload()}
</script>

</body>
</html>