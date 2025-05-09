<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-user';

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
        $name = text(
            label: "Enter your name",
        );
        $email = text(
            label: "Enter your email",
        );
        $password = password(
            label: "Enter your password",
        );


        $uid = str($email)
            ->replaceMatches('/[^a-zA-Z0-9]/', '')
            ->substr(0, 4)
            // random a-z 0-9
            ->append(substr(str_shuffle('abcdefghijklmnopqrstuvwxyz0123456789'), 0, 6))
            ->lower();

        User::create([
            'uid' => $uid,
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
        ]);

        $this->info('User created successfully!');
    }
}
