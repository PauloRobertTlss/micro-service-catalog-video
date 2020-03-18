<?php

namespace Tests\Stubs\Controllers;


use App\Http\Controllers\Api\BaseCrudController;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BaseCrudController
{

    protected function model()
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        return [
            'name' => 'required',
            'is_active' => 'required',
            'description' => 'nullable'
        ];
    }

    protected function rulesUpdate()
    {
        return [
            'name' => 'required',
            'is_active' => 'required',
            'description' => 'nullable'
        ];
    }
}