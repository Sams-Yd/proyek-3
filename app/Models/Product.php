<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'sku_singkat',
        'nama_produk',
        'stok_pcs',
        'stok_minimum',
        'harga_beli',
        'tgl_kadaluarsa',
    ];

    protected function casts(): array
    {
        return [
            'harga_beli' => 'decimal:2',
            'stok_pcs' => 'integer',
            'stok_minimum' => 'integer',
            'tgl_kadaluarsa' => 'date',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(ProductUnit::class)->orderBy('jumlah_konversi', 'asc');
    }

    public function transactionDetails(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }

    public function stockAdjustments(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function baseUnit(): ?ProductUnit
    {
        return $this->units()->where('jumlah_konversi', 1)->first() ?? $this->units()->first();
    }

    public function scopeLowStock(Builder $query): Builder
    {
        return $query->whereColumn('stok_pcs', '<=', 'stok_minimum');
    }

    public function scopeExpiringSoon(Builder $query, int $days = 30): Builder
    {
        return $query->whereNotNull('tgl_kadaluarsa')
            ->whereDate('tgl_kadaluarsa', '<=', Carbon::now()->addDays($days))
            ->whereDate('tgl_kadaluarsa', '>=', Carbon::now());
    }

    public function scopeExpired(Builder $query): Builder
    {
        return $query->whereNotNull('tgl_kadaluarsa')
            ->whereDate('tgl_kadaluarsa', '<', Carbon::now());
    }
}
