<x-app-layout title="Edit Product">
    <div style="max-width: 700px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">Edit Product</h1>
                <p style="color: var(--text-muted);">{{ $product->name }}</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <form action="{{ route('products.update', $product) }}" method="POST">
                @csrf
                @method('PUT')
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $product->name) }}" required>
                    </div>

                    <div>
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control" required>
                            @foreach(['panel', 'inverter', 'battery', 'mounting', 'accessory', 'service'] as $cat)
                                <option value="{{ $cat }}" {{ old('category', $product->category) == $cat ? 'selected' : '' }}>
                                    {{ ucfirst($cat) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label">SKU / Model Number</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku', $product->sku) }}">
                    </div>

                    <div>
                        <label class="form-label">Unit Price ($)</label>
                        <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price', $product->unit_price) }}" step="0.01" required>
                    </div>

                    <div>
                        <label class="form-label">Unit of Measure</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', $product->unit) }}" required>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); pt: 1.5rem;">
                    <a href="{{ route('products.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Product</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
