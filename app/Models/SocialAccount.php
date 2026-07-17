<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    protected $fillable = [
        'company_id',
        'provider',
        'provider_id',
        'token',
        'page_id',
        'page_token',
        'instagram_account_id',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
