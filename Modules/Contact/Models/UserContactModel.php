<?php

namespace Modules\Contact\Models;

use Core\Database\Model;

class UserContactModel extends Model {


    protected static string $table = 'user_contacts';
    protected static string $primaryKey = 'user_contact_id';
    protected array $fillable = [
        'user_id',
        'mode',
        'platform',
        'value',
        'priority',
        'is_main',
        'status',
    ];



}