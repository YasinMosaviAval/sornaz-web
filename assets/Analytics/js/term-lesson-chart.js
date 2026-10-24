(function () {
  'use strict';
  let current = [];
  const states = new Map();
  const english = () => document.documentElement.lang.startsWith('en');
  function aggregate(terms) {
    const courses = new Map((window.termCourses || []).map(course => [Number(course.id), course]));
    const rooms = new Map((window.termClassrooms || []).map(room => [Number(room.id), room]));
    const groups = new Map(), seen = new Set();
    let counted = 0, unknown = 0;
    for (const term of terms) {
      if (!term.students?.length || seen.has(term.id)) continue;
      seen.add(term.id);
      const course = courses.get(Number(term.courseId)), lessonId = course?.lessonId ?? course?.lesson_id;
      const key = lessonId ? String(lessonId) : 'unknown';
      const name = course?.instrument || course?.lessonTitle || (english() ? 'Unknown lesson' : 'درس نامشخص');
      const modes = new Set();
      for (const session of term.sessions || []) {
        const mode = session.deliveryMode || session.delivery_mode || rooms.get(Number(session.classroomId))?.deliveryMode;
        if (['online', 'in_person'].includes(mode)) modes.add(mode);
      }
      if (!modes.size) {
        const mode = rooms.get(Number(term.classroomId || term.scheduleDraft?.classroomId))?.deliveryMode;
        if (['online', 'in_person'].includes(mode)) modes.add(mode);
      }
      if (!modes.size) { unknown++; continue; }
      if (!groups.has(key)) groups.set(key, {name, in_person: 0, online: 0});
      modes.forEach(mode => groups.get(key)[mode]++);
      counted++;
    }
    return {rows: [...groups.values()].sort((a,b) => a.name.localeCompare(b.name, english() ? 'en' : 'fa')), counted, unknown};
  }
  function cylinder(index, color) {
    return (_params, api) => {
      const value = api.value(1);
      if (!value) return;
      const base = api.coord([api.value(0), 0]), top = api.coord([api.value(0), value]);
      const width = Math.min(34, api.size([1, 0])[0] * .28), x = top[0] + (index ? 1 : -1) * width * .6;
      const radius = Math.min(6, Math.max(2, (base[1] - top[1]) / 4));
      return {type:'group', children:[
        {type:'ellipse', shape:{cx:x,cy:base[1],rx:width/2,ry:radius},style:{fill:color}},
        {type:'rect', shape:{x:x-width/2,y:top[1],width,height:base[1]-top[1]},style:{fill:new echarts.graphic.LinearGradient(0,0,1,0,[{offset:0,color},{offset:.5,color:echarts.color.lift(color,.35)},{offset:1,color}])}},
        {type:'ellipse',shape:{cx:x,cy:top[1],rx:width/2,ry:radius},style:{fill:echarts.color.lift(color,.3),stroke:color}},
        {type:'text',style:{x,y:top[1]-radius-8,text:Number(value).toLocaleString(english()?'en-US':'fa-IR'),fill:getComputedStyle(document.documentElement).getPropertyValue('--text').trim() || '#64748b',align:'center',verticalAlign:'bottom',font:'12px Tahoma'}}
      ]};
    };
  }
  window.renderLessonModeChart = function (id, data, labels, force = false) {
    const element = document.getElementById(id);
    if (!element || !window.SornazCharts || !window.echarts) return;
    const en = english(), dark = document.documentElement.dataset.mode === 'dark';
    const primary = getComputedStyle(document.documentElement).getPropertyValue('--theme-600').trim() || '#6366f1';
    const colors = [primary, '#14b8a6'], names = en ? ['In person','Online'] : ['حضوری','آنلاین'];
    const next = JSON.stringify([data,en,dark,primary]);
    if (!force && states.get(id)?.signature === next && SornazCharts.get(element)) return;
    states.set(id, {data, labels, signature: next});
    document.getElementById(id + 'Summary').textContent = en ? `${data.counted} ${labels.unit[1]} · ${data.unknown} ${labels.unknown[1]}` : `${data.counted.toLocaleString('fa-IR')} ${labels.unit[0]} · ${data.unknown.toLocaleString('fa-IR')} ${labels.unknown[0]}`;
    const text = dark ? '#e2e8f0' : '#475569';
    const option = {
      animation: !matchMedia('(prefers-reduced-motion: reduce)').matches,
      color:colors, textStyle:{color:text}, aria:{enabled:true},
      title: data.rows.length ? undefined : {text:labels.empty[en ? 1 : 0],left:'center',top:'middle',textStyle:{color:text,fontSize:14}},
      legend:{top:0,textStyle:{color:text}},
      tooltip:{trigger:'axis',renderMode:'richText',axisPointer:{type:'shadow'}},
      grid:{left:35,right:25,top:70,bottom:75,containLabel:true},
      xAxis:{type:'category',data:data.rows.map(row=>row.name),axisLabel:{color:text,interval:0,overflow:'truncate',width:100}},
      yAxis:{type:'value',min:0,minInterval:1,axisLabel:{color:text},splitLine:{lineStyle:{color:dark?'#334155':'#e2e8f0'}}},
      dataZoom:data.rows.length>8?[{type:'slider',start:0,end:8/data.rows.length*100,bottom:0},{type:'inside'}]:[],
      series:['in_person','online'].map((mode,index)=>({name:names[index],type:'custom',renderItem:cylinder(index,colors[index]),dimensions:['lesson',labels.unit[en ? 1 : 0]],encode:{x:0,y:1,tooltip:1},data:data.rows.map((row,i)=>[i,row[mode]])}))
    };
    if (SornazCharts.get(element)) SornazCharts.update(element,option,true);
    else SornazCharts.create(element,option);
  };
  window.renderTermLessonChart = function (terms = current, force = false) {
    current = terms;
    window.renderLessonModeChart('termLessonChart', aggregate(terms), {
      unit:['ترم','terms'],
      unknown:['ترم با شیوهٔ برگزاری نامشخص','with no attendance mode'],
      empty:['ترم دارای هنرجو با این فیلترها یافت نشد','No matching student terms']
    }, force);
  };
  window.downloadTermLessonChart = function () {
    if (!window.SornazCharts?.get('#termLessonChart')) return;
    const link=document.createElement('a'); link.download='student-terms-by-lesson.png';link.href=SornazCharts.exportPng('#termLessonChart');link.click();
  };
  new MutationObserver(()=>states.forEach((state,id)=>window.renderLessonModeChart(id,state.data,state.labels,true))).observe(document.documentElement,{attributes:true,attributeFilter:['data-mode','data-theme','lang']});
})();
