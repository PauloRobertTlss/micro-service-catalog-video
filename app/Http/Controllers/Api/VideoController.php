<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\VideoResource;
use App\Models\Video;
use App\Rules\GenresHasCategoriesRules;
use Illuminate\Http\Request;

class VideoController extends BaseCrudController
{

    protected $rules;

    public function __construct()
    {
        $this->rules = [
            'title' => 'required|max:255',
            'description' => 'required',
            'year_launched' => 'required|date_format:Y',
            'opened' => 'boolean',
            'rating' => 'required|in:' . implode(',', Video::RATING_LIST),
            'duration' => 'required|integer',
            'categories_id' => 'required|array|exists:categories,id,deleted_at,NULL',
            'genres_id' => [
                'required',
                'array',
                'exists:genres,id,deleted_at,NULL'
            ],
            'thumb_file' => 'image|max:' . Video::THUMB_FILE_MAX_SIZE,
            'banner_file' => 'image|max:' . Video::BANNER_FILE_MAX_SIZE,
            'trailer_file' => 'mimetypes:video/mp4|max:' . Video::TRAILER_FILE_MAX_SIZE,
            'video_file' => 'mimetypes:video/mp4|max:'. Video::VIDEO_FILE_MAX_SIZE
        ];

    }

    protected function addRuleIfGenreHasCategories(Request $request)
    {
        $data = $request->get('categories_id');
        $categoriesId = is_array($data) ? $data : [];

        $this->rules['genres_id'][] = new GenresHasCategoriesRules($categoriesId);
    }


    public function store(Request $request)
    {

        $this->addRuleIfGenreHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesStore());

        /** @var Video $model */
        $model = $this->model()::create($validatedData);

        $model->refresh();
        $resource = $this->resource();
        return new $resource($model);

    }

    public function update(Request $request, $id)
    {

        $model = $this->findOrFail($id);
        $this->addRuleIfGenreHasCategories($request);
        $validatedData = $this->validate($request, $this->rulesUpdate());

        $model->update($validatedData);
        $model->refresh();
        $resource = $this->resource();
        return new $resource($model);


    }

    protected function model()
    {
        return Video::class;
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
      return VideoResource::class;
    }

    protected function resourceCollection()
    {
        return $this->resource();
    }
}
