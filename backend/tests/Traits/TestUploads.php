<?php
declare(strict_types=1);

namespace Tests\Traits;


use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\UploadedFile;

trait TestUploads
{

    protected function assertInvalidationFile
    (
        $field,
        $extension, $maxSize,
        string $rule,
        $ruleParams = []
    )
    {

        $routes = [
          [
              'method' => 'POST',
              'route' => $this->routeStore()
          ],[
              'method' => 'PUT',
              'route' => $this->routeUpdate()
          ],
        ];

        foreach ($routes as $route) {
            $file = UploadedFile::fake()->create("$field.1$extension"); //1

            $response = $this->json($route['method'], $route['route'], [$field => $file]);
            $this->assertInvalidationFields($response, [$field], $rule, $ruleParams);

            $file = UploadedFile::fake()->create("$field.$extension")->size($maxSize+1);

            $response = $this->json($route['method'], $route['route'], [$field => $file]);
            $this->assertInvalidationFields($response, [$field], 'max.file', ['max' => $maxSize]);

        }


    }

    protected function assertFilesExistsInStorage($model,array $files)
    {
        /**@var UploadedFile $model */

        foreach ($files  as $file)
        {
            \Storage::assertExists($model->relativeFilePath($file->hasName()));
        }

    }

}
