(() => {
    'use strict';
    const root = document.documentElement;
    const embedded = parent !== window && parent.location.origin === location.origin;
    const source = embedded ? parent.document.documentElement : root;
    function appearance() {
        root.dataset.theme = source.dataset.theme || localStorage.getItem('sornaz.theme') || 'indigo';
        root.dataset.mode = source.dataset.mode || localStorage.getItem('sornaz.mode') || 'light';
        if (embedded) for (const key of ['--site-font-family','--site-root-font-size']) {
            root.style.setProperty(key, getComputedStyle(source).getPropertyValue(key));
        }
    }
    appearance();
    const observer = new MutationObserver(appearance);
    if (embedded) observer.observe(source, {attributes:true, attributeFilter:['data-theme','data-mode','style']});
    addEventListener('pagehide', () => { observer.disconnect(); window.discardChatVoice?.(); }, {once:true});
    document.getElementById('chat').classList.remove('hidden');
    const notify = id => {
        if (embedded) parent.postMessage({type:'community-chat-selected', id:Number(id) || 0}, location.origin);
    };
    const open = window.openChat;
    window.openChat = async id => { await open(id); notify(id); };
    const close = window.closeMobileChat;
    window.closeMobileChat = () => { close(); notify(0); };
    window.chatReady.then(async () => {
        const id = Number(new URLSearchParams(location.search).get('id'));
        if (Number.isSafeInteger(id) && id > 0) await window.openChat(id);
    }).catch(error => AppDialog.alert(error.message));
})();
