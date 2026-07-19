<?php

namespace Core\Database\Concerns;

use Core\Database\DB;
use Core\Database\SoftDeletes;

trait HasCRUD {


    public function save(): bool {
        /*
        |--------------------------------------------------------------------------
        | Insert
        |--------------------------------------------------------------------------
        */
        if (empty($this->{static::$primaryKey})) {
            $result = DB::table(static::$table)->insert($this->toArray());
            if (!$result) {
                return false;
            }
            $this->{static::$primaryKey} = DB::table(static::$table)->lastInsertId();
            static::fireEvent('created', $this);
            $this->persistTranslations();
            return true;
        }
        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */
        $result = DB::table(static::$table)
            ->where(static::$primaryKey, $this->{static::$primaryKey})
            ->update($this->toArray());
        if ($result) {
            static::fireEvent('updated', $this);
            $this->persistTranslations();
        }
        return $result;
    }


    public static function create(array $data): static {
        $model = new static();
        $model->fill($data);
        $model->save();
        return $model;
    }


    public function update(array $data): bool {
        $this->fill($data);
        return $this->save();
    }


    public function delete(): bool {
        if (!static::fireEvent('deleting', $this)) {
            return false;
        }
        if (in_array(SoftDeletes::class, class_uses(static::class))) {
            $time = date('Y-m-d H:i:s');
            $result = static::query()
                ->withTrashed()
                ->where(static::$primaryKey, $this->{static::$primaryKey})
                ->update(['deleted_at' => $time]);
            if ($result) {
                $this->attributes['deleted_at'] = $time;
            }
        } else {
            $result = static::query()
                ->where(static::$primaryKey, $this->{static::$primaryKey})
                ->delete();
        }
        if ($result) {
            static::fireEvent('deleted', $this);
        }
        return $result;
    }


    protected function persistTranslations(): void {
        if (!$this->hasDirtyTranslations()) {
            return;
        }
        foreach ($this->getDirtyTranslations() as $field => $value) {
            $this->setTrans($field, $value);
        }
        $this->clearDirtyTranslations();
    }





}