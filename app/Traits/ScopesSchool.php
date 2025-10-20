<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
trait ScopesSchool
{
    public static function bootScopesSchool():void
    {
        static::addGlobalScope('maktab', function (Builder $builder) {
            // Only apply for logged-in admins
            if (auth()->check() && auth()->user()->role->name === 'admin') {
                $builder->where('maktab_id', auth()->user()->maktab_id);
            }
        });
    }
}
