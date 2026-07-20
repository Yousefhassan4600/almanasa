<?php

namespace App\Http\Controllers;

use App\Enums\ProviderType;
use App\Livewire\Website\LoginForm;
use App\Models\Provider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Str;

class AcademySiteController extends Controller
{
    public function __invoke(string $accountSubdomain, ?string $page = null): Response|RedirectResponse
    {
        $provider = Provider::query()
            ->where('subdomain', $accountSubdomain)
            ->where('is_active', true)
            ->where('pause_website', false)
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

        if (in_array($page, ['profile.html', 'my_lessons.html', 'cart.html', 'home_work.html', 'quiz.html', 'home_work_done.html', 'quiz_done.html', 'quiz_review.html'], true) && ! Auth::check()) {
            return redirect('/login');
        }

        if (in_array($page, ['cart.html', 'home_work.html', 'quiz.html', 'home_work_done.html', 'quiz_done.html', 'quiz_review.html'], true) && ! Auth::user()?->studentProfile()->exists()) {
            return redirect('/register');
        }

        $template = $this->templateFor($provider);
        $templatePage = $this->templatePage($page);
        $path = $template['path'].DIRECTORY_SEPARATOR.$templatePage;

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

    private function templatePage(string $page): string
    {
        return match ($page) {
            'quiz_review.html' => 'quiz_done.html',
            default => $page,
        };
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

        $html = $this->injectSingleTeacherPage($html, $provider, $page);
        $html = $this->injectLessonPage($html, $provider, $page);
        $html = $this->injectAssessmentPage($html, $provider, $page);
        $html = $this->injectAttemptResultPage($html, $provider, $page);
        $html = $this->injectCartPage($html, $provider, $page);
        $html = $this->injectWebsiteHeader($html, $provider, $page);
        $html = $this->injectHomeHero($html, $page);
        $html = $this->injectHomeHeroActions($html, $provider, $page);
        $html = $this->injectHomeSubjects($html, $provider);
        $html = $this->injectHomeCta($html, $provider);
        $html = $this->injectSubjectsPage($html, $provider, $page);
        $html = $this->injectTeachersPage($html, $provider, $page);

        $html = $this->injectAuthForm($html, $provider, $page);

        if ($page === 'profile.html') {
            $html = $this->injectProfileData($html, $provider);
        }

        $html = $this->injectWebsiteFooter($html);
        $html = $this->canonicalizePageUrls($html);
        $html = $this->applyWebsiteTheme($html, $provider);
        $html = $this->injectLivewireAssets($html);

        $homeBanner = $page === 'index.html'
            ? $provider->banners()
                ->where('is_active', true)
                ->oldest('sort_order')
                ->oldest('id')
                ->first()
            : null;

        return Blade::render($html, [
            'provider' => $provider,
            'homeBanner' => $homeBanner,
        ]);
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

        $component = $page === 'register.html'
            ? $this->livewireComponent('website.register-form', $provider, $page)
            : $this->livewireComponent('website.login-form', $provider, $page);

        $html = preg_replace(
            '/<form\b(?=[^>]*\bid="'.preg_quote($formId, '/').'")[^>]*>.*?<\/form>/s',
            $component,
            $html,
            1,
        ) ?? $html;

        return $this->removeLegacyAuthPageScripts($html, $page);
    }

    private function livewireComponent(string $name, Provider $provider, string $key): string
    {
        return "@livewire('{$name}', ['providerId' => {$provider->id}], key('{$name}-{$provider->id}-{$key}'))";
    }

    private function removeLegacyAuthPageScripts(string $html, string $page): string
    {
        if (! in_array($page, ['login.html', 'otp.html', 'register.html'], true)) {
            return $html;
        }

        return preg_replace(
            '/<script\b(?![^>]*\bsrc=)[^>]*>.*?(handleFormSubmit|handleOtpSubmit|handleProfileSubmit).*?<\/script>/s',
            '',
            $html,
        ) ?? $html;
    }

    private function themeColor(Provider $provider): string
    {
        return $provider->websitePrimaryColor();
    }

    private function secondaryThemeColor(Provider $provider): string
    {
        return $provider->websiteSecondaryColor();
    }

    private function applyWebsiteTheme(string $html, Provider $provider): string
    {
        $primaryColor = $this->themeColor($provider);
        $secondaryColor = $this->secondaryThemeColor($provider);

        $html = str_replace(
            [
                '#5D3FD3',
                '#FEB008',
                '#4c32b3',
                '#4a32b0',
                '#F59E0B',
                '#E59B00',
            ],
            [
                $primaryColor,
                $primaryColor,
                $secondaryColor,
                $secondaryColor,
                $secondaryColor,
                $secondaryColor,
            ],
            $html,
        );

        $themeStyle = <<<HTML
    <style>
        :root {
            --website-primary-color: {$primaryColor};
            --website-secondary-color: {$secondaryColor};
        }
    </style>
HTML;

        return str_replace('</head>', $themeStyle."\n    </head>", $html);
    }

    private function injectWebsiteHeader(string $html, Provider $provider, string $page): string
    {
        $logoutOnly = $page === 'register.html' && Auth::check() && ! Auth::user()?->studentProfile()->exists();
        $activePage = Str::beforeLast($page, '.html');

        return preg_replace(
            '/<header\b.*?<\/header>/s',
            '<x-website.header :provider="$provider" page="'.e($activePage).'" :logout-only="'.($logoutOnly ? 'true' : 'false').'" />',
            $html,
            1,
        ) ?? $html;
    }

    private function injectWebsiteAuthControls(string $html, Provider $provider): string
    {
        $hasCompletedProfile = Auth::check() && Auth::user()?->studentProfile()->exists();

        $desktop = "@livewire('website.auth-controls', ['providerId' => {$provider->id}, 'placement' => 'desktop'], key('website-auth-controls-desktop-{$provider->id}'))";
        $mobile = "@livewire('website.auth-controls', ['providerId' => {$provider->id}, 'placement' => 'mobile'], key('website-auth-controls-mobile-{$provider->id}'))";

        $html = preg_replace(
            '/<a\s+href="login\.html"\s+class="hidden lg:flex[^"]*"[^>]*>.*?<\/a>/s',
            $desktop,
            $html,
            1,
        ) ?? $html;

        $html = preg_replace(
            '/<div class="space-y-3 mt-8 border-t pt-4 flex flex-col">.*?<\/div>/s',
            $mobile,
            $html,
            1,
        ) ?? $html;

        return $hasCompletedProfile ? $html : $this->removeAuthenticatedHeaderShortcuts($html);
    }

    private function injectHomeCta(string $html, Provider $provider): string
    {
        return preg_replace(
            '/\s*<!-- cta -->\s*<section class="py-16 bg-white" dir="rtl">.*?جاهز للانطلاق نحو التفوق ؟.*?<\/section>/s',
            "\n@livewire('website.home-cta', ['providerId' => {$provider->id}], key('website-home-cta-{$provider->id}'))",
            $html,
            1,
        ) ?? $html;
    }

    private function injectHomeHero(string $html, string $page): string
    {
        if ($page !== 'index.html') {
            return $html;
        }

        return preg_replace(
            '/\s*<!-- hero section -->\s*<section\s+class="relative bg-gradient-to-b from-\[#F3F0FF\] to-white pt-12 pb-6 px-4 md:px-8 overflow-hidden"\s+dir="rtl"\s*>.*?<\/section>/s',
            "\n<x-website.home-hero :provider=\"\$provider\" :banner=\"\$homeBanner\" />\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectHomeHeroActions(string $html, Provider $provider, string $page): string
    {
        if ($page !== 'index.html') {
            return $html;
        }

        $isStandaloneTeacher = $provider->type === ProviderType::StandaloneTeacher;
        $exploreUrl = match (true) {
            ! Auth::check() => '/login',
            $isStandaloneTeacher => '/single_teacher',
            default => '/subjects',
        };
        $themeColor = $this->themeColor($provider);
        $startJourney = Auth::check()
            ? ''
            : '<a href="/login" class="w-full sm:w-auto text-white font-semibold text-lg px-8 py-4 rounded-[12px] shadow-lg transition-all hover:shadow-xl active:scale-95 text-center" style="background-color: '.$themeColor.'">ابدأ رحلتك الآن</a>';

        $actions = '<div class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4">'
            .$startJourney
            .'<a href="'.$exploreUrl.'" class="w-full sm:w-auto bg-transparent border-2 font-semibold text-lg px-8 py-4 rounded-[12px] transition-all active:scale-95 text-center" style="color: '.$themeColor.'; border-color: '.$themeColor.'">استكشف المواد</a>'
            .'</div>';

        return preg_replace(
            '/<div\s+class="flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4 pt-4"\s*>\s*<button\b[^>]*>\s*ابدأ رحلتك الآن\s*<\/button>\s*<button\b[^>]*>\s*استكشف المواد\s*<\/button>\s*<\/div>/s',
            $actions,
            $html,
            1,
        ) ?? $html;
    }

    private function injectWebsiteFooter(string $html): string
    {
        return preg_replace(
            '/<footer\b.*?<\/footer>/s',
            '<x-website.footer :provider="$provider" />',
            $html,
            1,
        ) ?? $html;
    }

    private function injectHomeSubjects(string $html, Provider $provider): string
    {
        return preg_replace(
            '/\s*<!-- subjects section -->\s*<section class="bg-white" dir="rtl">.*?<\/section>\s*(?=<!-- best teachers -->)/s',
            "\n@livewire('website.home-subjects', ['providerId' => {$provider->id}], key('website-home-subjects-{$provider->id}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectSubjectsPage(string $html, Provider $provider, string $page): string
    {
        if ($page !== 'subjects.html') {
            return $html;
        }

        return preg_replace(
            '/<section class="relative bg-white pb-20" dir="rtl">.*?<\/section>\s*<!-- subjects grid -->\s*<section class="py-12 bg-white" dir="rtl">.*?<\/section>/s',
            "\n@livewire('website.subjects-page', ['providerId' => {$provider->id}], key('website-subjects-page-{$provider->id}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectTeachersPage(string $html, Provider $provider, string $page): string
    {
        if ($page !== 'teachers.html') {
            return $html;
        }

        return preg_replace(
            '/\s*<!-- hero -->\s*<section\s+class="bg-gradient-to-br from-purple-50\/60 to-indigo-50\/40 py-12 md:py-20 overflow-hidden"\s+dir="rtl"\s*>.*?<\/section>\s*<!-- teachers grid -->\s*<section class="py-16 bg-white" dir="rtl">.*?<\/section>/s',
            "\n@livewire('website.teachers-page', ['providerId' => {$provider->id}], key('website-teachers-page-{$provider->id}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectSingleTeacherPage(string $html, Provider $provider, string $page): string
    {
        if ($page !== 'single_teacher.html') {
            return $html;
        }

        return preg_replace(
            '/(<\/header>)\s*.*?(?=<footer\b)/s',
            "$1\n@livewire('website.single-teacher-page', ['providerId' => {$provider->id}], key('website-single-teacher-page-{$provider->id}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectLessonPage(string $html, Provider $provider, string $page): string
    {
        if ($page !== 'lesson.html') {
            return $html;
        }

        return preg_replace(
            '/(<\/header>)\s*.*?(?=<footer\b)/s',
            "$1\n@livewire('website.lesson-page', ['providerId' => {$provider->id}], key('website-lesson-page-{$provider->id}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectAssessmentPage(string $html, Provider $provider, string $page): string
    {
        $type = match ($page) {
            'home_work.html' => 'assignment',
            'quiz.html' => 'exam',
            default => null,
        };

        if (! $type) {
            return $html;
        }

        return preg_replace(
            '/(<\/header>)\s*.*?(?=<footer\b)/s',
            "$1\n@livewire('website.assessment-page', ['providerId' => {$provider->id}, 'type' => '{$type}'], key('website-assessment-page-{$provider->id}-{$type}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectAttemptResultPage(string $html, Provider $provider, string $page): string
    {
        $type = match ($page) {
            'home_work_done.html' => 'assignment',
            'quiz_done.html' => 'exam',
            'quiz_review.html' => 'exam',
            default => null,
        };

        if (! $type) {
            return $html;
        }

        $showReview = $page === 'quiz_review.html' ? 'true' : 'false';

        return preg_replace(
            '/(<\/header>)\s*.*?(?=<footer\b)/s',
            "$1\n@livewire('website.attempt-result-page', ['providerId' => {$provider->id}, 'type' => '{$type}', 'showReview' => {$showReview}], key('website-attempt-result-page-{$provider->id}-{$type}-{$page}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectCartPage(string $html, Provider $provider, string $page): string
    {
        if ($page !== 'cart.html') {
            return $html;
        }

        return preg_replace(
            '/(<\/header>)\s*.*?(?=<footer\b)/s',
            "$1\n@livewire('website.cart-page', ['providerId' => {$provider->id}], key('website-cart-page-{$provider->id}'))\n",
            $html,
            1,
        ) ?? $html;
    }

    private function injectLivewireAssets(string $html): string
    {
        $html = str_replace('</head>', "    @livewireStyles\n    </head>", $html);

        return str_replace('</body>', "        @livewireScripts\n    </body>", $html);
    }

    private function removeAuthenticatedHeaderShortcuts(string $html): string
    {
        $html = preg_replace('/<a\s+href="cart\.html"\s+class="text-gray-700[^"]*"[^>]*>\s*<svg.*?<\/svg>\s*<\/a>/s', '', $html) ?? $html;
        $html = preg_replace('/<button\s+class="text-gray-700[^"]*"[^>]*>\s*<svg.*?<\/svg>\s*<\/button>/s', '', $html, 1) ?? $html;

        return preg_replace('/<a\s+href="profile\.html"\s+class="text-gray-700[^"]*"[^>]*>\s*<svg.*?<\/svg>\s*<\/a>/s', '', $html) ?? $html;
    }

    private function injectIncompleteProfileHeader(string $html, Provider $provider): string
    {
        $logoutControls = "@livewire('website.auth-controls', ['providerId' => {$provider->id}, 'placement' => 'desktop', 'logoutOnly' => true], key('website-register-logout-controls-{$provider->id}'))";

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
            '<div class="flex items-center gap-2 lg:gap-4" dir="rtl">'.$logoutControls.'</div></nav>',
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
