<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Frecuency extends Model
{
    /** @use HasFactory<\Database\Factories\FrecuencyFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'days',
    ];
    public function loans(): HasMany
    {
        return $this->hasMany(
            related: Loan::class,
            foreignKey: 'frecuency_id'
        );
    }
}
