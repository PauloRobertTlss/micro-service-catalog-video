<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

abstract class BaseCrudController extends Controller
{
    protected $paginationSize = 15;

    protected abstract function model();
    protected abstract function rulesStore();
    protected abstract function rulesUpdate();
    protected abstract function resource();
    protected abstract function resourceCollection();

    public function index(Request $request)
    {

        if ($request->has('only_trashed')) {
           return $this->model()::onlyTrashed()->get();
        }

        $data = !$this->paginationSize ? $this->model()::all() : $this->model()::paginate($this->paginationSize);
        $resourceCollection = $this->resourceCollection();

        $refClass = new \ReflectionClass($this->resourceCollection());
        $hasSub = $refClass->isSubclassOf(ResourceCollection::class);

        return $hasSub ? new $resourceCollection($data) : $resourceCollection::collection($data);

    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());

        $model = $this->model()::create($validatedData);
        $model->refresh();

        $resource = $this->resource();
        return new $resource($model);
    }

    protected function findOrFail($id)
    {
        $model = $this->model();
        $keyName = (new $model)->getRouteKeyName();
        return $this->model()::where($keyName, $id)->firstOrFail();

    }

    public function show($id)
    {
        $model =  $this->findOrFail($id);
        $resource = $this->resource();
        return new $resource($model);
    }

    public function update(Request $request, $id)
    {
        $model = $this->findOrFail($id);
        $validatedDate = $this->validate($request, $this->rulesUpdate());
        $model->update($validatedDate);
        $model->refresh();
        $resource = $this->resource();
        return new $resource($model);
    }

    public function destroy($id)
    {
        $model = $this->findOrFail($id);
        $model->delete();
        return response()->noContent(); //204
    }
}
