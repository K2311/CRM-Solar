<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Installation;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Quote;
use App\Models\ServiceTicket;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use \App\Traits\HasTenant;

    public function index()
    {
        $company = $this->tenantRequired();
        $user = auth()->user();

        // 1. Generic metrics (used across dashboards)
        $totalCustomers    = \App\Models\Customer::count();
        $activeLeads       = \App\Models\Lead::whereNotIn('stage', ['won', 'lost', 'junk'])->count();
        $totalRevenue      = \App\Models\Payment::sum('amount');
        $openTickets       = \App\Models\ServiceTicket::whereIn('status', ['open', 'in_progress'])->count();
        $wonLeads          = \App\Models\Lead::where('stage', 'won')->count();
        $totalLeads        = \App\Models\Lead::count();
        $recentInstalls    = \App\Models\Installation::where('status', 'completed')->count();

        // Lead by stage chart
        $leadsByStage = \App\Models\Lead::select('stage', DB::raw('count(*) as count'))
            ->groupBy('stage')->pluck('count', 'stage')->toArray();

        // Revenue chart
        $revenueMonthly = \App\Models\Payment::select(
                DB::raw("strftime('%Y-%m', payment_date) as month"),
                DB::raw('sum(amount) as total')
            )
            ->where('payment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')->toArray();

        // 2. Role-specific queries
        $stuckSubsidies = collect();
        $todayFollowups = collect();
        $mySurveys      = collect();
        $myInstallations = collect();
        $pendingInvoices = collect();

        if ($user->role === 'admin' || $user->role === 'owner') {
            // Stuck subsidies: registered / docs_submitted / approved for > 45 days
            $stuckSubsidies = \App\Models\Installation::whereIn('subsidy_status', ['registered', 'docs_submitted', 'approved'])
                ->where(function($q) {
                    $q->where('last_status_change_at', '<=', now()->subDays(45))
                      ->orWhereNull('last_status_change_at');
                })
                ->with('customer')
                ->get();
        }

        if ($user->role === 'sales' || $user->role === 'admin' || $user->role === 'owner') {
            // Today's/Past expected close follow-ups
            $todayFollowups = \App\Models\Lead::whereDate('expected_close_date', '<=', now()->toDateString())
                ->whereNotIn('stage', ['won', 'lost', 'junk'])
                ->with('customer')
                ->latest()
                ->get();
        }

        if ($user->role === 'technician' || $user->role === 'admin' || $user->role === 'owner') {
            // Assigned surveys
            $mySurveys = \App\Models\SiteSurvey::where('technician_id', $user->id)
                ->whereDate('survey_date', '>=', now()->toDateString())
                ->with('lead.customer')
                ->get();

            // Assigned active installations
            $myInstallations = \App\Models\Installation::where('assigned_user_id', $user->id)
                ->whereIn('status', ['scheduled', 'in_progress'])
                ->with('customer')
                ->get();
        }

        if ($user->role === 'accounts' || $user->role === 'admin' || $user->role === 'owner') {
            // Unpaid invoices
            $pendingInvoices = \App\Models\GstInvoice::where('status', 'unpaid')
                ->with('customer')
                ->get();
        }

        // Recent generic items
        $recentCustomers = \App\Models\Customer::latest()->limit(5)->get();
        $recentLeads     = \App\Models\Lead::with('customer')->latest()->limit(5)->get();
        $upcomingInstallations = \App\Models\Installation::where('status', 'scheduled')
            ->orderBy('scheduled_date')->limit(5)->get();

        return view('dashboard.index', compact(
            'company', 'totalCustomers', 'activeLeads', 'totalRevenue',
            'openTickets', 'wonLeads', 'totalLeads', 'recentInstalls',
            'leadsByStage', 'revenueMonthly', 'recentCustomers', 'recentLeads',
            'upcomingInstallations', 'stuckSubsidies', 'todayFollowups',
            'mySurveys', 'myInstallations', 'pendingInvoices'
        ));
    }
}
