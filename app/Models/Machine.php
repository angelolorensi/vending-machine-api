<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Machine extends Model
{
    use HasFactory;

    protected $table = 'machines';
    protected $primaryKey = 'machine_id';

    protected $fillable = [
        'location',
        'name',
        'status'
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'machine_id');
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class, 'machine_id');
    }
}
