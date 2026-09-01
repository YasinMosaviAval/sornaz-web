<?php
namespace Core\commands;
use Core\console\Command;use Core\database\DB;use Modules\Academy\Services\AcademySubscriptionService;
class SyncAcademySubscriptionsCommand extends Command{public function handle(array$arguments):int{try{$service=new AcademySubscriptionService();$count=0;foreach(DB::table('academies')->whereNull('deleted_at')->get()as$academy){$service->sync((int)$academy['academy_id']);$count++;}$this->info("Academy subscriptions synchronized: $count");return 0;}catch(\Throwable$e){$this->error($e->getMessage());return 1;}}}
