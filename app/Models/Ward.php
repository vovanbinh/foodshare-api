<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;
    protected $table = 'ward';

    protected $fillable = [
        'id',
        'name',
        'district_id',
    ];

    public function district()
    {
        return $this->belongsTo(district::class, 'district_id');
    }
}
