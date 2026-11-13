<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

/** Forgot password and set password (also used by invites). */
final class PasswordController extends Controller
{
    #[OA\Post(
        path: '/api/v1/admin/forgot-password',
        summary: 'Email a reset link. The answer is the same whether or not the email exists',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['email'], properties: [new OA\Property(property: 'email', type: 'string', format: 'email')])),
        tags: ['Admin · Auth'],
        responses: [new OA\Response(response: 200, description: 'Accepted'), new OA\Response(response: 429, description: 'Too many requests')]
    )]
    public function forgot(Request $request): JsonResponse
    {
        $email = mb_strtolower($request->validate(['email' => 'required|email|max:120'])['email']);

        if (User::query()->where('email', $email)->where('is_active', true)->exists()) {
            try {
                Password::broker()->sendResetLink(['email' => $email]);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return response()->json(['ok' => true]);
    }

    #[OA\Post(
        path: '/api/v1/admin/reset-password',
        summary: 'Set a new password with the emailed token; signs out every session',
        requestBody: new OA\RequestBody(required: true, content: new OA\JsonContent(required: ['token', 'email', 'password', 'password_confirmation'], properties: [
            new OA\Property(property: 'token', type: 'string'),
            new OA\Property(property: 'email', type: 'string', format: 'email'),
            new OA\Property(property: 'password', type: 'string', format: 'password'),
            new OA\Property(property: 'password_confirmation', type: 'string', format: 'password'),
        ])),
        tags: ['Admin · Auth'],
        responses: [new OA\Response(response: 200, description: 'Password changed'), new OA\Response(ref: '#/components/responses/ValidationError', response: 422)]
    )]
    public function reset(Request $request): JsonResponse
    {
        $data = $request->validate([
            'token' => 'required|string|max:200',
            'email' => 'required|email|max:120',
            'password' => ['required', 'confirmed', 'max:200', PasswordRule::min(10)->letters()->numbers()],
        ]);

        $data['email'] = mb_strtolower($data['email']);
        $status = Password::broker()->reset($data, function (User $user, string $password): void {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            $user->tokens()->delete();
        });

        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => 'الرابط غير صالح أو انتهت صلاحيته. اطلب رابطًا جديدًا.']);
        }

        return response()->json(['ok' => true]);
    }
}
