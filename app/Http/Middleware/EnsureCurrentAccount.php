<?php

namespace App\Http\Middleware;

use App\Models\Account;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCurrentAccount
{
    public function handle(Request $request, Closure $next, string $surface = 'any'): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        $account = $this->resolveAccount($request, $surface);

        if (! $account) {
            abort(403, 'No accessible account is available for this area.');
        }

        $request->attributes->set('current_account', $account);
        $request->session()->put('current_account_id', $account->id);

        return $next($request);
    }

    private function resolveAccount(Request $request, string $surface): ?Account
    {
        $requestedAccountId = $request->integer('account_id')
            ?: (int) $request->header('X-Account-Id')
            ?: (int) $request->session()->get('current_account_id');

        if ($requestedAccountId > 0) {
            $account = $this->accessibleAccounts($request, $surface)
                ->where('accounts.id', $requestedAccountId)
                ->first();

            if ($account) {
                return $account;
            }
        }

        return $this->accessibleAccounts($request, $surface)->first();
    }

    private function accessibleAccounts(Request $request, string $surface)
    {
        $query = Account::query()
            ->select('accounts.*')
            ->join('account_memberships', 'account_memberships.account_id', '=', 'accounts.id')
            ->where('account_memberships.user_id', $request->user()->id)
            ->where('account_memberships.status', 'active')
            ->where('accounts.status', 'active')
            ->orderBy('accounts.id');

        return match ($surface) {
            'dashboard' => $query->whereIn('accounts.type', ['saas_owner', 'academy', 'academy_teacher', 'standalone_teacher']),
            'website' => $query->whereIn('accounts.type', ['student', 'parent']),
            default => $query,
        };
    }
}
