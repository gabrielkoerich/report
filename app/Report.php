<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

class Report extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'model', 'context'
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'context' => 'array',
    ];

    /**
     * Context mutator.
     */
    public function setContextAttribute($context): void
    {
        foreach ($context as $key => $value) {
            if (is_null($value) || $value === '') {
                unset($context[$key]);
            }

            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    if ((is_null($v) || $v === '')) {
                        unset($context[$key]);

                        continue;
                    }
                }
            }
        }

        $this->attributes['context'] = $this->asJson($context);
    }

    public function getData(): Collection
    {
        return (new $this->model)
            ->filterBy($this->context)
            ->get();
    }
}
