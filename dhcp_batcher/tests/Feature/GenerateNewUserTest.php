<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GenerateNewUserTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function generating_a_new_user_succeeds()
    {
        $this->assertNull(User::where('email','=','test@example.com')->first());

        $code = Artisan::call("make:user", [
            'email' => 'test@example.com',
        ]);

        $this->assertEquals(0, $code);
        $this->assertNotNull(User::where('email','=','test@example.com')->first());
    }

    /**
     * @test
     */
    public function an_invalid_email_address_fails()
    {
        $code = Artisan::call("make:user", [
            'email' => 'foobar',
        ]);

        $this->assertEquals(1, $code);
        $this->assertNull(User::where('email','=','foobar')->first());
    }

    /**
     * @test
     */
    public function generating_a_duplicate_user_fails()
    {
        factory(User::class)->create(['email' => 'test@example.com']);

        $code = Artisan::call("make:user", [
            'email' => 'test@example.com',
        ]);

        $this->assertEquals(2, $code);
        $this->assertCount(1, User::where('email','=','test@example.com')->get());
    }
}
