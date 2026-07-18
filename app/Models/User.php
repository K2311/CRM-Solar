<?php

namespace App\Models;

use App\Services\PermissionService;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Auth\Passwords\CanResetPassword;

class User extends Authenticatable implements CanResetPasswordContract
{
    use HasFactory, Notifiable, CanResetPassword;

    protected $fillable = [
        'name', 'email', 'password', 'company_id', 'role',
        'is_super_admin', 'phone', 'avatar', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_super_admin'    => 'boolean',
            'is_active'         => 'boolean',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────────────────
    public function company() { return $this->belongsTo(Company::class); }
    public function userPermissions() { return $this->hasMany(UserPermission::class); }
    public function activities() { return $this->hasMany(Activity::class); }

    // ── Role Helpers ───────────────────────────────────────────────────────────
    public function isOwner(): bool  { return $this->role === 'owner'; }
    public function isAdmin(): bool  { return in_array($this->role, ['owner', 'admin']); }
    public function isMember(): bool { return $this->role === 'member'; }

    // ── Permission Check ───────────────────────────────────────────────────────
    public function hasPermission(string $permission): bool
    {
        if ($this->is_super_admin || $this->isOwner()) return true;
        return app(PermissionService::class)->check($this, $permission);
    }

    public function canDo(string $permission): bool
    {
        return $this->hasPermission($permission);
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar) return asset('storage/' . $this->avatar);
        $name = urlencode($this->name);
        return "https://ui-avatars.com/api/?name={$name}&background=10b981&color=fff&size=80";
    }
}
