<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class ResetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset a user password.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $email = trim($this->argument('email'));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $this->error("That is not a valid email address.");
            return 1;
        }

        $user = User::where('email','=',$email)->first();
        if ($user === null)
        {
            $this->error("No user exists with that email address. You can use 'php artisan make:user $email' to create one.'");
            return 2;
        }

        $password = str_random(32);

        $user->password = Hash::make($password);

        $user->save();

        $this->info("User password reset. The new password is $password.");
    }
}
