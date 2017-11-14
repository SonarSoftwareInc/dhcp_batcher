<?php

namespace Tests\Unit;

use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResetUserPasswordTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @test
     */
    public function resetting_a_password_changes_it()
    {
        $user = factory(User::class)->create(['password' => bcrypt('secret')]);

        $this->assertTrue(password_verify('secret', $user->password));

        $result = Artisan::call("reset",[
            'email' => $user->email
        ]);

        $user = $user->fresh();

        $this->assertFalse(password_verify($result, $user->password));
    }

    /**
     * @test
     */
    public function an_invalid_email_fails()
    {

        $result = Artisan::call("reset",[
            'email' => 'foo'
        ]);

        $this->assertEquals(1, $result);
    }

    /**
     * @test
     */
    public function a_valid_email_with_no_user_fails()
    {

        $result = Artisan::call("reset",[
            'email' => 'test@example.com'
        ]);

        $this->assertEquals(2, $result);
    }
}
