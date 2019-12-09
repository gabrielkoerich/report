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
        'domain', 'user_id',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected $with = ['user'];

    /**
     * The user relation
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
