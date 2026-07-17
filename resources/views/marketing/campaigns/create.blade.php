<x-app-layout title="Create Campaign">
    <div style="max-width: 1200px; margin: 0 auto;" x-data="campaignPreview()">
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
                <form action="{{ route('campaigns.store') }}" method="POST">
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
                                <option value="sms">SMS (Twilio)</option>
                                <option value="whatsapp">WhatsApp</option>
                                <option value="facebook">Facebook Post</option>
                                <option value="instagram">Instagram Post</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Target Segment</label>
                            <select name="segment" class="form-control" required>
                                <option value="all">All Contacts</option>
                                <option value="leads">Leads Only</option>
                                <option value="customers">Customers Only</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;" x-show="channel === 'email'">
                        <label class="form-label">Subject Line</label>
                        <input type="text" name="subject" class="form-control" x-model="subject" placeholder="e.g. Special Offer on Solar Installation!">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label">Message Body</label>
                        <textarea name="body" class="form-control" rows="8" x-model="body" placeholder="Type your message here... Use {name} for personalization." required></textarea>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Available placeholders: {name}, {company}, {email}, {phone}</p>
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
                <div x-show="['sms', 'whatsapp'].includes(channel)" class="animate-fade">
                    <div style="width: 280px; height: 500px; background: #000; border-radius: 2.5rem; border: 8px solid #334155; margin: 0 auto; position: relative; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);">
                        <div style="height: 60px; background: #1e293b; display: flex; align-items: center; padding: 0 1.5rem; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white; font-size: 0.75rem;">S</div>
                            <div style="font-size: 0.8rem; font-weight: 600; color: white;" x-text="channel === 'sms' ? 'Solar CRM' : 'SolarTech WhatsApp'"></div>
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

                <!-- Social Post Frame -->
                <div x-show="['facebook', 'instagram'].includes(channel)" class="animate-fade">
                    <div style="background: #1e293b; border-radius: 0.75rem; overflow: hidden; border: 1px solid var(--border);">
                        <div style="padding: 1rem; display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--primary); display: flex; align-items: center; justify-content: center; color: white;"><i :class="channel === 'facebook' ? 'bi bi-facebook' : 'bi bi-instagram'"></i></div>
                            <div>
                                <div style="font-size: 0.875rem; font-weight: 700; color: white;">{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }}</div>
                                <div style="font-size: 0.7rem; color: var(--text-muted);">Just now • <i class="bi bi-globe"></i></div>
                            </div>
                        </div>
                        <div style="padding: 0 1rem 1rem 1rem; font-size: 0.93rem; color: white; line-height: 1.5;">
                             <div x-text="body || 'Your post content will appear here...'"></div>
                        </div>
                        <div style="aspect-ratio: 16/9; background: #334155; display: flex; align-items: center; justify-content: center; color: var(--text-muted);">
                            <i class="bi bi-image" style="font-size: 3rem; opacity: 0.3;"></i>
                        </div>
                        <div style="padding: 0.75rem 1rem; border-top: 1px solid var(--border); display: flex; gap: 1.5rem; color: var(--text-muted); font-size: 0.875rem;">
                            <span><i class="bi bi-hand-thumbs-up"></i> Like</span>
                            <span><i class="bi bi-chat"></i> Comment</span>
                            <span><i class="bi bi-share"></i> Share</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('campaignPreview', () => ({
                name: '{{ old('name') }}',
                channel: '{{ old('channel', 'email') }}',
                subject: '{{ old('subject') }}',
                body: '{{ old('body') }}',
                
                get previewBody() {
                    if (!this.body) return 'Enter your message content to see a preview...';
                    return this.body
                        .replace(/{name}/g, '<strong>[Customer Name]</strong>')
                        .replace(/{company}/g, '<strong>{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }}</strong>')
                        .replace(/{email}/g, '<strong>[customer@example.com]</strong>')
                        .replace(/{phone}/g, '<strong>[+1 555-0123]</strong>');
                }
            }));
        });
    </script>
</x-app-layout>
