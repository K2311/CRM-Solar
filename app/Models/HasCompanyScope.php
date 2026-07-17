<?php

namespace App\Models;

use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

trait HasCompanyScope
{
    protected static function bootHasCompanyScope(): void
    {
        static::addGlobalScope(new CompanyScope());

        static::creating(function ($model) {
            if (app()->has('current_company_id') && empty($model->company_id)) {
                $model->company_id = app('current_company_id');
            }
        });
    }
}
