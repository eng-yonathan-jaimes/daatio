<?php

namespace Modules\Products\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Products\app\Models\Product;
use Modules\Stores\app\Models\Store;

class ProductController extends Controller
{
    public function index()
    {
        $storeIds = Store::where('store_user_id', auth()->id())->pluck('id');
        return Product::whereIn('product_store_id', $storeIds)->get();
    }
    public function store(Request $request) { return Product::create($request->all()); }
    public function show(Product $product) { return $product; }
    public function update(Request $request, Product $product) { $product->update($request->all()); return $product; }
    public function destroy(Product $product) { $product->update(['product_state' => 'Out of Stock']); return response()->noContent(); }
}
