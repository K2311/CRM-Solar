<x-app-layout title="Edit Campaign">
    <div style="max-width: 900px; margin: 0 auto;" x-data="campaignEditor()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Campaign</h1>
                <p style="color: var(--text-muted);">Modify your marketing campaign details.</p>
            </div>
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <form action="{{ route('campaigns.update', $campaign) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div>
                        <label class="form-label">Campaign Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $campaign->name) }}" required>
                    </div>
                    <div>
                        <label class="form-label">Channel</label>
                        <select name="channel" class="form-control" x-model="channel" required>
                            @foreach(\App\Models\Campaign::channels() as $ch)
                                <option value="{{ $ch }}" {{ old('channel', $campaign->channel) == $ch ? 'selected' : '' }}>
                                    {{ ucfirst($ch) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Target Segment</label>
                    <select name="segment" class="form-control" x-model="segment" required>
                        <optgroup label="Everything">
                            <option value="all"              {{ old('segment', $campaign->segment) == 'all'              ? 'selected' : '' }}>Everyone (All Leads & Customers)</option>
                            <option value="manual"           {{ old('segment', $campaign->segment) == 'manual'           ? 'selected' : '' }}>Specific Contacts (Manual)</option>
                        </optgroup>
                        <optgroup label="Customers">
                            <option value="customers_all"    {{ old('segment', $campaign->segment) == 'customers_all'    ? 'selected' : '' }}>All Customers</option>
                            <option value="customers_active" {{ old('segment', $campaign->segment) == 'customers_active' ? 'selected' : '' }}>Active Customers Only</option>
                        </optgroup>
                        <optgroup label="Leads">
                            <option value="leads_all"        {{ old('segment', $campaign->segment) == 'leads_all'        ? 'selected' : '' }}>All Leads</option>
                            <option value="leads_active"     {{ old('segment', $campaign->segment) == 'leads_active'     ? 'selected' : '' }}>Active Leads (Not won/lost)</option>
                            <option value="leads_new"        {{ old('segment', $campaign->segment) == 'leads_new'        ? 'selected' : '' }}>New Leads Only</option>
                            <option value="leads_engaged"    {{ old('segment', $campaign->segment) == 'leads_engaged'    ? 'selected' : '' }}>Engaged Leads (Survey/Quote)</option>
                            <option value="leads_lost"       {{ old('segment', $campaign->segment) == 'leads_lost'       ? 'selected' : '' }}>Lost Leads (Re-engagement)</option>
                        </optgroup>
                    </select>
                </div>

                <!-- Manual Contacts Selection -->
                <div style="margin-bottom: 1.5rem;" x-show="segment === 'manual'">
                    <label class="form-label">Select Specific Contacts</label>
                    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.bootstrap5.min.css" rel="stylesheet">
                    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
                    <div x-init="$nextTick(() => { new TomSelect($refs.contactSelect, { plugins: ['remove_button'], maxOptions: 50 }); })">
                        <select x-ref="contactSelect" name="selected_contacts[]" class="form-control" multiple placeholder="Search contacts by name, email, or phone...">
                            <optgroup label="Customers">
                            @foreach($customers as $c)
                                <option value="customer_{{ $c->id }}" {{ in_array('customer_'.$c->id, old('selected_contacts', $campaign->selected_contacts ?? [])) ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->email ?? $c->phone }})
                                </option>
                            @endforeach
                        </optgroup>
                            </optgroup>
                            <optgroup label="Leads">
                            @foreach($leads as $l)
                                <option value="lead_{{ $l->id }}" {{ in_array('lead_'.$l->id, old('selected_contacts', $campaign->selected_contacts ?? [])) ? 'selected' : '' }}>
                                    {{ $l->name }} ({{ $l->email ?? $l->phone }})
                                </option>
                            @endforeach
                            </optgroup>
                        </select>
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Start typing to search contacts.</p>
                </div>

                <div style="margin-bottom: 1.5rem;" x-show="channel === 'email'">
                    <label class="form-label">Subject Line (Email Only)</label>
                    <input type="text" name="subject" class="form-control" value="{{ old('subject', $campaign->subject) }}">
                </div>



                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Message / Caption</label>
                    <textarea name="body" class="form-control" rows="10" required>{{ old('body', $campaign->body) }}</textarea>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                        Available placeholders: {name}, {company}, {email}, {phone}
                    </p>
                    <p style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.25rem;" x-show="channel === 'whatsapp'">
                        <i class="bi bi-exclamation-triangle"></i> WhatsApp does not support HTML tags. Use <strong>*text*</strong> for bold and <strong>_text_</strong> for italics.
                    </p>
                    <p style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem;" x-show="channel === 'email'">
                        <i class="bi bi-check-circle"></i> HTML formatting is fully supported for Emails.
                    </p>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                    <a href="{{ route('campaigns.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Campaign</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('campaignEditor', () => ({
                channel:      '{{ old('channel', $campaign->channel) }}',
                segment:      '{{ old('segment', $campaign->segment) }}',
            }));
        });
    </script>
</x-app-layout>
