<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OwnerPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'auth_owner_id',
        'amount_paid',
        'paid_date',
        'remarks',
    ];

    public function authOwner()
    {
        return $this->belongsTo(AuthOwner::class, 'auth_owner_id');
    }
}
