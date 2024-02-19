<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImagesFood extends Model
{
    use HasFactory;

    protected $table = 'images_food';

    protected $fillable = [
        'food_id',
        'image_url',
    ];

    public function food()
    {
        return $this->belongsTo(Foods::class, 'food_id');
    }
}
