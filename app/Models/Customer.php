<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    /** @use HasFactory<\Database\Factories\CustomerFactory> */
    use HasFactory;
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'salary',
        'age',
        'gender',
        'avatar',
        'identification',
    ];
    public function loans()
    {
        return $this->hasMany(
            related: Loan::class,
            foreignKey: 'customer_id'
        );
    }
}
