<x-app-layout title="Schedule Installation">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">New Installation</h1>
                <p style="color: var(--text-muted);">Schedule a new solar panel installation project.</p>
            </div>
            <a href="{{ route('installations.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to list</a>
        </div>

        <div class="card">
            <form action="{{ route('installations.store') }}" method="POST" x-data="installForm()">
                @csrf
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Customer</label>
                        <select name="customer_id" class="form-control" x-model="customerId" required>
                            <option value="">Select a customer...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Related Lead (Optional)</label>
                        <select name="lead_id" class="form-control" x-model="leadId">
                            <option value="">None</option>
                            <template x-for="l in filteredLeads" :key="l.id">
                                <option :value="l.id" x-text="l.title + ' (' + l.customer.name + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Related Quote (Optional)</label>
                        <select name="quote_id" class="form-control" x-model="quoteId">
                            <option value="">None</option>
                            <template x-for="q in filteredQuotes" :key="q.id">
                                <option :value="q.id" x-text="'#' + q.quote_number + ' (' + q.customer.name + ')'"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">System Size (kW)</label>
                        <input type="number" name="system_size_kw" class="form-control" value="{{ old('system_size_kw') }}" step="0.1" placeholder="e.g. 5.5">
                    </div>

                    <div>
                        <label class="form-label">Panel Brand</label>
                        <input type="text" name="panel_brand" class="form-control" value="{{ old('panel_brand') }}" placeholder="e.g. REC, QCells">
                    </div>

                    <div>
                        <label class="form-label">Panel Count</label>
                        <input type="number" name="panel_count" class="form-control" value="{{ old('panel_count') }}" placeholder="e.g. 14">
                    </div>

                    <div>
                        <label class="form-label">Inverter Brand</label>
                        <input type="text" name="inverter_brand" class="form-control" value="{{ old('inverter_brand') }}" placeholder="e.g. Enphase, SolarEdge">
                    </div>

                    <div>
                        <label class="form-label">Scheduled Date</label>
                        <input type="date" name="scheduled_date" class="form-control" value="{{ old('scheduled_date') }}" required>
                    </div>

                    <div>
                        <label class="form-label">Assigned Team Lead</label>
                        <select name="assigned_user_id" class="form-control">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Installation Status</label>
                        <select name="status" class="form-control" required>
                            <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Installation Notes / Special Instructions</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <a href="{{ route('installations.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Schedule Installation</button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script>
        function installForm() {
            return {
                customerId: '{{ old('customer_id') }}',
                leadId: '{{ old('lead_id') }}',
                quoteId: '{{ old('quote_id') }}',
                allLeads: @json($leads),
                allQuotes: @json($quotes),
                
                get filteredLeads() {
                    if (!this.customerId) return [];
                    return this.allLeads.filter(l => l.customer_id == this.customerId);
                },
                get filteredQuotes() {
                    if (!this.customerId) return [];
                    return this.allQuotes.filter(q => q.customer_id == this.customerId);
                },
                
                init() {
                    this.$watch('customerId', (val) => {
                        let currentLead = this.allLeads.find(l => l.id == this.leadId);
                        if (currentLead && currentLead.customer_id != val) {
                            this.leadId = '';
                        }
                        let currentQuote = this.allQuotes.find(q => q.id == this.quoteId);
                        if (currentQuote && currentQuote.customer_id != val) {
                            this.quoteId = '';
                        }
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
