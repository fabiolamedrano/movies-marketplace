<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_amount', 'status'];

    protected function casts(): array
    {
        return [
            'status'       => OrderStatus::class,
            'total_amount' => 'decimal:2',
        ];
    }

    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(Movie::class, 'order_movie')
            ->withTrashed()
            ->withPivot(['quantity', 'price_at_purchase']);
    }
}