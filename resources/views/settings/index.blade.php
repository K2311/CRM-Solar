<x-app-layout title="Global Settings">
    <div x-data="{ tab: 'company' }">
        <div class="tab-container">
            <button class="tab-pill" :class="{ 'active': tab === 'company' }" @click="tab = 'company'">Company Profile</button>
            <button class="tab-pill" :class="{ 'active': tab === 'marketing' }" @click="tab = 'marketing'">Marketing APIs</button>
            <button class="tab-pill" :class="{ 'active': tab === 'email' }" @click="tab = 'email'">SMTP Email</button>
        </div>

        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <!-- Company Profile Tab -->
            <div x-show="tab === 'company'" x-transition:enter="animate-fade" class="card glass-card">
                <div style="display: grid; grid-template-columns: 350px 1fr; gap: 3rem;">
                    <div>
                        <div style="margin-bottom: 2rem;">
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Branding</h3>
                            <p style="color: var(--text-muted); font-size: 0.875rem;">Identity and logos for your business.</p>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">Company Logo</label>
                            <div style="padding: 2rem; background: rgba(0,0,0,0.2); border: 2px dashed var(--border); border-radius: 1rem; text-align: center; margin-bottom: 1rem;">
                                @if($company->logo)
                                <img src="{{ Storage::url($company->logo) }}" style="width: 120px; height: 120px; object-fit: contain; margin-bottom: 1rem; border-radius: 0.75rem;">
                                @else
                                <div style="font-size: 2.5rem; color: var(--border); margin-bottom: 1rem;"><i class="bi bi-image"></i></div>
                                @endif
                                <input type="file" name="logo" class="form-control" style="background: transparent; border: none; padding: 0; font-size: 0.8rem;">
                            </div>
                        </div>
                    </div>

                    <div>
                        <div style="margin-bottom: 2rem;">
                            <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 0.5rem;">Identity Details</h3>
                            <p style="color: var(--text-muted); font-size: 0.875rem;">Official information used on quotes and invoices.</p>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <div class="form-group">
                                <label class="form-label">Company Legal Name</label>
                                <input type="text" name="name" class="form-control" value="{{ $company->name }}" required placeholder="SolarTech Solutions Ltd">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Support Email</label>
                                <input type="email" name="email" class="form-control" value="{{ $company->email }}" placeholder="support@solartech.com">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Business Phone</label>
                                <input type="text" name="phone" class="form-control" value="{{ $company->phone }}" placeholder="+1 (555) 000-0000">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Timezone</label>
                                <select name="timezone" class="form-control">
                                    <option value="UTC" {{ $company->timezone === 'UTC' ? 'selected' : '' }}>UTC / Greenwich Mean Time</option>
                                    <option value="Asia/Kolkata" {{ $company->timezone === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                    <option value="America/New_York" {{ $company->timezone === 'America/New_York' ? 'selected' : '' }}>America/New_ York (EST)</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Default Currency</label>
                                <select name="currency" class="form-control" required>
                                    <option value="USD" {{ $company->currency === 'USD' ? 'selected' : '' }}>USD - US Dollar</option>
                                    <option value="EUR" {{ $company->currency === 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                                    <option value="INR" {{ $company->currency === 'INR' ? 'selected' : '' }}>INR - Indian Rupee</option>
                                    <option value="AUD" {{ $company->currency === 'AUD' ? 'selected' : '' }}>AUD - Australian Dollar</option>
                                    <option value="GBP" {{ $company->currency === 'GBP' ? 'selected' : '' }}>GBP - British Pound</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 0.5rem;">
                            <label class="form-label">Registered Address</label>
                            <textarea name="address" class="form-control" rows="3" placeholder="Street, City, State, ZIP...">{{ $company->address }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Marketing APIs Tab -->
            <div x-show="tab === 'marketing'" x-transition:enter="animate-fade" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                <div class="card glass-card">
                    <div class="card" style="background: rgba(239, 64, 64, 0.05); border: 1px solid rgba(239, 64, 64, 0.2); margin-bottom: 3rem; padding: 1.5rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: #ef4040; margin-bottom: 0.5rem;"><i class="bi bi-info-circle-fill"></i> Setup Guide</h4>
                        <ul style="font-size: 0.75rem; color: var(--text-muted); padding-left: 1.25rem; line-height: 1.4;">
                            <li>Login to <a href="https://console.twilio.com" target="_blank" style="color: #ef4040; text-decoration: underline;">Twilio Console</a>.</li>
                            <li>Copy <b>Account SID</b> and <b>Auth Token</b> from the homepage.</li>
                            <li>Buy a number under <b>Phone Numbers > Manage > Buy a number</b>.</li>
                            <li>For WhatsApp, use <b>Messaging > Try it Out > WhatsApp Sandbox</b> first.</li>
                        </ul>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(239, 64, 64, 0.1); color: #ef4040; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-chat-right-dots-fill"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 800;">Twilio SMS & WhatsApp</h3>
                            <p style="color: var(--text-muted); font-size: 0.75rem;">Connect your direct messaging channels.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Account SID</label>
                        <input type="text" name="settings[twilio_sid]" class="form-control" value="{{ $settings['twilio_sid'] ?? '' }}" placeholder="ACxxxxxxxxxxxxxxxxxxxxxxxx">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Auth Token</label>
                        <input type="password" name="settings[twilio_token]" class="form-control" value="{{ $settings['twilio_token'] ?? '' }}" placeholder="••••••••••••••••••••">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label">SMS From #</label>
                            <input type="text" name="settings[twilio_from]" class="form-control" value="{{ $settings['twilio_from'] ?? '' }}" placeholder="+1...">
                        </div>
                        <div class="form-group">
                            <label class="form-label">WhatsApp #</label>
                            <input type="text" name="settings[twilio_whatsapp_from]" class="form-control" value="{{ $settings['twilio_whatsapp_from'] ?? '' }}" placeholder="whatsapp:+1...">
                        </div>
                    </div>
                </div>

                <div class="card glass-card">
                    <div class="card" style="background: rgba(24, 119, 242, 0.05); border: 1px solid rgba(24, 119, 242, 0.2); margin-bottom: 3rem; padding: 1.5rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: #1877f2; margin-bottom: 0.5rem;"><i class="bi bi-info-circle-fill"></i> Setup Guide</h4>
                        <ul style="font-size: 0.75rem; color: var(--text-muted); padding-left: 1.25rem; line-height: 1.4;">
                            <li>Go to <a href="https://developers.facebook.com" target="_blank" style="color: #1877f2; text-decoration: underline;">Meta for Developers</a>.</li>
                            <li>Create a <b>Marketing App</b> and add <b>Marketing API</b> product.</li>
                            <li>Generate a <b>System User Token</b> under App Settings > Advanced.</li>
                            <li>Find your <b>Page ID</b> in the 'About' section of your Facebook Page.</li>
                        </ul>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(24, 119, 242, 0.1); color: #1877f2; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-facebook"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 800;">Meta Marketing</h3>
                            <p style="color: var(--text-muted); font-size: 0.75rem;">Manage Facebook and Instagram integrations.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">System User Access Token</label>
                        <input type="password" name="settings[meta_access_token]" class="form-control" value="{{ $settings['meta_access_token'] ?? '' }}" placeholder="EAAE...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Facebook Page ID</label>
                        <input type="text" name="settings[meta_page_id]" class="form-control" value="{{ $settings['meta_page_id'] ?? '' }}" placeholder="109837...">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Instagram Business ID</label>
                        <input type="text" name="settings[meta_ig_business_id]" class="form-control" value="{{ $settings['meta_ig_business_id'] ?? '' }}" placeholder="178414...">
                    </div>
                </div>
            </div>

            <!-- SMTP Email Tab -->
            <div x-show="tab === 'email'" x-transition:enter="animate-fade">
                <div class="card glass-card" style="max-width: 800px;">
                    <div class="card" style="background: rgba(14, 165, 233, 0.05); border: 1px solid rgba(14, 165, 233, 0.2); margin-bottom: 3rem; padding: 1.5rem;">
                        <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--primary); margin-bottom: 0.5rem;"><i class="bi bi-info-circle-fill"></i> Setup Guide</h4>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                            <ul style="font-size: 0.75rem; color: var(--text-muted); padding-left: 1.25rem; line-height: 1.4;">
                                <li><b>Recommended:</b> Use <a href="https://mailtrap.io" target="_blank" style="color: var(--primary); text-decoration: underline;">Mailtrap</a> for testing.</li>
                                <li>For production, use <b>SendGrid</b>, <b>Amazon SES</b>, or <b>Gmail</b>.</li>
                                <li>Ensure you use <b>Port 587</b> with <b>TLS</b> encryption.</li>
                            </ul>
                            <ul style="font-size: 0.75rem; color: var(--text-muted); padding-left: 1.25rem; line-height: 1.4;">
                                <li><b>Host:</b> Likely ends in <i>smtp.provider.com</i></li>
                                <li><b>Username:</b> Often your full email address.</li>
                                <li><b>Password:</b> Use an <b>App Password</b> for security.</li>
                            </ul>
                        </div>
                    </div>

                    <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 2rem;">
                        <div style="width: 48px; height: 48px; border-radius: 12px; background: rgba(14, 165, 233, 0.1); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                            <i class="bi bi-envelope-at-fill"></i>
                        </div>
                        <div>
                            <h3 style="font-size: 1.1rem; font-weight: 800;">Custom SMTP Gateway</h3>
                            <p style="color: var(--text-muted); font-size: 0.75rem;">Deliver marketing emails through your own server.</p>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" name="settings[mail_host]" class="form-control" value="{{ $settings['mail_host'] ?? '' }}" placeholder="smtp.mailtrap.io">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Port</label>
                            <input type="text" name="settings[mail_port]" class="form-control" value="{{ $settings['mail_port'] ?? '587' }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" name="settings[mail_username]" class="form-control" value="{{ $settings['mail_username'] ?? '' }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" name="settings[mail_password]" class="form-control" value="{{ $settings['mail_password'] ?? '' }}">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                        <div class="form-group">
                            <label class="form-label">Default From Address</label>
                            <input type="text" name="settings[mail_from_address]" class="form-control" value="{{ $settings['mail_from_address'] ?? '' }}" placeholder="marketing@solartech.com">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Encryption Protocol</label>
                            <select name="settings[mail_encryption]" class="form-control">
                                <option value="tls" {{ ($settings['mail_encryption'] ?? '') === 'tls' ? 'selected' : '' }}>STARTTLS (Standard)</option>
                                <option value="ssl" {{ ($settings['mail_encryption'] ?? '') === 'ssl' ? 'selected' : '' }}>SSL/TLS (Implicit)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div style="position: sticky; bottom: 2rem; margin-top: 3rem; background: var(--glass-bg); backdrop-filter: blur(12px); border: 1px solid var(--glass-border); padding: 1.25rem 2rem; border-radius: 1.25rem; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 10px 30px rgba(0,0,0,0.4); z-index: 10;">
                <p style="color: var(--text-muted); font-size: 0.875rem;"><i class="bi bi-info-circle" style="margin-right: 0.5rem;"></i> Be careful when modifying API credentials.</p>
                <button type="submit" class="btn btn-primary" style="padding: 0.8rem 2.5rem; font-size: 1rem; border-radius: 1rem;">
                    <i class="bi bi-check-lg" style="margin-right: 0.5rem;"></i> Save Configuration
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
