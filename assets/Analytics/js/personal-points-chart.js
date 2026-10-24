(function () {
  'use strict';
  let current = null, signature = '';
  window.renderPersonalPointsChart = function (balance = current, force = false) {
    if (!balance) return;
    current = balance;
    const host=document.getElementById('personalPointsChart');
    if (!host || !window.SornazCharts || !window.echarts) return;
    const en=document.documentElement.lang.startsWith('en'),dark=document.documentElement.dataset.mode==='dark';
    const labels={general:['عمومی','General'],professional:['حرفه‌ای','Professional'],specialized:['تخصصی','Specialized']};
    let negative=false;
    const data=Object.entries(labels).map(([id,names])=>{
      const raw=Number(balance[id]||0);if(raw<0)negative=true;
      return {id,name:names[en?1:0],value:Number.isFinite(raw)?Math.max(0,raw):0};
    });
    const primary=getComputedStyle(document.documentElement).getPropertyValue('--theme-600').trim()||'#6366f1';
    const next=JSON.stringify([data,negative,en,dark,primary]);
    if (!force && signature===next && SornazCharts.get(host)) return;
    signature=next;
    const total=data.reduce((sum,item)=>sum+item.value,0),text=dark?'#e2e8f0':'#475569';
    document.getElementById('personalPointsChartSummary').textContent=(en?`${total.toLocaleString('en-US')} confirmed points`:`${total.toLocaleString('fa-IR')} امتیاز تأییدشده`)+(negative?(en?' · Negative balances are excluded from pie shares.':' · موجودی منفی در سهم‌های نمودار دایره‌ای نمایش داده نمی‌شود.'):'');
    const option={
      animation:!matchMedia('(prefers-reduced-motion: reduce)').matches,
      aria:{enabled:true},textStyle:{color:text},color:['#f59e0b',primary,'#14b8a6'],
      title:total?undefined:{text:en?'No confirmed points yet':'هنوز امتیاز تأییدشده‌ای ثبت نشده است',left:'center',top:'middle',textStyle:{color:text,fontSize:14}},
      tooltip:{trigger:'item',renderMode:'richText',formatter:p=>`${p.name}\n${Number(p.value).toLocaleString(en?'en-US':'fa-IR')} ${en?'points':'امتیاز'} · ${p.percent}%`},
      legend:{bottom:0,textStyle:{color:text}},
      series:[{type:'pie',radius:'68%',center:['50%','45%'],data:total?data:[],stillShowZeroSum:false,showEmptyCircle:false,label:{color:text,show:total>0,formatter:p=>p.value?`${p.name}\n${Number(p.value).toLocaleString(en?'en-US':'fa-IR')} (${p.percent}%)`:''}}]
    };
    if(SornazCharts.get(host))SornazCharts.update(host,option,true);else SornazCharts.create(host,option);
  };
  new MutationObserver(()=>window.renderPersonalPointsChart(current,true)).observe(document.documentElement,{attributes:true,attributeFilter:['data-mode','data-theme','lang']});
})();
