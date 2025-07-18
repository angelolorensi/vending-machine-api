<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'transaction_id';
    protected $table = 'transactions';

    protected $fillable = [
        'employee_id',
        'machine_id',
        'slot_id',
        'product_id',
        'card_id',
        'points_deducted',
        'transaction_time',
        'status',
        'failure_reason'
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'status' => TransactionStatus::class,
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function machine(): BelongsTo
    {
        return $this->belongsTo(Machine::class, 'machine_id', 'machine_id');
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class, 'slot_id', 'slot_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class, 'card_id', 'card_id');
    }
}
