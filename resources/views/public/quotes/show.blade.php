<x-public-layout :title="'Quote ' . $quote->quote_number">
    <div style="background: white; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); overflow: hidden;">
        
        <!-- Header -->
        <div style="background: #0f172a; padding: 2rem; color: white; display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem;">
            <div>
                <h1 style="margin: 0; font-size: 2rem; font-weight: 700;">Proposal</h1>
                <div style="font-size: 1.1rem; opacity: 0.9;">{{ $quote->quote_number }}</div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 1.25rem; font-weight: 600;">{{ $quote->company->name ?? 'SolarCRM' }}</div>
            </div>
        </div>

        <!-- Details -->
        <div style="padding: 2rem; display: flex; flex-wrap: wrap; gap: 2rem; border-bottom: 1px solid #e2e8f0;">
            <div style="flex: 1; min-width: 250px;">
                <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; margin-bottom: 0.5rem; font-weight: 600;">Prepared For</div>
                <div style="font-weight: 600; font-size: 1.1rem;">{{ $quote->customer->name }}</div>
                <div style="color: #475569; margin-top: 0.25rem;">{{ $quote->customer->email }}</div>
                <div style="color: #475569;">{{ $quote->customer->phone }}</div>
                @if($quote->customer->address)
                    <div style="color: #475569; margin-top: 0.5rem;">{{ $quote->customer->address }}</div>
                @endif
            </div>
            <div style="flex: 1; min-width: 200px; text-align: right;">
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600; margin-right: 1rem;">Date Issued:</span>
                    <strong>{{ $quote->created_at->format('M d, Y') }}</strong>
                </div>
                @if($quote->valid_until)
                <div style="margin-bottom: 1rem;">
                    <span style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600; margin-right: 1rem;">Valid Until:</span>
                    <strong>{{ $quote->valid_until->format('M d, Y') }}</strong>
                </div>
                @endif
                <div>
                    <span style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; color: #64748b; font-weight: 600; margin-right: 1rem;">Total Amount:</span>
                    <strong style="font-size: 1.25rem; color: #059669;">₹{{ number_format($quote->total, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div style="padding: 2rem;">
            <h3 style="margin-top: 0; margin-bottom: 1rem; font-size: 1.25rem; color: #0f172a;">Project Scope</h3>
            
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left;">
                    <thead>
                        <tr style="border-bottom: 2px solid #e2e8f0;">
                            <th style="padding: 1rem 0; color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 600;">Description</th>
                            <th style="padding: 1rem; color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 600; text-align: right;">Qty</th>
                            <th style="padding: 1rem; color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 600; text-align: right;">Unit Price</th>
                            <th style="padding: 1rem 0 1rem 1rem; color: #64748b; font-size: 0.85rem; text-transform: uppercase; font-weight: 600; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quote->items as $item)
                        <tr style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 1rem 0;">
                                <div style="font-weight: 500; color: #0f172a;">{{ $item->product->name ?? 'Custom Item' }}</div>
                                <div style="font-size: 0.875rem; color: #64748b; margin-top: 0.25rem;">{{ $item->description }}</div>
                            </td>
                            <td style="padding: 1rem; text-align: right; color: #475569;">{{ number_format($item->qty, 2) }}</td>
                            <td style="padding: 1rem; text-align: right; color: #475569;">₹{{ number_format($item->unit_price, 2) }}</td>
                            <td style="padding: 1rem 0 1rem 1rem; text-align: right; font-weight: 500; color: #0f172a;">₹{{ number_format($item->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Totals -->
            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <div style="width: 100%; max-width: 350px;">
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Subtotal</span>
                        <span style="font-weight: 500;">₹{{ number_format($quote->subtotal, 2) }}</span>
                    </div>
                    @if($quote->discount > 0)
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Discount</span>
                        <span style="font-weight: 500; color: #ef4444;">-₹{{ number_format($quote->discount, 2) }}</span>
                    </div>
                    @endif
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9;">
                        <span style="color: #64748b;">Tax ({{ $quote->tax_rate }}%)</span>
                        <span style="font-weight: 500;">₹{{ number_format($quote->tax_amount, 2) }}</span>
                    </div>
                    
                    @if($quote->has_subsidy)
                    <div style="display: flex; justify-content: space-between; padding: 0.75rem 0; border-bottom: 1px solid #f1f5f9; background: #f0fdf4; margin: 0.5rem -1rem; padding: 0.75rem 1rem;">
                        <div>
                            <span style="color: #166534; font-weight: 600; display: block;">Estimated Subsidy</span>
                            <span style="color: #15803d; font-size: 0.8rem;">Central: ₹{{ number_format($quote->central_subsidy, 2) }} | State: ₹{{ number_format($quote->state_subsidy, 2) }}</span>
                        </div>
                        <span style="font-weight: 600; color: #166534;">-₹{{ number_format($quote->central_subsidy + $quote->state_subsidy, 2) }}</span>
                    </div>
                    @endif

                    <div style="display: flex; justify-content: space-between; padding: 1rem 0; border-top: 2px solid #e2e8f0; margin-top: 0.5rem;">
                        <span style="font-weight: 700; font-size: 1.1rem; color: #0f172a;">{{ $quote->has_subsidy ? 'Net Customer Cost' : 'Total' }}</span>
                        <span style="font-weight: 700; font-size: 1.25rem; color: #059669;">₹{{ number_format($quote->net_cost ?? $quote->total, 2) }}</span>
                    </div>
                </div>
            </div>
            
            @if($quote->notes)
            <div style="margin-top: 3rem; padding: 1.5rem; background: #f8fafc; border-radius: 8px; border-left: 4px solid #3b82f6;">
                <h4 style="margin-top: 0; margin-bottom: 0.5rem; font-size: 0.95rem; color: #0f172a;">Notes & Terms</h4>
                <div style="color: #475569; font-size: 0.9rem; white-space: pre-wrap;">{{ $quote->notes }}</div>
            </div>
            @endif
        </div>

        <!-- Acceptance Section -->
        <div style="background: #f8fafc; padding: 2.5rem; border-top: 1px solid #e2e8f0; text-align: center;">
            @if($quote->status === 'accepted')
                <div style="display: inline-flex; align-items: center; gap: 0.75rem; background: #dcfce7; color: #166534; padding: 1rem 2rem; border-radius: 50px; font-weight: 600; font-size: 1.1rem;">
                    <i class="bi bi-check-circle-fill" style="font-size: 1.5rem;"></i> Quote Accepted on {{ $quote->accepted_at->format('M d, Y h:i A') }}
                </div>
                <div style="margin-top: 1rem; color: #64748b; font-size: 0.9rem;">
                    Signed by: <strong>{{ $quote->signature_data }}</strong>
                </div>
            @else
                <h3 style="margin-top: 0; margin-bottom: 1.5rem; color: #0f172a;">Ready to proceed?</h3>
                <form action="{{ route('public.quotes.accept', $quote->public_token) }}" method="POST" style="max-width: 400px; margin: 0 auto; background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    @csrf
                    <div style="margin-bottom: 1.5rem; text-align: left;">
                        <label for="signature" style="display: block; margin-bottom: 0.5rem; font-weight: 600; color: #475569; font-size: 0.9rem;">Type your full name to digitally sign:</label>
                        <input type="text" name="signature" id="signature" required placeholder="John Doe" 
                            style="width: 100%; box-sizing: border-box; padding: 0.75rem 1rem; border: 1px solid #cbd5e1; border-radius: 6px; font-size: 1rem; font-family: inherit; outline: none; transition: border-color 0.2s;"
                            onfocus="this.style.borderColor='#3b82f6'" onblur="this.style.borderColor='#cbd5e1'">
                    </div>
                    <button type="submit" 
                        style="width: 100%; padding: 1rem; background: #10b981; color: white; border: none; border-radius: 6px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background 0.2s;"
                        onmouseover="this.style.background='#059669'" onmouseout="this.style.background='#10b981'">
                        <i class="bi bi-pen"></i> Accept & Approve Quote
                    </button>
                    <div style="margin-top: 1rem; font-size: 0.8rem; color: #94a3b8;">
                        By clicking accept, you agree to the terms of this proposal.
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-public-layout>
