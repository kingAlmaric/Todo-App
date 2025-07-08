<?php

namespace App\Policies;

use App\Models\Todo;
use App\Models\User;

class TodoPolicy
{
    public function update(User $user, Todo $todo)
    {
        return $user->id === $todo->user_id;
    }

    public function delete(User $user, Todo $todo)
    {
        return $user->id === $todo->user_id;
    }

    public function download(User $user, Todo $todo)
    {
        return $user->id === $todo->user_id;
    }
} 