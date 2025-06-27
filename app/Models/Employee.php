<?php

namespace App\Models;

use App\Enums\EmployeeStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';
    protected $primaryKey = 'employee_id';

    protected $fillable = [
        'name',
        'card_number',
        'classification_id',
        'status'
    ];

    protected $casts = [
        'status' => EmployeeStatus::class,
    ];

    public function classification(): BelongsTo
    {
        return $this->belongsTo(Classification::class, 'classification_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'employee_id');
    }
}
