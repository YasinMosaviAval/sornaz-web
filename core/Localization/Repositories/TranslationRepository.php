<?php

namespace Core\localization\Repositories;

use Core\localization\Contracts\TranslationRepositoryInterface;
use Core\localization\DTO\TranslationDTO;
use Core\localization\TranslationCollection;
use PDO;

class TranslationRepository implements TranslationRepositoryInterface {

    protected PDO $db;



    public function __construct(PDO $db) {
        $this->db = $db;
    }



    public function load(string $table, int|string $tableId, ?string $locale = null, int $version = 1): TranslationCollection {
        $locale ??= app()->getLocale();
        $stmt = $this->db->prepare("SELECT * FROM translations WHERE table_name = :table AND table_id = :id AND locale = :locale AND version = :version AND deleted_at IS NULL");
        $stmt->execute(['table'=>$table, 'id'=>$tableId, 'locale'=>$locale, 'version'=>$version]);
        $collection = new TranslationCollection();
        foreach ($stmt->fetchAll() as $row) {
            $collection->add(TranslationDTO::fromArray($row));
        }
        return $collection;
    }



    public function loadMany(string $table, array $ids, ?string $locale = null, int $version = 1): array {
        if(empty($ids)){
            return [];
        }
        $locale ??= app()->getLocale();
        $placeholders = implode(',', array_fill(0,count($ids),'?'));
        $sql = "SELECT * FROM translations WHERE table_name=? AND locale=? AND version=? AND table_id IN ($placeholders) AND deleted_at IS NULL";
        $stmt = $this->db->prepare($sql);
        $params = [$table, $locale, $version, ...$ids];
        $stmt->execute($params);
        $result = [];
        foreach($stmt->fetchAll() as $row){
            $id = $row['table_id'];
            if(!isset($result[$id])){
                $result[$id] = new TranslationCollection();
            }
            $result[$id]->add(TranslationDTO::fromArray($row));
        }
        return $result;
    }



    public function save(string $table, int|string $tableId, array $translations, ?string $locale = null, int $version = 1): bool {
        $locale ??= app()->getLocale();
        foreach($translations as $field=>$value){
            $stmt = $this->db->prepare("SELECT translation_id FROM translations WHERE table_name=? AND table_id=? AND field=? AND locale=? AND version=? LIMIT 1");
            $stmt->execute([$table, $tableId, $field, $locale, $version]);
            $translation = $stmt->fetch();
            if($translation) {
                $stmt = $this->db->prepare("UPDATE translations SET value=? WHERE translation_id=?");
                $stmt->execute([$value, $translation['translation_id']]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO translations (table_name, table_id, locale, field, value, version) VALUES (?,?,?,?,?,?)");
                $stmt->execute([$table, $tableId, $locale, $field, $value, $version]);
            }
        }
        return true;
    }



    public function delete(string $table, int|string $tableId, ?string $locale = null, int $version = 1): bool {
        $locale ??= app()->getLocale();
        $stmt = $this->db->prepare("UPDATE translations SET deleted_at=NOW() WHERE table_name=? AND table_id=? AND locale=? AND version=?");
        return $stmt->execute([$table, $tableId, $locale, $version]);
    }




}