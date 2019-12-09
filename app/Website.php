<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use Filter\Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain',
    ];
}
