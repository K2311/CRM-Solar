<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->category) $query->where('category', $request->category);
        $products = $query->latest()->paginate(20)->withQueryString();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|in:panel,inverter,battery,mounting,accessory,service',
            'sku'        => 'nullable|string|max:100',
            'description'=> 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'unit'       => 'required|string|max:30',
            'is_active'  => 'boolean',
        ]);
        Product::create($data);
        return redirect()->route('products.index')->with('success', 'Product added.');
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'category'   => 'required|in:panel,inverter,battery,mounting,accessory,service',
            'sku'        => 'nullable|string|max:100',
            'description'=> 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'unit'       => 'required|string|max:30',
            'is_active'  => 'boolean',
        ]);
        $product->update($data);
        return redirect()->route('products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('products.index')->with('success', 'Product deleted.');
    }
}
