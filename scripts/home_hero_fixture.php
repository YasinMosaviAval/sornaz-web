<?php
$language='fa';
function locale(){return $GLOBALS['language'];}
function direction(){return locale()==='en'?'ltr':'rtl';}
function e($v){return htmlspecialchars($v,ENT_QUOTES,'UTF-8');}
$source=file_get_contents(__DIR__.'/../Modules/Page/Resources/Views/home.php');
preg_match('~<section class="home-hero\b.*?</section>~s',$source,$match);
if(empty($match[0]))throw new RuntimeException('Hero missing');
$result=[];
foreach(['fa','en'] as $language){
 $isEnglish=$language==='en';$academySearchOptions=[];$homeSearchSelectLabels=['instrument'=>'Instrument','city'=>'City'];
 ob_start();eval('?>'.$match[0]);$result[$language]=ob_get_clean();
}
echo json_encode($result,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
