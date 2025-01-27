<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rate extends Model
{
    /** @use HasFactory<\Database\Factories\RateFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'percent',
        'fee',
        'state',
    ];
    public function loans()
    {
        return $this->hasMany(
            related: Loan::class,
            foreignKey: 'rate_id'
        );
    }
}
