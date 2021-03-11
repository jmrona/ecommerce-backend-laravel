<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductGallery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;


class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
        $this->middleware('userStatus');
    }

    public function storeImages($file, $id){
        $dir = '/'.$id;
        $full_path = public_path('storage/img/products').$dir;
        // localhost:8000/storage/img/products/$id/$image
        $file_ext = trim($file->getClientOriginalExtension());
        $file_name = time().'.'.$file_ext;

        // Checking if the directory exist
        if (!file_exists($full_path)) {
            mkdir($full_path, 666, true);
        }

        // Save a thumbnail of the image (200x200)
        $final_file = $full_path.'/'.$file_name;
        $img = Image::make($file);
        $img->resize(200,200, function( $constraint) {
            $constraint->aspectRatio();
        });

        // Getting pictures of products
        // $productGallery = ProductGallery::where('product_id', $id)->get();

        // if( $productGallery ){
        //     foreach ($productGallery as $key => $value) {
        //         unlink($full_path.'/'.$value['file_name']);
        //     }
        // }

        $productGallery = new ProductGallery();
        $productGallery->file_name = $file_name;
        $productGallery->save();
    }

    public function index()
    {
        $products = Product::with(['getPictures'])->orderBy('name', 'Asc')->get();
        if(!$products){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'No products found'
            ]);
        }

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Products loaded',
            'products' => $products
        ]);

    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'formImages' => 'max:5'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'The name, description or price field is empty'
            ]);
        }

        $category = Category::findOrFail($request->category);

        if(!$category){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Category no found'
            ]);
        }

        $product = new Product();
        $product->name = e($request->name);
        $product->description = e($request->description);
        $product->price = $request->price;
        $product->status = $request->status ?? 0;
        $product->category_id = $category->id;

        if($request->in_discount){
            $product->in_discount = $request->in_discount;
            $product->discount = $request->discount;
        }

        if(!$product->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something went wrong, please contact with admin'
            ]);
        }

        if($request->hasFile('files')){
            foreach($request->file('files') as $value) {
                $file = $value;
                $dir = '/'.$product->id;
                $full_path = public_path('storage/img/products').$dir;
                // localhost:8000/storage/img/products/$id/$image

                $file_ext = trim($file->getClientOriginalExtension());
                if($file_ext !== 'png' || $file_ext !== 'jpg' || $file_ext !== 'jpeg'){
                    return response()->json([
                        'status' => 400,
                        'ok' => false,
                        'msg' => 'You are trying to upload a file instead of a picture'
                    ]);
                }

                $file_name = $product->id.'-'.Str::random(10).'.'.$file_ext;

                // Checking if the directory exist
                if (!file_exists($full_path)) {
                    mkdir($full_path, 666, true);
                }

                // Save a thumbnail of the image (200x200)
                $final_file = $full_path.'/'.$file_name;
                $img = Image::make($file);
                $img->resize(200,200, function( $constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($final_file);

                $productGallery = new ProductGallery();
                $productGallery->file_name = $file_name;
                $productGallery->product_id = $product->id;
                $productGallery->save();
            }
        }

        $products = Product::with(['getPictures'])->orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product created successfully',
            'products' => $products
        ]);
    }

    public function update(Request $request, $id)
    {
        $product_updated = Product::with(['getPictures'])->where('id', $id)->first();

        if(!$product_updated){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Product no found'
            ]);
        }

        $pictures_pending = count($request->file('files'));
        $pictures_uploaded = count($product_updated->getPictures);
        if( $pictures_pending + $pictures_uploaded > 5){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'You cannot upload more than 5 pictures'
            ]);
        }

        $product_updated->name = $request->name;
        $product_updated->description = $request->description;
        $product_updated->price = $request->price;
        $product_updated->status = $request->status ?? 0;

        if(!$request->in_discount){
            $product_updated->in_discount = 0;
            $product_updated->discount = null;
        }else{
            $product_updated->in_discount = $request->in_discount;
            $product_updated->discount = $request->discount;
        }

        if(!$product_updated->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something went wrong, please contact with admin'
            ]);
        }

        if($request->hasFile('files')){
            foreach($request->file('files') as $value) {
                $file = $value;
                $dir = '/'.$product_updated->id;
                $full_path = public_path('storage/img/products').$dir;
                // localhost:8000/storage/img/products/$id/$image

                $file_ext = trim($file->getClientOriginalExtension());
                if($file_ext !== 'png' || $file_ext !== 'jpg' || $file_ext !== 'jpeg'){
                    return response()->json([
                        'status' => 400,
                        'ok' => false,
                        'msg' => 'Please, check your picture has a jpg or png format.'
                    ]);
                }

                $file_name = $product_updated->id.'-'.Str::random(10).'.'.$file_ext;

                // Checking if the directory exist
                if (!file_exists($full_path)) {
                    mkdir($full_path, 666, true);
                }

                // Save a thumbnail of the image (200x200)
                $final_file = $full_path.'/'.$file_name;
                $img = Image::make($file);
                $img->resize(200,200, function( $constraint) {
                    $constraint->aspectRatio();
                });
                $img->save($final_file);

                $productGallery = new ProductGallery();
                $productGallery->file_name = $file_name;
                $productGallery->product_id = $product_updated->id;
                $productGallery->save();
            }
        }

        $products = Product::with(['getPictures'])->orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product updated successfully',
            'products' => $products
        ]);
    }

    public function destroy ($id) {

        $product_deleted = Product::where('id', $id)->with(['getPictures'])->first();

        if(!$product_deleted){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something went wrong, please contact with admin'
            ]);
        }

        if(count($product_deleted->getPictures) > 0){
            $dir = '/'.$product_deleted->id;
            $full_path = public_path('storage/img/products').$dir;
            foreach($product_deleted->getPictures as $value){
                unlink($full_path.'/'.$value->file_name);
            }
            unlink($full_path);
        }

        $product_deleted->delete();
        $products = Product::with(['getPictures'])->orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product deleted successfully',
            'products' => $products
        ]);
    }

}
