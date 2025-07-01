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

    protected $fillable = [
        'name',
        'daily_juice_limit',
        'daily_meal_limit',
        'daily_snack_limit',
        'daily_point_limit',
        'daily_point_recharge_amount',
    ];


    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'classification_id');
    }
}
