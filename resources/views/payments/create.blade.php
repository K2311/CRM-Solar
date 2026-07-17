<x-app-layout title="Log Payment">
    <div style="max-width: 600px; margin: 0 auto;">
        <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
            <a href="{{ route('payments.index') }}" class="btn btn-outline" style="padding: 0.5rem;"><i class="bi bi-arrow-left"></i></a>
            <div>
                <h1 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 0.25rem;">Log Payment</h1>
                <p style="color: var(--text-muted);">Record a new payment transaction.</p>
            </div>
        </div>

        <div class="card" x-data="paymentForm({
            customers: {{ $customers->toJson() }},
            quotes: {{ $quotes->toJson() }},
            selectedCustomerId: '{{ old('customer_id', $selectedCustomer->id ?? '') }}',
            selectedQuoteId: '{{ old('quote_id', $selectedQuote->id ?? '') }}'
        })">
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Customer</label>
                    <select name="customer_id" class="form-control" x-model="customerId" @change="onCustomerChange" required>
                        <option value="">Select Customer</option>
                        <template x-for="customer in customers" :key="customer.id">
                            <option :value="customer.id" x-text="customer.name" :selected="customerId == customer.id"></option>
                        </template>
                    </select>
                </div>

                <div class="form-group" x-show="filteredQuotes.length > 0">
                    <label class="form-label">Related Quote (Optional)</label>
                    <select name="quote_id" class="form-control" x-model="quoteId">
                        <option value="">Select Quote</option>
                        <template x-for="quote in filteredQuotes" :key="quote.id">
                            <option :value="quote.id" x-text="'#' + quote.quote_number + ' - Total: ' + quote.total" :selected="quoteId == quote.id"></option>
                        </template>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Amount</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">{{ $currentCompany->currency_symbol }}</span>
                            <input type="number" step="0.01" name="amount" class="form-control" value="{{ old('amount', $selectedQuote->total ?? '') }}" style="padding-left: 2.5rem;" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', date('Y-m-d')) }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Payment Method</label>
                    <select name="method" class="form-control" required>
                        <option value="bank_transfer" {{ old('method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="cash" {{ old('method') == 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="cheque" {{ old('method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="card" {{ old('method') == 'card' ? 'selected' : '' }}>Card</option>
                        <option value="online" {{ old('method') == 'online' ? 'selected' : '' }}>Online</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Reference Number</label>
                    <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" placeholder="Transaction ID, Cheque #, etc.">
                </div>

                <div class="form-group">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="Additional details...">{{ old('notes') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save Payment</button>
                    <a href="{{ route('payments.index') }}" class="btn btn-outline" style="flex: 1; text-align: center;">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('paymentForm', (config) => ({
                customers: config.customers,
                quotes: config.quotes,
                customerId: config.selectedCustomerId,
                quoteId: config.selectedQuoteId,
                filteredQuotes: [],

                init() {
                    this.onCustomerChange();
                },

                onCustomerChange() {
                    if (!this.customerId) {
                        this.filteredQuotes = [];
                        this.quoteId = '';
                        return;
                    }
                    this.filteredQuotes = this.quotes.filter(q => q.customer_id == this.customerId);
                    
                    // If current quoteId doesn't belong to new customer, clear it
                    if (this.quoteId && !this.filteredQuotes.find(q => q.id == this.quoteId)) {
                        this.quoteId = '';
                    }
                }
            }));
        });
    </script>
</x-app-layout>
