<?php

namespace App\Support;

use App\Models\Account;
use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuditLogger
{
    /**
     * @var array<int, string>
     */
    private const SENSITIVE_KEYS = [
        'password',
        'otp',
        'remember_token',
        'deleted_by',
    ];

    public function record(string $action, Model $model): void
    {
        if ($model instanceof AuditLog || ! Schema::hasTable((new AuditLog)->getTable())) {
            return;
        }

        if (! $model->exists && ! in_array($action, ['deleted', 'forceDeleted'], true)) {
            return;
        }

        [$oldValues, $newValues] = match ($action) {
            'created' => [[], $this->sanitizeAttributes($model->getAttributes())],
            'updated' => $this->updatedValues($model),
            'deleted', 'forceDeleted' => [$this->sanitizeAttributes($model->getOriginal()), []],
            'restored' => [[], $this->sanitizeAttributes($model->getAttributes())],
            default => [[], []],
        };

        if ($action === 'updated' && $oldValues === [] && $newValues === []) {
            return;
        }

        AuditLog::query()->create([
            'provider_id' => $this->providerId($model),
            'user_id' => Auth::id(),
            'action' => $action,
            'auditable_type' => $model::class,
            'auditable_id' => $model->getKey(),
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
        ]);
    }

    /**
     * @return array{0: array<string, mixed>, 1: array<string, mixed>}
     */
    private function updatedValues(Model $model): array
    {
        $changedAttributes = Arr::except($model->getChanges(), ['updated_at']);

        if ($changedAttributes === []) {
            return [[], []];
        }

        return [
            $this->sanitizeAttributes(
                Arr::only($model->getOriginal(), array_keys($changedAttributes)),
            ),
            $this->sanitizeAttributes($changedAttributes),
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function sanitizeAttributes(array $attributes): array
    {
        foreach (self::SENSITIVE_KEYS as $key) {
            if (array_key_exists($key, $attributes)) {
                $attributes[$key] = '[redacted]';
            }
        }

        foreach ($attributes as $key => $value) {
            if (! is_string($value) || ! str($value)->trim()->startsWith(['{', '['])) {
                continue;
            }

            $decodedValue = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $attributes[$key] = $decodedValue;
            }
        }

        return $attributes;
    }

    private function providerId(Model $model): ?int
    {
        if (array_key_exists('provider_id', $model->getAttributes())) {
            return $model->getAttribute('provider_id');
        }

        $account = $this->currentAccount();

        return $account?->provider_id;
    }

    private function currentAccount(): ?Account
    {
        if (! app()->bound('request')) {
            return null;
        }

        $account = request()->attributes->get('current_account');

        if ($account instanceof Account) {
            return $account;
        }

        if (! request()->hasSession()) {
            return null;
        }

        $accountId = (int) request()->session()->get('current_account_id');

        if ($accountId <= 0) {
            return null;
        }

        return Account::query()->find($accountId);
    }
}
