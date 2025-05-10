<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $databases = $request->user()
            ->load(['databaseInfos'])
            ->databaseInfos
            ->map(function ($databaseInfo) {
                return [
                    'id' => $databaseInfo->id,
                    'name' => $databaseInfo->name,
                    'databaseName' => $databaseInfo->database_name,
                ];
            });
        return Inertia::render('dashboard', [
            'databases' => $databases,
        ]);
    }
}
