<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Interfaces\UserRepositoryInterface;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function model()
    {
        return User::class;
    }

    public function updateProfile($userId, array $data)
    {
        $user = $this->find($userId);
        $user->update($data);
        return $user;
    }

    public function updatePassword($userId, $password)
    {
        $user = $this->find($userId);
        $user->password = $password;
        $user->password_updated_at = now();
        $user->save();
        return $user;
    }

    public function updatePhoto($userId, $photoPath)
    {
        $user = $this->find($userId);
        $user->photo_path = $photoPath;
        $user->photo_updated_at = now();
        $user->save();
        return $user;
    }

    public function update2FASettings($userId, $enabled)
    {
        $user = $this->find($userId);
        $user->two_factor_enabled = $enabled;
        $user->save();
        return $user;
    }

    public function updateRole($userId, $role)
    {
        $user = $this->find($userId);
        $user->role = $role;
        $user->save();
        return $user;
    }

    public function updateStatus($userId, $status)
    {
        $user = $this->find($userId);
        $user->status = $status;
        $user->save();
        return $user;
    }

    public function incrementReportCount($userId)
    {
        $user = $this->find($userId);
        $user->reports_count = ($user->reports_count ?? 0) + 1;
        $user->save();
        return $user;
    }

    public function resetReportCount($userId)
    {
        $user = $this->find($userId);
        $user->reports_count = 0;
        $user->save();
        return $user;
    }

    public function countByStatus($status)
    {
        return $this->model->where('status', $status)->count();
    }

    public function getAdmins()
    {
        return $this->model->where('role', 'admin')->get();
    }
}
