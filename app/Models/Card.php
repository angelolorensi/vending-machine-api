<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Card extends Model
{
    use hasFactory;

    protected $primaryKey = 'card_id';
    protected $table = 'cards';

    protected $fillable = [
        'card_number',
        'points_balance',
        'status'
    ];

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class, 'card_id', 'card_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'card_id', 'card_id');
    }
}
