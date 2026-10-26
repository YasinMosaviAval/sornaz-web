(function () {
  'use strict';
  const chart = window.createPanelHeatmap?.('classroomHeatmap', true);
  let rows = [], rooms = [], week = '', end = '', selected = '', generation = 0, state = 'loading';
  const en = () => document.documentElement.lang.startsWith('en');
  const today = () => {const d=new Date();return `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;};
  function render() {
    const field=document.getElementById('classroomHeatmapRoom');
    if (!chart || !field) return;
    const ids=new Set(rooms.map(room=>String(room.id)));
    if(!ids.has(selected)) selected='';
    field.replaceChildren(new Option(en()?'All filtered classrooms':'همه کلاس‌های مطابق فیلتر',''));
    for(const room of rooms)field.add(new Option(room.name,String(room.id)));
    field.value=selected;
    if(state!=='ready'){chart.setState(state);return;}
    const chosen=rooms.filter(room=>!selected||String(room.id)===selected),allowed=new Set(chosen.map(room=>Number(room.id)));
    chart.render(rows.filter(row=>allowed.has(Number(row.classroomId))),`${week} — ${end}`,true,chosen);
  }
  window.updateClassroomHeatmapRooms = function (value) {rooms=value;render();};
  window.selectClassroomHeatmap = function (value) {selected=value;render();};
  window.loadClassroomHeatmap = async function (value) {
    if(!chart||!document.getElementById('classroomHeatmap'))return;
    const request=++generation;state='loading';render();
    try {
      const result=await classroomApi('/academy/admin/classrooms/heatmap?'+new URLSearchParams({week:value||week||today()}),null,'GET');
      if(request!==generation)return;
      if(!Array.isArray(result.heatmap)||!/^\d{4}-\d{2}-\d{2}$/.test(result.weekStart)||!/^\d{4}-\d{2}-\d{2}$/.test(result.weekEnd))throw new Error('Invalid heatmap response');
      rows=result.heatmap;week=result.weekStart;end=result.weekEnd;state='ready';
      document.getElementById('classroomHeatmapWeek').value=week;render();
    }catch(error){if(request===generation){state='error';rows=[];render();}}
  };
  window.changeClassroomHeatmapWeek = function (days) {
    const date=new Date((week||today())+'T12:00:00Z');date.setUTCDate(date.getUTCDate()+days);
    window.loadClassroomHeatmap(date.toISOString().slice(0,10));
  };
  window.downloadClassroomHeatmap = () => chart?.download();
  new MutationObserver(render).observe(document.documentElement,{attributes:true,attributeFilter:['lang']});
})();
