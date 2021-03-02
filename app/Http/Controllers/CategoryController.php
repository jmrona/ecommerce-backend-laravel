<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('isAdmin');
        $this->middleware('userStatus');
    }
    public function index()
    {
        $categories = Category::orderBy('name', 'Asc')->get();
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
}
