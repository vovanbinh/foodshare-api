<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    use HasFactory;

    protected $table = 'district';

    protected $fillable = [
        'id',
        'name',
        'province_id',
    ];

    public function province()
    {
        return $this->belongsTo(province::class, 'province_id');
    }
}