<?php

namespace Modules\Users\app\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Users\app\Models\User;
use Modules\Users\app\Models\UserLoginHistory;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'tenant_id' => ['required', 'integer', 'min:1'],
            'user_name' => ['required', 'string', 'max:255'],
            'user_lastName' => ['required', 'string', 'max:255'],
            'user_email' => ['required', 'email', 'max:255', 'unique:user,user_email'],
            'user_access' => ['sometimes', 'string', 'max:255'],
            'user_password' => ['required', 'string', 'min:8', 'confirmed'],
            'user_phone_number' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'tenant_id' => $data['tenant_id'],
            'user_name' => $data['user_name'],
            'user_lastName' => $data['user_lastName'],
            'user_email' => $data['user_email'],
            'user_access' => $data['user_access'] ?? 'owner',
            'user_password' => Hash::make($data['user_password']),
            'user_phone_number' => $data['user_phone_number'],
            'user_update_date' => now(),
        ]);

        $user->refresh();

        $this->recordLogin($request, $user);

        return response()->json([
            'token' => $user->createToken($this->tokenName($request))->plainTextToken,
            'user' => $this->userPayload($user),
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'user_email' => ['required', 'email'],
            'user_password' => ['required', 'string'],
        ]);

        $user = User::where('user_email', $credentials['user_email'])->first();

        if (! $user || ! Hash::check($credentials['user_password'], $user->user_password)) {
            throw ValidationException::withMessages([
                'user_email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $this->recordLogin($request, $user);

        return response()->json([
            'token' => $user->createToken($this->tokenName($request))->plainTextToken,
            'user' => $this->userPayload($user),
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'user' => $this->userPayload($request->user()),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $accessToken = $request->user()->currentAccessToken();

        if ($accessToken) {
            $accessToken->delete();
        }

        return response()->json([
            'message' => 'Logged out successfully.',
        ]);
    }

    private function recordLogin(Request $request, User $user): void
    {
        UserLoginHistory::create([
            'user_login_history_ip' => substr((string) $request->ip(), 0, 15),
            'user_login_history_device' => substr((string) $request->userAgent(), 0, 255),
            'user_login_history_user_id' => $user->id,
        ]);
    }

    private function tokenName(Request $request): string
    {
        return substr((string) ($request->userAgent() ?: 'api-token'), 0, 255);
    }

    private function userPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'tenant_id' => $user->tenant_id,
            'user_name' => $user->user_name,
            'user_lastName' => $user->user_lastName,
            'user_email' => $user->user_email,
            'user_access' => $user->user_access,
            'user_phone_number' => $user->user_phone_number,
            'user_creation' => $user->user_creation,
            'user_update_date' => $user->user_update_date,
        ];
    }
}
