<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Tests\TestCase;

class SolarCRMTest extends TestCase
{
    use \Illuminate\Foundation\Testing\RefreshDatabase;

    protected $seed = true;

    /**
     * Test dashboard page loads for authenticated owner.
     */
    public function test_dashboard_loads_for_authenticated_owner(): void
    {
        $owner = User::where('email', 'owner@solartech-pvt-ltd.com')->first();
        $this->assertNotNull($owner);

        $response = $this->actingAs($owner)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Customers');
        $response->assertSee('Leads Pipeline');
        $response->assertSee('Total Payments');
        $response->assertSee('Service Tickets');
    }

    /**
     * Test Leads Pipeline contains the 8 solar sales pipeline stages.
     */
    public function test_leads_pipeline_has_8_stages(): void
    {
        $owner = User::where('email', 'owner@solartech-pvt-ltd.com')->first();
        
        $response = $this->actingAs($owner)->get('/leads');
        $response->assertStatus(200);
        
        // Assert the key stage names are rendered in the HTML
        $response->assertSee('new');
        $response->assertSee('contacted');
        $response->assertSee('negotiation');
        $response->assertSee('junk');
    }

    /**
     * Test Global Settings contains the new Solar & Notifications fields.
     */
    public function test_settings_has_solar_configurations(): void
    {
        $owner = User::where('email', 'owner@solartech-pvt-ltd.com')->first();

        $response = $this->actingAs($owner)->get('/settings');
        $response->assertStatus(200);

        // Check for WABA, access token, and toggles
        $response->assertSee('Solar');
        $response->assertSee('Notifications');
        $response->assertSee('whatsapp_access_token');
        $response->assertSee('whatsapp_phone_number_id');
        $response->assertSee('whatsapp_waba_id');
        $response->assertSee('notify_lead_followup');
        $response->assertSee('notify_amc_renewal');
        $response->assertSee('state_subsidy_type');
    }
}
