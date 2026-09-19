(() => {
  'use strict';
  const boot = window.SOCIAL_BOOT || {}, fa = boot.locale !== 'en';
  const t = (a, b) => fa ? a : b;
  const root = document.getElementById('community-root');
  const esc = value => String(value ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
  const paths = {
    home:'M3 11 12 3l9 8v10h-6v-7H9v7H3z', heart:'M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8L12 21l8.8-8.6a5.5 5.5 0 0 0 0-7.8z',
    send:'m22 2-7 20-4-9-9-4 20-7ZM22 2 11 13', comment:'M21 11.5a8.5 8.5 0 0 1-8.5 8.5H3l2-5a8.5 8.5 0 1 1 16-3.5Z',
    save:'M5 3h14v19l-7-5-7 5z', plus:'M12 5v14M5 12h14M5 2h14a3 3 0 0 1 3 3v14a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V5a3 3 0 0 1 3-3Z',
    user:'M20 21a8 8 0 0 0-16 0M16 7a4 4 0 1 1-8 0 4 4 0 0 1 8 0Z', search:'M21 21l-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z',
    close:'m5 5 14 14M19 5 5 19', back:'m15 4-8 8 8 8', more:'M5 12h.01M12 12h.01M19 12h.01', trash:'M3 6h18M9 6V3h6v3M5 6l1 15h12l1-15M10 10v7M14 10v7',
    play:'m8 4 12 8-12 8z', pause:'M8 4v16M16 4v16', volume:'M11 3 5 8H2v8h3l6 5zM16 8a6 6 0 0 1 0 8M19 4a11 11 0 0 1 0 16',
    mute:'M11 3 5 8H2v8h3l6 5zM16 9l6 6M22 9l-6 6', refresh:'M20 7V2l-3 3a9 9 0 1 0 3 12M20 7h-5', image:'M3 3h18v18H3zM3 17l6-6 4 4 3-3 5 5M8 7h.01', down:'m5 9 7 7 7-7'
  };
  const icon = name => `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="${paths[name] || paths.user}"/></svg>`;
  const button = (name, label, action, extra = '') => {
    const className = extra.match(/class="([^"]*)"/)?.[1] || '';
    return `<button type="button" class="icon-button ${className}" aria-label="${esc(label)}" data-action="${action}" ${extra.replace(/class="[^"]*"/,'')}>${icon(name)}</button>`;
  };
  function mediaURL(value) {
    if (!value) return '';
    try {
      const u = new URL(value, location.origin);
      if (u.origin !== location.origin || !['http:','https:'].includes(u.protocol)) return '';
      return u.pathname.replace(/^\/api\/sornaz\/v1\/social\/media\//, boot.api + '/media/') + u.search;
    } catch (_) { return ''; }
  }
  const avatar = (user = {}, story = false) => `<span class="avatar${story ? ' story' : ''}">${mediaURL(user.avatar) ? `<img src="${esc(mediaURL(user.avatar))}" alt="" loading="lazy">` : icon('user')}</span>`;
  function age(value) {
    if (!value) return '';
    const date = new Date(String(value).replace(' ', 'T') + (/Z|[+-]\d\d:\d\d$/.test(value) ? '' : 'Z'));
    if (Number.isNaN(date.getTime())) return '';
    return new Intl.DateTimeFormat(fa ? 'fa-IR' : 'en', {month:'short',day:'numeric'}).format(date);
  }
  const cache = new Map(), pending = new Map(), busy = new Set();
  let blockedUntil = 0;
  async function api(path, data, fresh = false) {
    const key = boot.api + path;
    if (!data && !fresh && cache.get(key)?.until > Date.now()) return structuredClone(cache.get(key).value);
    if (!data && pending.has(key)) return structuredClone(await pending.get(key));
    if (Date.now() < blockedUntil) throw new Error(t('سرویس موقتاً در دسترس نیست؛ کمی بعد دوباره تلاش کنید.','Service temporarily unavailable. Please try again shortly.'));
    const task = (async () => {
      const controller = new AbortController(), timeout = setTimeout(() => controller.abort(), data instanceof FormData ? 120000 : 20000);
      try {
        let body;
        if (data instanceof FormData) body = data;
        else if (data) {
          body = new FormData();
          for (const [k,v] of Object.entries(data)) Array.isArray(v) ? v.forEach(x => body.append(k+'[]',x)) : body.append(k,v);
        }
        const response = await fetch(key, {method:data ? 'POST':'GET', body, credentials:'same-origin', redirect:'error', signal:controller.signal,
          headers:{Accept:'application/json','X-CSRF-Token':boot.csrf || '', 'Accept-Language':fa ? 'fa':'en'}});
        if (response.status >= 500 || response.status === 429) {
          const retry = response.headers.get('Retry-After');
          const seconds = /^\d+$/.test(retry || '') ? Number(retry) : (Date.parse(retry)-Date.now())/1000;
          blockedUntil = Date.now() + Math.min(3600,Math.max(30,seconds || 60))*1000;
        }
        if (!response.ok) throw new Error(response.status === 401 ? t('برای ادامه وارد حساب شوید.','Sign in to continue.') : response.status === 403 ? t('دسترسی مجاز نیست؛ صفحه را تازه کنید.','Access denied. Refresh the page.') : t('عملیات انجام نشد؛ دوباره تلاش کنید.','Could not complete the request. Please retry.'));
        let value = await response.json();
        if ('status' in value && 'data' in value) value = value.data;
        if (value.success === false) throw new Error(t('عملیات انجام نشد. اطلاعات را بررسی کنید.','Could not complete the request. Check your input.'));
        if ('data' in value) value = value.data;
        if (data) cache.clear();
        else { if(cache.size >= 64) cache.delete(cache.keys().next().value); cache.set(key,{until:Date.now()+60000,value}); }
        return value;
      } catch (e) {
        if (e instanceof TypeError || e.name === 'AbortError') {
          blockedUntil = Date.now()+30000;
          throw new Error(t('ارتباط برقرار نشد. اتصال اینترنت را بررسی کنید.','Could not connect. Check your internet connection.'));
        }
        throw e;
      } finally { clearTimeout(timeout); }
    })();
    if (!data) pending.set(key,task);
    try { return structuredClone(await task); } finally { if (!data) pending.delete(key); }
  }
  let toastTimer;
  function toast(message) { const n=document.getElementById('community-toast'); n.textContent=message; n.classList.add('show'); clearTimeout(toastTimer); toastTimer=setTimeout(()=>n.classList.remove('show'),3500); }
  async function once(key, el, fn) {
    if (busy.has(key)) return;
    busy.add(key); if(el) el.disabled=true;
    try { await fn(); } catch(e) { toast(e.message); } finally { busy.delete(key); if(el) el.disabled=false; }
  }
  const signedIn = () => { if(boot.userId) return true; toast(t('برای ادامه وارد حساب شوید.','Sign in to continue.')); return false; };
  const join = () => `<div class="empty">${icon('user')}<h2>${t('به جامعه سُرناز بپیوندید','Join the Sornaz community')}</h2><p>${t('اجرای خود را به اشتراک بگذارید و با اهالی موسیقی در ارتباط باشید.','Share your performances and connect with musicians.')}</p><div class="row"><a class="primary" href="/login">${t('ورود','Sign in')}</a><a class="text-button" href="/register">${t('ایجاد حساب','Create account')}</a></div></div>`;
  const empty = text => `<div class="empty">${icon('image')}<p>${esc(text)}</p></div>`;
  const postMap = new Map();
  let epoch = 0, currentRoute = '', stories = [], observer;
  const navItems = [['home','/community',t('خانه','Home')],['search','#search',t('جستجو','Search')],['send','/community/direct',t('پیام‌ها','Messages')],['heart','/community/notifications',t('اعلان‌ها','Notifications')],['plus','/community/create',t('ایجاد','Create')],['save','/community/saved',t('ذخیره‌شده‌ها','Saved')],['user',`/community/users/${boot.userId || 0}`,t('پروفایل','Profile')]];
  const navLink = ([i,url,label]) => `<a href="${url}" class="nav-link${currentRoute === url ? ' active':''}" ${url==='#search'?'data-action="search"':'data-route'} aria-label="${esc(label)}">${icon(i)}<span>${esc(label)}</span></a>`;
  function shell() {
    const brand=`<a class="brand" href="/"><img src="/assets/images/logo/cropped-favicon_512x512.jpg" alt=""><span>${t('سُرناز','Sornaz')}</span></a>`;
    root.innerHTML=`<aside class="side-nav">${brand}${navItems.map(navLink).join('')}<a href="/" class="nav-link side-bottom">${icon('back')}<span>${t('بازگشت به سایت','Back to website')}</span></a></aside><header class="mobile-top">${brand}<div class="row">${button('search',t('جستجو','Search'),'search')}${button('heart',t('اعلان‌ها','Notifications'),'notifications')}</div></header><main class="main" id="main-content"></main><nav class="mobile-nav" aria-label="${t('ناوبری جامعه','Community navigation')}">${navItems.filter((_,i)=>[0,2,4,6].includes(i)).map(navLink).join('')}</nav>`;
  }
  const main = () => document.getElementById('main-content');
  function navigate(path, replace = false) {
    if (!path.startsWith('/community')) return;
    history[replace?'replaceState':'pushState']({},'',path);
    render();
  }
  const header = title => `<header class="page-head">${button('back',t('برگشت','Back'),'back')}<h1 class="grow">${esc(title)}</h1></header>`;
  function remember(rows) { for(const p of rows) postMap.set(Number(p.id),p); return rows; }
  function media(p, detail = false) {
    const url = mediaURL(p.media);
    if(!url) return '';
    return String(p.mime).startsWith('video/') ? `<div class="post-image-wrap"><video class="post-media feed-video" src="${esc(url)}" muted playsinline loop preload="none" ${detail?'controls':''}></video>${button('mute',t('صدا','Sound'),'video-sound','class="post-video-toggle"')}</div>` : `<img class="post-media" src="${esc(url)}" alt="${esc(p.body || t('تصویر پست','Post image'))}" loading="lazy">`;
  }
  function postCard(p) {
    const id=Number(p.id), u=p.author || {};
    return `<article class="post-card" data-post="${id}"><header class="post-header row"><a data-route href="/community/users/${Number(u.id)}">${avatar(u)}</a><a class="grow" data-route href="/community/users/${Number(u.id)}"><strong>${esc(u.name)}</strong><time>${age(p.created_at)}</time></a>${Number(p.owner_id)===Number(boot.userId)?button('trash',t('حذف پست','Delete post'),'delete-post',`data-id="${id}"`):''}</header>${media(p)}<div class="post-actions"><button class="icon-button ${p.liked?'liked':''}" data-action="like" data-id="${id}" aria-label="${t('پسندیدن','Like')}" aria-pressed="${!!p.liked}">${icon('heart')}</button><a class="icon-button" data-route href="/community/posts/${id}" aria-label="${t('نظرها','Comments')}">${icon('comment')}</a>${button('send',t('ارسال برای اعضا','Send to members'),'share',`data-id="${id}"`)}<button class="icon-button save" data-action="save" data-id="${id}" aria-label="${t('ذخیره','Save')}" aria-pressed="${!!p.saved}">${icon('save')}</button></div><strong data-like-count>${Number(p.likes)||0} ${t('پسند','likes')}</strong><p class="caption"><strong>${esc(u.name)}</strong>${esc(p.body)}</p><a class="comments-link" href="/community/posts/${id}" data-route>${t('مشاهده و نوشتن نظر','View and write comments')}</a></article>`;
  }
  function videos() {
    observer?.disconnect();
    observer=new IntersectionObserver(entries=>entries.forEach(e=>{const v=e.target;if(e.isIntersecting && !document.hidden) v.play().catch(()=>{});else v.pause();}),{threshold:.65});
    document.querySelectorAll('.feed-video').forEach(v=>observer.observe(v));
  }
  function storyStrip() {
    const groups=new Map();
    for(const p of stories) { const id=Number(p.owner_id); if(!groups.has(id)) groups.set(id,[]); groups.get(id).push(p); }
    return `<div class="story-strip" aria-label="${t('استوری‌ها','Stories')}"><a class="story-person" data-route href="/community/create?kind=story"><span class="avatar story">${icon('plus')}</span><span>${t('استوری شما','Your story')}</span></a>${[...groups].map(([id,list])=>`<button class="story-person" data-action="story" data-owner="${id}">${avatar(list[0].author,true)}<span>${esc(list[0].author?.name)}</span></button>`).join('')}${stories.length && stories.length%30===0?`<button class="story-person" data-action="more-stories">${icon('plus')}<span>${t('بیشتر','More')}</span></button>`:''}</div>`;
  }
  async function feed(version, saved = false) {
    if(saved && !boot.userId) { main().innerHTML=join();return; }
    const [posts, storyRows]=await Promise.all([api('/posts'+(saved?'?saved=1':'')),saved?Promise.resolve([]):api('/posts?kind=story')]);
    if(version!==epoch) return;
    remember(posts); stories=remember(storyRows);
    main().innerHTML=`<div class="feed-layout"><section class="feed-column">${saved?header(t('ذخیره‌شده‌ها','Saved')):storyStrip()}<div id="feed-posts">${posts.map(postCard).join('') || empty(t('هنوز پستی منتشر نشده است.','No posts yet.'))}</div>${posts.length===30?`<button class="text-button load-more" data-action="more-posts" data-before="${Number(posts.at(-1).id)}" data-saved="${saved?1:0}">${t('نمایش بیشتر','Load more')}</button>`:''}</section><aside class="feed-aside"><h2>${t('جامعه سُرناز','Sornaz community')}</h2><p class="muted">${t('از تمرین‌های کوچک تا اجراهای بزرگ؛ موسیقی‌تان را با دیگران به اشتراک بگذارید.','From daily practice to your next performance. Share your music with the community.')}</p>${boot.userId?`<a class="primary" data-route href="/community/create">${t('پست جدید','Create post')}</a>`:join()}<p><small>© Sornaz</small></p></aside></div>`;
    videos();
  }
  const commentRow = c => `<div class="comment" data-comment="${Number(c.id)}"><a data-route href="/community/users/${Number(c.author.id)}">${avatar(c.author)}</a><div class="grow"><p><strong>${esc(c.author.name)}</strong> ${esc(c.body)}</p><small>${age(c.created_at)}</small></div>${c.canDelete?button('trash',t('حذف نظر','Delete comment'),'delete-comment',`data-id="${Number(c.id)}"`):''}</div>`;
  async function postDetail(id, version) {
    const [p,comments]=await Promise.all([api('/posts/'+id),api('/posts/'+id+'/comments')]);
    if(version!==epoch) return;
    remember([p]);
    main().innerHTML=`<section class="post-detail" data-post="${id}"><div class="detail-image">${media(p,true) || `<p class="empty">${esc(p.body)}</p>`}</div><div class="comment-panel">${header(t('نظرها','Comments'))}<div class="comment-list" id="comments"><div class="comment">${avatar(p.author)}<p><strong>${esc(p.author.name)}</strong> ${esc(p.body)}</p></div><div id="comment-rows">${comments.map(commentRow).join('')}</div>${comments.length===30?`<button class="text-button" data-action="more-comments" data-id="${id}" data-before="${Number(comments.at(-1).id)}">${t('نظرهای بیشتر','More comments')}</button>`:''}</div>${boot.userId?`<form class="composer" data-form="comment" data-id="${id}"><textarea name="body" maxlength="2000" required placeholder="${t('نظر خود را بنویسید…','Add a comment…')}" aria-label="${t('نظر','Comment')}"></textarea><button type="submit">${t('ارسال','Post')}</button></form>`:join()}</div></section>`;
  }
  function openDialog(title, content) {
    document.querySelector('dialog')?.close(); document.querySelector('dialog')?.remove();
    const d=document.createElement('dialog');d.className='dialog';d.innerHTML=`<div class="page-head"><h2 class="grow">${esc(title)}</h2>${button('close',t('بستن','Close'),'close-dialog')}</div>${content}`;
    document.body.append(d); d.addEventListener('close',()=>d.remove()); d.addEventListener('click',e=>{if(e.target===d)d.close();}); d.showModal();return d;
  }
  async function peopleDialog(mode, postId=0) {
    if(!signedIn()) return;
    const selected=new Set();
    const d=openDialog(mode==='share'?t('ارسال برای اعضا','Send to members'):t('جستجوی اعضا','Search members'),`<input class="input search-input" aria-label="${t('نام کاربری','Username')}" placeholder="${t('جستجوی نام کاربری…','Search username…')}"><div class="dialog-body"><div class="search-state">${t('در حال دریافت…','Loading…')}</div></div>${mode==='share'?`<div class="dialog-footer"><button class="primary" data-send-selected disabled>${t('ارسال','Send')}</button></div>`:''}`);
    let timer, serial=0;
    const search=async q=>{
      const n=++serial;
      try {
        const users=await api('/people?q='+encodeURIComponent(q));if(!d.isConnected || n!==serial)return;
        d.querySelector('.dialog-body').innerHTML=users.filter(u=>Number(u.id)!==Number(boot.userId)).map(u=> mode==='share'?`<label class="person-row">${avatar(u)}<span class="grow">${esc(u.name)}<small dir="ltr"> @${esc(u.username)}</small></span><input type="checkbox" value="${Number(u.id)}" ${selected.has(Number(u.id))?'checked':''}></label>`:`<button class="person-row" data-person="${Number(u.id)}">${avatar(u)}<span>${esc(u.name)}<small> @${esc(u.username)}</small></span></button>`).join('') || `<p class="search-state">${t('عضوی پیدا نشد.','No members found.')}</p>`;
      }catch(e){if(d.isConnected)d.querySelector('.dialog-body').textContent=e.message;}
    };
    d.querySelector('input').addEventListener('input',e=>{clearTimeout(timer);timer=setTimeout(()=>search(e.target.value.trim()),450);});
    d.addEventListener('close',()=>{clearTimeout(timer);serial++;});
    d.addEventListener('change',e=>{if(e.target.type!=='checkbox')return;const id=Number(e.target.value);if(e.target.checked && selected.size>=10){e.target.checked=false;toast(t('حداکثر ۱۰ نفر را انتخاب کنید.','Select up to 10 members.'));return;}e.target.checked?selected.add(id):selected.delete(id);d.querySelector('[data-send-selected]').disabled=!selected.size;});
    d.addEventListener('click',e=>{
      const person=e.target.closest('[data-person]');
      if(person) once('person',person,async()=>{const id=Number(person.dataset.person);if(mode==='direct'){const c=await api('/conversations',{user_id:id});d.close();navigate('/community/direct/'+Number(c.id));}else{d.close();navigate('/community/users/'+id);}});
      const send=e.target.closest('[data-send-selected]');
      if(send) once('share',send,async()=>{await api('/posts/'+postId+'/share',{user_ids:[...selected]});d.close();toast(t('ارسال شد.','Sent.'));});
    });
    await search('');
  }
  async function notifications(version) {
    if(!boot.userId){main().innerHTML=join();return;}
    const rows=await api('/notifications');if(version!==epoch)return;
    main().innerHTML=`<section class="content-page">${header(t('اعلان‌ها','Notifications'))}${rows.map(n=>`<button class="person-row ${n.read_at?'':'unread'}" data-action="notification" data-id="${Number(n.id)}" data-kind="${esc(n.kind)}" data-target="${Number(n.target_id)}" data-actor="${Number(n.actor_id)}">${avatar(n.actor)}<span class="grow"><strong>${esc(n.actor?.name)}</strong> ${esc(n.body)}<time class="notification-time">${age(n.created_at)}</time></span>${n.read_at?'':'<span aria-label="'+t('خوانده‌نشده','Unread')+'">●</span>'}</button>`).join('') || empty(t('هنوز اعلانی ندارید.','You have no notifications yet.'))}</section>`;
  }
  function messageRow(m) {
    const parts=String(m.body || '').split(/(\/community\/(?:posts|stories)\/[1-9]\d*)/g);
    const content=parts.map(p=>/^\/community\/(posts|stories)\/[1-9]\d*$/.test(p)?`<a class="shared-post" data-route href="${p}">${icon('image')}<span>${p.includes('/stories/')?t('مشاهده استوری','View story'):t('مشاهده پست','View post')}</span></a>`:esc(p)).join('');
    return `<div class="message ${m.mine?'mine':''}" data-message="${Number(m.id)}">${content}${m.file?`<p>${esc(m.file.name)}</p>`:''}<time>${esc(m.createdAt || '')}</time></div>`;
  }
  let conversationCursor=0;
  async function direct(id, version) {
    if(!boot.userId){main().innerHTML=join();return;}
    const [rows,data]=await Promise.all([api('/conversations'),id?api('/conversations/'+id+'/messages'):Promise.resolve(null)]);if(version!==epoch)return;
    const messages=data?.messages || [];conversationCursor=messages.length?Number(messages.at(-1).id):0;
    const c=rows.find(x=>Number(x.id)===id);
    main().innerHTML=`<section class="direct-layout ${id?'has-conversation':''}"><aside class="conversation-list"><header class="page-head"><h1 class="grow">${t('پیام‌ها','Messages')}</h1>${button('plus',t('پیام جدید','New message'),'new-direct')}${button('refresh',t('تازه‌سازی','Refresh'),'refresh')}</header>${rows.map(c=>`<a class="person-row" data-route href="/community/direct/${Number(c.id)}">${avatar({avatar:c.image})}<span class="grow"><strong>${esc(c.title || c.name)}</strong><p class="muted">${esc(c.lastMessage?.body || c.lastMessage || '')}</p></span>${Number(c.unread)>0?`<strong>${Number(c.unread)}</strong>`:''}</a>`).join('') || empty(t('هنوز گفت‌وگویی ندارید.','No conversations yet.'))}</aside><div class="conversation-pane">${id?`<header class="page-head"><a class="icon-button" data-route href="/community/direct" aria-label="${t('برگشت','Back')}">${icon('back')}</a>${avatar({avatar:c?.image})}<strong class="grow">${esc(c?.title || c?.name || t('گفت‌وگو','Conversation'))}</strong>${button('refresh',t('تازه‌سازی','Refresh'),'refresh')}</header><div class="message-list" id="messages">${messages.map(messageRow).join('')}</div><button class="text-button" data-action="more-messages" data-id="${id}">${t('دریافت پیام‌های بعدی','Check next messages')}</button><form class="composer" data-form="message" data-id="${id}"><textarea required name="body" maxlength="10000" aria-label="${t('پیام','Message')}" placeholder="${t('پیام بنویسید…','Message…')}"></textarea><button type="submit">${icon('send')}</button></form>`:`<div class="direct-intro">${icon('send')}<h2>${t('پیام‌های شما','Your messages')}</h2><p class="muted">${t('برای اعضای جامعه پیام بفرستید.','Send private messages to the community.')}</p><button class="primary" data-action="new-direct">${t('ارسال پیام','Send message')}</button></div>`}</div></section>`;
    const list=document.getElementById('messages');if(list)list.scrollTop=list.scrollHeight;
  }
  async function profile(id, version) {
    if(!id){main().innerHTML=join();return;}
    const [u,posts]=await Promise.all([api('/users/'+id),api('/posts?owner='+id)]);if(version!==epoch)return;remember(posts);
    main().innerHTML=`<section class="content-page">${header(u.username)}<div class="profile-cover">${avatar(u)}<div class="grow"><h2>${esc(u.name)}</h2><div class="profile-stats"><span>${Number(u.posts)} ${t('پست','posts')}</span><span>${Number(u.followers)} ${t('دنبال‌کننده','followers')}</span><span>${Number(u.following)} ${t('دنبال‌شونده','following')}</span></div><p class="caption">${esc(u.bio)}</p>${id!==Number(boot.userId)?`<div class="row"><button class="primary" data-action="follow" data-id="${id}" data-active="${u.isFollowing?0:1}">${u.isFollowing?t('دنبال می‌کنید','Following'):t('دنبال کردن','Follow')}</button><button class="text-button" data-action="direct-person" data-id="${id}">${t('پیام','Message')}</button></div>`:''}</div></div><div class="profile-posts">${posts.map(p=>`<a data-route href="/community/posts/${Number(p.id)}">${mediaURL(p.media)?String(p.mime).startsWith('video/')?icon('play'):`<img src="${esc(mediaURL(p.media))}" loading="lazy" alt="${esc(p.body)}">`:`<p>${esc(p.body)}</p>`}</a>`).join('')}</div></section>`;
  }
  function publishPage() {
    if(!boot.userId){main().innerHTML=join();return;}
    const story=new URLSearchParams(location.search).get('kind')==='story';
    main().innerHTML=`<section class="content-page">${header(t('اشتراک‌گذاری لحظه‌های موسیقی','Share your music'))}<form class="publish-form" data-form="publish"><label>${t('نوع محتوا','Content type')}<select class="input" name="kind"><option value="post">${t('پست','Post')}</option><option value="story" ${story?'selected':''}>${t('استوری','Story')}</option></select></label><label>${t('عکس یا ویدیو','Photo or video')}<input class="input" type="file" name="file" accept="image/jpeg,image/png,image/webp,video/mp4,video/webm"></label><div id="publish-preview"></div><label>${t('متن','Caption')}<textarea class="input" name="body" rows="5" maxlength="10000"></textarea></label><p class="muted">${t('تصویر تا ۱۰ مگابایت، ویدیو تا ۱۰۰ مگابایت. استوری پس از ۲۴ ساعت منقضی می‌شود.','Images up to 10 MB, videos up to 100 MB. Stories expire after 24 hours.')}</p><button class="primary" type="submit">${t('انتشار','Share')}</button></form></section>`;
    const input=main().querySelector('[type=file]');let preview;
    input.onchange=()=>{if(preview)URL.revokeObjectURL(preview);delete input.form.dataset.mediaId;const f=input.files[0];if(!f)return;preview=URL.createObjectURL(f);document.getElementById('publish-preview').innerHTML=f.type.startsWith('video/')?`<video controls class="publish-preview" src="${preview}"></video>`:`<img class="publish-preview" src="${preview}" alt="">`;};
  }
  async function render() {
    closeStory();document.querySelector('dialog')?.close();observer?.disconnect();currentRoute=location.pathname.replace(/\/$/,'');const version=++epoch;shell();main().innerHTML='<div class="skeleton" aria-busy="true"></div>';
    try {
      const parts=currentRoute.split('/').filter(Boolean), id=Number(parts[2]) || 0;
      switch(parts[1]) {
        case 'posts':await postDetail(id,version);break;
        case 'stories': {const p=await api('/posts/'+id);if(version!==epoch)return;if(p.kind!=='story')throw new Error(t('استوری پیدا نشد.','Story not found.'));remember([p]);main().innerHTML=empty(t('استوری','Story'));openStory([p]);break;}
        case 'direct':await direct(id,version);break;
        case 'notifications':await notifications(version);break;
        case 'users':await profile(id,version);break;
        case 'saved':await feed(version,true);break;
        case 'create':publishPage();break;
        default:await feed(version);
      }
    } catch(e) {if(version===epoch)main().innerHTML=`<div class="empty"><p>${esc(e.message)}</p>${boot.userId?'':`<a class="primary" href="/login">${t('ورود','Sign in')}</a>`}<button class="text-button" data-action="refresh">${t('تلاش دوباره','Retry')}</button></div>`;}
  }

  // Story timing belongs to the media timeline, including consecutive 60-second segments.
  let storyState=null, storyFrame=0;
  const emoji=['❤️','😂','😍','😮','😢','👏'];
  function closeStory() {
    const previous=storyState;cancelAnimationFrame(storyFrame);document.querySelector('.story-viewer video')?.pause();document.querySelector('.story-viewer')?.remove();storyState=null;document.body.style.overflow='';
    if(previous?.opener?.isConnected)previous.opener.focus({preventScroll:true});
    if(previous)videos();
  }
  function openStory(items) {
    closeStory();const ordered=[...items].sort((a,b)=>Number(a.id)-Number(b.id));if(!ordered.length)return;
    observer?.disconnect();document.querySelectorAll('.feed-video').forEach(v=>v.pause());
    storyState={items:ordered,index:0,part:0,durations:new Map(),elapsed:0,last:performance.now(),paused:false,reply:false,opener:document.activeElement};
    const el=document.createElement('div');el.className='story-viewer';el.setAttribute('role','dialog');el.setAttribute('aria-modal','true');el.setAttribute('aria-label',t('استوری','Story'));document.body.append(el);document.body.style.overflow='hidden';showStory();
    let touch;
    el.addEventListener('touchstart',e=>{touch={x:e.touches[0].clientX,y:e.touches[0].clientY};},{passive:true});
    el.addEventListener('touchend',e=>{if(!touch || !storyState)return;const dx=e.changedTouches[0].clientX-touch.x,dy=e.changedTouches[0].clientY-touch.y;if(dy < -55 && Math.abs(dy)>Math.abs(dx))replyPanel(true);else if(dy>70 && storyState.reply)replyPanel(false);touch=null;},{passive:true});
    el.addEventListener('keydown',e=>{
      if(e.key==='Tab'){
        const nodes=[...el.querySelectorAll('button:not(:disabled),input')].filter(n=>n.getClientRects().length);
        const index=nodes.indexOf(document.activeElement),next=e.shiftKey?index-1:index+1;
        if(next<0||next>=nodes.length){e.preventDefault();nodes[(next+nodes.length)%nodes.length]?.focus();}
      }
      if(e.key==='Escape'){if(storyState?.reply)replyPanel(false);else closeStory();}
      if(e.target.matches('input,textarea'))return;
      if(e.key==='ArrowRight')stepStory(1);if(e.key==='ArrowLeft')stepStory(-1);if(e.key==='ArrowUp')replyPanel(true);
    });
    el.querySelector('[data-action="close-story"]').focus();
  }
  function storyProgress() {
    const s=storyState;if(!s)return;
    const list=[];s.items.forEach((p,i)=>{const n=Math.max(1,Math.ceil((s.durations.get(Number(p.id)) || 5)/60));for(let part=0;part<n;part++)list.push(`<span><i data-progress="${i}:${part}" style="transform:scaleX(${i<s.index || i===s.index && part<s.part?1:0})"></i></span>`);});
    document.querySelector('.story-progress').innerHTML=list.join('');
  }
  function showStory() {
    const s=storyState;if(!s)return;cancelAnimationFrame(storyFrame);const view=s.view=(s.view||0)+1;const p=s.items[s.index],video=String(p.mime).startsWith('video/'),url=mediaURL(p.media);
    document.querySelector('.story-viewer video')?.pause();s.elapsed=0;s.last=performance.now();s.reply=false;
    document.querySelector('.story-viewer').innerHTML=`<section class="story-stage">${video?`<video class="story-media" src="${esc(url)}" autoplay muted playsinline preload="metadata"></video>`:`<img class="story-media" src="${esc(url)}" alt="${esc(p.body)}">`}<div class="story-overlay"></div><div class="story-top"><div class="story-progress"></div><div class="row">${avatar(p.author)}<strong class="grow">${esc(p.author?.name)}</strong><time>${age(p.created_at)}</time>${button('pause',t('مکث یا ادامه','Pause or resume'),'pause-story')}${video?button('mute',t('صدا','Sound'),'story-sound'):''}${button('close',t('بستن','Close'),'close-story')}</div></div><button class="story-hit story-prev" data-action="previous-story" aria-label="${t('قبلی','Previous')}"></button><button class="story-hit story-next" data-action="next-story" aria-label="${t('بعدی','Next')}"></button><div class="story-error"></div><p class="story-caption">${esc(p.body)}</p><div class="story-bottom"><button class="story-reply-trigger" data-action="open-reply">${t('پاسخ دهید… یا به بالا بکشید','Reply… or swipe up')}</button><div class="story-reply-panel">${button('down',t('بستن پاسخ','Close reply'),'close-reply')}<div class="emoji-row">${emoji.map(e=>`<button data-action="story-emoji" data-emoji="${e}" aria-label="${e}">${e}</button>`).join('')}</div><form class="story-reply-form" data-form="story-reply"><input name="body" maxlength="2000" required autocomplete="off" placeholder="${t('پاسخ خصوصی…','Send a private reply…')}" aria-label="${t('پاسخ به استوری','Reply to story')}"><button type="submit" class="icon-button" aria-label="${t('ارسال','Send')}">${icon('send')}</button></form></div></div></section>`;
    storyProgress();
    const v=document.querySelector('.story-media');
    if(video){
      v.addEventListener('loadedmetadata',()=>{if(storyState!==s || s.view!==view)return;if(!Number.isFinite(v.duration) || v.duration<=0)return;s.durations.set(Number(p.id),v.duration);v.currentTime=Math.min(s.part*60,v.duration);storyProgress();if(s.paused||s.reply)v.pause();else v.play().catch(()=>{s.paused=true;});});
      v.addEventListener('ended',()=>{if(storyState===s && s.view===view)stepStory(1);});
      v.play().catch(()=>{if(storyState===s)s.paused=true;});
    }else v.addEventListener('load',()=>{s.last=performance.now();});
    v.addEventListener('error',()=>{if(storyState!==s || s.view!==view)return;s.paused=true;document.querySelector('.story-error').innerHTML=`<p>${t('رسانه پخش نشد.','Could not load this media.')}</p><button class="primary" data-action="next-story">${t('استوری بعدی','Next story')}</button>`;});
    const tick=now=>{
      if(storyState!==s || s.view!==view)return;
      const dt=Math.min(100,now-s.last);s.last=now;
      const hold=s.paused||s.reply||document.hidden;
      let elapsed=s.elapsed, duration=5;
      if(video){duration=Math.min(60,(s.durations.get(Number(p.id))||60)-s.part*60);elapsed=Math.max(0,v.currentTime-s.part*60);if(hold&&!v.paused)v.pause();}
      else if(!hold&&v.complete&&v.naturalWidth){s.elapsed+=dt/1000;elapsed=s.elapsed;}
      const bar=document.querySelector(`[data-progress="${s.index}:${s.part}"]`);if(bar)bar.style.transform=`scaleX(${Math.min(1,elapsed/duration)})`;
      if(!hold && elapsed>=duration && duration>0){stepStory(1);return;}
      storyFrame=requestAnimationFrame(tick);
    };s.tick=tick;storyFrame=requestAnimationFrame(tick);
  }
  function stepStory(delta) {
    const s=storyState;if(!s)return;const parts=Math.max(1,Math.ceil((s.durations.get(Number(s.items[s.index].id))||5)/60));
    if(delta>0&&s.part+1<parts || delta<0&&s.part>0){
      s.part+=delta;s.paused=false;const v=document.querySelector('video.story-media');
      if(v){cancelAnimationFrame(storyFrame);v.currentTime=s.part*60;storyProgress();if(!s.reply)v.play().catch(()=>{});storyFrame=requestAnimationFrame(s.tick);return;}
    }else {s.index+=delta;s.part=0;if(s.index<0){s.index=0;return;}if(s.index>=s.items.length){closeStory();return;}if(delta<0)s.part=Math.max(0,Math.ceil((s.durations.get(Number(s.items[s.index].id))||5)/60)-1);}
    s.paused=false;showStory();
  }
  function replyPanel(open) {
    if(!storyState)return;storyState.reply=open;document.querySelector('.story-bottom').classList.toggle('open',open);
    const v=document.querySelector('.story-media');if(v.tagName==='VIDEO'){if(open)v.pause();else if(!storyState.paused)v.play().catch(()=>{});}
    if(open)document.querySelector('.story-reply-form input').focus({preventScroll:true});
  }
  async function storyReply(body='',reaction='') {
    if(!signedIn() || !storyState)return;
    const p=storyState.items[storyState.index];await api('/stories/'+Number(p.id)+'/reply',{body,emoji:reaction});
    toast(t('پاسخ خصوصی ارسال شد.','Private reply sent.'));if(storyState){const input=document.querySelector('.story-reply-form input');if(input)input.value='';replyPanel(false);}
  }

  document.addEventListener('click',e=>{
    const route=e.target.closest('a[data-route]');
    if(route && !e.ctrlKey && !e.metaKey && !e.shiftKey){e.preventDefault();document.querySelector('dialog')?.close();navigate(route.getAttribute('href'));return;}
    const el=e.target.closest('[data-action]');if(!el)return;e.preventDefault();const a=el.dataset.action,id=Number(el.dataset.id);
    if(a==='close-story'){closeStory();return;}if(a==='next-story'){stepStory(1);return;}if(a==='previous-story'){stepStory(-1);return;}
    if(a==='open-reply'){replyPanel(true);return;}if(a==='close-reply'){replyPanel(false);return;}
    if(a==='pause-story'&&storyState){storyState.paused=!storyState.paused;const v=document.querySelector('video.story-media');if(v)storyState.paused?v.pause():v.play().catch(()=>{});el.innerHTML=icon(storyState.paused?'play':'pause');return;}
    if(a==='story-sound'||a==='video-sound'){const v=a==='story-sound'?document.querySelector('video.story-media'):el.parentElement.querySelector('video');if(v){v.muted=!v.muted;el.innerHTML=icon(v.muted?'mute':'volume');}return;}
    if(a==='close-dialog'){el.closest('dialog').close();return;}
    if(a==='back'){navigate('/community');return;}
    if(a==='notifications'){navigate('/community/notifications');return;}
    once(a+':'+id,el,async()=>{
      if(a==='refresh'){cache.clear();await render();}
      if(a==='search')await peopleDialog('profile');
      if(a==='new-direct')await peopleDialog('direct');
      if(a==='share')await peopleDialog('share',id);
      if(a==='story'){const owner=Number(el.dataset.owner);openStory(stories.filter(p=>Number(p.owner_id)===owner));}
      if(a==='story-emoji')await storyReply('',el.dataset.emoji);
      if(a==='more-stories'){const rows=await api('/posts?kind=story&before='+Math.min(...stories.map(p=>Number(p.id))));stories.push(...remember(rows));document.querySelector('.story-strip').outerHTML=storyStrip();}
      if(a==='more-posts'){const rows=remember(await api('/posts?before='+el.dataset.before+(el.dataset.saved==='1'?'&saved=1':'')));document.getElementById('feed-posts').insertAdjacentHTML('beforeend',rows.map(postCard).join(''));if(rows.length<30)el.remove();else el.dataset.before=rows.at(-1).id;videos();}
      if(a==='like'||a==='save'){if(!signedIn())return;const p=postMap.get(id);const updated=await api('/posts/'+id+'/react',{kind:a==='like'?'like':'save',active:p[a==='like'?'liked':'saved']?'0':'1'});remember([updated]);el.closest('.post-card').outerHTML=postCard(updated);videos();}
      if(a==='delete-post'){if(!signedIn()||!confirm(t('این پست حذف شود؟','Delete this post?')))return;await api('/posts/'+id+'/delete',{});el.closest('.post-card').remove();}
      if(a==='more-comments'){const rows=await api('/posts/'+id+'/comments?before='+el.dataset.before);document.getElementById('comment-rows').insertAdjacentHTML('beforeend',rows.map(commentRow).join(''));if(rows.length<30)el.remove();else el.dataset.before=rows.at(-1).id;}
      if(a==='delete-comment'){if(!confirm(t('این نظر حذف شود؟','Delete this comment?')))return;const post=Number(el.closest('[data-post]').dataset.post);await api('/posts/'+post+'/comments/'+id+'/delete',{});el.closest('[data-comment]').remove();}
      if(a==='notification'){await api('/notifications/'+id+'/read',{});const path=el.dataset.kind==='message'?'/community/direct/':el.dataset.kind==='follow'?'/community/users/':'/community/posts/';navigate(path+(el.dataset.kind==='follow'?el.dataset.actor:el.dataset.target));}
      if(a==='direct-person'){if(!signedIn())return;const c=await api('/conversations',{user_id:id});navigate('/community/direct/'+Number(c.id));}
      if(a==='follow'){if(!signedIn())return;await api('/users/'+id+'/follow',{active:el.dataset.active});await render();}
      if(a==='more-messages'){const data=await api('/conversations/'+id+'/messages?after='+conversationCursor,undefined,true);const rows=data.messages||[];document.getElementById('messages').insertAdjacentHTML('beforeend',rows.filter(m=>!document.querySelector('[data-message="'+Number(m.id)+'"]')).map(messageRow).join(''));if(rows.length)conversationCursor=Number(rows.at(-1).id);else toast(t('پیام تازه‌ای نیست.','No new messages.'));}
    });
  });
  document.addEventListener('submit',e=>{
    const form=e.target.closest('[data-form]');if(!form)return;e.preventDefault();if(!signedIn())return;
    const type=form.dataset.form,id=Number(form.dataset.id),submit=form.querySelector('[type=submit]');
    once('submit:'+type,submit,async()=>{
      const body=form.elements.body?.value.trim() || '';
      if(type==='comment'){const c=await api('/posts/'+id+'/comments',{body});if(form.isConnected){document.getElementById('comment-rows').insertAdjacentHTML('afterbegin',commentRow(c));form.reset();}}
      if(type==='message'){const sent=await api('/conversations/'+id+'/messages',{body});if(!form.isConnected)return;form.reset();const data=await api('/conversations/'+id+'/messages?after='+(Number(sent.id)-1));if(!form.isConnected)return;const list=document.getElementById('messages');list.insertAdjacentHTML('beforeend',(data.messages||[]).filter(m=>!list.querySelector('[data-message="'+Number(m.id)+'"]')).map(messageRow).join(''));list.scrollTop=list.scrollHeight;}
      if(type==='story-reply')await storyReply(body);
      if(type==='publish'){
        const file=form.elements.file.files[0],kind=form.elements.kind.value;
        if(!body&&!file || kind==='story'&&!file)throw new Error(t('متن یا رسانه را اضافه کنید.','Add text or media.'));
        if(file && (!['image/jpeg','image/png','image/webp','video/mp4','video/webm'].includes(file.type)||file.size>(file.type.startsWith('image/')?10:100)*1024*1024))throw new Error(t('نوع یا حجم فایل مجاز نیست.','File type or size is not supported.'));
        let mediaId=Number(form.dataset.mediaId)||0;
        if(file&&!mediaId){const data=new FormData();data.append('file',file);const uploaded=await api('/media',data);mediaId=Number(uploaded.id);form.dataset.mediaId=String(mediaId);}
        await api('/posts',{kind,body,media_id:mediaId});navigate('/community');toast(t('منتشر شد.','Published.'));
      }
    });
  });
  window.addEventListener('popstate',render);
  document.addEventListener('visibilitychange',()=>{document.querySelectorAll('video').forEach(v=>{if(document.hidden)v.pause();});if(!document.hidden&&storyState&&!storyState.paused&&!storyState.reply)document.querySelector('video.story-media')?.play().catch(()=>{});});
  render();
})();
