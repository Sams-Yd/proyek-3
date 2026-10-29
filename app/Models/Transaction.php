<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_faktur',
        'user_id',
        'shift_id',
        'subtotal',
        'diskon',
        'total_akhir',
        'bayar',
        'kembalian',
        'metode_pembayaran',
        'waktu_transaksi',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'diskon' => 'decimal:2',
            'total_akhir' => 'decimal:2',
            'bayar' => 'decimal:2',
            'kembalian' => 'decimal:2',
            'waktu_transaksi' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shift(): BelongsTo
    {
        return $this->belongsTo(CashierShift::class, 'shift_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function calculateHpp(): float
    {
        $hpp = 0;
        foreach ($this->details as $detail) {
            $conversion = $detail->unit ? $detail->unit->jumlah_konversi : 1;
            $buyPrice = $detail->product ? (float) $detail->product->harga_beli : 0;
            $hpp += ($detail->qty * $conversion) * $buyPrice;
        }

        return $hpp;
    }

    public function calculateGrossProfit(): float
    {
        return (float) $this->total_akhir - $this->calculateHpp();
    }
}
