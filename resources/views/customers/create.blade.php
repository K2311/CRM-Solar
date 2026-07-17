<x-app-layout title="New Customer">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Add Customer</h1>
                <p style="color: var(--text-muted);">Create a new customer profile in your database.</p>
            </div>
            <a href="{{ route('customers.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to list</a>
        </div>

        <div class="card">
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                
                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--primary);">Basic Information</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. John Doe" required>
                    </div>

                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="john@example.com">
                    </div>

                    <div>
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000">
                    </div>

                    <div>
                        <label class="form-label">Customer Status</label>
                        <select name="status" class="form-control" required>
                            <option value="prospect" {{ old('status', 'prospect') == 'prospect' ? 'selected' : '' }}>Prospect</option>
                            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Acquisition Source</label>
                        <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="e.g. Google Search, TV, Referral">
                    </div>
                </div>

                <h3 style="font-size: 1rem; font-weight: 700; margin-bottom: 1.5rem; color: var(--primary);">Address & Logistics</h3>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="grid-column: span 3;">
                        <label class="form-label">Street Address</label>
                        <input type="text" name="address" class="form-control" value="{{ old('address') }}" placeholder="123 Solar Way">
                    </div>

                    <div>
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" value="{{ old('city') }}">
                    </div>

                    <div>
                        <label class="form-label">State / Province</label>
                        <input type="text" name="state" class="form-control" value="{{ old('state') }}">
                    </div>

                    <div>
                        <label class="form-label">Zip / Postal Code</label>
                        <input type="text" name="zip" class="form-control" value="{{ old('zip') }}">
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Internal Notes</label>
                    <textarea name="notes" class="form-control" rows="4" placeholder="Any special instructions or background info about this customer...">{{ old('notes') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); pt: 1.5rem;">
                    <a href="{{ route('customers.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Create Customer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
