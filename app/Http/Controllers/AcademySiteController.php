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
            ->where('type', AccountType::Academy->value)
            ->firstOrFail();

        $page = $this->normalizePage($page);
        $path = config('almanasa.academy_template_path').DIRECTORY_SEPARATOR.$page;

        abort_unless(is_file($path), 404);

        $html = file_get_contents($path);

        abort_if($html === false, 404);

        return response($this->prepareHtml($html, $account), 200, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
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

    private function prepareHtml(string $html, Account $account): string
    {
        $assetPath = rtrim(config('almanasa.academy_template_asset_path'), '/').'/';

        return str_replace(
            [
                'href="assets/',
                'src="assets/',
                '<title>Document</title>',
                'Edu Learning',
            ],
            [
                'href="'.$assetPath,
                'src="'.$assetPath,
                '<title>'.e($account->name).'</title>',
                e($account->name),
            ],
            $html,
        );
    }
}
