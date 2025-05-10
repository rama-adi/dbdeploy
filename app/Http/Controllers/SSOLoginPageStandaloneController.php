<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class SSOLoginPageStandaloneController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $loggedOut = false;
        if ($request->query('sso_is_logout')) {
            $loggedOut = true;

            // set PMA_TOKEN to past so it will be invalidated
            setcookie(
                name: 'PMA_TOKEN',
                value: '',
                expires_or_options: time() - 3600,
                path: '/',
                domain: env('COOKIE_DOMAIN'),
                httponly: true,
                secure: true
            );
        }

        $databases = $request->user()
            ->load(['databaseInfos'])
            ->databaseInfos
            ->map(function ($databaseInfo) {
                return [
                    'id' => $databaseInfo->id,
                    'name' => $databaseInfo->name,
                    'databaseName' => $databaseInfo->database_name,
                    'username' => $databaseInfo->username,
                ];
            });

        return Inertia::render('sso-login-page-standalone', [
            'databases' => $databases,
            'loggedOut' => $loggedOut,
        ]);
    }
}
