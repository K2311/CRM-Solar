<x-app-layout title="System Companies">
    <div style="margin-bottom: 2rem;">
        <h1 style="font-size: 1.5rem; font-weight: 800;">Global Company Administration</h1>
        <p style="color: var(--text-muted); font-size: 0.875rem;">Super-admin view of all registered tenants</p>
    </div>

    <div class="card glass-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Company Name</th>
                    <th>Domains/Emails</th>
                    <th>Members</th>
                    <th>Status</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($companies as $company)
                <tr>
                    <td>
                        <div style="font-weight: 700;">{{ $company->name }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Slug: {{ $company->slug }}</div>
                    </td>
                    <td>{{ $company->email }}</td>
                    <td>
                         <span class="badge badge-info">{{ $company->users_count }} Staff</span>
                    </td>
                    <td><span class="badge badge-success">Active</span></td>
                    <td style="text-align: right;">
                        <form action="{{ route('admin.impersonate', $company) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary" style="padding: 0.4rem 1rem; font-size: 0.75rem;">
                                <i class="bi bi-incognito"></i> Impersonate
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
