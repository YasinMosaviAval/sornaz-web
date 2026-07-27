<?php

namespace Modules\Blog\Repositories;

use Core\Localization\Contracts\TranslationRepositoryInterface;
use Modules\Blog\Contracts\BlogRepositoryInterface;
use Modules\Blog\DTO\BlogDTO;
use Modules\Blog\Services\TranslationMapper;
use PDO;

class BlogRepository implements BlogRepositoryInterface {

    protected PDO $db;
    protected TranslationRepositoryInterface $translations;


    public function __construct(PDO $db, TranslationRepositoryInterface $translations) {
        $this->db = $db;
        $this->translations = $translations;
    }



    public function paginate(int $page = 1, int $perPage = 15) {
        $offset = ($page - 1) * $perPage;
        $sql = "SELECT posts.* FROM posts WHERE posts.type='post' AND posts.status='published' AND posts.deleted_at IS NULL ORDER BY posts.published_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        $result = [];
        foreach ($posts as $post) {
            $post = $this->attachTranslations($post);
            $result[] = BlogDTO::fromArray($post);
        }
        return $result;
    }



    public function latest(int $limit = 10) {
        $sql = "SELECT * FROM posts WHERE type='post' AND status='published' AND deleted_at IS NULL ORDER BY published_at DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        $result = [];
        foreach ($posts as $post) {
            $post = $this->attachTranslations($post);
            $result[] = BlogDTO::fromArray($post);
        }
        return $result;
    }



    public function popular(int $limit = 10) {
        $sql = "SELECT * FROM posts WHERE type='post' AND status='published' AND deleted_at IS NULL ORDER BY views_count DESC LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $posts = $stmt->fetchAll();
        $result = [];
        foreach ($posts as $post) {
            $post = $this->attachTranslations($post);
            $result[] = BlogDTO::fromArray($post);
        }
        return $result;
    }



    public function find(int $id): ?BlogDTO {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE post_id=:id AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['id'=>$id]);
        $post = $stmt->fetch();
        if(!$post){
            return null;
        }
        $post = $this->attachTranslations($post);
        return BlogDTO::fromArray($post);
    }



    public function findBySlug(string $slug): ?BlogDTO {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE slug=:slug AND status='published' AND deleted_at IS NULL LIMIT 1");
        $stmt->execute(['slug'=>$slug]);
        $post=$stmt->fetch();
        if(!$post){
            return null;
        }
        $post = $this->attachTranslations($post);
        return BlogDTO::fromArray($post);
    }



    public function create(BlogDTO $dto): int {
        $stmt=$this->db->prepare("INSERT INTO posts (author_id, slug, type, status, visibility) VALUES (:author, :slug, :type, :status, :visibility)");
        $stmt->execute([
            'author'=>$dto->author_id,
            'slug'=>$dto->slug,
            'type'=>$dto->type ?? 'post',
            'status'=>$dto->status ?? 'draft',
            'visibility'=>$dto->visibility ?? 'public'
        ]);
        return (int)$this->db->lastInsertId();
    }



    public function update(int $id, BlogDTO $dto): bool {
        $stmt=$this->db->prepare("UPDATE posts SET slug=:slug, status=:status, visibility=:visibility WHERE post_id=:id");
        return $stmt->execute(['slug'=>$dto->slug, 'status'=>$dto->status, 'visibility'=>$dto->visibility, 'id'=>$id]);
    }



    public function delete(int $id): bool {
        $stmt=$this->db->prepare("UPDATE posts SET deleted_at=NOW() WHERE post_id=:id");
        return $stmt->execute(['id'=>$id]);
    }



    protected function attachTranslations(array $post): array {
        $stmt=$this->db->prepare("SELECT field, value FROM translations WHERE table_name='posts' AND table_id=:id AND locale='fa' AND deleted_at IS NULL");
        $stmt->execute(['id'=>$post['post_id']]);
        $rows=$stmt->fetchAll();
        $post['translations'] = TranslationMapper::map($rows);
        return $post;
    }




}