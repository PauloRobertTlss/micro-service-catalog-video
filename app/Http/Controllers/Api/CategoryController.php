<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'required|boolean'
    ];

    public function index(Request $request)
    {
        if ($request->has('only_trashed')) {
           return Category::onlyTrashed()->get();
        }

        return Category::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->rules);
        $category = Category::create($request->all());
        $category->refresh();
        return $category;
    }

    public function show(Category $category)
    {
        return $category;
    }

    public function update(Request $request, Category $category)
    {
        $this->validate($request, $this->rules);
        return $category->update($request->all());
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent(); //204
    }
}
