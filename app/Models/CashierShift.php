<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashierShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'modal_awal',
        'total_setoran_fisik',
        'total_sistem',
        'selisih',
        'waktu_buka',
        'waktu_tutup',
        'status',
        'catatan',
    ];

    protected function casts(): array
    {
        return [
            'modal_awal' => 'decimal:2',
            'total_setoran_fisik' => 'decimal:2',
            'total_sistem' => 'decimal:2',
            'selisih' => 'decimal:2',
            'waktu_buka' => 'datetime',
            'waktu_tutup' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'shift_id');
    }

    public function calculateCashSales(): float
    {
        return (float) $this->transactions()
            ->where('metode_pembayaran', 'tunai')
            ->sum('total_akhir');
    }

    public function calculateNonCashSales(): float
    {
        return (float) $this->transactions()
            ->where('metode_pembayaran', '!=', 'tunai')
            ->sum('total_akhir');
    }

    public function calculateExpectedSystemCash(): float
    {
        return (float) ($this->modal_awal + $this->calculateCashSales());
    }

    public function closeWithPhysicalCash(float $physicalCash, ?string $notes = null): void
    {
        $expected = $this->calculateExpectedSystemCash();
        $diff = $physicalCash - $expected;

        $this->update([
            'total_setoran_fisik' => $physicalCash,
            'total_sistem' => $expected,
            'selisih' => $diff,
            'waktu_tutup' => now(),
            'status' => 'closed',
            'catatan' => $notes,
        ]);
    }
}
