<?php
$config=json_encode($boot,JSON_UNESCAPED_UNICODE|JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
$html=file_get_contents(base_path('assets/notation/index.html'));
$html=str_replace('<head>','<head><base href="/assets/notation/">',$html);
$html=str_replace('<script src="notation.js"></script>','<script>window.NOTATION_BOOT='.$config.';</script><script src="notation.js"></script>',$html);
echo $html;