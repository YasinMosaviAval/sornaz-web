<?php

namespace Modules\Blog\DTO;

class BlogDTO {

    public ?int $post_id = null;
    public ?int $author_id = null;
    public ?string $slug = null;
    public array $categories = [];
    public ?int $cover_media_id = null;
    public ?string $type = null;
    public ?string $status = null;
    public ?string $visibility = null;
    public ?string $published_at = null;
    public int $views_count = 0;
    public int $comment_count = 0;
    public array $translations = [];


    public function __construct(public array $attributes=[]){
    }


    public static function fromArray(array $data): static {
        return new static($data);
    }


    public function toArray(): array {
        return $this->attributes;
    }



}