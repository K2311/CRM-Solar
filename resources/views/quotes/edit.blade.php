<x-app-layout title="Edit Quote">
    <div style="max-width: 1000px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Quote #{{ $quote->quote_number }}</h1>
                <p style="color: var(--text-muted);">Update pricing and items for this proposal.</p>
            </div>
            <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <form action="{{ route('quotes.update', $quote) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div>
                        <label class="form-label">Customer</label>
                        <input type="text" class="form-control" value="{{ $quote->customer->name }}" disabled>
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control">
                            <option value="draft" {{ old('status', $quote->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="sent" {{ old('status', $quote->status) == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="accepted" {{ old('status', $quote->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                            <option value="declined" {{ old('status', $quote->status) == 'declined' ? 'selected' : '' }}>Declined</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Valid Until</label>
                        <input type="date" name="valid_until" class="form-control" value="{{ old('valid_until', $quote->valid_until ? $quote->valid_until->format('Y-m-d') : '') }}">
                    </div>
                </div>

                <!-- Simple Table for Items (Read-only in this quick fix, or minimal edit) -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1rem;">Quote Items</h3>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="width: 100px;">Qty</th>
                                <th style="width: 150px;">Unit Price</th>
                                <th style="text-align: right; width: 150px;">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($quote->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td style="text-align: right;">{{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Subsidy & Payment Milestones -->
                <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border); border-radius: 0.75rem; padding: 1.5rem; margin-bottom: 2rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                        <div>
                            <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; font-size: 0.9rem; font-weight: 600; margin-bottom: 1rem; color: white;">
                                <input type="checkbox" name="has_subsidy" value="1" {{ old('has_subsidy', $quote->has_subsidy) ? 'checked' : '' }}>
                                Apply PM Surya Ghar Subsidy
                            </label>
                            <p style="color: var(--text-muted); font-size: 0.75rem; line-height: 1.4;">Checks products for 'panel' category to determine system capacity in kW, and automatically calculates Central + State subsidy discounts.</p>
                        </div>
                        <div>
                            <h4 style="font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 0.75rem;">Payment Schedule %</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label" style="font-size: 0.7rem;">Advance %</label>
                                    <input type="number" name="advance_milestone_pct" class="form-control" value="{{ old('advance_milestone_pct', $quote->advance_milestone_pct) }}">
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label" style="font-size: 0.7rem;">Delivery %</label>
                                    <input type="number" name="delivery_milestone_pct" class="form-control" value="{{ old('delivery_milestone_pct', $quote->delivery_milestone_pct) }}">
                                </div>
                                <div class="form-group" style="margin: 0;">
                                    <label class="form-label" style="font-size: 0.7rem;">Commission %</label>
                                    <input type="number" name="commissioning_milestone_pct" class="form-control" value="{{ old('commissioning_milestone_pct', $quote->commissioning_milestone_pct) }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 300px; gap: 2rem; border-top: 1px solid var(--border); padding-top: 2rem;">
                    <div>
                        <label class="form-label">Closing Notes / Terms</label>
                        <textarea name="notes" class="form-control" rows="4">{{ old('notes', $quote->notes) }}</textarea>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 1rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Subtotal</span>
                            <span style="font-weight: 600;">{{ number_format($quote->items->sum('subtotal'), 2) }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Tax Rate (%)</span>
                            <input type="number" name="tax_rate" class="form-control" step="0.1" value="{{ old('tax_rate', $quote->tax_rate) }}" style="width: 80px; text-align: right;">
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="color: var(--text-muted);">Discount ({{ $currentCompany->currency_symbol }})</span>
                            <input type="number" name="discount" class="form-control" step="0.01" value="{{ old('discount', $quote->discount) }}" style="width: 120px; text-align: right;">
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; border-top: 2px solid var(--border); padding-top: 1rem; margin-top: 0.5rem;">
                            <span>Total</span>
                            <span style="color: var(--primary);">{{ number_format($quote->total, 2) }}</span>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; margin-top: 2rem;">
                    <a href="{{ route('quotes.show', $quote) }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Quote</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
