<?php

namespace App\Livewire\Website;

use App\Actions\StudentPortal\Auth\ManageWebsiteOtp;
use App\Actions\StudentPortal\Layout\LoadProviderTheme;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;
use Livewire\Component;

class LoginForm extends Component
{
    #[Locked]
    public int $providerId;

    public string $dialCountryCode = '+20';

    public string $phone = '';

    public string $otp = '';

    public string $otp1 = '';

    public string $otp2 = '';

    public string $otp3 = '';

    public string $otp4 = '';

    public bool $otpSent = false;

    private ManageWebsiteOtp $manageWebsiteOtp;

    private LoadProviderTheme $loadProviderTheme;

    public function boot(ManageWebsiteOtp $manageWebsiteOtp, LoadProviderTheme $loadProviderTheme): void
    {
        $this->manageWebsiteOtp = $manageWebsiteOtp;
        $this->loadProviderTheme = $loadProviderTheme;
    }

    public function mount(int $providerId): void
    {
        $this->providerId = $providerId;
        $this->otpSent = session()->has($this->manageWebsiteOtp->challengeKey($this->providerId));
    }

    public function sendOtp(): mixed
    {
        $data = $this->validate([
            'dialCountryCode' => ['required', 'regex:/^\+[0-9]{1,4}$/'],
            'phone' => ['required', 'regex:/^[0-9]{7,15}$/'],
        ]);

        $otpData = $this->manageWebsiteOtp->send($this->providerId, $data['dialCountryCode'], $data['phone'], request()->ip());

        $this->phone = $otpData['phone'];
        $this->dialCountryCode = $otpData['dialCountryCode'];
        $this->otpSent = true;
        $this->resetOtpFields();

        return $this->redirect('/otp', navigate: false);
    }

    public function verify(): mixed
    {
        $this->otp = $this->otp !== '' ? $this->otp : $this->otp1.$this->otp2.$this->otp3.$this->otp4;

        $this->validate([
            'otp' => ['required', 'digits_between:4,8'],
        ]);

        $provider = $this->loadProviderTheme->handle($this->providerId)['provider'];
        $account = $this->manageWebsiteOtp->verify(
            $provider,
            $this->otp,
            request()->ip(),
        );

        Auth::login($account->owner);
        session()->regenerate();
        session()->put('current_account_id', $account->id);
        session()->put('current_provider_id', $provider->id);
        session()->forget($this->manageWebsiteOtp->challengeKey($this->providerId));

        $account->owner->loadMissing('studentProfile');

        if (! $account->owner->studentProfile) {
            return $this->redirect('/register', navigate: false);
        }

        return $this->redirect('/', navigate: false);
    }

    public function resetChallenge(): void
    {
        session()->forget($this->manageWebsiteOtp->challengeKey($this->providerId));
        $this->otpSent = false;
        $this->resetOtpFields();
    }

    public function render(): mixed
    {
        $theme = $this->loadProviderTheme->handle($this->providerId);

        return view('livewire.website.login-form', [
            'provider' => $theme['provider'],
            'themeColor' => $theme['themeColor'],
            'developmentOtp' => config('almanasa.website_otp_code'),
        ]);
    }

    public static function challengeKeyFor(int $providerId): string
    {
        return 'website_auth_challenge_'.$providerId;
    }

    private function resetOtpFields(): void
    {
        $this->reset('otp', 'otp1', 'otp2', 'otp3', 'otp4');
    }
}
