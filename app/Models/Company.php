<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Company extends Model
{
    protected $fillable = [
        'name', 'slug', 'logo', 'address', 'phone', 'email',
        'website', 'timezone', 'currency', 'is_active',
        'plan', 'plan_status', 'plan_expires_at', 'payment_method',
    ];

    protected $casts = [
        'plan_expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function owner(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(User::class)->where('role', 'owner');
    }

    public function getCurrencySymbolAttribute(): string
    {
        return match ($this->currency) {
            'EUR' => '€',
            'INR' => '₹',
            'GBP' => '£',
            'AUD' => 'A$',
            default => '$',
        };
    }

    public function settings(): HasMany
    {
        return $this->hasMany(CompanySetting::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function setting(string $key, mixed $default = null): mixed
    {
        $setting = $this->settings()->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public function setSetting(string $key, mixed $value): void
    {
        $this->settings()->updateOrCreate(['key' => $key], ['value' => $value]);
    }

    // Subscription limits helper methods
    public function getPlanDetailsAttribute(): array
    {
        $plan = \App\Models\Plan::where('slug', $this->plan)->first();
        if ($plan) {
            return [
                'name' => $plan->name,
                'price' => $plan->price,
                'user_limit' => $plan->user_limit,
                'lead_limit' => $plan->lead_limit,
                'features' => [
                    'whatsapp_templates' => (bool)$plan->whatsapp_templates,
                    'branding' => (bool)$plan->branding,
                ]
            ];
        }

        $plans = config('plans', []);
        return $plans[$this->plan] ?? $plans['demo'];
    }

    public function getPlanNameAttribute(): string
    {
        return $this->plan_details['name'] ?? 'Demo Trial';
    }

    public function hasReachedUserLimit(): bool
    {
        $limit = $this->plan_details['user_limit'] ?? 3;
        return $this->users()->count() >= $limit;
    }

    public function hasReachedLeadLimit(): bool
    {
        $limit = $this->plan_details['lead_limit'] ?? 15;
        return $this->leads()->count() >= $limit;
    }

    public function isPlanExpired(): bool
    {
        if ($this->plan_status === 'expired' || $this->plan_status === 'suspended') {
            return true;
        }

        if ($this->plan_expires_at && now()->greaterThan($this->plan_expires_at)) {
            return true;
        }

        return false;
    }

    public function hasFeatureEnabled(string $feature): bool
    {
        return (bool) ($this->plan_details['features'][$feature] ?? false);
    }
}
