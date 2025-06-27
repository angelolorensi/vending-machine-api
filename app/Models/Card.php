<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    public function employee()
    {
        return $this->hasOne(Employee::class, 'card_id', 'card_id');
    }
}
