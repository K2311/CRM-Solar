<x-app-layout title="Add Product">
    <div style="max-width: 700px; margin: 0 auto;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <div>
                <h1 style="font-size: 1.875rem; font-weight: 800; margin-bottom: 0.5rem;">New Product</h1>
                <p style="color: var(--text-muted);">Add solar hardware or services to your catalog.</p>
            </div>
            <a href="{{ route('products.index') }}" class="btn btn-outline"><i class="bi bi-arrow-left"></i> Back</a>
        </div>

        <div class="card">
            <form action="{{ route('products.store') }}" method="POST">
                @csrf
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 1.5rem;">
                    <div style="grid-column: span 2;">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. 400W Mono Solar Panel" required>
                    </div>

                    <div>
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control" required>
                            <option value="panel" {{ old('category') == 'panel' ? 'selected' : '' }}>Solar Panel</option>
                            <option value="inverter" {{ old('category') == 'inverter' ? 'selected' : '' }}>Inverter</option>
                            <option value="battery" {{ old('category') == 'battery' ? 'selected' : '' }}>Battery Storage</option>
                            <option value="mounting" {{ old('category') == 'mounting' ? 'selected' : '' }}>Mounting Gear</option>
                            <option value="accessory" {{ old('category') == 'accessory' ? 'selected' : '' }}>Accessory</option>
                            <option value="service" {{ old('category') == 'service' ? 'selected' : '' }}>Labor / Service</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label">SKU / Model Number</label>
                        <input type="text" name="sku" class="form-control" value="{{ old('sku') }}" placeholder="SKU-123">
                    </div>

                    <div>
                        <label class="form-label">Unit Price ($)</label>
                        <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price') }}" step="0.01" required>
                    </div>

                    <div>
                        <label class="form-label">Unit of Measure</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', 'unit') }}" placeholder="e.g. unit, watt, hour" required>
                    </div>
                </div>

                <div style="margin-bottom: 1.5rem;">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                </div>

                <div style="display: flex; gap: 1rem; justify-content: flex-end; border-top: 1px solid var(--border); pt: 1.5rem;">
                    <a href="{{ route('products.index') }}" class="btn btn-outline">Cancel</a>
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
