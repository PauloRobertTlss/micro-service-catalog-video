<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

abstract class BaseCrudController extends Controller
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'required|boolean'
    ];

    protected abstract function model();

    public function index(Request $request)
    {
        if ($request->has('only_trashed')) {
           return $this->model()::onlyTrashed()->get();
        }

        return $this->model()::all();
    }

//    public function store(Request $request)
//    {
//        $this->validate($request, $this->rules);
//        $category = $this->model()::create($request->all());
//        $category->refresh();
//        return $category;
//    }
//
//    public function show(Category $category)
//    {
//        return $category;
//    }
//
//    public function update(Request $request, Category $category)
//    {
//        $this->validate($request, $this->rules);
//        return $category->update($request->all());
//    }
//
//    public function destroy(Category $category)
//    {
//        $category->delete();
//        return response()->noContent(); //204
//    }
}
