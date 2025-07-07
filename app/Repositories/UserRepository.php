<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getActiveUsers(): Collection
    {
        return $this->model->where('is_active', true)->get();
    }

    public function getAdmins(): Collection
    {
        return $this->model->where('role', 'admin')->get();
    }

    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    public function getUsersWithPosts(): Collection
    {
        return $this->model->with(['posts' => function ($query) {
            $query->published();
        }])->get();
    }
}
