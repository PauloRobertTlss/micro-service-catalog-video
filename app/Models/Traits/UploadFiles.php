<?php

namespace App\Models\Traits;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;

trait UploadFiles
{
    public $oldFiles = [];

    protected abstract function uploadPath(): string;

    public static function bootUploadFiles(){

        static::updating(function (Model $model) {
           $fieldsUpdated = array_keys($model->getDirty()); //campos atualizados

           $filesUpdated = array_intersect($fieldsUpdated, self::$fileFields);

           $filesFiltered = Arr::where($filesUpdated, function ($fileField) use ($model) {
              return $model->getOriginal($fileField); //old value
           });

           $model->oldFiles = array_map(function ($fileField) use ($model) {
               return $model->getOriginal($fileField);
           } , $filesFiltered);

        });
    }

    /**
     * @param UploadedFile[] $files
     */
    public function uploadFiles(array $files)
    {

        foreach ($files as $key => $file) {
            $this->uploadFile($file);
        }
    }

    public function uploadFile(UploadedFile $file)
    {
        $file->store($this->uploadPath());
    }

    public function deleteFiles(array $files)
    {
        foreach ($files as $key => $file) {
            $this->deleteFile($file);
        }
    }

    public function deleteOldFiles()
    {
        $this->deleteFiles($this->oldFiles);
    }

    /**
     * @param string|UploadedFile $file
     */
    public function deleteFile($file)
    {
        $fileName = $file instanceof UploadedFile ? $file->hashName() : $file;
        \Storage::delete("{$this->uploadPath()}/{$fileName}");

    }

    public static function extractFiles(array &$attributes = [])
    {
        $files = [];
        foreach (self::$fileFields as $key => $attribute)
        {
            if (isset($attributes[$attribute]) && $attributes[$attribute] instanceof UploadedFile) {
                $files[] = $attributes[$attribute];
                $attributes[$attribute] = $attributes[$attribute]->hashName();
            }
        }

        return $files;
    }

}