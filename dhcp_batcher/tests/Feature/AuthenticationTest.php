<?php

namespace Tests\Feature;

use App\Http\Middleware\VerifyCsrfToken;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use DatabaseTransactions;

    public function setUp()
    {
        parent::setUp();
        $this->withoutMiddleware([VerifyCsrfToken::class]);
    }

    /** @test */
    public function an_authenticated_user_can_login()
    {
        factory(User::class)->create([
            'email' => 'test@example.com',
            'name' => 'Foobar',
            'password' => bcrypt('foo'),
        ]);

        $this->post("/login",[
            'email' => 'test@example.com',
            'password' => 'foo',
        ]);

        $this->get("/home")->assertStatus(200);
    }

    /** @test */
    public function an_unauthenticated_user_cannot_login()
    {
        factory(User::class)->create([
            'email' => 'test@example.com',
            'name' => 'Foobar',
            'password' => bcrypt('foo')
        ]);

        $this->post("/login",[
            'email' => 'foo@example.com',
            'password' => 'foo'
        ])->assertRedirect('/');

        $this->get("/home")->assertStatus(302)->assertRedirect("/login");
    }

    /** @test */
    public function multiple_login_failures_cause_logins_to_be_throttled_after_5_failures()
    {
        factory(User::class)->create([
            'email' => 'test@example.com',
            'name' => 'Foobar',
            'password' => bcrypt('foo')
        ]);

        $i = 1;
        while ($i < 6)
        {
            $this->post("/login",[
                'email' => 'test@example.com',
                'password' => 'bar'
            ]);
            $i++;
        }

        $this->post("/login",[
            'email' => 'test@example.com',
            'password' => 'foo'
        ])->assertRedirect('/');
    }
}
