<?php

namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Http\Resources\GenreResource;
use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends BaseCrudController
{
    private $rules = [
        'name' => 'required|max:255',
        'is_active' => 'boolean',
        'description' => 'nullable',
        'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL'
    ];

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, $this->rulesStore());

        /** @var Video $model */
        $self = $this;

        $model = \DB::transaction(function () use ($self, $request, $validatedData) {
            $model = $this->model()::create($validatedData);
            $self->handleRelations($model, $request);
            return $model;
        });

        $model->refresh();
        $resource = $this->resource();
        return new $resource($model);
    }

    protected function handleRelations($genre, Request $request)
    {
        $genre->categories()->sync($request->get('categories_id'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, $this->rulesUpdate());
        $model = $this->findOrFail($id);
        $self = $this;

        $model = \DB::transaction(function () use ($self,$model, $request, $validatedData) {
            $model->update($validatedData);
            $self->handleRelations($model, $request);
            return $model;
        });

        $resource = $this->resource();
        return new $resource($model);

    }


    protected function model()
    {
        return Genre::class;
    }

    protected function rulesStore()
    {
        return $this->rules;
    }

    protected function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resource()
    {
        return GenreResource::class;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }
}
