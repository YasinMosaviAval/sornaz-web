<?php
$config=json_encode($boot,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
$html=file_get_contents(base_path('assets/notation/index.html'));
$html=str_replace('<head>','<head><base href="/assets/notation/">',$html);
if (!empty($boot['websitePanel'])) {
    $html=str_replace('</head>', '<link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css"><link rel="stylesheet" href="/assets/theme/theme.css"><link rel="stylesheet" href="/assets/notation/panel.css"></head>', $html);
    $html=str_replace('<body>', '<body class="website-panel">', $html);
    $html=str_replace('</body>', '<script>try{const p=parent.document.documentElement;document.documentElement.dataset.mode=p.dataset.mode;document.documentElement.dataset.theme=p.dataset.theme;new MutationObserver(()=>{document.documentElement.dataset.mode=p.dataset.mode;document.documentElement.dataset.theme=p.dataset.theme;}).observe(p,{attributes:true,attributeFilter:["data-mode","data-theme"]});if(parent!==window){const app=document.getElementById("app");const report=()=>parent.postMessage({type:"sornaz-notation-height",height:Math.max(600,Math.ceil(app.getBoundingClientRect().height)+16)},location.origin);new ResizeObserver(report).observe(app);report();}}catch(e){}</script></body>', $html);
}
$html=str_replace('<script src="notation.js"></script>','<script>window.NOTATION_BOOT='.$config.';</script><script src="notation.js"></script>',$html);
echo $html;
