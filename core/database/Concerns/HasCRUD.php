<?php

namespace Core\Database\Concerns;

use Core\Database\DB;
use Core\Database\SoftDeletes;

trait HasCRUD {


    // protected static string $table;
    // protected static string $primaryKey = 'id';

    public function save(): bool {
        return DB::table(static::$table)->insert($this->attributes);
    }


    public static function create(array $data): bool {
        $model = new static();
        $model->fill($data);
        $model->updateTimestampsOnCreate($model->attributes);
        if (!static::fireEvent('creating', $model)) {return false;}
        $result = DB::table(static::$table)->insert($model->toArray());
        if ($result) {static::fireEvent('created', $model);}
        return $result;
    }


    public function update(array $data): bool {
        if (!static::fireEvent('updating', $this)) {return false;}
        $this->fill($data);
        $this->updateTimestampOnUpdate($this->attributes);
        $result = static::query()->where(static::$primaryKey, $this->{static::$primaryKey})->update($this->toArray());
        if ($result) {static::fireEvent('updated', $this);}
        return $result;
    }

    public function delete(): bool {
        if (!static::fireEvent('deleting', $this)) {return false;}
        if (in_array(SoftDeletes::class, class_uses(static::class))) {
            $time = date('Y-m-d H:i:s');
            $result = static::query()->withTrashed()->where(static::$primaryKey, $this->{static::$primaryKey})->update(['deleted_at' => $time]);
            if ($result) {$this->attributes['deleted_at'] = $time;}
        } else {
            $result = static::query()->where(static::$primaryKey, $this->{static::$primaryKey})->delete();}
        if ($result) {static::fireEvent('deleted', $this);}
        return $result;
    }


    // public function delete(): bool {
    //     static::fireEvent('deleting', $this);
    //     if (in_array(SoftDeletes::class, class_uses(static::class))) {
    //         $result = static::query()->where(static::$primaryKey, $this->{static::$primaryKey})->update(['deleted_at' => date('Y-m-d H:i:s')]);
    //     } else {
    //         $result = static::query()->where(static::$primaryKey, $this->{static::$primaryKey})->delete();
    //     }
    //     if ($result) {
    //         if (isset($this->attributes['deleted_at'])) {
    //             $this->attributes['deleted_at'] = date('Y-m-d H:i:s');
    //         }
    //         static::fireEvent('deleted', $this);
    //     }
    //     return $result;
    // }


}