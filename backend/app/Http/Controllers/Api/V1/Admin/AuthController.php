<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

final class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/v1/admin/login',
        summary: 'Sign in and get a bearer token',
        tags: ['Admin · Auth'],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(
            required: ['email', 'password'],
            properties: [
                new OA\Property(property: 'email', type: 'string', format: 'email'),
                new OA\Property(property: 'password', type: 'string', format: 'password'),
            ]
        )),
        responses: [
            new OA\Response(response: 200, description: 'Signed in', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'token', type: 'string'),
                new OA\Property(property: 'expires_at', type: 'string', format: 'date-time'),
                new OA\Property(property: 'user', ref: '#/components/schemas/AdminUser'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
            new OA\Response(response: 429, description: 'Too many attempts'),
        ]
    )]
    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => 'required|email|max:120',
            'password' => 'required|string|max:200',
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        // Same message for a wrong email, a wrong password or a disabled account.
        if (! $user || ! $user->is_active || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        $expires = now()->addMinutes((int) config('sanctum.expiration'));
        $token = $user->createToken('dashboard', ['*'], $expires)->plainTextToken;
        $user->forceFill(['last_login_at' => now()])->save();

        return response()->json(['token' => $token, 'expires_at' => $expires->toIso8601String(), 'user' => $this->userShape($user)]);
    }

    #[OA\Post(
        path: '/api/v1/admin/logout',
        summary: 'Revoke the current token',
        security: [['sanctum' => []]],
        tags: ['Admin · Auth'],
        responses: [
            new OA\Response(response: 204, description: 'Signed out'),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(null, 204);
    }

    #[OA\Get(
        path: '/api/v1/admin/me',
        summary: 'The signed-in user',
        security: [['sanctum' => []]],
        tags: ['Admin · Auth'],
        responses: [
            new OA\Response(response: 200, description: 'User', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/AdminUser'),
            ])),
            new OA\Response(ref: '#/components/responses/Unauthenticated', response: 401),
        ]
    )]
    public function me(Request $request): JsonResponse
    {
        return response()->json(['data' => $this->userShape($request->user())]);
    }

    private function userShape(User $user): array
    {
        return ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'role' => $user->role];
    }
}
