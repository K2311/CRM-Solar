<x-app-layout title="Dashboard">
    <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- KPI Cards -->
        <div class="card glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-people"></i>
                </div>
                <span class="badge badge-info">Total</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ number_format($totalCustomers) }}</div>
            <div style="color: var(--text-muted); font-size: 0.875rem;">Customers</div>
        </div>

        <div class="card glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(245, 158, 11, 0.1); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-funnel"></i>
                </div>
                <span class="badge badge-warning">Active</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ number_format($activeLeads) }}</div>
            <div style="color: var(--text-muted); font-size: 0.875rem;">Leads Pipeline</div>
        </div>

        <div class="card glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(16, 185, 129, 0.1); color: #10b981; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <span class="badge badge-success">Revenue</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ number_format($totalRevenue, 2) }}</div>
            <div style="color: var(--text-muted); font-size: 0.875rem;">Total Payments</div>
        </div>

        <div class="card glass-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                    <i class="bi bi-headset"></i>
                </div>
                <span class="badge badge-danger">Open</span>
            </div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $openTickets }}</div>
            <div style="color: var(--text-muted); font-size: 0.875rem;">Service Tickets</div>
        </div>
    </div>

    <!-- Admin/Owner Alerts: Stuck Subsidies -->
    @if((auth()->user()->role === 'admin' || auth()->user()->role === 'owner') && $stuckSubsidies->isNotEmpty())
    <div class="card animate-fade" style="border: 1px solid rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05); margin-bottom: 2rem; display: flex; align-items: center; justify-content: space-between; padding: 1.25rem 2rem; border-radius: 1.25rem;">
        <div style="display: flex; align-items: center; gap: 1.25rem;">
            <div style="width: 44px; height: 44px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.25rem;">
                <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div>
                <h4 style="font-weight: 800; font-size: 0.95rem; color: #ef4444; margin: 0 0 0.15rem 0;">Stuck PM Surya Ghar Subsidy Claims</h4>
                <p style="color: var(--text-muted); font-size: 0.8rem; margin: 0;">{{ $stuckSubsidies->count() }} installations have pending subsidies delayed for over 45 days.</p>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
            @foreach($stuckSubsidies->take(3) as $sub)
                <a href="{{ route('installations.show', $sub) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">{{ $sub->customer->name }} ({{ $sub->subsidy_status }})</a>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Sales Alerts: Today's Followups -->
    @if(auth()->user()->role === 'sales' && $todayFollowups->isNotEmpty())
    <div class="card animate-fade" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--primary);"><i class="bi bi-clock-history"></i> Today's Follow-up Actions</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            @foreach($todayFollowups->take(4) as $lf)
            <div class="card glass-card" style="padding: 1rem; border-color: rgba(14, 165, 233, 0.2); margin: 0;">
                <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.25rem; color: white;">{{ $lf->customer->name }}</div>
                <p style="font-size: 0.75rem; color: var(--text-muted); margin: 0 0 0.75rem 0; text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">Project: {{ $lf->title }}</p>
                <a href="{{ route('leads.show', $lf) }}" class="btn btn-primary" style="padding: 0.35rem; font-size: 0.75rem; justify-content: center; width: 100%;">View Lead details</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Field Technician: Active Surveys & Milestones -->
    @if(auth()->user()->role === 'technician' && ($mySurveys->isNotEmpty() || $myInstallations->isNotEmpty()))
    <div class="animate-fade" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        @if($mySurveys->isNotEmpty())
        <div class="card" style="margin: 0;">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: #f59e0b;"><i class="bi bi-geo-alt-fill"></i> Today's Site Surveys</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($mySurveys->take(3) as $srv)
                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <div>
                        <div style="font-weight: 600; font-size: 0.85rem; color: white;">{{ $srv->lead->customer->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Scheduled Date: {{ $srv->survey_date->format('M d, Y') }}</div>
                    </div>
                    <a href="{{ route('surveys.show', $srv) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">Details</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($myInstallations->isNotEmpty())
        <div class="card" style="margin: 0;">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: var(--primary);"><i class="bi bi-tools"></i> Active Solar Projects</h3>
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                @foreach($myInstallations->take(3) as $inst)
                <div style="display: flex; justify-content: space-between; align-items: center; background: rgba(255,255,255,0.02); padding: 0.75rem 1rem; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <div>
                        <div style="font-weight: 600; font-size: 0.85rem; color: white;">{{ $inst->customer->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Size: {{ $inst->system_size_kw }} kW | Brand: {{ $inst->panel_brand }}</div>
                    </div>
                    <a href="{{ route('installations.show', $inst) }}" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">Milestones</a>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Accounts: Unpaid Invoices and Pending Collections -->
    @if(auth()->user()->role === 'accounts' && $pendingInvoices->isNotEmpty())
    <div class="card animate-fade" style="margin-bottom: 2rem;">
        <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem; color: #ef4444;"><i class="bi bi-cash-stack"></i> Pending Collections & Unpaid GST Invoices</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem;">
            @foreach($pendingInvoices->take(4) as $inv)
            <div class="card glass-card" style="padding: 1rem; border-color: rgba(239, 68, 68, 0.2); margin: 0;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                    <span style="font-weight: 800; font-size: 0.8rem; color: white;">{{ $inv->invoice_number }}</span>
                    <span class="badge" style="background: rgba(239,68,68,0.1); color: #ef4444; border: none; font-size: 0.65rem; padding: 0.15rem 0.4rem;">UNPAID</span>
                </div>
                <div style="font-weight: 700; font-size: 0.85rem; margin-bottom: 0.25rem; color: white;">{{ $inv->customer->name }}</div>
                <div style="font-size: 0.95rem; font-weight: 800; color: var(--primary); margin-bottom: 0.75rem;">{{ $currentCompany->currency_symbol }}{{ number_format($inv->grand_total, 2) }}</div>
                <a href="{{ route('invoices.show', $inv) }}" class="btn btn-outline" style="padding: 0.35rem; font-size: 0.75rem; justify-content: center; width: 100%;">View Invoice</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Charts Row -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700;">Leads by Stage</h3>
                <div style="font-size: 0.75rem; color: var(--text-muted);">Current Pipeline Acquisition</div>
            </div>
            <div style="height: 300px;">
                <canvas id="leadsChart"></canvas>
            </div>
        </div>
        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                <h3 style="font-size: 1rem; font-weight: 700;">Revenue Trend</h3>
                <span class="badge badge-success">+12%</span>
            </div>
            <div style="height: 300px;">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Bottom Lists -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Upcoming Installations</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($upcomingInstallations as $install)
                    <tr>
                        <td style="font-weight: 600;">{{ $install->customer->name }}</td>
                        <td style="color: var(--text-muted);">{{ $install->scheduled_date->format('M d, Y') }}</td>
                        <td><span class="badge badge-info">{{ $install->status }}</span></td>
                    </tr>
                    @endforeach
                    @if($upcomingInstallations->isEmpty())
                    <tr><td colspan="3" style="text-align: center; color: var(--text-muted);">No upcoming installs</td></tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="card">
            <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem;">Recent Leads</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Lead</th>
                        <th>Value</th>
                        <th>Stage</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentLeads as $lead)
                    <tr>
                        <td style="font-weight: 600;">{{ $lead->customer->name }}</td>
                        <td style="color: var(--primary);">{{ number_format($lead->value, 2) }}</td>
                        <td><span class="badge" style="background: {{ \App\Models\Lead::stageColors()[$lead->stage] ?? '#eee' }}; color: white; border: none;">{{ $lead->stage }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const leadsCtx = document.getElementById('leadsChart').getContext('2d');
        new Chart(leadsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(array_keys($leadsByStage)) !!},
                datasets: [{
                    label: 'Leads',
                    data: {!! json_encode(array_values($leadsByStage)) !!},
                    backgroundColor: ['#6366f1', '#3b82f6', '#f59e0b', '#8b5cf6', '#10b981', '#ef4444'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#94a3b8' } },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });

        const revCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($revenueMonthly)) !!},
                datasets: [{
                    data: {!! json_encode(array_values($revenueMonthly)) !!},
                    borderColor: '#10b981',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { display: false },
                    x: { grid: { display: false }, ticks: { color: '#94a3b8' } }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
