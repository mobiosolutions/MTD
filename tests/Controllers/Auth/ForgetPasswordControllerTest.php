<?php

namespace Tests\Controllers\Auth;

use Mockery;
use App\User;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ForgetPasswordControllerTest extends DuskTestCase
{

    use WithFaker;
    use DatabaseTransactions;

    public $mockForgotPasswordController;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockForgotPasswordController = Mockery::mock(ForgotPasswordController::class)->makePartial();
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
            'showLinkRequestForm',
            'sendResetLinkEmail',
            'validateEmail',
            'credentials',
            'sendResetLinkResponse',
            'sendResetLinkFailedResponse',
            'broker',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockForgotPasswordController, $method);
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
     * request should fail when no email is provided
     *
     * @test
     */
    public function request_should_fail_when_no_email_is_provided()
    {
        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/password/reset', []);

        $response->assertSessionHasErrors(['email']);
    }

    /**
     * Check email exist in the system.
     *
     * @test
     */
    public function test_email_exists_in_system()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'admin@gmail.com'
        ]);
    }

    /**
     * Check if email not exist in the system.
     *
     * @test
     */
    public function test_email_not_exists_in_system()
    {
        $this->assertDatabaseMissing('users', [
            'email' => 'notexistemail@gmail.com'
        ]);
    }


    /**
     * Test if forget password request failed.
     *
     * @test
     */
    public function test_check_if_forget_password_request_failed()
    {

        $email = $this->faker->email();
        $data = [
            'email' => $email,
        ];

        $response = $this->post('/password/email', $data);
        $response->assertSessionHasErrors();
    }

    /**
     * Test if forget password request success.
     *
     * @test
     */
    public function test_check_if_forget_password_request_success()
    {

        $response = $this->post('/password/email', [
            'email' => 'admin@gmail.com',
        ]);
        $response->assertStatus(302);
    }
}
