<?php

namespace App\Http\Controllers;

use App\Models\DatabaseInfo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SSOLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function viaDirect(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $databaseInfo = DatabaseInfo::where('username', $request->username)
            ->where('password', $request->password)
            ->firstOrFail();

        return $this->login(
            $request->user(),
            $databaseInfo
        );
    }

    public function viaID(Request $request, DatabaseInfo $databaseInfo)
    {

        return $this->login(
            $request->user(),
            $databaseInfo
        );
    }

    /**
     * @param Request $request
     * @param DatabaseInfo $databaseInfo
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function login(User $user, DatabaseInfo $databaseInfo): \Symfony\Component\HttpFoundation\Response
    {
        $token = Str::random(60);
        $user
            ->phpmyadminSessions()
            ->create([
                'expired_at' => now()->addHour(),
                'token' => Str::random(60),
                'username' => $databaseInfo->username,
                'password' => $databaseInfo->password,
            ]);


        // set cookie on phpmyadmin.yucca-ai.xyz
        // 1h expiry
        setcookie(
            name: 'PMA_TOKEN',
            value: $token,
            expires_or_options: time() + 3600,
            path: '/',
            domain: '.yucca-ai.xyz',
            httponly: true,
            secure: true
        );

        return Inertia::location('https://phpmyadmin.yucca-ai.xyz/index.php?route=/database/structure&db=' . $databaseInfo->database_name);
    }
}
