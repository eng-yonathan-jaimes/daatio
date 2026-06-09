<?php

namespace Modules\Stores\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Stores\app\Models\Store;
use Modules\Stores\app\Models\StoreType;

class StoreController extends Controller
{
    public function index() { return Store::where('store_user_id', auth()->id())->get(); }
    public function store(Request $request)
    {
        $data = $request->all();
        $data['store_user_id'] = auth()->id();
        return Store::create($data);
    }
    public function show(Store $store) { return $store; }
    public function update(Request $request, Store $store) { $store->update($request->all()); return $store; }
    public function destroy(Store $store) { $store->update(['store_active' => false]); return response()->noContent(); }
}

class StoreTypeController extends Controller
{
    public function index() { return StoreType::all(); }
    public function store(Request $request) { return StoreType::create($request->all()); }
    public function show(StoreType $type) { return $type; }
    public function update(Request $request, StoreType $type) { $type->update($request->all()); return $type; }
    public function destroy(StoreType $type) { $type->delete(); return response()->noContent(); }
}
