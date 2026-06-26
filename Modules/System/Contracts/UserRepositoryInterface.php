<?php
namespace Modules\System\Contracts;

interface UserRepositoryInterface
{
    public function all(): array;

    public function find(
        int $id
    ): ?array;

    public function create(
        array $data
    ): bool;

    public function update(
        int $id,
        array $data
    ): bool;

    public function delete(
        int $id
    ): bool;
}