<?php

namespace Modules\Auth\Contracts;

use Illuminate\Support\Collection;
use Modules\Auth\Models\User;
use Modules\System\Contracts\BaseRepositoryInterface;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function create(array $data): User;

    public function update(array $data): bool;

    public function delete(): bool;

    public function getUsers(): Collection;

    public function getUsername(string $username): User;

    public function getEmail(string $email): User;

    public function getId(string $id): User;
}
