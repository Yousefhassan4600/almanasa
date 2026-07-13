<?php

namespace App\Http\Middleware;

use App\Models\Account;
use App\Models\Provider;
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

        $account->loadMissing('provider');

        $request->attributes->set('current_account', $account);
        $request->attributes->set('current_provider', $account->provider);
        $request->session()->put('current_account_id', $account->id);
        $request->session()->put('current_provider_id', $account->provider_id);

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
            ->with('provider')
            ->leftJoin('employees', function ($join) use ($request): void {
                $join->on('employees.account_id', '=', 'accounts.id')
                    ->where('employees.user_id', $request->user()->id)
                    ->where('employees.status', 'active');
            })
            ->where(function ($query) use ($request): void {
                $query
                    ->where('accounts.owner_user_id', $request->user()->id)
                    ->orWhereNotNull('employees.id');
            })
            ->where('accounts.is_active', true)
            ->orderBy('accounts.id');

        if ($surface === 'website') {
            $accountSubdomain = $request->route('accountSubdomain');

            if (is_string($accountSubdomain) && $accountSubdomain !== '') {
                $providerId = Provider::query()
                    ->where('subdomain', $accountSubdomain)
                    ->value('id');

                $query->where('accounts.provider_id', $providerId);
            }
        }

        return match ($surface) {
            'dashboard' => $query
                ->whereIn('accounts.type', ['saas_owner', 'academy', 'academy_teacher', 'standalone_teacher'])
                ->where(function ($query): void {
                    $query
                        ->where('accounts.type', 'saas_owner')
                        ->orWhereHas('provider.activeSubscription');
                }),
            'website' => $query->whereIn('accounts.type', ['student', 'parent']),
            default => $query,
        };
    }
}
