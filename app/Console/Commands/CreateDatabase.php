<?php

namespace App\Console\Commands;

use App\Jobs\ProvisionNewDatabaseJob;
use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\{text, password, confirm, select, info};

class CreateDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-database';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $assignToUser = confirm(
            label: "Do you want to assign the database to a user?",
        );

        $uid = null;
        $user = null;

        if ($assignToUser) {
            $users = User::all()->pluck("name", "uid");

            $uid = select(
                label: "Select a user to assign the database to",
                options: $users,
            );

            info("Database will be assigned to {$users[$uid]}");

            $user = User::where('uid', $uid)->first();
        } else {
            info("Database will not be assigned to any user");
        }

        $dbName = text(
            label: "Enter the name of the database",
            hint: "Max length is 24 characters",
        );

        $name = text(
            label: "What should the name be in the dashboard",
            hint: "This will only be displayed in the dashboard for easy identification",
        );


        // Apply prefix if a user is assigned
        if ($assignToUser && $user) {
            $prefix = str($uid)->substr(0, 8)->lower()->append('_');

            $dbName = $prefix->append(str($dbName)->lower())->substr(0, 24);
        } else {
            $dbName = str($dbName)->lower()->substr(0, 24);
        }

        $dbPassword = password(
            label: "Enter the password for the database user",
        );

        dispatch_sync(new ProvisionNewDatabaseJob(
            dbName: $dbName,
            dbUser: $dbName,
            dbPassword: $dbPassword,
        ));

        if ($assignToUser && $user) {
            $user->databaseInfos()->create([
                'user_id' => $user->id,
                'username' => $dbName,
                'name' => $name,
                'database_name' => $dbName,
                'password' => $dbPassword,
            ]);
            info("Database assigned to {$user->name}");
        }

        info("Database created successfully");
        info("Database name: {$dbName}");
        info("Database user: {$dbName}");
        info("Database password: {$dbPassword}");
        info("Connection IP address/host: " . env('DB_HOST') . ":" . env('DB_PORT'));
        info("PhpMyAdmin URL: https://phpmyadmin.yucca-ai.xyz (use the same detail as above)");
    }

}
