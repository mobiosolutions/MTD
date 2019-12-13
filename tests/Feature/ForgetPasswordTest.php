<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Notifications\ResetPassword;

class ForgetPasswordTest extends TestCase
{
    /** @test */
    /**
     * Test For Get Forget Password Page.
     *
     * @return void
     */
    public function testPasswordResetForm()
    {
        $response = $this->get('/password/reset/token');
        $response->assertStatus(200);
    }

    /**
     * Test For Send Mail For Reset Password Successfully.
     *
     * @return void
     */
    public function testPasswordResetSendEmail()
    {
        $user = factory(User::class)->create();
        $this->expectsNotification($user, ResetPassword::class);
        $response = $this->post('/password/email', ['email' => $user->email]);
        $response->assertStatus(302);
    }

    /**
     * Test For Does Not Send Mail For Reset Password.
     *
     * @return void
     */
    public function testPasswordResetDoesNotSendEmail()
    {
        $this->doesntExpectJobs(ResetPassword::class);
        $this->post('/password/email', ['email' => 'abcinvalid@email.com']);
    }

    /**
     * Test For Change password successfully.
     *
     * @return void
     */
    public function testChangesUsersPassword()
    {
        $user = factory(User::class)->create();
        $token = Password::createToken($user);
        $response = $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
