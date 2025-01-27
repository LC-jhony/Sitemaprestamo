<?php

namespace App\Models;

use App\Models\Customer;
use App\Models\Frecuency;
use App\Models\Plan;
use App\Models\Rate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    /** @use HasFactory<\Database\Factories\LoanFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'amount',
        'frecuency_id',
        'user_id',
        'customer_id',
        'rate_id',
    ];
    public function frecuency(): BelongsTo
    {
        return $this->belongsTo(
            related: Frecuency::class,
            foreignKey: 'frecuency_id',
        );
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
            foreignKey: 'user_id',
        );
    }
    public function customer(): BelongsTo
    {
        return $this->belongsTo(
            related: Customer::class,
            foreignKey: 'customer_id',
        );
    }
    public function rate(): BelongsTo
    {
        return $this->belongsTo(
            related: Rate::class,
            foreignKey: 'rate_id',
        );
    }
    public function plans()
    {
        return $this->hasMany(
            related: Plan::class,
            foreignKey: 'loan_id',
        );
    }
}
