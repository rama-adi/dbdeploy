<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PDO;

class DeprovisionDatabaseJob implements ShouldQueue
{
    use Queueable;


    public function __construct(
        private string $dbName,
        private string $dbUser
    )
    {
        $this->dbName = preg_replace('/[^a-zA-Z0-9_]/', '_', $dbName);
        $this->dbUser = preg_replace('/[^a-zA-Z0-9_]/', '_', $dbUser);
    }

    public function handle()
    {
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $rootUser = env('DB_ADMIN_USER');
        $rootPass = env('DB_ADMIN_PASSWORD');


        $pdo = new PDO("mysql:host=$host;port=$port;charset=utf8mb4", $rootUser, $rootPass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        // Drop user first (revokes all privileges)
        $pdo->exec("DROP USER IF EXISTS '{$this->dbUser}'@'%'");

        // Drop database
        $pdo->exec("DROP DATABASE IF EXISTS `{$this->dbName}`");
    }
}
