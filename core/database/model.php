<?php

namespace Core\Database;

use Core\Database\Concerns\HasAttributes;
use Core\Database\Concerns\HasBooting;
use Core\Database\Concerns\HasCRUD;
use Core\Database\Concerns\HasEvents;
use Core\Database\Concerns\HasGlobalScopes;
use Core\Database\Concerns\HasQueries;
use Core\Database\Concerns\HasRelationships;
use Core\Database\Concerns\HasObservers;
use Core\Database\Concerns\HasTimestamps;
use Core\Database\Concerns\GuardsAttributes;
use Core\Database\Concerns\HasMagicMethods;
use Core\Database\Concerns\LoadsRelations;
use Core\Database\Concerns\HasTranslations;
use Core\Translation\TranslationManager;

abstract class Model {


    use HasAttributes;
    use HasBooting;
    use HasCRUD;
    use HasEvents;
    use HasGlobalScopes;
    use HasQueries;
    use HasRelationships;
    use HasObservers;
    use HasTimestamps;
    use GuardsAttributes;
    use HasMagicMethods;
    use LoadsRelations;
    use HasTranslations;

    protected static string $table;
    protected static string $primaryKey = 'id';
    protected array $relations = [];
    protected ?TranslationManager $translator = null;

    public function setRelation(string $name, mixed $value): static {
        $this->relations[$name] = $value;
        return $this;
    }



    public function getRelation(string $name): mixed {return $this->relations[$name] ?? null;}



    public function relationLoaded(string $name): bool {return array_key_exists($name, $this->relations);}



    public static function getTable(): string {return static::$table;}


    public function translator(): TranslationManager {
        if ($this->translator === null) {
            $this->translator = new TranslationManager();
        }
        return $this->translator->for($this);
    }


    public function trans(string $field, ?string $locale = null, int $version = 1): mixed {
        return $this->translator()->get($this, $field, $locale, $version);
    }


    public function setTrans(string $field, mixed $value, ?string $locale = null, int $version = 1): bool {
        return $this->translator()->set($this, null, $field, $value, $locale, $version);
    }



}

