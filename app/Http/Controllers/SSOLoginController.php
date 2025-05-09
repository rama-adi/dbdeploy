<?php

namespace App\Http\Controllers;

use App\Models\DatabaseInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SSOLoginController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, DatabaseInfo $databaseInfo)
    {
        // check for existing nonce that are not expired, if no create one
        $nonce = $databaseInfo
            ->loginNonces()
            ->where('expires_at', '>', now())
            ->first();

        if (!$nonce) {
            $nonce = $databaseInfo->loginNonces()->create([
                'nonce' => Str::random(40),
                'expires_at' => now()->addHour(),
            ]);
        }

        // set cookie on phpmyadmin.yucca-ai.xyz
        // 1h expiry
        setcookie(
            name: 'NONCE',
            value: $nonce->nonce,
            expires_or_options: time() + 3600,
            path: '/',
            domain: 'phpmyadmin.yucca-ai.xyz',
            httponly: true,
            secure: true
        );

        return redirect()->away('https://phpmyadmin.yucca-ai.xyz/index.php?route=/database/structure&db=' . $databaseInfo->database_name);
    }
}
