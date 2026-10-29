<?php
// Explicit CLI-only seed. Never invoked by normal web/API requests.
if(PHP_SAPI!=='cli'){http_response_code(404);exit;}
if(!in_array('--apply',$argv,true)){fwrite(STDERR,"Review the migration and back up the database first. Run with --apply to seed the existing curriculum.\n");exit(1);}
require_once dirname(__DIR__).'/vendor/autoload.php';
require base_path('bootstrap/app.php');
$store=new \Modules\Education\Services\MusicPracticeStore();$store->ready();
(new \Modules\Education\Services\MusicPracticeSeeder())->seed($store);
echo "Music practice catalog seeded without modifying existing definitions or results (Iranian: 180, Western: 44).\n";
