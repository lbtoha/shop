<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    public $timestamps = false;

    public $table = 'seos';

    protected $guarded = ['id'];

    public function model(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo('model', 'model_type', 'model_id', 'id');
    }
}
