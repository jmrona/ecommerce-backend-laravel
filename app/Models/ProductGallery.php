<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    use HasFactory;

    /**
         * The table associated with the model.
         *
         * @var string
         */
        protected $table = 'products_gallery';
}
