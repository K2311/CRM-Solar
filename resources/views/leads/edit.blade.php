<x-app-layout title="Edit Lead">
    <div style="max-width: 800px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Lead</h1>
                <p style="color: var(--text-muted);">Update details for project: {{ $lead->title }}</p>
            </div>
            <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back to details</a>
        </div>

        <div class="card">
            <form action="{{ route('leads.update', $lead) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Project Title / Short Description</label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $lead->title) }}" required>
                    </div>

                    <div style="grid-column: span 2; border: 1px solid var(--border); padding: 1.5rem; border-radius: 0.5rem; background: var(--bg-surface);">
                        <div style="margin-bottom: 1rem; display: flex; gap: 1.5rem; align-items: center; border-bottom: 1px solid var(--border); padding-bottom: 1rem;">
                            <span style="font-weight: 700; color: var(--text-main);">Prospect Details</span>
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="customer_type" value="existing" {{ old('customer_type', 'existing') === 'existing' ? 'checked' : '' }} onchange="document.getElementById('new_customer_fields').style.display='none'; document.getElementById('existing_customer_fields').style.display='block';"> 
                                Select Existing Customer
                            </label>
                            <label style="cursor: pointer; display: flex; align-items: center; gap: 0.5rem;">
                                <input type="radio" name="customer_type" value="new" {{ old('customer_type') === 'new' ? 'checked' : '' }} onchange="document.getElementById('new_customer_fields').style.display='grid'; document.getElementById('existing_customer_fields').style.display='none';"> 
                                Quick Add New Prospect
                            </label>
                        </div>

                        <div id="existing_customer_fields" style="display: {{ old('customer_type', 'existing') === 'existing' ? 'block' : 'none' }};">
                            <label class="form-label">Customer <span style="color: #ef4444;">*</span></label>
                            <select name="customer_id" class="form-control">
                                <option value="">Select a customer...</option>
                                @foreach($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $lead->customer_id) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->name }} ({{ $customer->email ?? 'No email' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('customer_id') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        </div>

                        <div id="new_customer_fields" style="display: {{ old('customer_type') === 'new' ? 'grid' : 'none' }}; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div>
                                <label class="form-label">Name <span style="color: #ef4444;">*</span></label>
                                <input type="text" name="new_customer_name" class="form-control" value="{{ old('new_customer_name') }}" placeholder="John Doe">
                                @error('new_customer_name') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label">Email</label>
                                <input type="email" name="new_customer_email" class="form-control" value="{{ old('new_customer_email') }}" placeholder="john@example.com">
                                @error('new_customer_email') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="form-label">Phone</label>
                                <input type="text" name="new_customer_phone" class="form-control" value="{{ old('new_customer_phone') }}" placeholder="+1 234 567 890">
                                @error('new_customer_phone') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Lead Stage</label>
                        <select name="stage" class="form-control" required>
                            @foreach(\App\Models\Lead::stages() as $stage)
                                <option value="{{ $stage }}" {{ old('stage', $lead->stage) == $stage ? 'selected' : '' }}>{{ strtoupper($stage) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Lead Source</label>
                        <select name="source" class="form-control">
                            <option value="">Select source...</option>
                            <option value="referral" {{ old('source', $lead->source) == 'referral' ? 'selected' : '' }}>Referral</option>
                            <option value="google_ads" {{ old('source', $lead->source) == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                            <option value="dealer" {{ old('source', $lead->source) == 'dealer' ? 'selected' : '' }}>Dealer</option>
                            <option value="walk_in" {{ old('source', $lead->source) == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                            <option value="housing_society" {{ old('source', $lead->source) == 'housing_society' ? 'selected' : '' }}>Housing Society</option>
                            <option value="other" {{ old('source', $lead->source) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Estimated Value</label>
                        <div style="position: relative;">
                            <span style="position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-muted);">{{ $currentCompany->currency_symbol ?? '$' }}</span>
                            <input type="number" name="value" class="form-control" value="{{ old('value', $lead->value) }}" step="0.01" style="padding-left: 2rem;">
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Assigned To</label>
                        <select name="assigned_user_id" class="form-control">
                            <option value="">Unassigned</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('assigned_user_id', $lead->assigned_user_id) == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">Expected Close Date</label>
                        <input type="date" name="expected_close_date" class="form-control" value="{{ old('expected_close_date', $lead->expected_close_date ? $lead->expected_close_date->format('Y-m-d') : '') }}">
                    </div>

                    <div style="grid-column: span 2;">
                        <label class="form-label">Interested Products</label>
                        @if($products->isEmpty())
                            <div style="padding: 1rem; background: var(--bg-surface); border: 1px dashed var(--border); border-radius: 0.5rem; text-align: center; color: var(--text-muted);">
                                No products available. <a href="{{ route('products.create') }}" style="color: var(--primary); text-decoration: underline;">Add products</a> to your catalog first.
                            </div>
                        @else
                            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 0.75rem; max-height: 200px; overflow-y: auto; padding: 1rem; border: 1px solid var(--border); border-radius: 0.5rem; background: var(--bg-surface);">
                                @foreach($products as $product)
                                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; padding: 0.5rem; border-radius: 0.25rem; transition: background 0.2s;" onmouseover="this.style.background='rgba(0,0,0,0.02)'" onmouseout="this.style.background='transparent'">
                                        <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" {{ collect(old('product_ids', $lead->products->pluck('id')))->contains($product->id) ? 'checked' : '' }} style="accent-color: var(--primary); width: 1.1rem; height: 1.1rem;">
                                        <span style="font-size: 0.9rem;">{{ $product->name }} <span style="color: var(--text-muted); font-size: 0.75rem;">({{ strtoupper($product->category) }})</span></span>
                                    </label>
                                @endforeach
                            </div>
                            @error('product_ids') <span style="color: #ef4444; font-size: 0.75rem;">{{ $message }}</span> @enderror
                        @endif
                    </div>
                </div>

                <div x-data="{ stage: '{{ $lead->stage }}' }" @change="stage = $event.target.value" style="margin-bottom: 1.5rem;">
                    <div x-show="stage === 'lost'" class="animate-fade" style="margin-top: 1rem;">
                        <label class="form-label" style="color: #ef4444;">Reason for Loss</label>
                        <textarea name="lost_reason" class="form-control" rows="3" placeholder="Why was this lead lost?">{{ old('lost_reason', $lead->lost_reason) }}</textarea>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="4">{{ old('notes', $lead->notes) }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem; margin-top: 1.5rem;">
                    <a href="{{ route('leads.show', $lead) }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Lead</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
