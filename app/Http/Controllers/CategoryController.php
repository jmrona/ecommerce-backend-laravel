<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
        $this->middleware('userStatus');
    }

    public function index()
    {
        // $categories = Category::orderBy('name', 'Asc')->get();
        $categories = $categories = DB::select('
            SELECT cat.*, c.name AS main_category_name
            FROM categories cat
            LEFT JOIN (SELECT id, `name` FROM `categories`) c
            ON cat.main_category = c.id
        ');
        if(!$categories){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'No found categories'
            ]);
        }

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Categories loaded',
            'categories' => $categories
        ]);

    }

    public function store(Request $request)
    {

        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'The name field is required'
            ]);
        }

        $category = new Category();
        $category->name = e($request->name);
        $category->description = e($request->description ?? null);


        if($request->main_category != '0'){
            $cat = Category::find($request->main_category);
            if($cat->main_category !== null){
                return response()->json([
                    'status' => 400,
                    'ok' => false,
                    'msg' => 'This category has a main category already'
                ]);
            }
        }

        if($request->main_category == 0){
            $category->main_category = null;
        }else{
            $category->main_category = $request->main_category;
        }

        if(!$category->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'No found categories'
            ]);
        }

        $categories = DB::select('
            SELECT cat.*, c.name AS main_category_name
            FROM categories cat
            LEFT JOIN (SELECT id, `name` FROM `categories`) c
            ON cat.main_category = c.id
        ');
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Categories created successfully',
            'categories' => $categories
        ]);

    }

    public function update(Request $request, $id)
    {

        $rules = [
            'name' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'The name field is required'
            ]);
        }

        $category = Category::where('id', $id)->first();
        $category->name = e($request->name);
        $category->description = e($request->description ?? null);

        if($request->main_category != '0'){
            $cat = Category::find($request->main_category);
            if($cat->main_category !== null){
                return response()->json([
                    'status' => 400,
                    'ok' => false,
                    'msg' => 'This category has a main category already'
                ]);
            }
        }

        if($request->main_category == 0){
            $category->main_category = null;
        }else{
            $category->main_category = $request->main_category;
        }

        if(!$category->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'No found categories'
            ]);
        }

        $categories = DB::select('
            SELECT cat.*, c.name AS main_category_name
            FROM categories cat
            LEFT JOIN (SELECT id, `name` FROM `categories`) c
            ON cat.main_category = c.id
        ');
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Categories updated successfully',
            'categories' => $categories
        ]);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if(!$category){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Category no found'
            ]);
        }

        // Deleting subcategories
        if(Category::where('main_category', $id)->get()){
            Category::where('main_category', $id)->delete();
        }

        if(!$category->delete()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something was wrong! Please, contact to the admin'
            ]);
        }

        $categories = DB::select('
            SELECT cat.*, c.name AS main_category_name
            FROM categories cat
            LEFT JOIN (SELECT id, `name` FROM `categories`) c
            ON cat.main_category = c.id
        ');

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Category deleted successfully',
            'categories' => $categories
        ]);

    }
}
