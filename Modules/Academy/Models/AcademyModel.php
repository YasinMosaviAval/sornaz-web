<?php

namespace Modules\Academy\Models;

use Core\Database\Model;
use Modules\Academy\Scopes\AcademyScope;

class AcademyModel extends Model {

    // protected static string $table = 'users';
    // protected static string $primaryKey = 'user_id';
    protected static string $table='academies';
    protected static string $primaryKey='academy_id';



    // protected array $fillable = [
    //     'username',
    //     'email',
    //     'phone',
    //     'status',
    //     'locale',
    //     'timezone',
    //     'avatar_file_id',
    //     'type'
    // ];

    protected array $fillable = [
        'user_id',
    ];

    protected array $translated = [
        'title',
        'summary',
        'description',
    ];


    /*
    |--------------------------------------------------------------------------
    | Local Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeAcademies($query) {return $query->where('type', 'academy');}

    public function scopeActive($query) {return $query->where('status', 1);}

    public function scopeInactive($query) {return $query->where('status', 0);}


    protected static function bootModel(): void {
        static::addGlobalScope(new AcademyScope());
    }

}