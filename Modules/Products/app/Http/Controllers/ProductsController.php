<?php

namespace Modules\Products\app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Products\app\Models\Product;
use Modules\Stores\app\Models\Store;

class ProductsController extends Controller
{
    private function userStoreIds()
    {
        return Store::where('store_user_id', Auth::id())->pluck('id');
    }

    public function index()
    {
        $products = Product::whereIn('product_store_id', $this->userStoreIds())
            ->orderBy('product_name')
            ->get();

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_weight' => ['nullable', 'numeric', 'min:0'],
            'product_value' => ['nullable', 'numeric', 'min:0'],
            'product_currency' => ['nullable', 'string', 'max:3'],
        ]);

        $store = Store::where('store_user_id', Auth::id())
            ->where('store_active', true)
            ->first();

        Product::create([
            'product_store_id' => $store?->id ?? 1,
            'product_name' => $validated['product_name'],
            'product_type' => 0,
            'product_weight' => $validated['product_weight'] ?? 0,
            'product_value' => $validated['product_value'] ?? 0,
            'product_currency' => $validated['product_currency'] ?? 'USD',
            'product_state' => 'In Stock',
            'product_registration_date' => now(),
        ]);

        return redirect()->route('products.index')
            ->with('status', 'Product created.');
    }

    public function edit($id)
    {
        $product = Product::whereIn('product_store_id', $this->userStoreIds())->findOrFail($id);

        return view('products.edit', compact('product'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::whereIn('product_store_id', $this->userStoreIds())->findOrFail($id);

        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
            'product_weight' => ['nullable', 'numeric', 'min:0'],
            'product_value' => ['nullable', 'numeric', 'min:0'],
            'product_currency' => ['nullable', 'string', 'max:3'],
            'product_state' => ['required', 'in:In Stock,Out of Stock'],
        ]);

        $product->update([
            'product_name' => $validated['product_name'],
            'product_weight' => $validated['product_weight'] ?? 0,
            'product_value' => $validated['product_value'] ?? 0,
            'product_currency' => $validated['product_currency'] ?? 'USD',
            'product_state' => $validated['product_state'],
        ]);

        return redirect()->route('products.index')
            ->with('status', 'Product updated.');
    }

    public function destroy($id)
    {
        $product = Product::whereIn('product_store_id', $this->userStoreIds())->findOrFail($id);
        $product->delete();

        return redirect()->route('products.index')
            ->with('status', 'Product deleted.');
    }
}
