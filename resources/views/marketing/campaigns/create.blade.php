<x-app-layout title="Create Campaign">
    <div style="max-width: 1200px; margin: 0 auto;" x-data="campaignCreator()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">New Marketing Campaign</h1>
                <p style="color: var(--text-muted);">Reach out to your leads and customers across multiple channels.</p>
            </div>
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; align-items: start;">
            <!-- Form -->
            <div class="card">
                <!-- WhatsApp Warning -->
                <div x-show="channel === 'whatsapp' && !selectedTemplateId" class="animate-fade" style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.25rem;">
                    <div style="display: flex; gap: 0.75rem;">
                        <i class="bi bi-exclamation-triangle-fill" style="color: #f59e0b; font-size: 1.2rem;"></i>
                        <div>
                            <h4 style="color: #92400e; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">Custom Message Restriction</h4>
                            <p style="color: #b45309; font-size: 0.8rem; margin: 0;">
                                You are typing a custom message. Due to WhatsApp's 24-hour rule, this will <strong>only</strong> be delivered to customers who have messaged you within the last 24 hours. To blast all leads/customers anytime, please select a Meta-approved <strong>Template</strong> from the dropdown below.
                            </p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('campaigns.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div style="grid-column: span 2;">
                            <label class="form-label">Campaign Name</label>
                            <input type="text" name="name" class="form-control" x-model="name" placeholder="e.g. Summer Discount Blast" required>
                        </div>
                        <div>
                            <label class="form-label">Channel</label>
                            <select name="channel" class="form-control" x-model="channel" required>
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Target Segment</label>
                            <select name="segment" class="form-control" x-model="segment" required>
                                <optgroup label="Everything">
                                    <option value="all">Everyone (All Leads & Customers)</option>
                                    <option value="manual">Specific Contacts (Manual)</option>
                                </optgroup>
                                <optgroup label="Customers">
                                    <option value="customers_all">All Customers</option>
                                    <option value="customers_active">Active Customers Only</option>
                                </optgroup>
                                <optgroup label="Leads">
                                    <option value="leads_all">All Leads</option>
                                    <option value="leads_active">Active Leads (Not won/lost)</option>
                                    <option value="leads_new">New Leads Only</option>
                                    <option value="leads_engaged">Engaged Leads (Survey/Quote)</option>
                                    <option value="leads_lost">Lost Leads (Re-engagement)</option>
                                </optgroup>
                            </select>
                        </div>
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
                                    <option value="customer_{{ $c->id }}">{{ $c->name }} ({{ $c->email ?? $c->phone }})</option>
                                @endforeach
                            </optgroup>
                            <optgroup label="Leads">
                                @foreach($leads as $l)
                                    <option value="lead_{{ $l->id }}">{{ $l->name }} ({{ $l->email ?? $l->phone }})</option>
                                @endforeach
                            </optgroup>
                        </select>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Start typing to search contacts.</p>
                    </div>

                    <!-- Email subject -->
                    <div style="margin-bottom: 1.5rem;" x-show="channel === 'email'">
                        <label class="form-label">Subject Line</label>
                        <input type="text" name="subject" class="form-control" x-model="subject" placeholder="e.g. Special Offer on Solar Installation!">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label">Use a Saved Template (Optional)</label>
                        <select class="form-control" x-model="selectedTemplateId" @change="applyTemplate()">
                            <option value="">-- Write Custom Message --</option>
                            <template x-for="template in availableTemplates" :key="template.id">
                                <option :value="template.id" x-text="template.name"></option>
                            </template>
                        </select>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;" x-show="selectedTemplateId">Template applied! You can still edit the message below before sending.</p>
                    </div>



                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label">Message / Caption</label>
                        <textarea name="body" class="form-control" rows="6" x-model="body"
                            placeholder="Type your message here... Use {name} for personalisation."
                            required></textarea>
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
                        <button type="submit" class="btn btn-primary">Create Draft</button>
                    </div>
                </form>
            </div>

            <!-- Preview Sidebar -->
            <div style="position: sticky; top: 2rem;">
                <h3 style="font-size: 0.875rem; font-weight: 700; text-transform: uppercase; color: var(--text-muted); margin-bottom: 1rem;">Live Preview</h3>

                <!-- SMS / WhatsApp Frame -->
                <div x-show="channel === 'whatsapp'" class="animate-fade">
                    <div style="width: 280px; height: 500px; background: #000; border-radius: 2.5rem; border: 8px solid #334155; margin: 0 auto; position: relative; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                        <div style="height: 60px; background: #1e293b; display: flex; align-items: center; padding: 0 1.5rem; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem;">W</div>
                            <div style="font-size: 0.8rem; font-weight: 600; color: white;">SolarTech WhatsApp</div>
                        </div>
                        <div style="padding: 1rem; display: flex; flex-direction: column; gap: 1rem; height: calc(100% - 60px); background: #0f172a;">
                            <div style="max-width: 85%; align-self: flex-start; background: #334155; padding: 0.75rem; border-radius: 1rem 1rem 1rem 0; font-size: 0.8rem; color: white; line-height: 1.4;">
                                <div x-text="previewBody"></div>
                            </div>
                        </div>
                    </div>
                    <p style="text-align: center; font-size: 0.75rem; color: var(--text-muted); margin-top: 1rem;">Mobile Device Mockup</p>
                </div>

                <!-- Email Frame -->
                <div x-show="channel === 'email'" class="animate-fade">
                    <div style="background: white; border-radius: 0.75rem; border: 1px solid var(--border); overflow: hidden; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);">
                        <div style="padding: 1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                            <div style="font-size: 0.75rem; color: #64748b; margin-bottom: 0.25rem;">Subject: <span style="color: #1e293b; font-weight: 600;" x-text="subject || '(No Subject)'"></span></div>
                            <div style="font-size: 0.75rem; color: #64748b;">From: <span style="color: #1e293b; font-weight: 600;">{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }} <{{ optional(auth()->user()->company)->email ?? 'noreply@solar-crm.com' }}></span></div>
                        </div>
                        <div style="padding: 2rem; min-height: 200px; color: #334155; font-size: 0.9rem; line-height: 1.6;">
                            <div x-html="previewBody.replace(/\n/g, '<br>')"></div>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            const allTemplates = @json($templates);
            
            Alpine.data('campaignCreator', () => ({
                name:          '{{ old('name') }}',
                channel:       '{{ old('channel', 'email') }}',
                segment:       '{{ old('segment', 'all') }}',
                subject:       '{{ old('subject') }}',
                body:          '{{ old('body') }}',
                selectedTemplateId: '',
                
                get availableTemplates() {
                    return allTemplates.filter(t => t.channel === this.channel);
                },

                applyTemplate() {
                    if (!this.selectedTemplateId) return;
                    const template = allTemplates.find(t => t.id == this.selectedTemplateId);
                    if (template) {
                        this.body = template.body;
                        if (this.channel === 'email' && template.subject) {
                            this.subject = template.subject;
                        }
                    }
                },

                get previewBody() {
                    if (!this.body) return 'Enter your message content to see a preview...';
                    const company = '{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }}';
                    return this.body
                        .replace(/{name}/g,    '<strong>[Customer Name]</strong>')
                        .replace(/{company}/g, `<strong>${company}</strong>`)
                        .replace(/{email}/g,   '<strong>[customer@example.com]</strong>')
                        .replace(/{phone}/g,   '<strong>[+91 98765-43210]</strong>');
                },
                
                init() {
                    this.$watch('channel', () => {
                        this.selectedTemplateId = '';
                    });
                }
            }));
        });
    </script>
</x-app-layout>
