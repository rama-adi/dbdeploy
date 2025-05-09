<?php

namespace App\Console\Commands;

use App\Jobs\ProvisionNewDatabaseJob;
use Illuminate\Console\Command;
use function Laravel\Prompts\{text, password};

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
        $dbName = text(
            label: "Enter the name of the database",
        );
        $dbUser = text(
            label: "Enter the name of the database user",
        );
        $dbPassword = password(
            label: "Enter the password for the database user",
        );
        dispatch_sync(new ProvisionNewDatabaseJob(
            dbName: $dbName,
            dbUser: $dbUser,
            dbPassword: $dbPassword,
        ));

        $this->info("Database created successfully");
        $this->info("Database name: {$dbName}");
        $this->info("Database user: {$dbUser}");
        $this->info("Database password: {$dbPassword}");
        $this->info("Connection IP address/host: " . env('DB_HOST') . ":" . env('DB_PORT'));
        $this->info("PhpMyAdmin URL: https://phpmyadmin.yucca-ai.xyz (use the same detail as above)");
    }
}
