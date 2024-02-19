<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    use HasFactory;

    protected $table = 'rates';
    protected $fillable = ['food_transaction_id', 'rating', 'review'];

    public function foodTransaction()
    {
        return $this->belongsTo(FoodTransactions::class, 'food_transaction_id', 'id');
    }
}
