<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = ['name', 'module', 'action', 'description'];

    public static function allModules(): array
    {
        return [
            'customers'     => ['view', 'create', 'edit', 'delete', 'export'],
            'leads'         => ['view', 'create', 'edit', 'delete', 'assign'],
            'quotes'        => ['view', 'create', 'edit', 'delete', 'send'],
            'installations' => ['view', 'create', 'edit', 'delete'],
            'tickets'       => ['view', 'create', 'edit', 'delete', 'assign'],
            'products'      => ['view', 'create', 'edit', 'delete'],
            'payments'      => ['view', 'create', 'edit', 'delete'],
            'marketing'     => ['view', 'create', 'send', 'delete'],
            'reports'       => ['view', 'export'],
            'settings'      => ['view', 'edit'],
            'team'          => ['view', 'invite', 'edit_roles', 'remove'],
        ];
    }
}
