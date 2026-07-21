<?php

namespace Modules\Blog\DTO;

class BlogDTO
{
    public ?int $post_id = null;
    public ?int $author_id = null;

    public array $categories = [];

    public ?string $cover = null;
    public ?int $cover_media_id = null;

    public ?string $slug = null;

    public int $views_count = 0;

    public ?string $published_at = null;

    public ?string $type = null;

    public ?string $status = null;

    public ?string $visibility = null;

    public ?int $visibility_user_id = null;

    public ?string $password = null;

    public ?int $comment_count = 0;

    public ?string $name = null;

    public ?string $pinged = null;

    public ?string $guid = null;

    public ?string $related_posts_id = null;

    public ?string $created_at = null;

    public ?string $updated_at = null;

    public array $translations = [];

    public static function fromArray(array $row): static
    {
        $dto = new static();

        foreach ($row as $key => $value) {

            if (!property_exists($dto, $key)) {
                continue;
            }

            switch ($key) {

                case 'categories':

                    if (is_array($value)) {
                        $dto->categories = $value;
                    } elseif ($value) {
                        $dto->categories = explode(',', $value);
                    }

                    break;

                case 'translations':

                    $dto->translations = is_array($value)
                        ? $value
                        : [];

                    break;

                default:

                    $dto->$key = $value;

            }

        }

        return $dto;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }
}