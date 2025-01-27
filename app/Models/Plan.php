<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Plan extends Model
{
    /** @use HasFactory<\Database\Factories\PlanFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'loan_id',
        'date',
        'number',
        'payment',
        'interest',
        'amort',
        'balance',
    ];
    public function loan(): BelongsTo
    {
        return $this->belongsTo(
            related: Loan::class,
            foreignKey: 'loan_id'
        );
    }
}
