(function () {
  'use strict';
  let current = [], signature = '';
  window.renderContractTypeCharts = function (members = current, force = false) {
    current = members;
    if (!document.getElementById('contractTypePie') || !window.SornazCharts || !window.echarts) return;
    const en = document.documentElement.lang.startsWith('en'), dark = document.documentElement.dataset.mode === 'dark';
    const selected = document.getElementById('filterStaffType')?.value || '';
    const labels = {teacher:['مدرس','Teacher'],receptionist:['پذیرش','Receptionist'],manager:['مدیر','Manager'],owner:['مالک','Owner'],other:['سایر','Other']};
    const groups = new Map(), people = new Set();
    for (const member of members) {
      const person = String(member.user_id || member.id);
      for (const contract of member.contracts?.length ? member.contracts : [member]) {
        if (!(Number(contract.contractId || contract.member_contract_id) > 0) || ['student','waiting'].includes(contract.type) || !contract.type || (selected && contract.type !== selected)) continue;
        if (!groups.has(contract.type)) groups.set(contract.type, {name:labels[contract.type]?.[en?1:0] || contract.typeLabel || (en?'Other':'سایر'),people:new Set()});
        groups.get(contract.type).people.add(person);people.add(person);
      }
    }
    const data=[...groups.entries()].sort(([a],[b])=>a.localeCompare(b)).map(([type,group])=>({id:type,name:group.name,value:group.people.size}));
    const primary=getComputedStyle(document.documentElement).getPropertyValue('--theme-600').trim()||'#6366f1';
    const next=JSON.stringify([data,people.size,en,dark,primary]);
    if (!force && signature===next && SornazCharts.get('#contractTypePie') && SornazCharts.get('#contractTypeDonut')) return;
    signature=next;
    const total=data.reduce((sum,item)=>sum+item.value,0),text=dark?'#e2e8f0':'#475569';
    document.getElementById('contractTypeChartsSummary').textContent=en?`${people.size} people · ${total} contract-type memberships`:`${people.size.toLocaleString('fa-IR')} عضو دارای قرارداد · ${total.toLocaleString('fa-IR')} عضویت در انواع قرارداد`;
    for (const [id,radius] of [['contractTypePie','68%'],['contractTypeDonut',['38%','68%']]]) {
      const option={
        animation:!matchMedia('(prefers-reduced-motion: reduce)').matches,
        aria:{enabled:true},textStyle:{color:text},color:[primary,'#14b8a6','#f59e0b','#ec4899','#38bdf8','#a78bfa'],
        title:data.length?undefined:{text:en?'No matching contracted members':'عضو دارای قرارداد مطابق فیلترها یافت نشد',left:'center',top:'middle',textStyle:{color:text,fontSize:13}},
        tooltip:{trigger:'item',renderMode:'richText',formatter:p=>`${p.name}\n${Number(p.value).toLocaleString(en?'en-US':'fa-IR')} ${en?'members':'عضو'} · ${p.percent}%`},
        legend:{type:'scroll',bottom:0,textStyle:{color:text}},
        series:[{type:'pie',radius,center:['50%','45%'],data,label:{color:text,formatter:p=>`${p.name}\n${p.value} (${p.percent}%)`},labelLine:{length:10,length2:8},emptyCircleStyle:{color:dark?'#334155':'#e2e8f0'},showEmptyCircle:false}]
      };
      if(SornazCharts.get('#'+id))SornazCharts.update('#'+id,option,true);else SornazCharts.create('#'+id,option);
    }
  };
  new MutationObserver(()=>window.renderContractTypeCharts(current,true)).observe(document.documentElement,{attributes:true,attributeFilter:['data-mode','data-theme','lang']});
})();
