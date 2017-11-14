<?php

namespace App\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class TestSonarCredentials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sonar:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the credentials in the .env file.';

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
        $client = App::make('HttpClient');

        try {
            $client->get(config("sonar.url") . "/api/v1/accounts?limit=1",[
                'auth' => [
                    config("sonar.username"),
                    config("sonar.password"),
                ]
            ]);
        }
        catch (Exception $e)
        {
            $this->error("The request failed with " . $e->getMessage());
            return 1;
        }

        $this->info("Success!");
    }
}
