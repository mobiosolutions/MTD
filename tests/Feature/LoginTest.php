<?php

namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{
    /** @test */
    /**
     * Test Post Method While Log In.
     *
     * @return void
     */
    public function testLoginPost()
    {
        $response = $this->call('POST', '/login', [
            'email' => 'admin@gmail.com',
            'password' => 'adminadmin',
            '_token' => csrf_token()
        ]);
        $this->assertEquals(302, $response->getStatusCode());
        $response->assertRedirect('/home');
    }

    /**
     * Test Log In When Pass Incorrect username or password.
     *
     * @return void
     */
    public function testLoginFalse()
    {
        $response = $this->post('/login', [
            'email' => 'user@ad.com',
            'password' => 'incorrectpass',
        ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test Log In When User Login Successfully.
     *
     * @return void
     */
    public function testUserLoginSuccessfully()
    {

        $response = $this->post('/login', [
            'email' => 'admin@gmail.com',
            'password' => 'adminadmin',
        ]);
        $response->assertRedirect('/home');
    }
}
