<?php

namespace Modules\Blog\Repositories;

use Modules\Blog\Contracts\CategoryRepositoryInterface;
use Modules\Blog\DTO\CategoryDTO;
use PDO;

class CategoryRepository implements CategoryRepositoryInterface
{
    protected PDO $db;

    public function __construct()
    {
        $this->db = db();
    }

    public function all(): array
    {
        $stmt = $this->db->query("
            SELECT *
            FROM categories
            WHERE deleted_at IS NULL
            ORDER BY name
        ");

        $rows = $stmt->fetchAll();

        return array_map(
            fn($row) => CategoryDTO::fromArray(
                $this->attachTranslations($row)
            ),
            $rows
        );
    }

    public function find(int $id): ?CategoryDTO
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE category_id=:id
            LIMIT 1
        ");

        $stmt->execute([
            'id'=>$id
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return CategoryDTO::fromArray(
            $this->attachTranslations($row)
        );
    }

    public function findBySlug(string $slug): ?CategoryDTO
    {
        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE slug=:slug
            LIMIT 1
        ");

        $stmt->execute([
            'slug'=>$slug
        ]);

        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return CategoryDTO::fromArray(
            $this->attachTranslations($row)
        );
    }

    public function findMany(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("
            SELECT *
            FROM categories
            WHERE category_id IN ($placeholders)
        ");

        $stmt->execute($ids);

        $rows = $stmt->fetchAll();

        return array_map(
            fn($row)=>CategoryDTO::fromArray(
                $this->attachTranslations($row)
            ),
            $rows
        );
    }

    protected function attachTranslations(array $row): array
    {
        $row['translations'] =
            TranslationRepository::table('categories')
                ->record($row['category_id'])
                ->locale('fa')
                ->get();

        return $row;
    }
}