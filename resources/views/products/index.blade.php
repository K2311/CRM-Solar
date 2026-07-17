<x-app-layout title="Product Catalog">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 800;">Products & Components</h1>
            <p style="color: var(--text-muted); font-size: 0.875rem;">Manage your solar inventory and pricing</p>
        </div>
        @if(auth()->user()->canDo('products.create'))
        <a href="{{ route('products.create') }}" class="btn btn-primary"><i class="bi bi-box-seam"></i> Add Product</a>
        @endif
    </div>

    <div style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); display: grid; gap: 1.5rem;">
        @foreach($products as $product)
        <div class="card glass-card">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
                <div style="padding: 0.5rem; background: rgba(14, 165, 233, 0.1); border-radius: 0.75rem; color: var(--primary);">
                    <i class="bi bi-{{ \App\Models\Product::categoryIcons()[$product->category] ?? 'box' }}" style="font-size: 1.5rem;"></i>
                </div>
                <span class="badge badge-info" style="font-size: 0.6rem;">{{ $product->category }}</span>
            </div>
            <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem;">{{ $product->name }}</h3>
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-bottom: 1.5rem;">{{ Str::limit($product->description, 100) }}</p>
            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 1rem;">
                <div style="font-size: 1.25rem; font-weight: 800; color: var(--primary);">{{ $currentCompany->currency_symbol }}{{ number_format($product->unit_price, 2) }}<span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 400;"> / {{ $product->unit }}</span></div>
                <div style="display: flex; gap: 0.5rem;">
                    @if(auth()->user()->canDo('products.edit'))
                    <a href="{{ route('products.edit', $product) }}" class="btn btn-outline" style="padding: 0.4rem 0.8rem; font-size: 0.75rem;">Edit</a>
                    @endif
                    @if(auth()->user()->canDo('products.delete'))
                    <form action="{{ route('products.destroy', $product) }}" method="POST" id="del-product-{{ $product->id }}">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; background: rgba(239,68,68,0.15); color: #ef4444; border: 1px solid rgba(239,68,68,0.3);"
                            onclick="swalDelete(this, 'Delete \'{{ addslashes($product->name) }}\'? This cannot be undone.')">Delete</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 2rem;">
        {{ $products->links() }}
    </div>
</x-app-layout>
