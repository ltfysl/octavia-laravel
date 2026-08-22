<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthTokenController extends Controller
{
    /** Rate limited in routes/api.php (6 per minute). */
    public function store(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:255'],
            // Subset of read/write; unknown abilities are rejected so
            // tokens cannot silently gain future permissions.
            'abilities' => ['nullable', 'array'],
            'abilities.*' => ['string', Rule::in(['read', 'write'])],
        ]);

        /** @var User|null $user */
        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $abilities = $credentials['abilities'] ?? ['read'];
        $token = $user->createToken($credentials['device_name'] ?? 'api', $abilities);

        return response()->json([
            'token' => $token->plainTextToken,
            // Sanctum hashes tokens; expose only safe metadata.
            'expires_at' => null,
            'user' => $user->only(['id', 'name', 'email']),
        ], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['ok' => true]);
    }
}
