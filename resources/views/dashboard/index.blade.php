<x-app-layout title="Dashboard">
    <div class="dashboard-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
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
                    borderColor: '#0ea5e9',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: 'rgba(14, 165, 233, 0.1)'
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
