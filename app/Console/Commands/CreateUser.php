<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use function Laravel\Prompts\{text, password, info, confirm};

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
            label: "Enter the name of the user",
        );
        $email = text(
            label: "Enter the email of the user",
        );
        $password = password(
            label: "Enter the password for the user",
        );

        $isAdmin = confirm(
            label: "Is this user an admin?",
        );

        $rand = str(Str::random(5))->lower();

        $uid = str($email)
            ->before('@')
            ->substr(0, 3)
            ->lower()
            ->append($rand);

        $user = User::create([
            'uid' => $uid,
            'name' => $name,
            'email' => $email,
            'password' => bcrypt($password),
            'is_admin' => $isAdmin,
        ]);

        info("User {$user->name} created with UID {$user->uid}");
    }
}
