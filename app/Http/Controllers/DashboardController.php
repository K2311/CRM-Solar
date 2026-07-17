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

        // KPIs
        $totalCustomers    = Customer::count();
        $activeLeads       = Lead::whereNotIn('stage', ['won', 'lost'])->count();
        $totalRevenue      = Payment::sum('amount');
        $openTickets       = ServiceTicket::whereIn('status', ['open', 'in_progress'])->count();
        $wonLeads          = Lead::where('stage', 'won')->count();
        $totalLeads        = Lead::count();
        $recentInstalls    = Installation::where('status', 'completed')->count();

        // Chart: Leads by stage
        $leadsByStage = Lead::select('stage', DB::raw('count(*) as count'))
            ->groupBy('stage')->pluck('count', 'stage')->toArray();

        // Chart: Revenue last 6 months
        $revenueMonthly = Payment::select(
                DB::raw("strftime('%Y-%m', payment_date) as month"),
                DB::raw('sum(amount) as total')
            )
            ->where('payment_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')->toArray();

        // Chart: Tickets by priority
        $ticketsByPriority = ServiceTicket::select('priority', DB::raw('count(*) as count'))
            ->groupBy('priority')->pluck('count', 'priority')->toArray();

        // Recent activities
        $recentCustomers = Customer::latest()->limit(5)->get();
        $recentLeads     = Lead::with('customer')->latest()->limit(5)->get();
        $upcomingInstallations = Installation::where('status', 'scheduled')
            ->orderBy('scheduled_date')->limit(5)->get();

        return view('dashboard.index', compact(
            'company', 'totalCustomers', 'activeLeads', 'totalRevenue',
            'openTickets', 'wonLeads', 'totalLeads', 'recentInstalls',
            'leadsByStage', 'revenueMonthly', 'ticketsByPriority',
            'recentCustomers', 'recentLeads', 'upcomingInstallations'
        ));
    }
}
