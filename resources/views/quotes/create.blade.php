<x-app-layout title="Create New Quote">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Create Quote</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Generate a professional proposal for {{ $lead ? $lead->customer->name : 'customer' }}</p>
        </div>
        <a href="{{ route('quotes.index') }}" class="btn btn-outline">Cancel</a>
    </div>

    <style>
        .line-items-table th {
            padding: 0.75rem 0.5rem !important;
        }
        .line-items-table td {
            padding: 0.75rem 0.5rem !important;
            vertical-align: top !important;
        }
        .line-items-table .form-control {
            padding: 0.5rem 0.75rem !important;
            height: auto !important;
            font-size: 0.875rem !important;
        }
        .product-field-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
    </style>

    <form action="{{ route('quotes.store') }}" method="POST" x-data="quoteForm()">
        @csrf
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <!-- Customer Selection -->
                <div class="card">
                    <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; text-transform: uppercase;">Proposal Recipient</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">Customer</label>
                            <select name="customer_id" class="form-control" required>
                                <option value="">Select Customer</option>
                                @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id', $lead ? $lead->customer_id : '') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Related Lead (Optional)</label>
                            <select name="lead_id" class="form-control">
                                <option value="">No Direct Lead</option>
                                @foreach($leads as $l)
                                <option value="{{ $l->id }}" {{ old('lead_id', $lead ? $lead->id : '') == $l->id ? 'selected' : '' }}>{{ $l->title }} ({{ $l->customer->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="card">
                    <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; text-transform: uppercase;">Line Items</h4>
                    <table class="data-table line-items-table">
                        <thead>
                            <tr>
                                <th style="width: 45%;">Product/Description</th>
                                <th style="width: 12%;">Qty</th>
                                <th style="width: 18%;">Unit Price</th>
                                <th style="width: 15%;">Subtotal</th>
                                <th style="text-align: right; width: 10%;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in items" :key="index">
                                <tr>
                                    <td>
                                        <div class="product-field-group">
                                            <select :name="'items['+index+'][product_id]'" class="form-control" style="font-size: 0.8rem;" x-model="item.product_id" @change="setProduct(index, item.product_id)">
                                                <option value="">Custom Item</option>
                                                @foreach($products as $p)
                                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="text" :name="'items['+index+'][description]'" class="form-control" x-model="item.description" placeholder="Description" required>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" :name="'items['+index+'][qty]'" class="form-control" x-model.number="item.qty" style="max-width: 80px;">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" :name="'items['+index+'][unit_price]'" class="form-control" x-model.number="item.unit_price" style="max-width: 120px;">
                                    </td>
                                    <td style="font-weight: 700; vertical-align: middle; padding-top: 1.2rem !important;">
                                        {{ $currentCompany->currency_symbol }}<span x-text="(item.qty * item.unit_price).toFixed(2)"></span>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle; padding-top: 1.2rem !important;">
                                        <button type="button" class="btn" style="color: #ef4444;" @click="removeItem(index)" x-show="items.length > 1"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <button type="button" class="btn btn-outline" style="margin-top: 1rem; width: 100%; border-style: dashed;" @click="addItem()">
                        <i class="bi bi-plus"></i> Add Another Item
                    </button>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                <div class="card glass-card">
                    <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; text-transform: uppercase;">Quote Totals</h4>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Subtotal</span>
                            <span style="font-weight: 600;">{{ $currentCompany->currency_symbol }}<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 0.75rem;">Tax Rate (%)</label>
                            <input type="number" name="tax_rate" class="form-control" x-model.number="taxRate" value="{{ old('tax_rate', 0) }}">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 0.75rem;">Discount ({{ $currentCompany->currency_symbol }})</label>
                            <input type="number" name="discount" class="form-control" x-model.number="discount" value="{{ old('discount', 0) }}">
                        </div>
                        <hr style="border: none; border-top: 1px solid var(--border);">
                        <div style="display: flex; justify-content: space-between; font-size: 1.25rem;">
                            <span style="font-weight: 800;">TOTAL</span>
                            <span style="font-weight: 800; color: var(--primary);">{{ $currentCompany->currency_symbol }}<span x-text="total.toFixed(2)"></span></span>
                        </div>
                        <div x-show="hasSubsidy" style="display: flex; justify-content: space-between; font-size: 1.25rem; border-top: 1px dashed var(--border); padding-top: 0.5rem; margin-top: 0.5rem;">
                            <span style="font-weight: 800; color: #10b981;">NET COST</span>
                            <span style="font-weight: 800; color: #10b981;">{{ $currentCompany->currency_symbol }}<span x-text="netCost.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Subsidy & Payment Milestones -->
                <div class="card glass-card">
                    <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1.25rem; text-transform: uppercase;">Solar Subsidy & Milestones</h4>
                    
                    <div class="form-group">
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.875rem; font-weight: 600;">
                            <input type="checkbox" name="has_subsidy" x-model="hasSubsidy">
                            Apply PM Surya Ghar Subsidy
                        </label>
                    </div>

                    <div x-show="hasSubsidy" class="animate-fade" style="margin-bottom: 1.5rem; background: rgba(0,0,0,0.2); padding: 0.75rem 1rem; border-radius: 0.5rem; font-size: 0.8rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                            <span>Est. System Capacity:</span>
                            <span style="font-weight: 700;" x-text="estimatedCapacity.toFixed(2) + ' kW'"></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.4rem;">
                            <span>Central Subsidy:</span>
                            <span style="font-weight: 700; color: #10b981;" x-text="'- ' + '{{ $currentCompany->currency_symbol }}' + estimatedCentralSubsidy.toFixed(2)"></span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>State Subsidy:</span>
                            <span style="font-weight: 700; color: #10b981;" x-text="'- ' + '{{ $currentCompany->currency_symbol }}' + estimatedStateSubsidy.toFixed(2)"></span>
                        </div>
                    </div>

                    <h5 style="font-size: 0.75rem; font-weight: 800; color: var(--text-muted); margin-bottom: 0.75rem; text-transform: uppercase;">Payment Schedule %</h5>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 0.65rem;">Advance</label>
                            <input type="number" name="advance_milestone_pct" class="form-control" value="10">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 0.65rem;">Delivery</label>
                            <input type="number" name="delivery_milestone_pct" class="form-control" value="70">
                        </div>
                        <div class="form-group" style="margin: 0;">
                            <label class="form-label" style="font-size: 0.65rem;">Commission</label>
                            <input type="number" name="commissioning_milestone_pct" class="form-control" value="20">
                        </div>
                    </div>
                </div>

                <div class="card">
                    <h4 style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 1rem; text-transform: uppercase;">Details</h4>
                    <div class="form-group">
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', date('Y-m-d', strtotime('+30 days'))) }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="Terms and conditions...">{{ old('notes') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                        Save & Generate Quote
                    </button>
                </div>
            </div>
        </div>
    </form>
</x-app-layout>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('quoteForm', () => ({
        items: [{ product_id: '', description: '', qty: 1, unit_price: 0 }],
        taxRate: {{ old('tax_rate', 0) }},
        discount: {{ old('discount', 0) }},
        hasSubsidy: false,
        products: {!! $products->mapWithKeys(fn($p) => [$p->id => $p])->toJson() !!},

        get subtotal() {
            return this.items.reduce((acc, item) => acc + (item.qty * item.unit_price), 0);
        },
        get tax() {
            return this.subtotal * (this.taxRate / 100);
        },
        get total() {
            return this.subtotal + this.tax - this.discount;
        },
        get estimatedCapacity() {
            let capacity = 0;
            this.items.forEach(item => {
                if (item.product_id && this.products[item.product_id] && this.products[item.product_id].category === 'panel') {
                    capacity += ((this.products[item.product_id].capacity_watts || 0) * item.qty) / 1000.0;
                }
            });
            return capacity;
        },
        get estimatedCentralSubsidy() {
            if (!this.hasSubsidy || this.estimatedCapacity <= 0) return 0;
            let cap = this.estimatedCapacity;
            if (cap <= 2) {
                return cap * 30000;
            } else {
                let base = 60000;
                let extra = Math.min(1.0, cap - 2.0) * 18000;
                return base + extra;
            }
        },
        get estimatedStateSubsidy() {
            if (!this.hasSubsidy || this.estimatedCapacity <= 0) return 0;
            let cap = this.estimatedCapacity;
            let stateType = '{{ $currentCompany->setting('state_subsidy_type', 'flat') }}';
            let stateRate = {{ floatval($currentCompany->setting('state_subsidy_rate', 0)) }};
            if (stateType === 'per_kw') {
                return cap * stateRate;
            } else {
                return stateRate;
            }
        },
        get netCost() {
            return Math.max(0, this.total - this.estimatedCentralSubsidy - this.estimatedStateSubsidy);
        },
        addItem() {
            this.items.push({ product_id: '', description: '', qty: 1, unit_price: 0 });
        },
        removeItem(index) {
            this.items.splice(index, 1);
        },
        setProduct(index, pid) {
            if (this.products[pid]) {
                this.items[index].description = this.products[pid].name;
                this.items[index].unit_price = this.products[pid].unit_price;
            } else {
                this.items[index].description = '';
                this.items[index].unit_price = 0;
            }
        }
    }));
});
</script>
