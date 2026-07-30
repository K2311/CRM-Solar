<x-app-layout title="Create Template">
    <div style="max-width: 1200px; margin: 0 auto;" x-data="templatePreview()">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">New Template</h1>
                <p style="color: var(--text-muted);">Save reusable messages for future campaigns across all channels.</p>
            </div>
            <a href="{{ route('templates.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div style="display: grid; grid-template-columns: 1.2fr 0.8fr; gap: 2rem; align-items: start;">
            <!-- Form -->
            <div class="card">
                
                <!-- WhatsApp Meta Warning -->
                <div x-show="channel === 'whatsapp'" class="animate-fade" style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 1rem; margin-bottom: 1.5rem; border-radius: 0.25rem;">
                    <div style="display: flex; gap: 0.75rem;">
                        <i class="bi bi-exclamation-triangle-fill" style="color: #f59e0b; font-size: 1.2rem;"></i>
                        <div>
                            <h4 style="color: #92400e; font-weight: 600; font-size: 0.875rem; margin-bottom: 0.25rem;">Meta Approval Required</h4>
                            <p style="color: #b45309; font-size: 0.8rem; margin: 0;">
                                To send campaigns via WhatsApp, this template <strong>must</strong> also be created and approved in your Meta Business Manager Dashboard with the exact same name. Unapproved templates will instantly fail to send.
                            </p>
                        </div>
                    </div>
                </div>
                <form action="{{ route('templates.store') }}" method="POST">
                    @csrf
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                        <div>
                            <label class="form-label">Template Name</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Welcome Email, Service Reminder" required>
                        </div>
                        <div>
                            <label class="form-label">Channel</label>
                            <select name="channel" class="form-control" x-model="channel" required>
                                <option value="email">Email</option>
                                <option value="whatsapp">WhatsApp</option>
                            </select>
                        </div>
                    </div>

                    <div style="margin-bottom: 1.5rem;" x-show="channel === 'email'">
                        <label class="form-label">Subject (Optional)</label>
                        <input type="text" name="subject" class="form-control" x-model="subject" value="{{ old('subject') }}">
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <label class="form-label">Message Template</label>
                        <textarea name="body" class="form-control" rows="10" x-model="body" required>{{ old('body') }}</textarea>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">Use {name}, {company}, {email}, {phone} as placeholders.</p>
                        <p style="font-size: 0.75rem; color: #f59e0b; margin-top: 0.25rem;" x-show="channel === 'whatsapp'">
                            <i class="bi bi-exclamation-triangle"></i> WhatsApp does not support HTML tags. Use <strong>*text*</strong> for bold and <strong>_text_</strong> for italics.
                        </p>
                        <p style="font-size: 0.75rem; color: #10b981; margin-top: 0.25rem;" x-show="channel === 'email'">
                            <i class="bi bi-check-circle"></i> HTML formatting is fully supported for Emails.
                        </p>
                    </div>

                    <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); padding-top: 1.5rem;">
                        <a href="{{ route('templates.index') }}" class="btn btn-outline">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save Template</button>
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
                                <div x-html="previewBody"></div>
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
                            <div style="font-size: 0.75rem; color: #64748b;">From: <span style="color: #1e293b; font-weight: 600;">{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }} &lt;{{ optional(auth()->user()->company)->email ?? 'noreply@solar-crm.com' }}&gt;</span></div>
                        </div>
                        <div style="padding: 2rem; min-height: 200px; color: #334155; font-size: 0.9rem; line-height: 1.6;">
                            <div x-html="previewBody"></div>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('templatePreview', () => ({
                channel: @json(old('channel', 'email')),
                subject: @json(old('subject')),
                body: @json(old('body', '')),
                
                get previewBody() {
                    const content = this.body || 'Enter your template content to see a preview...';
                    let preview = content
                        .replace(/{name}/g, '<strong style="color:var(--primary)">[Recipient Name]</strong>')
                        .replace(/{company}/g, '<strong style="color:var(--primary)">{{ optional(auth()->user()->company)->name ?? 'Solar CRM' }}</strong>')
                        .replace(/{email}/g, '<strong style="color:var(--primary)">[recipient@example.com]</strong>')
                        .replace(/{phone}/g, '<strong style="color:var(--primary)">[+1 555-0123]</strong>');
                    
                    return preview.replace(/\n/g, '<br>');
                }
            }));
        });
    </script>
</x-app-layout>
