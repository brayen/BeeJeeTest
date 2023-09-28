<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TodoScope implements Scope
{
    use HasFactory;

    public function apply(Builder $builder, Model $model)
    {
        $builder->where('deleted', 0);
    }
}
