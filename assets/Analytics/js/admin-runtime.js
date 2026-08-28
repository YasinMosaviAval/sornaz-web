(function(){
'use strict';
if(window.AdminPanelRuntime)return;
const source=(crypto.randomUUID?.()||String(Date.now())+Math.random());
const channel='BroadcastChannel'in window?new BroadcastChannel('sornaz-admin-runtime'):null;
const refreshers=[];
const pending=new Map();
let closingInline=false;

function mutationDetail(url,method){return{source,url:String(url||''),method:String(method||'POST').toUpperCase(),at:Date.now()};}
function matches(rule,url){return typeof rule==='function'?rule(url):rule instanceof RegExp?rule.test(url):String(url).startsWith(String(rule));}
function runRefreshers(detail){
    document.dispatchEvent(new CustomEvent('sornaz:remote-mutation',{detail}));
    window.dispatchEvent(new CustomEvent('sornaz:remote-mutation',{detail}));
    document.dispatchEvent(new Event('visibilitychange'));
    refreshers.forEach(entry=>{
        if(!matches(entry.match,detail.url))return;
        clearTimeout(pending.get(entry));
        pending.set(entry,setTimeout(async()=>{if(entry.busy)return;entry.busy=true;try{await entry.refresh(detail);}catch(error){console.error('Admin realtime refresh failed:',error);}finally{entry.busy=false;}},entry.delay));
    });
}
function publish(url,method){const detail=mutationDetail(url,method);channel?.postMessage(detail);window.dispatchEvent(new CustomEvent('sornaz:data-mutated',{detail}));}
channel?.addEventListener('message',event=>{const detail=event.data;if(!detail||detail.source===source||!detail.url)return;runRefreshers(detail);});

const nativeFetch=window.fetch.bind(window);
window.fetch=async function(input,init){
    const response=await nativeFetch(input,init);
    const method=String(init?.method||(input instanceof Request?input.method:'GET')).toUpperCase();
    if(response.ok&&!['GET','HEAD','OPTIONS'].includes(method))publish(input instanceof Request?input.url:input,method);
    return response;
};

function expandedRows(){const marked=[...document.querySelectorAll('[class*="inline-expand"], [data-admin-inline-expand]')],formRows=[...document.querySelectorAll('tr')].filter(row=>row.previousElementSibling&&row.querySelector('input,select,textarea')&&row.querySelector('button[onclick*="toggle"][onclick*="Inline"],button[onclick*="toggle"][onclick*="Edit"]'));return[...new Set([...marked,...formRows])].filter(row=>row.offsetParent!==null);}
function openerFor(row){const previous=row.previousElementSibling;return previous?.querySelector('[data-inline-toggle],button[data-term-action="inline-edit"],button[onclick*="toggle"][onclick*="InlineEdit"],button[onclick*="toggle"][onclick*="Inline"],button[onclick^="editBranch("]')||null;}
document.addEventListener('click',event=>{
    if(closingInline||event.target.closest('#modalContainer,.admin-inline-modal'))return;
    const rows=expandedRows();if(!rows.length||rows.some(row=>row.contains(event.target)))return;
    for(const row of rows){
        const opener=openerFor(row);if(!opener)continue;
        if(opener===event.target||opener.contains(event.target))continue;
        closingInline=true;try{opener.click();}finally{closingInline=false;}
    }
},true);

window.AdminPanelRuntime={
    register(match,refresh,options={}){const entry={match,refresh,delay:Number(options.delay??80),busy:false};refreshers.push(entry);return()=>{const index=refreshers.indexOf(entry);if(index>=0)refreshers.splice(index,1);};},
    publish,
    refresh(detail){runRefreshers(detail||mutationDetail('manual','GET'));}
};

const globalLoaders=[
    [/\/academy\/admin\/courses(?:\/|$)/,()=>window.loadCourses?.()],
    [/\/academy\/admin\/terms(?:\/|$)|\/academy\/admin\/term-/,()=>window.loadTerms?.()],
    [/\/academy\/admin\/branch-offerings(?:\/|$)/,()=>{window.branchOfferingData=null;return window.loadBranchOfferings?.();}],
    [/\/analytics\/admin-user-access(?:\/|$)|\/analytics\/admin-access-/,()=>Promise.all([window.loadAccessUsers?.(),window.loadAccessCatalog?.()].filter(Boolean))],
    [/\/analytics\/admin-notifications(?:\/|$)/,()=>window.loadAdminNotifications?.()],
    [/\/analytics\/admin-media(?:\/|$)/,()=>window.loadAdminMedia?.()],
    [/\/analytics\/admin-post-categories(?:\/|$)/,()=>window.loadPostCategories?.()]
];
globalLoaders.forEach(([match,refresh])=>window.AdminPanelRuntime.register(match,refresh));
})();
