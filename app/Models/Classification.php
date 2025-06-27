<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classification extends Model
{
    use HasFactory;

    protected $table = 'classifications';
    protected $primaryKey = 'classification_id';

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'classification_id');
    }
}
