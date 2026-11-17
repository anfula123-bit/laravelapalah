<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'transaction_date',
        'total_amount',
        'payment_method',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'total_amount' => 'decimal:2',
        ];
    }

    /**
     * @return HasMany<TransactionDetail, $this>
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
