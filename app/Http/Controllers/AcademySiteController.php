<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\ProviderType;
use App\Livewire\Website\LoginForm;
use App\Models\City;
use App\Models\Country;
use App\Models\EducationStage;
use App\Models\Grade;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AcademySiteController extends Controller
{
    public function __invoke(string $accountSubdomain, ?string $page = null): Response|RedirectResponse
    {
        $provider = Provider::query()
            ->where('subdomain', $accountSubdomain)
            ->where('is_active', true)
            ->where('website_enabled', true)
            ->whereHas('activeSubscription')
            ->whereIn('type', [
                ProviderType::Academy,
                ProviderType::StandaloneTeacher,
            ])
            ->firstOrFail();

        $requestedPage = trim($page ?? '', '/');

        if (preg_match('/^[A-Za-z0-9_\-\/]+\.html$/', $requestedPage) === 1) {
            return redirect($this->canonicalPageUrl($requestedPage), 301);
        }

        $page = $this->normalizePage($page);

        if (in_array($page, ['login.html', 'otp.html'], true) && Auth::check() && Auth::user()?->studentProfile()->exists()) {
            return redirect('/');
        }

        if ($page === 'otp.html' && ! session()->has(LoginForm::challengeKeyFor($provider->id))) {
            return redirect('/login');
        }

        if ($page === 'register.html') {
            if (! Auth::check()) {
                return redirect('/login');
            }

            if (Auth::user()?->studentProfile()->exists()) {
                return redirect('/');
            }
        }

        if ($page === 'profile.html' && ! Auth::check()) {
            abort(403);
        }

        $template = $this->templateFor($provider);
        $path = $template['path'].DIRECTORY_SEPARATOR.$page;

        abort_unless(is_file($path), 404);

        $html = file_get_contents($path);

        abort_if($html === false, 404);

        return response($this->prepareHtml($html, $provider, $template['asset_path'], $page), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * @return array{path: string, asset_path: string}
     */
    private function templateFor(Provider $provider): array
    {
        return match ($provider->type) {
            ProviderType::Academy => [
                'path' => config('almanasa.academy_template_path'),
                'asset_path' => config('almanasa.academy_template_asset_path'),
            ],
            ProviderType::StandaloneTeacher => [
                'path' => config('almanasa.teacher_template_path'),
                'asset_path' => config('almanasa.teacher_template_asset_path'),
            ],
            default => abort(404),
        };
    }

    private function normalizePage(?string $page): string
    {
        $page = trim($page ?: 'index.html', '/');

        abort_if($page === '' || Str::contains($page, ['..', '\\']), 404);

        if (! Str::endsWith($page, '.html')) {
            $page .= '.html';
        }

        abort_unless(preg_match('/^[A-Za-z0-9_\-\/]+\.html$/', $page) === 1, 404);

        return $page;
    }

    private function prepareHtml(string $html, Provider $provider, string $assetPath, string $page): string
    {
        $assetPath = rtrim($assetPath, '/').'/';

        $html = str_replace(
            [
                'href="assets/',
                'href="./assets/',
                'src="assets/',
                'src="./assets/',
                '<title>Document</title>',
                'Edu Learning',
            ],
            [
                'href="'.$assetPath,
                'href="'.$assetPath,
                'src="'.$assetPath,
                'src="'.$assetPath,
                '<title>'.e($provider->name).'</title>',
                e($provider->name),
            ],
            $html,
        );

        $html = $this->injectWebsiteAuthControls($html, $provider);

        if ($page === 'register.html' && Auth::check() && ! Auth::user()?->studentProfile()->exists()) {
            $html = $this->injectIncompleteProfileHeader($html);
        }

        $html = $this->injectAuthForm($html, $provider, $page);

        if ($page === 'profile.html') {
            $html = $this->injectProfileData($html, $provider);
        }

        return $this->canonicalizePageUrls($html);
    }

    private function injectAuthForm(string $html, Provider $provider, string $page): string
    {
        if (! in_array($page, ['login.html', 'otp.html', 'register.html'], true)) {
            return $html;
        }

        $formId = match ($page) {
            'register.html' => 'profile-completion-form',
            'otp.html' => 'otp-verification-form',
            default => 'phone-verification-form',
        };

        $formHtml = $page === 'register.html'
            ? $this->registerFormHtml($provider)
            : $this->loginFormHtml($provider, $page === 'otp.html');

        return preg_replace(
            '/<form id="'.preg_quote($formId, '/').'".*?<\\/form>/s',
            $formHtml,
            $html,
            1,
        ) ?? $html;
    }

    private function loginFormHtml(Provider $provider, bool $otpSent): string
    {
        return view('livewire.website.login-form', [
            'provider' => $provider,
            'themeColor' => $this->themeColor($provider),
            'developmentOtp' => config('almanasa.website_otp_code'),
            'otpSent' => $otpSent,
        ])->render();
    }

    private function registerFormHtml(Provider $provider): string
    {
        return view('livewire.website.register-form', [
            'provider' => $provider,
            'themeColor' => $this->themeColor($provider),
            'countries' => Country::query()->orderBy('name')->get(),
            'cities' => City::query()->orderBy('name')->get(),
            'educationStages' => EducationStage::query()->orderBy('sort_order')->get(),
            'grades' => Grade::query()->orderBy('sort_order')->get(),
            'genders' => Gender::options(),
        ])->render();
    }

    private function themeColor(Provider $provider): string
    {
        return $provider->type === ProviderType::StandaloneTeacher ? '#FEB008' : '#5D3FD3';
    }

    private function injectWebsiteAuthControls(string $html, Provider $provider): string
    {
        $themeColor = $this->themeColor($provider);
        $hasCompletedProfile = Auth::check() && Auth::user()?->studentProfile()->exists();

        if ($hasCompletedProfile) {
            $desktop = '<a href="/profile" class="hidden lg:flex bg-transparent text-gray-700 border-2 border-gray-200 py-2.5 px-4 text-sm lg:text-base font-semibold rounded-[12px] transition-all hover:bg-gray-50 active:scale-95 items-center justify-center whitespace-nowrap">الملف الشخصي</a>'
                .'<form method="POST" action="/logout" class="hidden lg:flex">'.csrf_field().'<button type="submit" class="bg-transparent text-red-600 border-2 border-red-100 py-2.5 px-4 text-sm lg:text-base font-semibold rounded-[12px] transition-all hover:bg-red-50 active:scale-95 whitespace-nowrap">تسجيل الخروج</button></form>';
            $mobile = '<a href="/profile" class="w-full text-center bg-transparent text-gray-700 border-2 border-gray-200 py-3 px-6 rounded-[12px] font-semibold transition-all hover:bg-gray-50">الملف الشخصي</a>'
                .'<form method="POST" action="/logout" class="w-full">'.csrf_field().'<button type="submit" class="w-full text-center bg-transparent text-red-600 border-2 border-red-100 py-3 px-6 rounded-[12px] font-semibold transition-all hover:bg-red-50">تسجيل الخروج</button></form>';
        } else {
            $desktop = '<a href="/login" class="hidden lg:flex bg-transparent text-['.$themeColor.'] border-2 border-['.$themeColor.'] py-2.5 px-4 text-sm lg:text-base font-semibold rounded-[12px] transition-all hover:bg-gray-50 active:scale-95 items-center justify-center whitespace-nowrap">تسجيل الدخول</a>';
            $mobile = '<a href="/login" class="w-full text-center bg-transparent text-['.$themeColor.'] border-2 border-['.$themeColor.'] py-3 px-6 rounded-[12px] font-semibold transition-all hover:bg-gray-50">تسجيل الدخول</a>';
        }

        $html = preg_replace('/<a href="register\.html".*?<\\/a>\\s*<a href="login\.html".*?<\\/a>/s', $desktop, $html, 1) ?? $html;

        $html = preg_replace('/<a href="register\.html".*?<\\/a>\\s*<a href="login\.html".*?<\\/a>/s', $mobile, $html, 1) ?? $html;

        return $hasCompletedProfile ? $html : $this->removeAuthenticatedHeaderShortcuts($html);
    }

    private function removeAuthenticatedHeaderShortcuts(string $html): string
    {
        $html = preg_replace('/<a href="cart\.html" class="text-gray-700.*?<\\/a>/s', '', $html) ?? $html;
        $html = preg_replace('/<button class="text-gray-700.*?<\\/button>/s', '', $html, 1) ?? $html;
        $html = preg_replace('/<a href="profile\.html" class="text-gray-700.*?<\\/a>/s', '', $html) ?? $html;

        return preg_replace('/<a href="profile\.html"\\s+class="w-full.*?<\\/a>/s', '', $html) ?? $html;
    }

    private function injectIncompleteProfileHeader(string $html): string
    {
        $logoutForm = '<form method="POST" action="/logout" class="flex">'.csrf_field().'<button type="submit" class="bg-transparent text-red-600 border-2 border-red-100 py-2.5 px-4 text-sm lg:text-base font-semibold rounded-[12px] transition-all hover:bg-red-50 active:scale-95 whitespace-nowrap">تسجيل الخروج</button></form>';

        $html = preg_replace(
            '/<button id="openSidebarBtn".*?<\\/button>/s',
            '',
            $html,
            1,
        ) ?? $html;

        $html = preg_replace(
            '/<ul class="hidden lg:flex.*?<\\/ul>\\s*(?=<div class="flex items-center gap-2 lg:gap-4" dir="rtl">)/s',
            '<div class="hidden lg:block"></div>',
            $html,
            1,
        ) ?? $html;

        $html = preg_replace(
            '/<div class="flex items-center gap-2 lg:gap-4" dir="rtl">.*?<\\/div>\\s*<\\/nav>/s',
            '<div class="flex items-center gap-2 lg:gap-4" dir="rtl">'.$logoutForm.'</div></nav>',
            $html,
            1,
        ) ?? $html;

        return preg_replace(
            '/<div id="sidebarOverlay".*?<\\/header>/s',
            '</header>',
            $html,
            1,
        ) ?? $html;
    }

    private function injectProfileData(string $html, Provider $provider): string
    {
        $user = Auth::user()?->loadMissing([
            'studentProfile.city',
            'studentProfile.education_stage',
            'studentProfile.grade',
        ]);

        if (! $user) {
            return $html;
        }

        $profile = $user->studentProfile;
        $avatar = $profile?->avatar
            ? asset('storage/'.$profile->avatar)
            : 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=200&h=200';

        $html = preg_replace(
            '/<img src="https:\\/\\/images\\.unsplash\\.com\\/photo-1534528741775-53994a69daeb\\?auto=format&fit=crop&q=80&w=200&h=200"\\s+alt="Avatar" class="w-20 h-20 rounded-full object-cover ring-4 ring-purple-50">/s',
            '<img src="'.e($avatar).'" alt="Avatar" class="w-20 h-20 rounded-full object-cover ring-4 ring-purple-50">',
            $html,
            1,
        ) ?? $html;

        $replacements = [
            'أهلاً بك، أحمد' => 'أهلاً بك، '.e($user->name ?: 'طالب'),
            'طالب في الصف العاشر' => e($profile?->grade?->name ?? 'طالب'),
            '+20 01015620825' => e(trim(($user->dial_country_code ?? '').' '.$user->phone)),
            'Mona Physics Platform' => e($provider->name),
            'Primary' => e($profile?->education_stage?->name ?? 'غير محدد'),
            'Two' => e($profile?->grade?->name ?? 'غير محدد'),
            'el tahrer' => e($profile?->school_name ?? 'غير محدد'),
            'Cairo' => e($profile?->city?->name ?? 'غير محدد'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $html);
    }

    private function canonicalizePageUrls(string $html): string
    {
        return preg_replace_callback(
            '/(?<quote>["\'])(?:\.?\/)?(?<page>[A-Za-z0-9_\-][A-Za-z0-9_\-\/]*)\.html(?<suffix>[?#][^"\']*)?\k<quote>/',
            fn (array $matches): string => $matches['quote'].$this->canonicalPageUrl($matches['page'].'.html').($matches['suffix'] ?? '').$matches['quote'],
            $html,
        ) ?? $html;
    }

    private function canonicalPageUrl(string $page): string
    {
        $page = Str::beforeLast($page, '.html');

        return $page === 'index' ? '/' : '/'.$page;
    }
}
