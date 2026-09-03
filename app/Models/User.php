<?php

namespace App\Models;

use App\Core\Database\Model\Model;

class User extends Model {

    public function tasks(){
        return $this->hasMany(Task::class, 'manager_id')->where('tasks.title', 'Escrow');
    }

    public function contacts(){
        return $this->hasMany(ContactList::class, 'user_id');
    }
}
