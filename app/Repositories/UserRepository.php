<?php

namespace Modules\Auth\Repositories;

use Illuminate\Support\Collection;
use Modules\Auth\Contracts\UserRepositoryInterface;
use Modules\Auth\Models\User;
use Modules\System\Repositories\BaseRepository;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->model = $user;

    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(array $data): bool
    {
        return $this->model->update($data);
    }

    public function delete(): bool
    {
        return $this->model->delete();
    }

    public function getUsers(): Collection
    {
        return $this->model->all();
    }

    public function getUsername(string $username): User
    {
        return $this->model->where('username', $username)->first();
    }

    public function getEmail(string $email): User
    {
        return $this->model->where('email', $email)->first();
    }

    public function getId(string $id): User
    {
        return $this->model->where('id', $id)->first();
    }
}
