<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;

class ProductGalleryController extends Controller
{
    public function deletePicture ($id) {

        $picture_deleted = ProductGallery::where('id', $id)->first();

        if(!$picture_deleted){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something went wrong, please contact with admin'
            ]);
        }

        // Delete picture file
        $dir = '/'.$picture_deleted->product_id;
        $full_path = public_path('storage/img/products').$dir;
        unlink($full_path.'/'.$picture_deleted->file_name);

        $picture_id = $picture_deleted->product_id;

        $picture_deleted->delete();
        $products = Product::with(['getPictures'])->orderBy('name', 'Asc')->get();
        $product_updating = Product::with(['getPictures'])->where('id', $picture_id)->first();

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product deleted successfully',
            'products' => $products,
            'product_updating' => $product_updating
        ]);
    }
}
