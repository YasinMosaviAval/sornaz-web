<?php
// Reviewed CLI-only migration library, never a web-request dependency.
final class DatabaseHardeningMigration
{
    public function __construct(private PDO $pdo) {}
    private function value(string $sql,array $parameters=[]): mixed { $s=$this->pdo->prepare($sql);$s->execute($parameters);return $s->fetchColumn(); }
    private function column(string $table,string $column): bool {return (bool)$this->value('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=?',[$table,$column]);}
    private function table(string $table): bool {return (bool)$this->value('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=?',[$table]);}
    private function index(string $table,string $name): bool {return (bool)$this->value('SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND INDEX_NAME=?',[$table,$name]);}
    private function addIndex(string $table,string $name,string $columns,bool $unique=false): void {if(!$this->index($table,$name)){$this->pdo->exec("ALTER TABLE `$table` ADD ".($unique?'UNIQUE ':'')."INDEX `$name` ($columns)");}}
    private function relationships(): array
    {
        return [
            ['p_academy_branches','academy_id','p_academies','academy_id'],['p_academy_branches','user_id','f_users','user_id'],
            ['p_academy_branch_members','academy_id','p_academies','academy_id'],['p_academy_branch_members','branch_id','p_academy_branches','branch_id'],['p_academy_branch_members','user_id','f_users','user_id'],
            ['p_academy_branch_course_term_invoices','term_id','p_academy_branch_course_terms','term_id'],['p_academy_branch_course_term_invoices','member_id','p_academy_branch_members','member_id'],
            ['p_academy_branch_course_term_invoice_installments','invoice_id','p_academy_branch_course_term_invoices','term_invoice_id'],
            ['f_user_profiles','user_id','f_users','user_id'],['f_user_settings','user_id','f_users','user_id'],['f_world_iran_counties','province_id','f_world_iran_provinces','province_id'],
        ];
    }
    public function inspect(): array
    {
        $errors=[];$duplicates=[];
        if($this->value("SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND (TABLE_TYPE<>'BASE TABLE' OR ENGINE<>'InnoDB')")){$errors[]='non_transactional_tables_or_views';}
        foreach(['TRIGGERS'=>'TRIGGER_SCHEMA','ROUTINES'=>'ROUTINE_SCHEMA','EVENTS'=>'EVENT_SCHEMA'] as $object=>$schema){if($this->value("SELECT COUNT(*) FROM information_schema.$object WHERE $schema=DATABASE()")){$errors[]="review_objects:$object";}}
        if($this->value('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_NAME IS NOT NULL AND (TABLE_SCHEMA=DATABASE() OR REFERENCED_TABLE_SCHEMA=DATABASE()) AND (TABLE_SCHEMA<>DATABASE() OR REFERENCED_TABLE_SCHEMA<>DATABASE())')){$errors[]='cross_schema_fk';}
        if($this->table('p_academy_term_invoice_payments') && $this->value('SELECT COALESCE(MAX(payment_id),0) FROM f_financial_system_payments') > PHP_INT_MAX - $this->value('SELECT COALESCE(MAX(payment_id),0) FROM p_academy_term_invoice_payments')){$errors[]='payment_id_overflow';}
        foreach($this->relationships() as [$child,$column,$parent,$key]){
            if($this->value("SELECT COUNT(*) FROM `$child` c LEFT JOIN `$parent` p ON p.`$key`=c.`$column` WHERE c.`$column` IS NOT NULL AND p.`$key` IS NULL")){$errors[]="orphan:$child.$column";}
            $s=$this->pdo->prepare('SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND ((TABLE_NAME=? AND COLUMN_NAME=?) OR (TABLE_NAME=? AND COLUMN_NAME=?))');$s->execute([$child,$column,$parent,$key]);$types=$s->fetchAll(PDO::FETCH_COLUMN);
            $normalized=array_map(fn($type)=>preg_replace('/\(\d+\)/','',$type),$types);
            $countyConversion=$child==='f_world_iran_counties' && count($types)===2 && in_array('int',$normalized,true) && in_array('int unsigned',$normalized,true) && preg_replace('/\(\d+\)/','',(string)$this->value("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_world_iran_provinces' AND COLUMN_NAME='province_id'"))==='int';
            if($countyConversion && $this->value('SELECT COUNT(*) FROM f_world_iran_counties WHERE province_id>2147483647')){$errors[]='county_province_out_of_range';}
            if(count($types)!==2||($normalized[0]!==$normalized[1]&&!$countyConversion)){$errors[]="type:$child.$column";}
        }
        foreach(['f_user_profiles'=>'user_id','f_user_settings'=>'user_id,`key`'] as $table=>$key){$present=$table==='f_user_settings'?' AND `key` IS NOT NULL':'';if($this->value("SELECT COUNT(*) FROM (SELECT $key FROM `$table` WHERE deleted_at IS NULL AND user_id IS NOT NULL $present GROUP BY $key HAVING COUNT(*)>1) d")){$errors[]="active_duplicates:$table";}}
        if($this->value('SELECT COUNT(*) FROM (SELECT county_id FROM f_world_iran_counties GROUP BY county_id HAVING COUNT(*)>1) d')){$errors[]='duplicate_county_ids';}
        foreach(['p_translations','f_translations'] as $table){$duplicates[$table]=(int)$this->value("SELECT COUNT(*) FROM (SELECT table_name,table_id,field,locale,version FROM `$table` WHERE deleted_at IS NULL GROUP BY table_name,table_id,field,locale,version HAVING COUNT(*)>1) d");}
        if($this->table('p_academy_term_invoice_payments')&&$this->value('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE REFERENCED_TABLE_SCHEMA=DATABASE() AND REFERENCED_TABLE_NAME=?',['p_academy_term_invoice_payments'])){$errors[]='incoming_payment_fk';}
        return ['errors'=>$errors,'translation_duplicate_groups'=>$duplicates];
    }
    public function apply(string $directory): array
    {
        $state=$this->inspect();if($state['errors']){throw new LogicException('Preflight: '.implode(', ',$state['errors']));}
        $this->backup($directory);$this->reportTranslations($directory);
        if($this->column('f_user_settings',' user_setting_id')){$this->pdo->exec('ALTER TABLE f_user_settings CHANGE COLUMN ` user_setting_id` user_setting_id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT');}
        if(!$this->index('f_world_iran_counties','PRIMARY')){$this->pdo->exec('ALTER TABLE f_world_iran_counties ADD PRIMARY KEY(county_id)');}
        $countyType=$this->value("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='f_world_iran_counties' AND COLUMN_NAME='province_id'");
        if(str_contains($countyType,'unsigned')){$this->pdo->exec('ALTER TABLE f_world_iran_counties MODIFY province_id INT NULL');}
        $this->addIndex('f_world_iran_counties','idx_county_province','province_id');
        foreach(['f_user_profiles'=>'user_id','f_user_settings'=>'user_id,`key`'] as $table=>$key){
            if(!$this->column($table,'active_unique')){$this->pdo->exec("ALTER TABLE `$table` ADD active_unique TINYINT GENERATED ALWAYS AS (CASE WHEN deleted_at IS NULL THEN 1 ELSE NULL END) STORED");}
            $this->addIndex($table,'uq_active_owner',$key.',active_unique',true);
        }
        $this->addLookupIndexes();$this->addRelationships();$this->mergePayments();return $state;
    }
    private function addLookupIndexes(): void
    {
        $indexes=[['p_academy_branches','idx_branch_academy','academy_id,deleted_at'],['p_academy_branches','idx_branch_user','user_id,deleted_at'],['p_academy_branch_members','idx_member_branch','branch_id,deleted_at,status'],['p_academy_branch_members','idx_member_academy_user','academy_id,user_id,deleted_at'],['p_academy_branch_course_term_invoices','idx_invoice_term','term_id,deleted_at'],['p_academy_branch_course_term_invoices','idx_invoice_member','member_id,deleted_at'],['p_academy_branch_course_term_invoice_installments','idx_installment_invoice','invoice_id,deleted_at,installment_number'],['f_user_roles','idx_user_role_lookup','user_id,deleted_at,role_id'],['f_user_permissions','idx_user_permission_lookup','user_id,deleted_at,permission_id'],['f_access_system_role_permissions','idx_role_permission_lookup','role_id,deleted_at,permission_id']];
        foreach($indexes as [$table,$name,$columns]){$this->addIndex($table,$name,$columns);}
    }
    private function addRelationships(): void
    {
        foreach($this->relationships() as $position=>[$child,$column,$parent,$key]){
            if(!$this->value('SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=? AND COLUMN_NAME=? AND REFERENCED_TABLE_NAME=? AND REFERENCED_COLUMN_NAME=?',[$child,$column,$parent,$key])){$this->pdo->exec("ALTER TABLE `$child` ADD CONSTRAINT `fk_hardening_$position` FOREIGN KEY (`$column`) REFERENCES `$parent` (`$key`) ON DELETE RESTRICT ON UPDATE RESTRICT");}
        }
    }
    public function backup(string $directory): void
    {
        if(!is_dir($directory)&&!mkdir($directory,0700,true)){throw new RuntimeException('Backup directory unavailable');}
        file_put_contents($directory.'/.htaccess',"Require all denied\n");$stream=fopen($directory.'/before.sql','xb');if(!$stream){throw new RuntimeException('Backup unavailable');}
        fwrite($stream,"SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS=0;\n");
        $tables=$this->pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA=DATABASE() AND TABLE_TYPE='BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
        $this->pdo->beginTransaction();
        try{foreach($tables as $table){
            if(!preg_match('/^[a-z0-9_]+$/',$table)){throw new LogicException('Unsupported table name');}
            if(fwrite($stream,$this->pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM)[1].";\n")===false){throw new RuntimeException('Incomplete schema backup');}
            $generated=$this->pdo->query("SELECT COLUMN_NAME FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='$table' AND EXTRA LIKE '%GENERATED%'")->fetchAll(PDO::FETCH_COLUMN);
            foreach($this->pdo->query("SELECT * FROM `$table`") as $row){foreach($generated as $column){unset($row[$column]);}$columns='`'.implode('`,`',array_keys($row)).'`';$values=implode(',',array_map(fn($v)=>$v===null?'NULL':$this->pdo->quote((string)$v),array_values($row)));if(fwrite($stream,"INSERT INTO `$table` ($columns) VALUES ($values);\n")===false){throw new RuntimeException('Incomplete backup');}}
        }$this->pdo->commit();}catch(Throwable $error){$this->pdo->rollBack();fclose($stream);throw $error;}
        if(fwrite($stream,"SET FOREIGN_KEY_CHECKS=1;\n")===false||!fflush($stream)){fclose($stream);throw new RuntimeException('Backup flush failed');}fclose($stream);
    }
    private function reportTranslations(string $directory): void
    {
        $reports=[];foreach(['p_translations','f_translations'] as $table){$reports[$table]=$this->pdo->query("SELECT t.* FROM `$table` t JOIN (SELECT table_name,table_id,field,locale,version FROM `$table` WHERE deleted_at IS NULL GROUP BY table_name,table_id,field,locale,version HAVING COUNT(*)>1) d ON t.table_name<=>d.table_name AND t.table_id<=>d.table_id AND t.field<=>d.field AND t.locale<=>d.locale AND t.version<=>d.version WHERE t.deleted_at IS NULL ORDER BY t.translation_id")->fetchAll(PDO::FETCH_ASSOC);}
        if(file_put_contents($directory.'/translation-review.json',json_encode($reports,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR))===false){throw new RuntimeException('Review write failed');}
    }
    private function mergePayments(): void
    {
        if(!$this->column('f_financial_system_payments','record_type')){$this->pdo->exec(file_get_contents(__DIR__.'/../../docs/database/migrations/2026_10_04_unified_payments_columns.sql'));}
        if(!$this->table('p_academy_term_invoice_payments')){return;}
        $this->pdo->beginTransaction();
        try{
            $offset=(int)$this->value('SELECT COALESCE(MAX(payment_id),0) FROM f_financial_system_payments');$count=(int)$this->value('SELECT COUNT(*) FROM p_academy_term_invoice_payments');
            $transferred=(int)$this->value("SELECT COUNT(*) FROM f_financial_system_payments WHERE source_table='academy_term_invoice_payments'");
            if($transferred){
                if($transferred!==$count){throw new LogicException('Incomplete previous transfer; manual review required');}
                $offset=(int)$this->value("SELECT MIN(payment_id-source_id) FROM f_financial_system_payments WHERE source_table='academy_term_invoice_payments'");
            }else{$this->pdo->exec("INSERT INTO f_financial_system_payments (payment_id,record_type,source_table,source_id,invoice_id,installment_id,payer_id,gateway,method,payer_name,bank_card_type,amount,currency,currency_id,callback_token,authority,reference_id,reference_code,card_pan,card_hash,status,gateway_code,gateway_message,description,requested_at,verified_at,paid_at,created_at,created_by,updated_at,updated_by,deleted_at,deleted_by)
SELECT payment_id+$offset,'academy_term','academy_term_invoice_payments',payment_id,invoice_id,installment_id,user_id,gateway,payment_method,payer_name,bank_card_type,amount,currency,(SELECT MIN(currency_id) FROM f_financial_system_currency c WHERE BINARY c.code=BINARY p_academy_term_invoice_payments.currency),callback_token,authority,reference_id,reference_id,card_pan,card_hash,status,gateway_code,gateway_message,description,requested_at,verified_at,CASE WHEN status='paid' THEN verified_at ELSE NULL END,created_at,created_by,updated_at,updated_by,deleted_at,deleted_by FROM p_academy_term_invoice_payments");}
            if((int)$this->value("SELECT COUNT(*) FROM f_financial_system_payments WHERE source_table='academy_term_invoice_payments'")!==$count){throw new LogicException('Payment count mismatch');}
            $this->verifyPayments($offset);$this->remapReferences($offset);$this->pdo->commit();
        }catch(Throwable $error){$this->pdo->rollBack();throw $error;}
        $this->pdo->exec('DROP TABLE p_academy_term_invoice_payments');
    }
    private function verifyPayments(int $offset): void
    {
        $pairs=['user_id'=>'payer_id','payment_method'=>'method'];foreach(['invoice_id','installment_id','gateway','payer_name','bank_card_type','amount','currency','callback_token','authority','reference_id','card_pan','card_hash','status','gateway_code','gateway_message','description','requested_at','verified_at','created_at','created_by','updated_at','updated_by','deleted_at','deleted_by'] as $column){$pairs[$column]=$column;}
        $numeric=['user_id','invoice_id','installment_id','amount','gateway_code','created_by','updated_by','deleted_by'];
        $conditions=implode(' OR ',array_map(fn($a,$b)=>in_array($a,$numeric,true)?"NOT (s.`$a` <=> t.`$b`)":"NOT (BINARY s.`$a` <=> BINARY t.`$b`)",array_keys($pairs),array_values($pairs)));
        $conditions.=" OR t.record_type<>'academy_term' OR t.source_table<>'academy_term_invoice_payments' OR NOT(t.source_id<=>s.payment_id)";
        if($this->value("SELECT COUNT(*) FROM p_academy_term_invoice_payments s LEFT JOIN f_financial_system_payments t ON t.payment_id=s.payment_id+$offset WHERE t.payment_id IS NULL OR $conditions")){throw new LogicException('Payment values mismatch');}
    }
    private function remapReferences(int $offset): void
    {
        foreach(['p_translations','f_translations'] as $table){$this->pdo->exec("UPDATE `$table` t JOIN p_academy_term_invoice_payments s ON s.payment_id=t.table_id SET t.table_name='financial_system_payments',t.table_id=t.table_id+$offset WHERE t.table_name='academy_term_invoice_payments'");}
        $this->pdo->exec("UPDATE f_user_messages t JOIN p_academy_term_invoice_payments s ON s.payment_id=t.related_entity_id SET t.related_entity_type='financial_system_payments',t.related_entity_id=t.related_entity_id+$offset WHERE t.related_entity_type='academy_term_invoice_payments'");
        $this->pdo->exec("UPDATE f_financial_system_ledger_entries t JOIN p_academy_term_invoice_payments s ON s.payment_id=t.reference_id SET t.reference_type='financial_system_payments',t.reference_id=t.reference_id+$offset WHERE t.reference_type='academy_term_invoice_payments'");
        $this->pdo->exec("UPDATE f_user_points t JOIN p_academy_term_invoice_payments s ON s.payment_id=t.reference_id SET t.reference_type='financial_system_payments',t.reference_id=t.reference_id+$offset WHERE t.reference_type='academy_term_invoice_payments'");
    }
}
