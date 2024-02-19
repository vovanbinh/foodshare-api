<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food_Locations extends Model
{
    use HasFactory;
    protected $table = 'food_locations';
    protected $fillable = [
        'name',
        'description',
        'time',
        'image',
        'address',
        'contact_person',
        'status',
        'contact_number',
        'province_id',
        'district_id',
        'ward_id',
    ];
    public function province()
    {
        return $this->belongsTo(province::class, 'province_id');
    }

    public function district()
    {
        return $this->belongsTo(district::class, 'district_id');
    }

    public function ward()
    {
        return $this->belongsTo(ward::class, 'ward_id');
    }
    public function foodTransactions()
    {
        return $this->hasMany(FoodTransactions::class, 'food_id');
    }

}
