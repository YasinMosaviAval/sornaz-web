(function () {
    'use strict';
    if (window.__sornazTrackingStarted || !window.fetch || !window.JSON) return;
    window.__sornazTrackingStarted = true;

    const ENDPOINT = '/system/tracking/ingest';
    const FLUSH_MS = 15000;
    const ACTIVE_MS = 5000;
    const IDLE_MS = 60000;
    const startedAt = Date.now();
    const uuid = () => crypto.randomUUID ? crypto.randomUUID() : 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, c => { const r=Math.random()*16|0; return (c==='x'?r:(r&3|8)).toString(16); });
    const storage = (kind, key, fallback) => { try { const s=kind==='local'?localStorage:sessionStorage; let v=s.getItem(key); if(!v){v=fallback();s.setItem(key,v);} return v; } catch(_){return fallback();} };
    const setSession = (key,value) => { try { sessionStorage.setItem(key,String(value)); } catch(_){} };
    const getNumber = (key, fallback=0) => { try { return Number(sessionStorage.getItem(key)||fallback)||0; } catch(_){return fallback;} };

    const visitUuid=storage('session','sornaz.tracking.visit',uuid);
    const guestId=storage('local','sornaz.tracking.guest',uuid);
    const tabId=storage('session','sornaz.tracking.tab',()=>uuid().replaceAll('-',''));
    const pageViewUuid=uuid();
    const pageSequence=getNumber('sornaz.tracking.pages',0)+1;
    setSession('sornaz.tracking.pages',pageSequence);
    let eventSequence=getNumber('sornaz.tracking.sequence',0);
    let events=[],intervals=[],sections=new Map(),sending=false,dirty=true,completed=false;
    let lastInteraction=Date.now(), lastTick=Date.now(), currentState='active', stateStarted=Date.now();
    let activeMs=0,idleMs=0,readingMs=0,visibleMs=0,hiddenMs=0,clickCount=0,interactionCount=0,keypressCount=0,formInteractionCount=0,maxScrollDepth=0,maxScrollY=0;
    let firstInteractionAt=null,lastInteractionAt=null;

    function pageType(){
        const p=location.pathname;
        if(p.includes('admin-panel'))return'dashboard';
        if(p.includes('article'))return'blog';
        if(p.includes('academ'))return'academy';
        if(p.includes('profile')||p.includes('user'))return'profile';
        if(p==='/'||p.includes('/home'))return'home';
        return'other';
    }
    function cleanText(value,max){ return String(value||'').replace(/\s+/g,' ').trim().slice(0,max); }
    function sectionKey(el,index){
        const own=el.dataset.trackSection||el.id||el.getAttribute('aria-label');
        if(own)return cleanText(own,100).replace(/[^\w\u0600-\u06ff.:-]+/g,'_');
        const heading=el.querySelector&&el.querySelector('h1,h2,h3,h4');
        return ('section_'+index+'_'+cleanText(heading&&heading.textContent,45)).replace(/[^\w\u0600-\u06ff.:-]+/g,'_');
    }
    function ensureSection(el,index){
        const key=sectionKey(el,index); if(!sections.has(key))sections.set(key,{key,type:(el.tagName||'section').toLowerCase(),impressions:0,visibleMs:0,activeMs:0,idleMs:0,readingMs:0,maxVisibility:0,interactions:0,clicks:0,firstSeenAt:null,lastSeenAt:null,visible:false,visibleSince:null});
        el.dataset.trackingSectionKey=key; return sections.get(key);
    }
    const observed=[];
    const observer=('IntersectionObserver'in window)?new IntersectionObserver(entries=>entries.forEach(entry=>{
        const s=sections.get(entry.target.dataset.trackingSectionKey);if(!s)return;
        s.maxVisibility=Math.max(s.maxVisibility,Math.round(entry.intersectionRatio*100));
        if(entry.isIntersecting&&entry.intersectionRatio>=.15&&!s.visible){s.visible=true;s.visibleSince=Date.now();s.impressions++;s.firstSeenAt=s.firstSeenAt||Date.now();queue('section_enter',{sectionKey:s.key,value:s.maxVisibility});}
        else if((!entry.isIntersecting||entry.intersectionRatio<.15)&&s.visible){s.visible=false;s.visibleSince=null;s.lastSeenAt=Date.now();queue('section_leave',{sectionKey:s.key,value:s.maxVisibility});}
        dirty=true;
    }),{threshold:[0,.15,.25,.5,.75,1]}):null;

    function discoverSections(){
        if(!observer)return;
        const nodes=[...document.querySelectorAll('[data-track-section], main > section, main > article, main > div.section, article > section')];
        nodes.forEach((el,i)=>{if(el.dataset.trackingObserved)return;el.dataset.trackingObserved='1';ensureSection(el,i);observer.observe(el);observed.push(el);});
    }
    function closestSection(target){const el=target&&target.closest&&target.closest('[data-tracking-section-key]');return el?sections.get(el.dataset.trackingSectionKey):null;}
    function markInteraction(target,isClick){
        const now=Date.now();lastInteraction=now;lastInteractionAt=now;firstInteractionAt=firstInteractionAt||now;interactionCount++;dirty=true;
        const s=closestSection(target);if(s){s.interactions++;if(isClick)s.clicks++;s.lastSeenAt=now;}
    }
    function targetInfo(target){
        const el=target&&target.closest?target.closest('button,a,[role="button"],input,select,textarea,label,[data-track-action]'):null;
        if(!el)return{};
        const sensitive=el.matches('input[type="password"],input[name*="token" i],input[name*="otp" i],input[name*="national" i],input[name*="card" i],textarea');
        return {targetType:(el.tagName||'').toLowerCase(),targetId:cleanText(el.id||el.dataset.trackAction,191),targetName:cleanText(el.getAttribute('name'),191),targetText:sensitive?'':cleanText(el.getAttribute('aria-label')||el.title||el.textContent||el.value,120)};
    }
    function queue(name,extra={}){
        if(events.length>=100)return;
        eventSequence++;setSession('sornaz.tracking.sequence',eventSequence);
        events.push(Object.assign({uuid:uuid(),sequence:eventSequence,name,category:'behavior',action:name,at:Date.now(),trusted:null,url:location.href,scrollX:Math.round(scrollX),scrollY:Math.round(scrollY),scrollDepth:maxScrollDepth},extra));dirty=true;
    }
    function classify(now){if(document.visibilityState==='hidden')return'hidden';const gap=now-lastInteraction;if(gap<=ACTIVE_MS)return'active';if(gap<=IDLE_MS)return'reading';return'idle';}
    function closeInterval(now,next){
        const duration=Math.max(0,now-stateStarted);if(duration<250){currentState=next;stateStarted=now;return;}
        intervals.push({uuid:uuid(),type:currentState,startedAt:stateStarted,endedAt:now,durationMs:duration,sectionKey:[...sections.values()].find(s=>s.visible)?.key||''});
        if(intervals.length>50)intervals.shift();currentState=next;stateStarted=now;
    }
    function tick(){
        const now=Date.now(),delta=Math.min(5000,Math.max(0,now-lastTick));lastTick=now;
        const next=classify(now);if(next!==currentState)closeInterval(now,next);
        if(document.visibilityState==='hidden')hiddenMs+=delta;else visibleMs+=delta;
        if(next==='active')activeMs+=delta;else if(next==='reading')readingMs+=delta;else if(next==='idle')idleMs+=delta;
        sections.forEach(s=>{if(!s.visible)return;s.visibleMs+=delta;s.lastSeenAt=now;if(next==='active')s.activeMs+=delta;else if(next==='reading')s.readingMs+=delta;else if(next==='idle')s.idleMs+=delta;});
        dirty=true;
    }
    function scrollMetrics(){
        const root=document.documentElement,body=document.body;const h=Math.max(root.scrollHeight,body?body.scrollHeight:0,innerHeight);maxScrollY=Math.max(maxScrollY,Math.round(scrollY));maxScrollDepth=Math.max(maxScrollDepth,Math.min(100,Math.round(((scrollY+innerHeight)/Math.max(h,1))*100)));
    }
    function snapshot(reason){
        tick();const now=Date.now(),total=now-startedAt,isEnd=['pagehide','beforeunload','logout'].includes(reason);
        return {_token:window.adminCsrfToken||'',batchUuid:uuid(),visitUuid,pageViewUuid,guestId,url:location.href,session:{status:isEnd?'ended':classify(now)==='idle'?'idle':'active',endReason:isEnd?(reason==='logout'?'logout':'tab_closed'):null,startedAt,lastActivityAt:lastInteraction,endedAt:isEnd?now:null,totalMs:total,pageViews:pageSequence,activeMs,idleMs,visibleMs,hiddenMs,language:document.documentElement.lang||navigator.language,timezone:Intl.DateTimeFormat().resolvedOptions().timeZone,screenWidth:screen.width,screenHeight:screen.height,viewportWidth:innerWidth,viewportHeight:innerHeight,pixelRatio:devicePixelRatio||1,platform:navigator.platform||'',appVersion:'web-1'},page:{tabId,sequence:pageSequence,path:location.pathname,canonicalUrl:(document.querySelector('link[rel="canonical"]')||{}).href||location.href,title:document.title,routeName:document.body.dataset.route||'',pageType:pageType(),entityType:document.body.dataset.entityType||'',entityId:document.body.dataset.entityId||null,locale:document.documentElement.lang||'',enteredAt:startedAt,exitedAt:isEnd?now:null,totalMs:total,visibleMs,hiddenMs,activeMs,idleMs,readingMs,interactionCount,keypressCount,formInteractionCount,maxScrollDepth,maxScrollY,contentHeight:Math.max(document.documentElement.scrollHeight,document.body?document.body.scrollHeight:0),clickCount,firstInteractionAt,lastInteractionAt,completed:isEnd,exitReason:isEnd?reason:null,referrer:document.referrer},events:events.splice(0),intervals:intervals.splice(0),sections:[...sections.values()].filter(s=>s.impressions||s.visibleMs||s.interactions).map(s=>{const out={key:s.key,type:s.type,impressions:s.impressions,visibleMs:s.visibleMs,activeMs:s.activeMs,idleMs:s.idleMs,readingMs:s.readingMs,maxVisibility:s.maxVisibility,interactions:s.interactions,clicks:s.clicks,firstSeenAt:s.firstSeenAt,lastSeenAt:s.lastSeenAt};s.impressions=s.visibleMs=s.activeMs=s.idleMs=s.readingMs=s.interactions=s.clicks=0;s.firstSeenAt=null;return out;})};
    }
    function restore(batch){events=batch.events.concat(events);intervals=batch.intervals.concat(intervals);batch.sections.forEach(x=>{const s=sections.get(x.key);if(!s)return;s.impressions+=x.impressions;s.visibleMs+=x.visibleMs;s.activeMs+=x.activeMs;s.idleMs+=x.idleMs;s.readingMs+=x.readingMs;s.interactions+=x.interactions;s.clicks+=x.clicks;s.firstSeenAt=s.firstSeenAt||x.firstSeenAt;});}
    async function flush(reason='heartbeat',beacon=false){
        if(sending&&!beacon)return;if(!dirty&&!beacon)return;scrollMetrics();const batch=snapshot(reason);dirty=false;
        const body=JSON.stringify(batch);if(beacon&&navigator.sendBeacon){const ok=navigator.sendBeacon(ENDPOINT,new Blob([body],{type:'application/json'}));if(!ok)restore(batch);return;}
        sending=true;try{const r=await fetch(ENDPOINT,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':window.adminCsrfToken||''},body,keepalive:true});if(!r.ok)throw new Error('tracking '+r.status);}catch(_){restore(batch);dirty=true;}finally{sending=false;}
    }

    document.addEventListener('click',e=>{clickCount++;markInteraction(e.target,true);const s=closestSection(e.target);queue('click',Object.assign(targetInfo(e.target),{trusted:e.isTrusted,sectionKey:s?s.key:'',x:Math.round(e.pageX),y:Math.round(e.pageY),viewportX:Math.round(e.clientX),viewportY:Math.round(e.clientY)}));},true);
    document.addEventListener('keydown',e=>{keypressCount++;markInteraction(e.target,false);if(['Enter','Escape','Tab','ArrowUp','ArrowDown','ArrowLeft','ArrowRight'].includes(e.key))queue('keypress',{label:e.key,targetType:(e.target.tagName||'').toLowerCase(),trusted:e.isTrusted});},true);
    document.addEventListener('submit',e=>{formInteractionCount++;markInteraction(e.target,false);queue('form_submit',{targetType:'form',targetId:cleanText(e.target.id,191),targetName:cleanText(e.target.name,191),trusted:e.isTrusted});},true);
    document.addEventListener('focusin',e=>{if(e.target.matches('input,select,textarea')){formInteractionCount++;markInteraction(e.target,false);queue('field_focus',targetInfo(e.target));}},true);
    document.addEventListener('visibilitychange',()=>{const next=classify(Date.now());if(next!==currentState)closeInterval(Date.now(),next);queue(document.hidden?'page_hidden':'page_visible');if(document.hidden)flush('hidden');});
    addEventListener('scroll',()=>{scrollMetrics();dirty=true;},{passive:true});
    addEventListener('resize',()=>{dirty=true;});
    addEventListener('pagehide',()=>{if(completed)return;completed=true;closeInterval(Date.now(),'hidden');queue('page_view_end');flush('pagehide',true);});
    addEventListener('online',()=>queue('network_online'));addEventListener('offline',()=>queue('network_offline'));
    new MutationObserver(()=>discoverSections()).observe(document.documentElement,{childList:true,subtree:true});
    discoverSections();scrollMetrics();queue('session_start');queue('page_view_start');
    setInterval(tick,1000);setInterval(()=>flush('heartbeat'),FLUSH_MS);
    setTimeout(()=>flush('initial'),1200);
    window.SornazTracking={track:(name,data)=>queue(name,data||{}),flush:()=>flush('manual'),setConsent:()=>{}};
})();
