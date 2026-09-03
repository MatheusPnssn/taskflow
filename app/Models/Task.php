<?php

namespace App\Models;

use App\Core\Database\Model\Model;

class Task extends Model {

    public function features(){
        return $this->hasMany(TaskFeature::class, 'task_id');
    }
}