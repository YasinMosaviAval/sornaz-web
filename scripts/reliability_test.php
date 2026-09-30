<?php
// Isolated SQLite records and simulated gateway responses; no financial requests.
$map=require __DIR__.'/../vendor/composer/autoload_classmap.php';
spl_autoload_register(static function($class)use($map){if(isset($map[$class]))require $map[$class];});
$pdo=new PDO('sqlite::memory:');$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE,PDO::FETCH_ASSOC);$pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$root=__DIR__.'/../storage/reliability-test-'.bin2hex(random_bytes(6));mkdir($root,0700);
function storage_path($path){return $GLOBALS['root'].'/'.$path;}
register_shutdown_function(static function()use($root){foreach(glob($root.'/payment-locks/*.lock')as$file)unlink($file);if(is_dir($root.'/payment-locks'))rmdir($root.'/payment-locks');rmdir($root);});
function db(){return $GLOBALS['pdo'];}
function env($key,$default=null){return ['ZARINPAL_MERCHANT_ID'=>'fixture','APP_URL'=>'https://fixture.test','ZARINPAL_SANDBOX'=>true][$key]??$default;}
function session(){return new class {public function get($key,$default=null){return $key==='suppress_database_notifications'?true:$default;}};}
function transaction($work){db()->beginTransaction();try{$result=$work();db()->commit();return $result;}catch(Throwable$e){db()->rollBack();throw $e;}}
function check($ok,$label){if(!$ok)throw new RuntimeException($label);$GLOBALS['checks']++;}
function denied($work){try{$work();}catch(RuntimeException$e){$GLOBALS['checks']++;return;}throw new LogicException('Expected rejection');}
$checks=0;
$pdo->exec("CREATE TABLE fixture(id INTEGER PRIMARY KEY,tenant INTEGER,flag INTEGER,value TEXT);
INSERT INTO fixture VALUES(1,1,1,'a'),(2,1,0,'b'),(3,2,1,'c');
CREATE TABLE related(owner INTEGER); INSERT INTO related VALUES(2);");
use Core\database\DB;
check(count(DB::table('fixture')->whereRaw('flag=?',[1])->where('tenant',1)->get())===1,'Raw/normal binding order');
DB::table('fixture')->whereRaw('flag=?',[1])->where('tenant',1)->update(['value'=>'updated']);
check($pdo->query("SELECT value FROM fixture WHERE id=1")->fetchColumn()==='updated','Scoped update failed');
check($pdo->query("SELECT value FROM fixture WHERE id=3")->fetchColumn()==='c','Update escaped tenant scope');
check(count(DB::table('fixture')->where('id',1)->orWhere('id',3)->where('tenant',1)->get())===1,'OR grouping escaped tenant');
check(count(DB::table('fixture')->whereExists(DB::table('related')->whereColumn('owner','fixture.id'))->where('tenant',1)->get())===1,'EXISTS ordering');
DB::table('fixture')->whereRaw('flag=?',[1])->where('tenant',2)->delete();
check((int)$pdo->query('SELECT COUNT(*) FROM fixture')->fetchColumn()===2,'Scoped delete failed');
DB::table('fixture')->whereRaw('id=?',[2])->update(['flag'=>2]);
check((int)$pdo->query('SELECT flag FROM fixture WHERE id=1')->fetchColumn()===1,'Raw-only mutation updated every row');

$pdo->exec("CREATE TABLE creator_course_orders(id INTEGER PRIMARY KEY,course_id INTEGER,buyer_id INTEGER,seller_id INTEGER,amount INTEGER,currency TEXT,status TEXT,token TEXT,authority TEXT,reference_id TEXT,paid_at TEXT);");
$repo=new Modules\CourseMarket\Repositories\CourseRepository($pdo);
$courses=new class($repo) extends Modules\CourseMarket\Services\CourseService {public int $price=100;public function course(int$id):array{return ['id'=>$id,'owner_id'=>9,'title'=>'Fixture','price'=>$this->price,'status'=>'published'];}};
$gateway=new class($repo,$courses) extends Modules\CourseMarket\Services\PaymentService {
 public int $requests=0;public int $verifications=0;public bool $timeout=false;public int $code=100;public string $inquiry='IN_BANK';
 protected function request(string$action,array$payload):array{if($action==='inquiry')return ['data'=>['code'=>100,'status'=>$this->inquiry]];if($action==='request'){++$this->requests;return ['data'=>['code'=>100,'authority'=>'AUTHORITY_FIXTURE_'.$this->requests]];}++$this->verifications;if($this->timeout)throw new RuntimeException('Simulated timeout');return ['data'=>['code'=>$this->code,'ref_id'=>12345]];}
};
$first=$gateway->start(7,1);check($first===$gateway->start(7,1)&&$gateway->requests===1,'Duplicate checkout request');
$order=$repo->one('SELECT * FROM creator_course_orders WHERE id=1');
$gateway->timeout=true;denied(fn()=>$gateway->callback($order['token'],$order['authority'],'OK'));
check($repo->one('SELECT status FROM creator_course_orders WHERE id=1')['status']==='pending','Timeout incorrectly finalized order');
$gateway->timeout=false;$gateway->code=101;$paid=$gateway->callback($order['token'],$order['authority'],'OK');
check($paid['status']==='paid','Already-verified gateway recovery failed');
$calls=$gateway->verifications;check($gateway->callback($order['token'],$order['authority'],'NOK')['status']==='paid'&&$gateway->verifications===$calls,'Late cancellation downgraded paid order');
$gateway->start(7,1);check($gateway->requests===1,'Paid course charged again');
$gateway->start(8,1);$other=$repo->one('SELECT * FROM creator_course_orders WHERE buyer_id=8');denied(fn()=>$gateway->callback($other['token'],$other['authority'],'NOK'));
check($repo->one('SELECT status FROM creator_course_orders WHERE buyer_id=8')['status']==='canceled','Canceled order remained pending');
$gateway->inquiry='PAID';$beforeRequests=$gateway->requests;$gateway->start(8,1);
check($gateway->requests===$beforeRequests&&$repo->one('SELECT status FROM creator_course_orders WHERE buyer_id=8')['status']==='paid','Cancellation hint allowed a second charge before checking gateway state');
$gateway->inquiry='IN_BANK';$gateway->start(10,1);$failed=$repo->one('SELECT * FROM creator_course_orders WHERE buyer_id=10');
denied(fn()=>$gateway->callback($failed['token'],$failed['authority'],'NOK'));
$gateway->inquiry='FAILED';$gateway->start(10,1);
check((int)$pdo->query('SELECT COUNT(*) FROM creator_course_orders WHERE buyer_id=10')->fetchColumn()===2,'Gateway-confirmed failure did not permit retry');
$gateway->inquiry='UNKNOWN';denied(fn()=>$gateway->start(10,1));$gateway->inquiry='IN_BANK';
$legacy=$repo->insert('creator_course_orders',['buyer_id'=>7,'course_id'=>1,'seller_id'=>9,'amount'=>100,'status'=>'pending','authority'=>'LEGACY_AUTHORITY','token'=>'legacy-token']);
check($gateway->callback('legacy-token','LEGACY_AUTHORITY','OK')['status']==='paid_review','Legacy duplicate settlement was not held for review');
check($gateway->callback('legacy-token','LEGACY_AUTHORITY','NOK')['status']==='paid_review','Duplicate review lost after replay');
$courses->price=0;$gateway->start(7,2);$gateway->start(7,2);check((int)$pdo->query('SELECT COUNT(*) FROM creator_course_orders WHERE course_id=2')->fetchColumn()===1,'Duplicate free entitlement');
denied(fn()=>Modules\System\Services\PaymentMutex::resume(['amount'=>100,'authority'=>'x'],200));
denied(fn()=>Modules\System\Services\PaymentMutex::resume(['amount'=>100,'authority'=>null],100));
$second=new PDO('sqlite::memory:');
Modules\System\Services\PaymentMutex::run($pdo,'contention',function()use($second){denied(fn()=>Modules\System\Services\PaymentMutex::run($second,'contention',fn()=>true));});
check(Modules\System\Services\PaymentMutex::run($second,'contention',fn()=>true),'Lock was not released');
try{Modules\System\Services\PaymentMutex::run($pdo,'exception',fn()=>throw new RuntimeException('fixture'));}catch(RuntimeException){}
check(Modules\System\Services\PaymentMutex::run($second,'exception',fn()=>true),'Exception leaked lock');

$pdo->exec("CREATE TABLE users(user_id INTEGER,phone TEXT,email TEXT); INSERT INTO users VALUES(7,NULL,NULL);
CREATE TABLE academy_branch_course_term_invoices(term_invoice_id INTEGER PRIMARY KEY,status TEXT,updated_at TEXT,updated_by INTEGER,deleted_at TEXT);
CREATE TABLE academy_branch_course_term_invoice_installments(term_invoice_installment_id INTEGER PRIMARY KEY,invoice_id INTEGER,installment_number INTEGER,amount INTEGER,status TEXT,paid_at TEXT,updated_at TEXT,updated_by INTEGER,deleted_at TEXT);
INSERT INTO academy_branch_course_term_invoices VALUES(1,'issued',NULL,NULL,NULL);
ALTER TABLE academy_branch_course_term_invoices ADD COLUMN payable_amount INTEGER DEFAULT 200;
INSERT INTO academy_branch_course_term_invoice_installments VALUES(1,1,1,100,'pending',NULL,NULL,NULL,NULL),(2,1,2,100,'pending',NULL,NULL,NULL,NULL);
CREATE TABLE academy_term_invoice_payments(payment_id INTEGER PRIMARY KEY,invoice_id INTEGER,installment_id INTEGER,user_id INTEGER,amount INTEGER,currency TEXT,callback_token TEXT,status TEXT,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,authority TEXT,gateway_code INTEGER,requested_at TEXT,gateway_message TEXT,reference_id TEXT,card_pan TEXT,card_hash TEXT,verified_at TEXT,deleted_at TEXT);");
$pdo->exec("ALTER TABLE academy_branch_course_term_invoices ADD COLUMN currency_id INTEGER DEFAULT 1; CREATE TABLE financial_system_currency(currency_id INTEGER,code TEXT); INSERT INTO financial_system_currency VALUES(1,'IRT');");
$invoiceGateway=new class extends Modules\Academy\Services\ZarinpalPaymentService {
 public int $calls=0;public bool $missingRef=false;
 protected function payable(int$a,int$i,int$s):array{return [DB::table('academy_branch_course_term_invoices')->where('term_invoice_id',$i)->first(),DB::table('academy_branch_course_term_invoice_installments')->where('term_invoice_installment_id',$s)->first()];}
 protected function request(string$path,array$data):array{if(str_contains($path,'inquiry'))return ['data'=>['code'=>100,'status'=>'IN_BANK']];if(str_contains($path,'request'))return ['data'=>['code'=>100,'authority'=>'INVOICE_AUTH_'.++$this->calls]];return ['data'=>['code'=>100,'ref_id'=>$this->missingRef?'':54321]];}
};
$pdo->exec("UPDATE financial_system_currency SET code='IRR'");
denied(fn()=>$invoiceGateway->start(7,1,1));check($invoiceGateway->calls===0,'Currency mismatch reached gateway');
$pdo->exec("UPDATE financial_system_currency SET code='IRT'");
$url=$invoiceGateway->start(7,1,1);check($invoiceGateway->start(7,1,1)===$url&&$invoiceGateway->calls===1,'Duplicate invoice request');
$payment=DB::table('academy_term_invoice_payments')->where('payment_id',1)->first();
check($invoiceGateway->callback($payment['callback_token'],$payment['authority'],'OK')['success'],'Invoice verification');
check(DB::table('academy_branch_course_term_invoices')->first()['status']==='partial','Partial invoice status');
check($invoiceGateway->callback($payment['callback_token'],$payment['authority'],'NOK')['success'],'Invoice cancellation downgraded paid payment');
$invoiceGateway->start(7,1,2);$payment=DB::table('academy_term_invoice_payments')->where('payment_id',2)->first();
$invoiceGateway->callback($payment['callback_token'],$payment['authority'],'OK');check(DB::table('academy_branch_course_term_invoices')->first()['status']==='paid','Fully settled invoice status');
$pdo->exec("UPDATE academy_term_invoice_payments SET status='pending' WHERE payment_id=2");
check($invoiceGateway->callback($payment['callback_token'],$payment['authority'],'OK')['requiresReview'],'Duplicate verified funds not flagged');
check($invoiceGateway->callback($payment['callback_token'],$payment['authority'],'OK')['requiresReview'],'Replay lost reconciliation state');
$pdo->exec("ALTER TABLE academy_term_invoice_payments ADD COLUMN gateway TEXT;
ALTER TABLE academy_term_invoice_payments ADD COLUMN payment_method TEXT;
ALTER TABLE academy_term_invoice_payments ADD COLUMN payer_name TEXT;
ALTER TABLE academy_term_invoice_payments ADD COLUMN bank_card_type TEXT;
ALTER TABLE academy_term_invoice_payments ADD COLUMN description TEXT;
ALTER TABLE academy_branch_course_term_invoice_installments ADD COLUMN approved_at TEXT;
ALTER TABLE academy_branch_course_term_invoice_installments ADD COLUMN approved_by INTEGER;
INSERT INTO academy_branch_course_term_invoices(term_invoice_id,status) VALUES(2,'issued');
INSERT INTO academy_branch_course_term_invoice_installments(term_invoice_installment_id,invoice_id,installment_number,amount,status) VALUES(3,2,1,100,'pending'),(4,2,2,100,'pending');");
$offline=new class extends Modules\Academy\Services\OfflineInstallmentPaymentService {protected function allowedInvoices(int$actor):array{return [2];}};
$manual=['method'=>'pos','reference'=>'POS-123','payerName'=>'Fixture user','bankCardType'=>'melli','paymentDate'=>'2026-01-01','paymentTime'=>'12:00'];
$offline->record(7,2,3,$manual);denied(fn()=>$offline->record(7,2,3,$manual));
denied(fn()=>$offline->record(7,2,4,$manual));
$invoiceGateway->start(7,2,4);$manual['reference']='POS-456';denied(fn()=>$offline->record(7,2,4,$manual));

$pdo->exec("CREATE TABLE academies(academy_id INTEGER,user_id INTEGER,created_at TEXT,deleted_at TEXT);
INSERT INTO academies VALUES(1,7,'2026-01-01',NULL);
CREATE TABLE academy_subscription_periods(subscription_period_id INTEGER PRIMARY KEY,academy_id INTEGER,period_start TEXT,period_end TEXT,amount INTEGER,currency TEXT,status TEXT,due_date TEXT,paid_at TEXT,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,deleted_at TEXT);
INSERT INTO academy_subscription_periods(subscription_period_id,academy_id,period_start,period_end,amount,currency,status) VALUES(1,1,'2026-02-01','2026-03-01',2000000,'IRT','pending');
CREATE TABLE academy_subscription_payments(subscription_payment_id INTEGER PRIMARY KEY,subscription_period_id INTEGER,academy_id INTEGER,user_id INTEGER,amount INTEGER,currency TEXT,callback_token TEXT,status TEXT,created_at TEXT,created_by INTEGER,updated_at TEXT,updated_by INTEGER,authority TEXT,gateway_code INTEGER,requested_at TEXT,gateway_message TEXT,reference_id TEXT,verified_at TEXT,deleted_at TEXT);");
$subscription=new class extends Modules\Academy\Services\AcademySubscriptionService {
 public int $requests=0;public bool $missingRef=false;
 protected function period(int$a,int$id):array{return DB::table('academy_subscription_periods')->where('subscription_period_id',$id)->first();}
 protected function request(string$path,array$data):array{if(str_contains($path,'inquiry'))return ['data'=>['code'=>100,'status'=>'IN_BANK']];return str_contains($path,'request')?['data'=>['code'=>100,'authority'=>'SUBSCRIPTION_'.++$this->requests]]:['data'=>['code'=>100,'ref_id'=>$this->missingRef?'':777]];}
};
$first=$subscription->start(7,1,1);check($subscription->start(7,1,1)===$first&&$subscription->requests===1,'Duplicate subscription request');
denied(fn()=>$subscription->start(7,1,3));
$sub=DB::table('academy_subscription_payments')->where('subscription_payment_id',1)->first();
$subscription->missingRef=true;denied(fn()=>$subscription->callback($sub['callback_token'],$sub['authority'],'OK'));
check(DB::table('academy_subscription_periods')->where('subscription_period_id',1)->first()['status']==='pending','Missing reference activated subscription');
$subscription->missingRef=false;check($subscription->callback($sub['callback_token'],$sub['authority'],'OK')['success'],'Subscription not settled');
$periodCount=(int)$pdo->query('SELECT COUNT(*) FROM academy_subscription_periods')->fetchColumn();
check($subscription->callback($sub['callback_token'],$sub['authority'],'NOK')['success'],'Paid subscription canceled by replay');
check((int)$pdo->query('SELECT COUNT(*) FROM academy_subscription_periods')->fetchColumn()===$periodCount,'Replay created extra subscription period');
$nextPeriod=DB::table('academy_subscription_periods')->orderBy('subscription_period_id','DESC')->first();
$subscription->start(7,(int)$nextPeriod['subscription_period_id'],1);
$newPayment=DB::table('academy_subscription_payments')->orderBy('subscription_payment_id','DESC')->first();
DB::table('academy_subscription_periods')->where('subscription_period_id',(int)$nextPeriod['subscription_period_id'])->update(['status'=>'canceled']);
check($subscription->callback($newPayment['callback_token'],$newPayment['authority'],'OK')['requiresReview'],'Canceled subscription activated by a late payment');
check(DB::table('academy_subscription_periods')->where('subscription_period_id',(int)$nextPeriod['subscription_period_id'])->first()['status']==='canceled','Late payment overwrote canceled subscription');
$pdo->exec("INSERT INTO academy_branch_course_term_invoices(term_invoice_id,status,payable_amount) VALUES(3,'issued',100);
INSERT INTO academy_branch_course_term_invoice_installments(term_invoice_installment_id,invoice_id,installment_number,amount,status) VALUES(5,3,1,100,'pending');");
$invoiceGateway->start(7,3,5);
$receipt=DB::table('academy_term_invoice_payments')->orderBy('payment_id','DESC')->first();
$pdo->exec('UPDATE academy_branch_course_term_invoices SET payable_amount=200 WHERE term_invoice_id=3');
check($invoiceGateway->callback($receipt['callback_token'],$receipt['authority'],'OK')['requiresReview'],'Inconsistent invoice was settled automatically');
check(DB::table('academy_term_invoice_payments')->where('payment_id',(int)$receipt['payment_id'])->first()['status']==='paid','Verified receipt lost during reconciliation');
check(DB::table('academy_branch_course_term_invoice_installments')->where('term_invoice_installment_id',5)->first()['status']==='pending','Inconsistent installment overwritten');
echo "Reliability: $checks checks passed.\n";
