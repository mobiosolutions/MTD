<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    /** @test */
    /**
     * Test For Get Sign Up Form.
     *
     * @return void
     */
    public function testRegisterForm()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Test When Sign Up Successfully.
     *
     * @return void
     */
    public function testRegisterSuccessfully()
    {
        $user = factory(User::class)->make();

        $response = $this->post('/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'password_confirmation' => $user->password
        ]);

        $response->assertRedirect('/home');
        $response->assertStatus(302);
    }

    /**
     * Test For Not Sign Up Successfully.
     *
     * @return void
     */
    public function testDoesNotRegister()
    {
        $user = factory(User::class)->make();

        $response = $this->post('/register', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => 'secret',
            'password_confirmation' => 'invalid'
        ]);

        $response->assertSessionHasErrors();
        $response->assertStatus(302);
    }
}
