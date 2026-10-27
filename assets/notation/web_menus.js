/* Portal notation action menus out of card/table overflow without resizing the page. */
(function(root){
'use strict';
if(root.NotationMenus)return;
let active=null,sequence=0,frame;
const enabled=()=>document.body?.classList.contains('browser-notation');
let parentDocument;
try{if(parent!==root&&parent.location.origin===location.origin)parentDocument=parent.document;}catch(_){}
function portalDocument(){return parentDocument||document;}
function installPortalStyle(){
 if(!parentDocument)return;
 const original=document.querySelector('link[href*="web_menus.css"]'),source=original?.href;
 if(!source)return;
 try{
  const css=[...original.sheet.cssRules].map(rule=>rule.cssText).join('\n');let style=parentDocument.querySelector('style[data-notation-menu-style]');
  if(!style){style=parentDocument.createElement('style');style.dataset.notationMenuStyle='';parentDocument.head.append(style);}
  if(style.dataset.source!==source){style.textContent=css;style.dataset.source=source;}
  return;
 }catch(_){}
 let link=parentDocument.querySelector('link[data-notation-menu-style]');
 if(!link){link=parentDocument.createElement('link');link.rel='stylesheet';link.dataset.notationMenuStyle='';parentDocument.head.append(link);}
 if(link.href!==source)link.href=source;
}
function restoreAttribute(node,name,value){if(value===null)node.removeAttribute(name);else node.setAttribute(name,value);}
function close(restoreFocus=false){
 const current=active;if(!current)return;active=null;
 current.resize?.disconnect();current.panel.removeEventListener('click',current.activate,true);
 try{if(current.panel.matches(':popover-open'))current.panel.hidePopover();}catch(_){}
 restoreAttribute(current.panel,'popover',current.popover);
 restoreAttribute(current.panel,'style',current.style);
 restoreAttribute(current.panel,'role',current.role);
 current.panel.classList.remove('notation-floating-menu');
 current.items.forEach(item=>{restoreAttribute(item.node,'role',item.role);restoreAttribute(item.node,'tabindex',item.tabindex);});
 if(current.placeholder.parentNode)current.placeholder.replaceWith(current.panel);else current.panel.remove();
 current.details.open=false;current.summary.setAttribute('aria-expanded','false');
 if(restoreFocus&&current.summary.isConnected)current.summary.focus({preventScroll:true});
}
function viewport(){
 const style=getComputedStyle(document.documentElement),center=parseFloat(style.getPropertyValue('--notation-dialog-center')),height=parseFloat(style.getPropertyValue('--notation-visible-height'));
 return {left:8,right:Math.max(8,innerWidth-8),top:Number.isFinite(center)&&Number.isFinite(height)?Math.max(8,center-height/2+8):8,bottom:Number.isFinite(center)&&Number.isFinite(height)?Math.min(innerHeight-8,center+height/2-8):innerHeight-8};
}
function anchorVisible(summary,bounds){
 const rect=summary.getBoundingClientRect();let left=Math.max(rect.left,bounds.left),right=Math.min(rect.right,bounds.right),top=Math.max(rect.top,bounds.top),bottom=Math.min(rect.bottom,bounds.bottom);
 for(let node=summary.parentElement;node&&node!==document.body;node=node.parentElement){const style=getComputedStyle(node),box=node.getBoundingClientRect();if(/auto|scroll|hidden|clip/.test(style.overflowX)){left=Math.max(left,box.left);right=Math.min(right,box.right);}if(/auto|scroll|hidden|clip/.test(style.overflowY)){top=Math.max(top,box.top);bottom=Math.min(bottom,box.bottom);}}
 return summary.getClientRects().length&&right>left&&bottom>top;
}
function position(){
 const current=active;if(!current)return;
 if(!enabled()||!current.details.isConnected){close(false);return;}
 const local=viewport();if(!anchorVisible(current.summary,local)){close(false);return;}
 let anchor=current.summary.getBoundingClientRect(),bounds=local;
 if(current.portalDocument!==document){
  const owner=root.frameElement,box=owner?.getBoundingClientRect();if(!box?.width||!box.height){close(false);return;}
  const x=box.width/Math.max(1,owner.offsetWidth),y=box.height/Math.max(1,owner.offsetHeight);
  anchor={left:box.left+(anchor.left+owner.clientLeft)*x,right:box.left+(anchor.right+owner.clientLeft)*x,top:box.top+(anchor.top+owner.clientTop)*y,bottom:box.top+(anchor.bottom+owner.clientTop)*y};
  const view=current.portalDocument.defaultView;bounds={left:8,right:view.innerWidth-8,top:8,bottom:view.innerHeight-8};
 }
 const panel=current.panel,height=Math.max(1,bounds.bottom-bounds.top),width=Math.max(1,bounds.right-bounds.left);
 panel.style.maxHeight=height+'px';panel.style.maxWidth=width+'px';
 const box=panel.getBoundingClientRect(),rtl=getComputedStyle(current.summary).direction==='rtl';
 const left=Math.max(bounds.left,Math.min(rtl?anchor.left:anchor.right-box.width,bounds.right-box.width));
 const below=bounds.bottom-anchor.bottom-6,above=anchor.top-bounds.top-6;
 let top=below>=box.height||below>=above?anchor.bottom+6:anchor.top-box.height-6;
 top=Math.max(bounds.top,Math.min(top,bounds.bottom-box.height));
 panel.style.left=Math.round(left)+'px';panel.style.top=Math.round(top)+'px';panel.style.visibility='visible';
}
function schedule(){cancelAnimationFrame(frame);frame=requestAnimationFrame(position);}
function open(summary,last=false){
 const details=summary.parentElement;if(active?.details===details){close(true);return;}
 close(false);const panel=details.querySelector(':scope > div');if(!panel)return;
 const placeholder=document.createComment('notation action menu');panel.before(placeholder);
 const items=[...panel.querySelectorAll('button')].map(node=>({node,role:node.getAttribute('role'),tabindex:node.getAttribute('tabindex')}));
 const current={details,summary,panel,placeholder,items,style:panel.getAttribute('style'),popover:panel.getAttribute('popover'),role:panel.getAttribute('role'),portalDocument:portalDocument()};
 current.activate=event=>{const button=event.target.closest('button');if(active!==current||!button||!panel.contains(button)||button.disabled)return;event.preventDefault();event.stopPropagation();close(true);button.click();};
 active=current;details.open=false;panel.id||='notation-menu-'+(++sequence);summary.setAttribute('aria-controls',panel.id);summary.setAttribute('aria-haspopup','menu');summary.setAttribute('aria-expanded','true');
 panel.setAttribute('role','menu');panel.classList.add('notation-floating-menu');panel.style.cssText+=';position:fixed;inset:auto;margin:0;visibility:hidden';items.forEach(({node})=>{node.setAttribute('role','menuitem');node.tabIndex=-1;});
 installPortalStyle();current.portalDocument.body.append(panel);panel.addEventListener('click',current.activate,true);
 if(typeof panel.showPopover==='function'){panel.setAttribute('popover','manual');try{panel.showPopover();}catch(_){panel.removeAttribute('popover');}}
 position();if(active!==current)return;
 const choices=items.map(item=>item.node).filter(node=>!node.disabled);(last?choices.at(-1):choices[0])?.focus({preventScroll:true});
 if(typeof ResizeObserver==='function'){current.resize=new ResizeObserver(schedule);current.resize.observe(panel);}
}
document.addEventListener('click',event=>{const summary=event.target.closest?.('.sheet-menu > summary');if(!enabled()||!summary)return;event.preventDefault();event.stopPropagation();open(summary);},true);
document.addEventListener('pointerdown',event=>{if(active&&!active.panel.contains(event.target)&&!active.summary.contains(event.target))close(false);},true);
function keys(event){
 const summary=event.target.closest?.('.sheet-menu > summary');
 if(!active){if(enabled()&&summary&&['ArrowDown','ArrowUp'].includes(event.key)){event.preventDefault();event.stopPropagation();open(summary,event.key==='ArrowUp');}return;}
 if(event.key==='Escape'){event.preventDefault();event.stopPropagation();close(true);return;}
 if(event.key==='Tab'){close(true);return;}
 if(!active.panel.contains(event.target))return;
 const choices=active.items.map(item=>item.node).filter(node=>!node.disabled),at=choices.indexOf(active.portalDocument.activeElement);let next;
 if(event.key==='ArrowDown')next=(at+1)%choices.length;
 else if(event.key==='ArrowUp')next=(at+choices.length-1)%choices.length;
 else if(event.key==='Home')next=0;
 else if(event.key==='End')next=choices.length-1;
 if(next!==undefined){event.preventDefault();event.stopPropagation();choices[next]?.focus({preventScroll:true});}
}
document.addEventListener('keydown',keys,true);
root.addEventListener('resize',schedule);root.addEventListener('scroll',schedule,{capture:true,passive:true});
root.addEventListener('message',event=>{if(event.origin===location.origin&&event.source===parent&&event.data?.type==='sornaz-notation-viewport')schedule();});
const parentOutside=event=>{if(active?.panel.contains(event.target))return;close(false);};
if(parentDocument){installPortalStyle();parentDocument.addEventListener('pointerdown',parentOutside,true);parentDocument.addEventListener('keydown',keys,true);parentDocument.defaultView.addEventListener('resize',schedule);parentDocument.addEventListener('scroll',schedule,true);}
root.addEventListener('pagehide',()=>{close(false);parentDocument?.removeEventListener('pointerdown',parentOutside,true);parentDocument?.removeEventListener('keydown',keys,true);parentDocument?.defaultView.removeEventListener('resize',schedule);parentDocument?.removeEventListener('scroll',schedule,true);});
function observe(){const observer=new MutationObserver(()=>{if(active&&(!active.details.isConnected||!enabled()))close(false);});const app=document.getElementById('app');if(app)observer.observe(app,{childList:true,subtree:true});if(document.body)observer.observe(document.body,{attributes:true,attributeFilter:['class']});}
if(document.readyState==='loading')document.addEventListener('DOMContentLoaded',observe,{once:true});else observe();
root.NotationMenus={close,reposition:schedule};
})(globalThis);
