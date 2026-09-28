<?php

namespace App\Http\Controllers;

use App\Support\WebsiteUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProviderWebsiteAuthController extends Controller
{
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(WebsiteUrl::path('/login'));
    }
}
