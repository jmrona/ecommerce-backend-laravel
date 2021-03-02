<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
        $this->middleware('userStatus');
    }

    public function index()
    {
        $products = Product::orderBy('name', 'Asc')->get();
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
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something went wrong, please contact with admin'
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

        $products = Product::orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product created successfully',
            'products' => $products
        ]);
    }

    public function update(Request $request, $id)
    {
        $product_updated = Product::where('id', $id)->first();

        if(!$product_updated){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Product no found'
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

        $products = Product::orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product updated successfully',
            'products' => $products
        ]);
    }

    public function destroy ($id) {

        $product_deleted = Product::where('id', $id)->first();

        if(!$product_deleted){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something went wrong, please contact with admin'
                ]);
            }

        $product_deleted->delete();
        $products = Product::orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Product deleted successfully',
            'products' => $products
        ]);
    }
}
