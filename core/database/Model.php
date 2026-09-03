<?php

namespace Core\database;

use Core\database\Concerns\HasAttributes;
use Core\database\Concerns\HasBooting;
use Core\database\Concerns\HasCRUD;
use Core\database\Concerns\HasEvents;
use Core\database\Concerns\HasGlobalScopes;
use Core\database\Concerns\HasQueries;
use Core\database\Concerns\HasRelationships;
use Core\database\Concerns\HasObservers;
use Core\database\Concerns\HasTimestamps;
use Core\database\Concerns\GuardsAttributes;
use Core\database\Concerns\HasMagicMethods;
use Core\database\Concerns\LoadsRelations;
use Core\database\Concerns\HasTranslations;
use Core\translation\TranslationManager;

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
    protected array $translated = [];
    protected array $translatedAttributes = [];
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

    public function getTranslatedAttributes(): array {
        return $this->translated;
    }

    public function setTranslatedAttribute(string $field, mixed $value): void {
        $this->translatedAttributes[$field] = $value;
    }

    public function getTranslatedAttribute(string $field): mixed {
        return $this->translatedAttributes[$field] ?? null;
    }

    public function getDirtyTranslations(): array {
        return $this->translatedAttributes;
    }

    public function clearDirtyTranslations(): void {
        $this->translatedAttributes = [];
    }

    public function hasDirtyTranslations(): bool {
        return !empty($this->translatedAttributes);
    }
}
