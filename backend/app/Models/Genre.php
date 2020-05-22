<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Genre extends Model
{
    use SoftDeletes, UuidTrait;

    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'id' => 'string',
        'is_active' => 'bool'
    ];

    protected $dates = [

        'deleted_at'
    ];

    public $incrementing = false;


    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

}
