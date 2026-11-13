<?php

namespace App\Models;

use App\Enums\MovieFormat;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['title', 'genre', 'format', 'price', 'stock', 'synopsis'];

    protected function casts(): array
    {
        return [
            'format' => MovieFormat::class,
            'price'  => 'decimal:2',
            'stock'  => 'integer',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_movie')
            ->withPivot(['quantity', 'price_at_purchase']);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeOfFormat(Builder $query, string $format): Builder
    {
        return $query->where('format', $format);
    }

    public function isAvailable(): bool
    {
        return $this->stock > 0;
    }
}