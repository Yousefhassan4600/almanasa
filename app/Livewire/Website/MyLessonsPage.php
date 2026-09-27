<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Codes\RedeemProviderCode;
use App\Actions\StudentPortal\Students\LoadMyLessons;
use App\Models\Provider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Component;

class MyLessonsPage extends Component
{
    #[Locked]
    public int $providerId;

    public string $code = '';

    public bool $codeRedeemed = false;

    private LoadMyLessons $loadMyLessons;

    public function boot(LoadMyLessons $loadMyLessons): void
    {
        $this->loadMyLessons = $loadMyLessons;
    }

    public function render(): mixed
    {
        $provider = Provider::query()
            ->with('owner:id,first_name,last_name')
            ->findOrFail($this->providerId);
        $data = $this->loadMyLessons->handle($provider, Auth::user());

        return view('livewire.website.my-lessons-page', [
            'provider' => $provider,
            ...$data,
        ]);
    }

    public function redeemCode(RedeemProviderCode $redeemProviderCode): void
    {
        $this->codeRedeemed = false;
        $this->validate(['code' => ['required', 'string', 'max:255']]);

        if (! Auth::check()) {
            $this->addError('code', 'يجب تسجيل الدخول أولاً.');

            return;
        }

        try {
            $redeemProviderCode->handle(Provider::query()->findOrFail($this->providerId), Auth::user(), $this->code);
        } catch (ValidationException $exception) {
            $this->addError('code', collect($exception->errors())->flatten()->first());

            return;
        }

        $this->code = '';
        $this->codeRedeemed = true;
    }
}
