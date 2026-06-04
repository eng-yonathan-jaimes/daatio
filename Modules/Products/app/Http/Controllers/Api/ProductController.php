<?php

namespace Modules\Products\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Products\app\Models\Product;

class ProductController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $products = Product::with(['store'])->paginate($request->query('per_page', 15));

        return response()->json($products);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'product_store_id' => ['required', 'integer', 'min:1'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_type' => ['required', 'integer', 'min:1'],
            'product_weight' => ['sometimes', 'numeric', 'min:0'],
            'product_value' => ['required', 'numeric', 'min:0'],
            'product_state' => ['required', 'string', 'in:In Stock,Out of Stock'],
        ]);

        $data['product_registration_date'] = now();

        $product = Product::create($data);

        return response()->json(['data' => $product->load(['store'])], 201);
    }

    public function show(Product $product): JsonResponse
    {
        return response()->json(['data' => $product->load(['store'])]);
    }

    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate([
            'product_store_id' => ['sometimes', 'integer', 'min:1'],
            'product_name' => ['sometimes', 'string', 'max:255'],
            'product_type' => ['sometimes', 'integer', 'min:1'],
            'product_weight' => ['sometimes', 'numeric', 'min:0'],
            'product_value' => ['sometimes', 'numeric', 'min:0'],
            'product_state' => ['sometimes', 'string', 'in:In Stock,Out of Stock'],
        ]);

        $product->update($data);

        return response()->json(['data' => $product->load(['store'])]);
    }

    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted successfully.']);
    }
}
