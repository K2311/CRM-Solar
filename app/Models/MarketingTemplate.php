<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingTemplate extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'name', 'channel', 'subject', 'body', 'variables', 'is_active'];
    protected $casts = ['variables' => 'array', 'is_active' => 'boolean'];

    public function company() { return $this->belongsTo(Company::class); }
}
