<?php

namespace Modules\System\Repositories;

use Core\database\Repository;
// use Modules\System\Models\SystemModel;

class SystemRepository extends Repository {

    // protected ?string $model = SystemModel::class;
    // protected string $table = 'systems';
    // protected string $primaryKey = 'system_id';

    protected string $table = 'settings';
    protected string $primaryKey = 'setting_id';
    protected ?string $model = null;


    public function findByPage(string $page): array {
        $locale = 'fa';
        return $this->query()
            ->select(
                'settings.setting_id',
                // 'settings.parent_id',
                // 'settings.page',
                // 'settings.sort_order',
                'settings.variable_name',
                // 'settings.value as value',
                // 'settings.url',
                // 'settings.source',
                // 'settings.status',
                // 'settings.icon',
                'translations.translation_id as translation_id',
                // 'translations.field as translation_field',
                'translations.value as translated_value',
            )
            ->leftJoin(
                'translations',
                'settings.setting_id',
                '=',
                'translations.table_id'
            )
            ->where('translations.table_name', 'settings')
            ->where('translations.locale', $locale)
            ->where('settings.page', $page)
            ->whereNull('settings.deleted_at')
            ->whereNull('translations.deleted_at')
            ->orderBy('settings.sort_order')
            ->get();
    }



}
