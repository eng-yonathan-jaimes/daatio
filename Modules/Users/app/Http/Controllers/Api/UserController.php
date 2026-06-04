<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\Users\app\Models\User;

class UserController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $users = User::paginate($request->query('per_page', 15));

        return response()->json($users);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'min:1'],
            'user_name' => ['required', 'string', 'max:255'],
            'user_lastName' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255', 'unique:user,user_email'],
            'user_access' => ['required', 'string', 'max:255'],
            'user_password' => ['required', 'string', 'min:8'],
            'user_phone_number' => ['required', 'string', 'max:255'],
        ]);

        $data['user_password'] = Hash::make($data['user_password']);
        $data['user_update_date'] = now();

        $user = User::create($data);

        return response()->json(['data' => $user], 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => $user]);
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['sometimes', 'integer', 'min:1'],
            'user_name' => ['sometimes', 'string', 'max:255'],
            'user_lastName' => ['sometimes', 'string', 'max:255'],
            'user_email' => ['sometimes', 'email', 'max:255', 'unique:user,user_email,'.$user->id],
            'user_access' => ['sometimes', 'string', 'max:255'],
            'user_password' => ['sometimes', 'string', 'min:8'],
            'user_phone_number' => ['sometimes', 'string', 'max:255'],
        ]);

        if (isset($data['user_password'])) {
            $data['user_password'] = Hash::make($data['user_password']);
        }

        $data['user_update_date'] = now();

        $user->update($data);

        return response()->json(['data' => $user]);
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }
}
