<?php

namespace App\Http\Middleware;

use App\Http\Responses\ApiResponse;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleBearerToken
{
    private const ROLE_MODEL_MAP = [
        'student' => User::class,
    ];

    public function handle(Request $request, Closure $next, ?string $role = null, string ...$roles): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return $this->unauthorized(__('bearer_token_required'));
        }

        $user = Auth::guard('sanctum')->user();

        if (! $user) {
            return $this->unauthorized(__('invalid_or_expired_token'));
        }

        // if (isset($user->is_active) && ! $user->is_active && ! $this->allowsInactiveAccount($request)) {
        //     return $this->unauthorized(__('invalid_or_expired_token'));
        // }

        $allowedRoles = array_values(array_filter([$role, ...$roles]));

        if (count($allowedRoles) > 0) {
            foreach ($allowedRoles as $allowedRole) {
                if (! isset(self::ROLE_MODEL_MAP[$allowedRole])) {
                    return $this->unauthorized(__('invalid_role_context'));
                }
            }

            $isAllowedRole = false;

            foreach ($allowedRoles as $allowedRole) {
                $modelClass = self::ROLE_MODEL_MAP[$allowedRole];

                if ($user instanceof $modelClass) {
                    $isAllowedRole = true;
                    break;
                }
            }

            if (! $isAllowedRole) {
                return $this->unauthorized(__('invalid_role_context'));
            }
        }

        $request->attributes->set('auth_user', $user);
        $request->attributes->set('auth_role', $this->resolveRoleFromUser($user));

        return $next($request);
    }

    private function resolveRoleFromUser(object $user): ?string
    {
        foreach (self::ROLE_MODEL_MAP as $mappedRole => $modelClass) {
            if ($user instanceof $modelClass) {
                return $mappedRole;
            }
        }

        return null;
    }

    private function allowsInactiveAccount(Request $request): bool
    {
        return in_array($request->route()?->getActionMethod(), [
            'traderCompleteProfile',
            'supplierCompleteProfile',
        ], true);
    }

    private function unauthorized(string $message)
    {
        return ApiResponse::make()
            ->success(false)
            ->message($message)
            ->statusCode(401)
            ->toResponse(request());
    }
}
