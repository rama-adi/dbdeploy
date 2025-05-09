<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use PDO;

class ProvisionNewDatabaseJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        private string $dbName,
        private string $dbUser,
        private string $dbPassword)
    {

    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Remote MySQL root/admin credentials
        $host = env('DB_HOST');
        $port = env('DB_PORT');
        $rootUser = env('DB_ADMIN_USER');
        $rootPass = env('DB_ADMIN_PASSWORD');

        $dsn = "mysql:host=$host;port=$port;charset=utf8mb4";
        $pdo = new \PDO($dsn, $rootUser, $rootPass, [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION
        ]);

        // Create a database
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$this->dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Drop the user if it already exists (optional safety)
        $pdo->exec("DROP USER IF EXISTS '{$this->dbUser}'@'%'");

        // Create a user with limited privileges
        $pdo->exec("CREATE USER '{$this->dbUser}'@'%' IDENTIFIED BY '{$this->dbPassword}'");

        // Grant ONLY privileges to this database
        $pdo->exec("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, DROP, INDEX, ALTER ON `{$this->dbName}`.* TO '{$this->dbUser}'@'%'");

        // Disallow creating other databases by not granting global privileges
        $pdo->exec("FLUSH PRIVILEGES");
    }
}
