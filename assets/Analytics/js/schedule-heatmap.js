(function () {
  'use strict';
  let current = [], context = '', signature = '', state = 'ready';
  const en = () => document.documentElement.lang.startsWith('en');
  const element = () => document.getElementById('scheduleHeatmap');
  window.setScheduleHeatmapState = function (value) {
    state = value;
    if (!element()) return;
    window.SornazCharts?.dispose(element());
    element().replaceChildren();
    document.getElementById('scheduleHeatmapSummary').textContent = value === 'loading' ? (en()?'Loading heatmap…':'در حال دریافت نقشهٔ حرارتی…') : (en()?'Heatmap could not be loaded.':'دریافت نقشهٔ حرارتی ناموفق بود.');
  };
  window.renderScheduleHeatmap = function (rows, scope = '', force = false) {
    current = rows; context = scope; state = 'ready';
    const host = element();
    if (!host || !window.SornazCharts || !window.echarts) return;
    const bins = Array.from({length:7},()=>Array(24).fill(0)), seen = new Set();
    let total = 0, unknown = 0;
    for (const row of rows) {
      if (row.id != null && seen.has(row.id)) continue;
      if (row.id != null) seen.add(row.id);
      const count = Number(row.count ?? 1), date = new Date(row.date + 'T12:00:00Z');
      const minutes = value => /^\d{2}:\d{2}/.test(String(value)) ? Number(value.slice(0,2))*60+Number(value.slice(3,5)) : NaN;
      const start=minutes(row.startTime),end=minutes(row.endTime);
      if (!Number.isFinite(date.getTime()) || !(start>=0 && end>start && end<=1440) || !Number.isFinite(count) || count<=0) {unknown+=Number.isFinite(count)?count:1;continue;}
      total+=count;
      const day=(date.getUTCDay()+1)%7;
      for(let hour=Math.floor(start/60);hour<Math.ceil(end/60);hour++) bins[day][hour]+=count;
    }
    const english=en(),dark=document.documentElement.dataset.mode==='dark';
    const primary=getComputedStyle(document.documentElement).getPropertyValue('--theme-600').trim()||'#6366f1';
    const next=JSON.stringify([bins,total,unknown,scope,english,dark,primary]);
    if(!force && next===signature && SornazCharts.get(host))return;
    signature=next;
    const days=english?['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday']:['شنبه','یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه'];
    const hours=Array.from({length:24},(_,i)=>`${String(i).padStart(2,'0')}:00`);
    const data=bins.flatMap((values,day)=>values.map((value,hour)=>[day,hour,value]));
    const text=dark?'#e2e8f0':'#475569';
    document.getElementById('scheduleHeatmapSummary').textContent=english?`${total} sessions · ${unknown} with invalid date/time · ${scope}`:`${total.toLocaleString('fa-IR')} جلسه · ${unknown.toLocaleString('fa-IR')} با تاریخ/ساعت نامشخص · ${scope}`;
    const option={
      animation:!matchMedia('(prefers-reduced-motion: reduce)').matches,
      aria:{enabled:true},textStyle:{color:text},
      title:total?undefined:{text:english?'No matching sessions':'جلسه‌ای مطابق فیلترها یافت نشد',left:'center',top:'middle',textStyle:{color:text,fontSize:14}},
      tooltip:{position:'top',renderMode:'richText',formatter:p=>`${days[p.value[0]]} · ${hours[p.value[1]]}\n${Number(p.value[2]).toLocaleString(english?'en-US':'fa-IR')} ${english?'sessions':'جلسه'}`},
      grid:{top:40,left:65,right:15,bottom:80},
      xAxis:{type:'category',data:days,splitArea:{show:true},axisLabel:{color:text,interval:0,rotate:innerWidth<600?40:0}},
      yAxis:{type:'category',data:hours,inverse:true,splitArea:{show:true},axisLabel:{color:text,interval:1}},
      visualMap:{min:0,max:Math.max(1,...data.map(cell=>cell[2])),calculable:true,orient:'horizontal',left:'center',bottom:5,textStyle:{color:text},inRange:{color:[dark?'#1e293b':'#f1f5f9',primary]}},
      series:[{type:'heatmap',data:total?data:[],label:{show:true,color:text,formatter:p=>p.value[2]?Number(p.value[2]).toLocaleString(english?'en-US':'fa-IR'):''},emphasis:{itemStyle:{shadowBlur:8,shadowColor:'rgba(0,0,0,.35)'}}}]
    };
    if(SornazCharts.get(host))SornazCharts.update(host,option,true);else SornazCharts.create(host,option);
  };
  window.downloadScheduleHeatmap=()=>{
    if(!window.SornazCharts?.get('#scheduleHeatmap'))return;
    const link=document.createElement('a');link.download='class-schedule-heatmap.png';link.href=SornazCharts.exportPng('#scheduleHeatmap');link.click();
  };
  new MutationObserver(()=>{if(state==='ready')window.renderScheduleHeatmap(current,context,true);}).observe(document.documentElement,{attributes:true,attributeFilter:['data-mode','data-theme','lang']});
})();
