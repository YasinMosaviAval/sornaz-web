(function () {
  'use strict';
  window.createPanelHeatmap = function (prefix, occupancy = false) {
  let current = [], rooms = [], context = '', signature = '', state = 'ready';
  const en = () => document.documentElement.lang.startsWith('en');
  const element = () => document.getElementById(prefix);
  const setState = function (value) {
    state = value;
    if (!element()) return;
    window.SornazCharts?.dispose(element());
    element().replaceChildren();
    document.getElementById(prefix + 'Summary').textContent = value === 'loading' ? (en()?'Loading heatmap…':'در حال دریافت نقشهٔ حرارتی…') : (en()?'Heatmap could not be loaded.':'دریافت نقشهٔ حرارتی ناموفق بود.');
  };
  const render = function (rows, scope = '', force = false, selectedRooms = rooms) {
    rooms = selectedRooms;
    current = rows; context = scope; state = 'ready';
    const host = element();
    if (!host || !window.SornazCharts || !window.echarts) return;
    const bins = Array.from({length:7},()=>Array(24).fill(0)), seen = new Set();
    let total = 0, unknown = 0;
    const busy = Array.from({length:7},()=>Array.from({length:24},()=>new Set()));
    const available = new Set(rooms.filter(room => room.statusValue === "available").map(room=>Number(room.id)));
    for (const row of rows) {
      if (row.id != null && seen.has(row.id)) continue;
      if (row.id != null) seen.add(row.id);
      const count = Number(row.count ?? 1), date = new Date(row.date + 'T12:00:00Z');
      const minutes = value => /^\d{2}:\d{2}/.test(String(value)) ? Number(value.slice(0,2))*60+Number(value.slice(3,5)) : NaN;
      const start=minutes(row.startTime),end=minutes(row.endTime);
      if (!Number.isFinite(date.getTime()) || !(start>=0 && end>start && end<=1440) || !Number.isFinite(count) || count<=0) {unknown+=Number.isFinite(count)?count:1;continue;}
      total+=count;
      const day=(date.getUTCDay()+1)%7;
      for(let hour=Math.floor(start/60);hour<Math.ceil(end/60);hour++) {
        if (occupancy) { if (available.has(Number(row.classroomId))) busy[day][hour].add(Number(row.classroomId)); bins[day][hour]=busy[day][hour].size; }
        else bins[day][hour]+=count;
      }
    }
    const english=en(),dark=document.documentElement.dataset.mode==='dark';
    const primary=getComputedStyle(document.documentElement).getPropertyValue('--theme-600').trim()||'#6366f1';
    const next=JSON.stringify([bins,total,unknown,scope,english,dark,primary,rooms]);
    if(!force && next===signature && SornazCharts.get(host))return;
    signature=next;
    const days=english?['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday']:['شنبه','یکشنبه','دوشنبه','سه‌شنبه','چهارشنبه','پنجشنبه','جمعه'];
    const hours=Array.from({length:24},(_,i)=>`${String(i).padStart(2,'0')}:00`);
    const data=bins.flatMap((values,day)=>values.map((value,hour)=>[day,hour,value]));
    const text=dark?'#e2e8f0':'#475569';
    document.getElementById(prefix + 'Summary').textContent=english?`${total} sessions · ${unknown} with invalid date/time · ${scope}`:`${total.toLocaleString('fa-IR')} جلسه · ${unknown.toLocaleString('fa-IR')} با تاریخ/ساعت نامشخص · ${scope}`;
    if (occupancy) document.getElementById(prefix + 'Summary').textContent = english ? `${available.size} available classrooms · ${rooms.length-available.size} unavailable/pending · ${scope}` : `${available.size.toLocaleString('fa-IR')} کلاس در دسترس · ${(rooms.length-available.size).toLocaleString('fa-IR')} خارج از دسترس/در انتظار · ${scope}`;
    const option={
      animation:!matchMedia('(prefers-reduced-motion: reduce)').matches,
      aria:{enabled:true},textStyle:{color:text},
      title:occupancy&&rooms.length?{text:scope,left:'center',top:0,textStyle:{color:text,fontSize:14}}:(occupancy ? rooms.length : total)?undefined:{text:english?'No matching sessions':'جلسه‌ای مطابق فیلترها یافت نشد',left:'center',top:'middle',textStyle:{color:text,fontSize:14}},
      tooltip:{position:'top',renderMode:'richText',formatter:p=>`${days[p.value[0]]} · ${hours[p.value[1]]}\n${Number(p.value[2]).toLocaleString(english?'en-US':'fa-IR')} ${occupancy?(english?'occupied classrooms':'کلاس پر'):(english?'sessions':'جلسه')}${occupancy?' · '+Math.max(0,available.size-p.value[2]).toLocaleString(english?'en-US':'fa-IR')+' '+(english?'without bookings':'بدون رزرو'):''}`},
      grid:{top:40,left:65,right:15,bottom:80},
      xAxis:{type:'category',data:days,splitArea:{show:true},axisLabel:{color:text,interval:0,rotate:innerWidth<600?40:0}},
      yAxis:{type:'category',data:hours,inverse:true,splitArea:{show:true},axisLabel:{color:text,interval:1}},
      visualMap:{min:0,max:Math.max(1,...data.map(cell=>cell[2])),calculable:true,orient:'horizontal',left:'center',bottom:5,textStyle:{color:text},inRange:{color:[dark?'#1e293b':'#f1f5f9',primary]}},
      series:[{type:'heatmap',data:(occupancy ? rooms.length : total)?data:[],label:{show:true,color:text,formatter:p=>p.value[2]?Number(p.value[2]).toLocaleString(english?'en-US':'fa-IR'):''},emphasis:{itemStyle:{shadowBlur:8,shadowColor:'rgba(0,0,0,.35)'}}}]
    };
    if(SornazCharts.get(host))SornazCharts.update(host,option,true);else SornazCharts.create(host,option);
  };
  const download=()=>{
    if(!window.SornazCharts?.get(element()))return;
    const link=document.createElement('a');link.download=prefix+'.png';link.href=SornazCharts.exportPng(element());link.click();
  };
  new MutationObserver(()=>{if(state==='ready')render(current,context,true);}).observe(document.documentElement,{attributes:true,attributeFilter:['data-mode','data-theme','lang']});
  return {render, setState, download};
  };
  const schedule = window.createPanelHeatmap('scheduleHeatmap');
  window.renderScheduleHeatmap = schedule.render;
  window.setScheduleHeatmapState = schedule.setState;
  window.downloadScheduleHeatmap = schedule.download;
})();
