<?php

namespace Modules\System\Observers;

use Modules\System\Models\User;

class UserObserver {

    public function creating(User $user) {echo "creating<br>";}
    public function created(User $user) {echo "created<br>";}
    public function updating(User $user) {echo "updating<br>";}
    public function updated(User $user) {echo "updated<br>";}
    public function deleting(User $user) {echo "deleting<br>";}
    public function deleted(User $user) {echo "deleted<br>";}

}