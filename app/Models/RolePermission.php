<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = ['company_id', 'role', 'permission_id', 'granted'];
    protected $casts = ['granted' => 'boolean'];
}
