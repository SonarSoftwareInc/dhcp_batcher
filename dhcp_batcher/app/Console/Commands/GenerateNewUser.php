<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class GenerateNewUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:user {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a new user account.';

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
        if ($user !== null)
        {
            $this->error("A user already exists with that email. Please use the 'artisan reset $email' command to reset it.");
            return 2;
        }

        $password = str_random(32);

        $user = new User([
            'name' => $email,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $user->save();

        $this->info("User created. Login with a username of $email and a password of $password.");
    }
}
