<?php

namespace Tests\Controllers\Auth;

use Mockery;
use App\User;
use Tests\DuskTestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use App\Http\Controllers\Auth\ResetPasswordController;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ResetPasswordControllerTest extends DuskTestCase
{
    use DatabaseTransactions;

    public $mockResetPassword;
    public $mockResetPasswordController;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockResetPassword = Mockery::mock(ResetPassword::class)->makePartial();
        $this->mockResetPasswordController = Mockery::mock(ResetPasswordController::class)->makePartial();
    }

    /**
     * Clear test environment before start test
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Check for method exists or not.
     *
     * @test
     */
    public function method_exists()
    {
        $methodsToCheck = [
            'showResetForm',
            'reset',
            'rules',
            'validationErrorMessages',
            'credentials',
            'resetPassword',
            'setUserPassword',
            'sendResetResponse',
            'sendResetFailedResponse',
            'broker',
            'guard',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockResetPasswordController, $method);
        }
    }

    /**
     * Check forget password route exist.
     *
     * @test
     */
    public function test_forget_password_route_is_exist()
    {
        $response = $this->call('GET', '/password/reset');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Sends the password reset email when the user exists.
     *
     * @test
     */
    public function test_send_password_reset_email()
    {
        Notification::fake();
        $user = factory(User::class)->create();
        $this->expectsNotification($user, 'Illuminate\Auth\Notifications\ResetPassword');
        $response = $this->post('/password/email', ['email' => $user->email]);
        $response->assertStatus(302);
    }

    /**
     * Does not send a password reset email when the user does not exist.
     *
     * @test
     */
    public function test_does_not_send_password_reset_email()
    {
        $this->doesntExpectJobs($this->mockResetPassword);
        $this->post('/password/email', ['email' => 'invalid@email.com']);
    }

    /**
     * Displays the form to reset a password.
     *
     * @test
     */
    public function test_displays_password_reset_form()
    {
        $response = $this->get('/password/reset/token');
        $response->assertStatus(200);
    }

      /**
     * Allows a user to reset their password.
     *
     * @return void
     */
    public function test_changes_users_password()
    {
        $user = factory(User::class)->create();
        $token = Password::createToken($user);
        $this->post('/password/reset', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password'
        ]);
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
