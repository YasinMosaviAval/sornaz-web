/* Website note views use the panel's shared table and pagination renderer. */
(function(root){
'use strict';
let view='table';try{if(localStorage.getItem('notation_web_list_view')==='cards')view='cards';}catch(_){}
const pages=new Map(),sizes=10;
function controls(t,h){const icon=grid=>`<svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">${grid?'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>':'<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M3 15h18M9 3v18"/>'}</svg>`;return `<div class="notation-view-switch" role="group" aria-label="${h(t('Display mode'))}">${['table','cards'].map(mode=>`<button type="button" data-action="web-list-view" data-view="${mode}" aria-label="${h(t(mode==='table'?'Table view':'Card view'))}" title="${h(t(mode==='table'?'Table view':'Card view'))}" aria-pressed="${mode===view}" class="${mode===view?'is-active':''}">${icon(mode==='cards')}</button>`).join('')}</div>`;}
function setView(next){if(!['table','cards'].includes(next))return;view=next;try{localStorage.setItem('notation_web_list_view',view);}catch(_){} }
function notes(items,key,nested,options){
 const {h,t,card,actions}=options,page=Math.min(pages.get(key)||1,Math.max(1,Math.ceil(items.length/sizes)));pages.set(key,page);
 const visible=items.slice((page-1)*sizes,page*sizes);let html;
 if(view==='cards')html='<div class="notation-note-cards">'+visible.map(item=>card(item,nested)).join('')+'</div>';
 else {
  const columns=['Title','Composer','Storage','Updated','Actions'].map(label=>({value:t(label)}));
  const rows=visible.map(item=>({attributes:`data-sheet-id="${Number(item.id)}"`,cells:[{html:`<button type="button" class="open notation-table-title" data-action="open" data-id="${Number(item.id)}">${h(item.metadata?.title||item.title)}</button>`},{value:[item.metadata?.composer,item.metadata?.arranger||item.author].filter(Boolean).join(' – ')},{value:t(item.local?(item.uploaded?'Uploaded':'Local'):'Uploaded')},{value:item.updated_at||''},{html:actions(item)}]}));
  if(!rows.length)rows.push({cells:[{value:t('No sheets yet.'),attributes:'colspan="5" class="notation-table-empty"'}]});
  html='<div class="admin-ui-table notation-table-box"><div class="notation-table-scroll">'+root.AdminPanelTable.tableHTML({columns,rows,attributes:'class="notation-table"'})+'</div></div>';
 }
 return html+`<footer class="notation-pagination" data-notation-page-key="${h(key)}" data-total="${items.length}" hidden></footer>`;
}
function render(items,options){
 if(!items.length)return '';
 if(items.some(item=>item.kind==='list'))return items.map(item=>item.kind==='list'?options.card(item,false,options.expanded.has(item.id)?notes(item.items||[],'list:'+item.id,true,options):''):notes([item],'item:'+item.id,false,options)).join('');
 return notes(items,'root',false,options);
}
function paginate(scope,rerender){scope.querySelectorAll('[data-notation-page-key]').forEach(footer=>{const key=footer.dataset.notationPageKey;root.AdminPanelTable.renderPagination(footer,{total:Number(footer.dataset.total),perPage:sizes,page:pages.get(key)||1,onChange:next=>{pages.set(key,next);rerender();}});});}
root.NotationWebList={controls,setView,render,paginate,resetPages:()=>pages.clear(),nextBatch:count=>pages.set('root',Math.floor(count/sizes)+1)};
})(globalThis);
