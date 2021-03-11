<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{
    use HasFactory, HasApiTokens;

    /**
         * The table associated with the model.
         *
         * @var string
    */
    protected $table = 'products';

    public function getPictures(){
        return $this->hasMany(ProductGallery::class);
    }
}
