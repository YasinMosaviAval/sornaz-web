const fs=require('node:fs'),path=require('node:path'),{spawnSync}=require('node:child_process');
if(process.env.SORNAZ_NOTATION_TEST_MYSQL!=='1'){console.error('Set SORNAZ_NOTATION_TEST_MYSQL=1 for the disposable local fixture on port 3319.');process.exit(2);}
process.chdir(path.resolve(__dirname,'..'));
const php=String.raw`<?php
require 'core/database/TableNames.php';require 'core/database/PrefixedPDO.php';require 'core/translation/EntityText.php';require 'Modules/CourseMarket/Repositories/CourseRepository.php';require 'Modules/Notation/Services/NotationService.php';
$p=new Core\database\PrefixedPDO('mysql:host=127.0.0.1;port=3319;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
$name='sornaz_notation_catalog_test_'.bin2hex(random_bytes(6));$created=false;
function checkCatalog($value,$message){if(!$value)throw new LogicException($message);}
try{
$p->exec("CREATE DATABASE $name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");$created=true;$p->exec("USE $name");
$p->exec('CREATE TABLE f_translations (translation_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,table_name VARCHAR(100),table_id BIGINT UNSIGNED,field VARCHAR(100),locale VARCHAR(10),value TEXT,version INT,created_at DATETIME,created_by BIGINT,updated_at DATETIME,updated_by BIGINT,deleted_at DATETIME NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
$sql=file_get_contents('docs/database/migrations/2026_11_09_notation_scales.sql');$p->exec($sql);
checkCatalog((int)$p->query('SELECT COUNT(*) FROM notation_scales')->fetchColumn()===16,'Missing seeded scales');checkCatalog((int)$p->query("SELECT COUNT(*) FROM f_translations WHERE table_name='notation_scales'")->fetchColumn()===32,'Missing translations');
$id=(int)$p->query("SELECT id FROM notation_scales WHERE code='major'")->fetchColumn();Core\translation\EntityText::save('notation_scales',$id,'title','Custom major',1,$p,'en');$p->exec("UPDATE notation_scales SET position=-1 WHERE code='iran_shur'");$p->exec("UPDATE notation_scales SET active=0 WHERE code='iran_chahargah'");$p->exec($sql);
checkCatalog((int)$p->query("SELECT COUNT(*) FROM f_translations WHERE table_name='notation_scales'")->fetchColumn()===32,'Rerun duplicated translations');checkCatalog(Core\translation\EntityText::get('notation_scales',$id,'title',$p,'en')==='Custom major','Rerun overwrote translation');checkCatalog((int)$p->query("SELECT position FROM notation_scales WHERE code='iran_shur'")->fetchColumn()===-1,'Rerun overwrote order');
$s=new Modules\Notation\Services\NotationService(new Modules\CourseMarket\Repositories\CourseRepository($p));$items=$s->scales();checkCatalog(count($items)===15&&$items[0]['id']==='iran_shur','Active catalog order changed');checkCatalog(array_column($items,null,'id')['major']['en']==='Custom major','API translation lost');checkCatalog($p->query('SHOW COLUMNS FROM notation_scales')->fetchAll(PDO::FETCH_COLUMN)===['id','code','position','active','updated_at'],'Language columns in primary table');checkCatalog($p->query("SELECT ENGINE FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_notation_scales'")->fetchColumn()==='InnoDB','Nontransactional table');
echo "Real MySQL scales: migration/rerun, EntityText, ordering, visibility, translated API payload and structural-only columns passed.\n";
}finally{if($p->inTransaction())$p->rollBack();if($created)$p->exec("DROP DATABASE $name");}
`;
const result=spawnSync('php',[],{input:php,encoding:'utf8'});process.stdout.write(result.stdout);process.stderr.write(result.stderr);process.exit(result.status??1);
