<?php
$config=json_encode($boot,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
$html=file_get_contents(base_path('assets/notation/index.html'));
$html=str_replace('<head>','<head><base href="/assets/notation/">',$html);
if (!empty($boot['websitePanel'])) {
    $html=str_replace('</head>', '<link rel="stylesheet" href="/assets/vendor/vazirmatn/vazirmatn.css"><link rel="stylesheet" href="/assets/theme/theme.css"><link rel="stylesheet" href="/assets/notation/panel.css"></head>', $html);
    $html=str_replace('<body>', '<body class="website-panel">', $html);
    $html=str_replace('</body>', '<script>try{const p=parent.document.documentElement;document.documentElement.dataset.mode=p.dataset.mode;document.documentElement.dataset.theme=p.dataset.theme;new MutationObserver(()=>{document.documentElement.dataset.mode=p.dataset.mode;document.documentElement.dataset.theme=p.dataset.theme;}).observe(p,{attributes:true,attributeFilter:["data-mode","data-theme"]});if(parent!==window){const app=document.getElementById("app");const report=()=>parent.postMessage({type:"sornaz-notation-height",height:Math.max(1,Math.ceil(app.getBoundingClientRect().height))},location.origin);new ResizeObserver(report).observe(app);report();}}catch(e){}</script></body>', $html);
}
$html=str_replace('<script src="notation.js"></script>','<script>window.NOTATION_BOOT='.$config.';</script><script src="notation.js"></script>',$html);
if (!empty($boot['webStorage'])) {
    ob_start();
    (static function () { require base_path('Modules/Analytics/Resources/Views/sections/panel-table-template.php'); })();
    $tableMarkup=ob_get_clean();
    $html=str_replace('<script src="notation.js"></script>', $tableMarkup.'<script src="/assets/Analytics/js/admin-ui-templates.js?v='.filemtime(base_path('assets/Analytics/js/admin-ui-templates.js')).'"></script><script src="web_lists.js"></script><script src="notation.js"></script>', $html);
}
$html=str_replace('/assets/notation/panel.css"', '/assets/notation/panel.css?v='.filemtime(base_path('assets/notation/panel.css')).'"', $html);
// Each entry point pins all local dependencies to the same deployed asset revision.
$html=preg_replace_callback('/\b(src|href)="((?:vendor\/)?[a-z_]+\.(?:js|css))"/', static function ($match) {
    $path=base_path('assets/notation/'.$match[2]);
    return $match[1].'="'.$match[2].'?v='.(is_file($path) ? filemtime($path) : 1).'"';
}, $html);
echo $html;
