<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/** Dashboard accounts. Admin only. New users get an email link to set their own password. */
final class UserController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/users',
        summary: 'All dashboard users',
        security: [['sanctum' => []]],
        tags: ['Admin · Users'],
        responses: [
            new OA\Response(response: 200, description: 'Users', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/UserAdmin')),
            ])),
            new OA\Response(response: 403, description: 'Admins only'),
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(['data' => User::query()->orderBy('name')->get()->map($this->shape(...))]);
    }

    #[OA\Post(
        path: '/api/v1/admin/users',
        summary: 'Invite a user; they receive an email to set a password',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['name', 'email', 'role'], properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'role', type: 'string', enum: [User::ROLE_ADMIN, User::ROLE_EDITOR]),
        ])),
        tags: ['Admin · Users'],
        responses: [
            new OA\Response(response: 201, description: 'Invited', content: new OA\JsonContent(properties: [
                new OA\Property(property: 'data', ref: '#/components/schemas/UserAdmin'),
                new OA\Property(property: 'invite_sent', type: 'boolean'),
            ])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'email' => 'required|email:rfc|max:120|unique:users,email',
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_EDITOR])],
        ]);

        // A random password nobody knows; the invite link replaces it.
        $user = User::query()->create([...$data, 'email' => mb_strtolower($data['email']), 'password' => Str::random(40), 'is_active' => true]);
        $sent = $this->sendLink($user);

        return response()->json(['data' => $this->shape($user->refresh()), 'invite_sent' => $sent], 201);
    }

    #[OA\Put(
        path: '/api/v1/admin/users/{id}',
        summary: 'Change name, role or access',
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(content: new OA\JsonContent(properties: [
            new OA\Property(property: 'name', type: 'string'),
            new OA\Property(property: 'role', type: 'string', enum: [User::ROLE_ADMIN, User::ROLE_EDITOR]),
            new OA\Property(property: 'is_active', type: 'boolean'),
        ])),
        tags: ['Admin · Users'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [
            new OA\Response(response: 200, description: 'Updated', content: new OA\JsonContent(properties: [new OA\Property(property: 'data', ref: '#/components/schemas/UserAdmin')])),
            new OA\Response(ref: '#/components/responses/ValidationError', response: 422),
        ]
    )]
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        $data = $request->validate([
            'name' => 'sometimes|required|string|max:120',
            'role' => ['sometimes', Rule::in([User::ROLE_ADMIN, User::ROLE_EDITOR])],
            'is_active' => 'sometimes|boolean',
        ]);

        // Nobody can lock themselves out, and the last admin stays an admin.
        if ($user->is($request->user()) && (($data['is_active'] ?? true) === false || ($data['role'] ?? User::ROLE_ADMIN) !== User::ROLE_ADMIN)) {
            return $this->refuse('لا يمكنك إيقاف حسابك أو تغيير صلاحيتك بنفسك.');
        }

        $user->update($data);
        if (! $user->is_active) {
            $user->tokens()->delete();
        }

        return response()->json(['data' => $this->shape($user)]);
    }

    #[OA\Post(
        path: '/api/v1/admin/users/{id}/invite',
        summary: 'Send the set-password email again',
        security: [['sanctum' => []]],
        tags: ['Admin · Users'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 200, description: 'Result', content: new OA\JsonContent(properties: [new OA\Property(property: 'invite_sent', type: 'boolean')]))]
    )]
    public function invite(int $id): JsonResponse
    {
        return response()->json(['invite_sent' => $this->sendLink(User::query()->findOrFail($id))]);
    }

    #[OA\Delete(
        path: '/api/v1/admin/users/{id}',
        summary: 'Delete a user',
        security: [['sanctum' => []]],
        tags: ['Admin · Users'],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        responses: [new OA\Response(response: 204, description: 'Deleted'), new OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    )]
    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = User::query()->findOrFail($id);
        if ($user->is($request->user())) {
            return $this->refuse('لا يمكنك حذف حسابك.');
        }
        $user->tokens()->delete();
        $user->delete();

        return response()->json(null, 204);
    }

    private function sendLink(User $user): bool
    {
        try {
            return Password::broker()->sendResetLink(['email' => $user->email]) === Password::RESET_LINK_SENT;
        } catch (\Throwable $e) {
            report($e);

            return false;
        }
    }

    private function refuse(string $message): JsonResponse
    {
        return response()->json(['message' => $message, 'errors' => ['user' => [$message]]], 422);
    }

    private function shape(User $u): array
    {
        return [
            'id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role, 'is_active' => $u->is_active,
            'last_login_at' => $u->last_login_at?->toIso8601String(), 'created_at' => $u->created_at?->toIso8601String(),
        ];
    }
}
