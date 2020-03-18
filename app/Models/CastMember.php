<?php

namespace App\Models;

use App\Models\Traits\UuidTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CastMember extends Model
{
    use UuidTrait, SoftDeletes;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;

    protected $fillable = [
      'name',
      'type'
    ];

    protected $dates = [
        'deleted_at'
    ];

    public $incrementing = false;

}
