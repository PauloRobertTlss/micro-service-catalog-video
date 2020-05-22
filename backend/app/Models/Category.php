<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Category extends Model
{

    use SoftDeletes, UuidTrait;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'boolean'
    ];

    protected $dates = [
      'deleted_at'
    ];

    public $incrementing = false;


    public function videos()
    {
        return $this->belongsToMany(Video::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }


}
