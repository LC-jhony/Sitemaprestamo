<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    /** @use HasFactory<\Database\Factories\PaymentFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'loan_id',
        'amount',
        'interest',
        'amort',
        'type',
    ];
}
