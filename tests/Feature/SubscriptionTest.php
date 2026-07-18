<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use App\Models\Lead;
use Tests\TestCase;

class SubscriptionTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected $seed = true;

    /**
     * Test self-registered company starts on demo plan.
     */
    public function test_self_registration_assigns_demo_plan(): void
    {
        $response = $this->post('/register', [
            'company_name'          => 'Alpha Solar Ltd',
            'name'                  => 'Alpha Owner',
            'email'                 => 'alpha@solar.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $company = Company::where('name', 'Alpha Solar Ltd')->first();
        $this->assertNotNull($company);
        $this->assertEquals('demo', $company->plan);
        $this->assertEquals('active', $company->plan_status);
        $this->assertNotNull($company->plan_expires_at);
    }

    /**
     * Test user limit enforcement.
     */
    public function test_user_limit_enforced_on_demo_plan(): void
    {
        // Get seeded company owner
        $owner = User::where('email', 'owner@solartech-pvt-ltd.com')->first();
        $company = $owner->company;

        // Verify currently has 4 active users (seeded by default: owner, staff, sales, tech, accounts)
        // Wait, since we seeded 5 users by default, we've already exceeded the Demo limit!
        // So trying to invite a new user must fail!
        $response = $this->actingAs($owner)->post('/team/invite', [
            'name'  => 'New Team Member',
            'email' => 'newuser@solartech.com',
            'role'  => 'member',
        ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('Limit reached', session('error'));
    }

    /**
     * Test redirection when subscription plan is expired.
     */
    public function test_redirect_to_expired_when_plan_is_expired(): void
    {
        $owner = User::where('email', 'owner@solartech-pvt-ltd.com')->first();
        $company = $owner->company;

        // Mark company plan as expired
        $company->update([
            'plan_status' => 'expired'
        ]);

        // Attempting to hit dashboard
        $response = $this->actingAs($owner)->get('/');
        $response->assertRedirect('/billing/expired');

        // Can still access billing page and upgrade post
        $response = $this->actingAs($owner)->get('/billing');
        $response->assertStatus(200);
    }

    /**
     * Test super admin plans management CRUD.
     */
    public function test_super_admin_can_manage_pricing_plans(): void
    {
        $admin = User::where('email', 'admin@solar.com')->first();
        $this->assertNotNull($admin);

        // 1. Index
        $response = $this->actingAs($admin)->get('/admin/plans');
        $response->assertStatus(200);

        // 2. Create / Store
        $response = $this->actingAs($admin)->post('/admin/plans', [
            'slug'               => 'growth',
            'name'               => 'Growth Solar Plan',
            'price'              => 79.00,
            'user_limit'         => 15,
            'lead_limit'         => 200,
            'whatsapp_templates' => true,
        ]);
        $response->assertRedirect('/admin/plans');
        $this->assertDatabaseHas('plans', ['slug' => 'growth', 'price' => 79.00]);

        // 3. Edit / Update
        $plan = \App\Models\Plan::where('slug', 'growth')->first();
        $response = $this->actingAs($admin)->put("/admin/plans/{$plan->id}", [
            'name'               => 'Growth Plan Updated',
            'price'              => 89.00,
            'user_limit'         => 20,
            'lead_limit'         => 250,
            'branding'           => true,
        ]);
        $response->assertRedirect('/admin/plans');
        $this->assertDatabaseHas('plans', ['slug' => 'growth', 'price' => 89.00, 'name' => 'Growth Plan Updated']);

        // 4. Delete
        $response = $this->actingAs($admin)->delete("/admin/plans/{$plan->id}");
        $response->assertRedirect('/admin/plans');
        $this->assertDatabaseMissing('plans', ['slug' => 'growth']);
    }

    /**
     * Test preventing deletion of plans in use.
     */
    public function test_cannot_delete_active_plan_in_use(): void
    {
        $admin = User::where('email', 'admin@solar.com')->first();
        $demoPlan = \App\Models\Plan::where('slug', 'demo')->first();

        // demo plan is currently assigned to company, deleting should fail
        $response = $this->actingAs($admin)->delete("/admin/plans/{$demoPlan->id}");
        $response->assertRedirect('/admin/plans');
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('plans', ['slug' => 'demo']);
    }
}
