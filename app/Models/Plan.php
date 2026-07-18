<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    protected $fillable = [
        'slug', 'name', 'price', 'user_limit', 'lead_limit', 'whatsapp_templates', 'branding'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'user_limit' => 'integer',
        'lead_limit' => 'integer',
        'whatsapp_templates' => 'boolean',
        'branding' => 'boolean',
    ];
}
