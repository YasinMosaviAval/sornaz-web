(function(){
    'use strict';
    const locale=window.adminLocale||document.documentElement.lang||'fa';
    if(locale==='fa')return;
    const dictionary=window.adminUiMap||{};
    const translateText=text=>{let result=text;Object.keys(dictionary).sort((a,b)=>b.length-a.length).forEach(key=>{result=result.split(key).join(dictionary[key]);});return result;};
    function translate(root){
        if(!root||root.nodeType!==1||root.closest('tbody,[data-no-admin-translate]'))return;
        const walker=document.createTreeWalker(root,NodeFilter.SHOW_TEXT);const nodes=[];while(walker.nextNode())nodes.push(walker.currentNode);
        nodes.forEach(node=>{if(!node.parentElement?.closest('tbody,[data-no-admin-translate],script,style'))node.nodeValue=translateText(node.nodeValue);});
        root.querySelectorAll('input[placeholder],textarea[placeholder],input[value]').forEach(el=>{if(el.closest('tbody,[data-no-admin-translate]'))return;if(el.placeholder)el.placeholder=translateText(el.placeholder);if(el.tagName==='INPUT'&&['button','submit'].includes(el.type))el.value=translateText(el.value);});
    }
    const run=()=>translate(document.body); if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',run);else run();
    new MutationObserver(records=>records.forEach(record=>record.addedNodes.forEach(node=>{if(node.nodeType===1)translate(node);}))).observe(document.documentElement,{childList:true,subtree:true});
})();
