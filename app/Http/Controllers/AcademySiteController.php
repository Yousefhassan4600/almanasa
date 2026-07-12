<?php

namespace App\Http\Controllers;

use App\Enums\AccountStatus;
use App\Enums\AccountType;
use App\Models\Account;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class AcademySiteController extends Controller
{
    public function __invoke(string $accountSubdomain, ?string $page = null): Response
    {
        $account = Account::query()
            ->where('subdomain', $accountSubdomain)
            ->where('status', AccountStatus::Active->value)
            ->whereIn('type', [
                AccountType::Academy->value,
                AccountType::StandaloneTeacher->value,
            ])
            ->firstOrFail();

        $page = $this->normalizePage($page);
        $template = $this->templateFor($account);
        $path = $template['path'].DIRECTORY_SEPARATOR.$page;

        abort_unless(is_file($path), 404);

        $html = file_get_contents($path);

        abort_if($html === false, 404);

        return response($this->prepareHtml($html, $account, $template['asset_path']), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }

    /**
     * @return array{path: string, asset_path: string}
     */
    private function templateFor(Account $account): array
    {
        return match ($account->type) {
            AccountType::Academy => [
                'path' => config('almanasa.academy_template_path'),
                'asset_path' => config('almanasa.academy_template_asset_path'),
            ],
            AccountType::StandaloneTeacher => [
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

    private function prepareHtml(string $html, Account $account, string $assetPath): string
    {
        $assetPath = rtrim($assetPath, '/').'/';

        return str_replace(
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
                '<title>'.e($account->name).'</title>',
                e($account->name),
            ],
            $html,
        );
    }
}
