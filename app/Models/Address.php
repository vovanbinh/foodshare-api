<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'contact_information',
        'province',
        'district',
        'commune',
        'home_number',
        'formatted_address',
        'user_id',
        'lat',
        'lon',
        'note'
    ];

    public function user()
    {
        return $this->belongsTo(Users::class);
    }
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
}
