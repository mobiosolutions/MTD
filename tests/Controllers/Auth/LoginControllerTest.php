<?php

namespace Tests\Controllers\Auth;

use Auth;
use Mockery;
use App\User;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\Auth\LoginController;

class LoginControllerTest extends DuskTestCase
{

    use WithFaker;

    public $mockUser;
    public $mockValidator;
    public $mockLoginController;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockValidator = $this->app['validator'];
        $this->mockUser = Mockery::mock(User::class)->makePartial();
        $this->mockLoginController = Mockery::mock(LoginController::class)->makePartial();
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
        $this->mockLoginController = Mockery::mock(LoginController::class);
        $methodsToCheck = [
            'showLoginForm',
            'login',
            'validateLogin',
            'attemptLogin',
            'credentials',
            'sendLoginResponse',
            'authenticated',
            'sendFailedLoginResponse',
            'username',
            'logout',
            'loggedOut',
            'guard'
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockLoginController, $method);
        }
    }

    /**
     * Check Login route exist.
     *
     * @test
     */
    public function test_login_route_is_exist()
    {
        $response = $this->call('GET', '/login');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test Post Method While Log In.
     *
     * @test
     */
    public function test_check_post_method_for_login()
    {
        $response = $this->call('POST', '/login', [
            'email' => 'admin@gmail.com',
            'password' => 'adminadmin',
            '_token' => csrf_token()
        ]);

        $response->assertRedirect('/home');
    }

    /**
     * Validate login details of user
     *
     * @test
     */
    public function test_login_request_data_valid_or_not()
    {
        $data = [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];

        $rules = [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];

        $validate = $this->mockValidator->make($data, $rules);
        $this->assertTrue($validate->passes());
    }

      /**
     * Check Email id exist in the system.
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
     * Check if email id not exist in the system.
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
     * test user cannot view login form when authenticated
     *
     * @test
     */
    public function test_user_cannot_view_login_form_when_authenticated()
    {
        $user = factory(User::class)->make();
        $response = $this->actingAs($user)->get('/login');
        $response->assertRedirect('/home');
    }

    /**
     * Test if user login failed.
     *
     * @test
     */
    public function test_check_if_user_login_failed()
    {

        $email = $this->faker->email();
        $password = $this->faker->password();
        $data = [
            'email' => $email,
            'password' => $password,
        ];

        $response = $this->post('/login', $data);
        $response->assertSessionHasErrors();
    }

    /**
     * Test if user login success.
     *
     * @test
     */
    public function test_check_if_user_login_success()
    {

        $response = $this->post('/login', [
            'email' => 'admin@gmail.com',
            'password' => 'adminadmin'
        ]);
        $this->assertAuthenticatedAs(Auth::user());
    }
}
