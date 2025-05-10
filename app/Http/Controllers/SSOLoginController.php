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
        $now = time();

        // Look for a valid session with same username and password
        $latestSession = $user->phpmyadminSessions()
            ->where('username', $databaseInfo->username)
            ->where('password', $databaseInfo->password)
            ->orderByDesc('expired_at')
            ->first();

        if ($latestSession && (int) $latestSession->expired_at > $now) {
            // Reuse token and its expiry
            $token = $latestSession->token;
            $expiry = (int) $latestSession->expired_at;
        } else {
            // Create new token and expiry
            $token = Str::random(60);
            $expiry = $now + 3600; // 1 hour

            $user->phpmyadminSessions()->create([
                'expired_at' => $expiry, // UNIX timestamp
                'token'      => $token,
                'username'   => $databaseInfo->username,
                'password'   => $databaseInfo->password,
            ]);
        }

        // Set PMA_TOKEN cookie to match session expiration
        setcookie(
            name: 'PMA_TOKEN',
            value: $token,
            expires_or_options: $expiry,
            path: '/',
            domain: '.yucca-ai.xyz',
            httponly: true,
            secure: true
        );

        return Inertia::location(
            'https://phpmyadmin.yucca-ai.xyz/index.php?route=/database/structure&db=' . $databaseInfo->database_name
        );
    }


}
