<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    protected $fillable = ['company_id', 'user_id', 'permission_id', 'type'];
}
