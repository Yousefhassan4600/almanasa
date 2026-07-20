<?php

namespace App\Livewire\Admin;

use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Component;

class AccountPicker extends Component
{
    public string $selectedAccountId = '';

    public string $redirectUrl = '';

    public function mount(): void
    {
        $currentAccount = request()->attributes->get('current_account');

        if ($currentAccount instanceof Account) {
            $this->selectedAccountId = (string) $currentAccount->getKey();
        } else {
            $this->selectedAccountId = (string) session('current_account_id', '');
        }

        $this->redirectUrl = request()->fullUrl();
    }

    public function switchAccount(string $accountId): mixed
    {
        $account = $this->accessibleDashboardAccounts()
            ->firstWhere('id', (int) $accountId);

        if (! $account instanceof Account) {
            $this->addError('selectedAccountId', __('This account is not available.'));

            return null;
        }

        session()->put('current_account_id', $account->id);
        session()->put('current_provider_id', $account->provider_id);

        $this->selectedAccountId = (string) $account->id;

        return $this->redirect($this->redirectUrl ?: url()->current(), navigate: false);
    }

    public function render(): View
    {
        return view('livewire.admin.account-picker', [
            'accounts' => $this->accessibleDashboardAccounts(),
        ]);
    }

    public function accountLabel(Account $account): string
    {
        $type = $account->type instanceof AccountType
            ? $account->type->value
            : (string) $account->type;

        $typeLabel = AccountType::options()[$type] ?? Str::headline($type);
        $providerName = $account->provider?->name;

        return $providerName
            ? "{$providerName} - {$typeLabel}"
            : $typeLabel;
    }

    /**
     * @return Collection<int, Account>
     */
    private function accessibleDashboardAccounts(): Collection
    {
        $user = auth()->user();

        if (! $user) {
            return collect();
        }

        return Account::query()
            ->select('accounts.*')
            ->with('provider')
            ->leftJoin('employees', function ($join) use ($user): void {
                $join->on('employees.account_id', '=', 'accounts.id')
                    ->where('employees.user_id', $user->id)
                    ->where('employees.is_active', true);
            })
            ->where(function (Builder $query) use ($user): void {
                $query
                    ->where('accounts.owner_user_id', $user->id)
                    ->orWhereNotNull('employees.id');
            })
            ->where('accounts.is_active', true)
            ->whereIn('accounts.type', [
                AccountType::SaasOwner->value,
                AccountType::Academy->value,
                AccountType::AcademyTeacher->value,
                AccountType::StandaloneTeacher->value,
            ])
            ->where(function (Builder $query): void {
                $query
                    ->where('accounts.type', '!=', AccountType::AcademyTeacher->value)
                    ->orWhereHas('academyTeacherAssignments', fn (Builder $query): Builder => $query->where('is_active', true));
            })
            ->where(function (Builder $query): void {
                $query
                    ->where('accounts.type', AccountType::SaasOwner->value)
                    ->orWhereHas('provider.activeSubscription');
            })
            ->distinct()
            ->orderBy('accounts.id')
            ->get();
    }
}
