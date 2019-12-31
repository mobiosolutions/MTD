<?php

namespace Tests\Controllers;

use Mockery;
use App\User;
use Tests\DuskTestCase;
use App\Http\Controllers\UserController;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends DuskTestCase
{

    use WithFaker;
    use DatabaseTransactions;

    public $mockUser;
    public $mockRequest;
    public $mockValidator;
    public $mockUserController;
    public $mockChangePasswordRequest;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockUser = Mockery::mock(User::class);
        $this->mockValidator = $this->app['validator'];
        $this->mockChangePasswordRequest = Mockery::mock(ChangePasswordRequest::class);
        $this->mockRequest = Mockery::mock(\Illuminate\Http\Request::class)->makePartial();
        $this->mockUserController = Mockery::mock(UserController::class, [$this->mockUser])->makePartial();
    }

    /**
     * Clear test environment before start test
     */
    public function tearDown(): void
    {
        // DO NOT DELETE
        Mockery::close();
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
            'changePassword',
            'createUser',
            'getUsers',
            'updateUser',
            'deleteUser',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockUserController, $method);
        }
    }

    /**
     * Check route is post request and exist.
     *
     * @test
     */
    public function test_routes_for_change_password()
    {
        
        $user = factory(User::class)->make([
            'password' => bcrypt($password = 'adminadmin'),
        ]);
        $this->be($user);
        $response = $this->call('POST', '/password/change', array(
            'old_password' => 'adminadmin',
            'new_password' => 'admin123',
            'confirm_password' => 'admin123'
        ));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * request should fail when no old_password is provided
     *
     * @test
     */
    public function request_should_fail_when_no_old_password_is_provided()
    {
        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/password/change', [
                'new_password' => 'admin123',
                'confirm_password' => 'admin123'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['old_password']);
    }

    /**
     * request should fail when no new_password is provided
     *
     * @test
     */
    public function request_should_fail_when_no_new_password_is_provided()
    {
        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/password/change', [
                'old_password' => 'adminadmin',
                'confirm_password' => 'admin123'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['new_password']);
    }


    /**
     * request should fail when no confirm_password is provided
     *
     * @test
     */
    public function request_should_fail_when_no_confirm_password_is_provided()
    {
        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/password/change', [
                'old_password' => 'adminadmin',
                'new_password' => 'admin123',
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['confirm_password']);
    }

    /**
     * request should fail when new_password confirm_password is not matched
     *
     * @test
     */
    public function request_should_fail_when_new_password_confirm_password_is_not_matched()
    {
        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/password/change', [
                'old_password' => 'adminadmin',
                'new_password' => 'admin123',
                'confirm_password' => 'admin123123'
            ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /**
     * test when validation true
     *
     * @test
     */
    public function test_when_validation_true()
    {
        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/password/change', [
                'old_password' => 'adminadmin',
                'new_password' => 'admin123',
                'confirm_password' => 'admin123'
            ]);

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * test change password success
     *
     * @test
     */
    public function test_change_password_success()
    {
        $data = [
            'old_password' => 'adminadmin',
            'new_password' => 'admin123',
            'confirm_password' => 'admin123'
        ];
        $user = factory(\App\User::class)->create();
        $response = $this->actingAs($user, 'api')->json('POST', '/password/change', $data);

        $response->assertStatus(200);
        $response->assertJson([
            'old_password' => 'adminadmin',
            'new_password' => 'admin123',
            'confirm_password' => 'admin123',
        ]);
    }



    /**
     * test get user details route.
     *
     * @test 
     */
    public function test_get_user_details_route()
    {
        $response = $this->call('GET', '/getUsers');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * test get user details
     *
     * @test 
     */
    public function test_get_user_details()
    {
        $users = factory('App\User')->create();
        $response = $this->get('/getUsers');
        $response->assertSee($users->name);
        $response->assertStatus(200);
    }


    /**
     * test get user details by id route.
     *
     * @test 
     */
    public function test_get_user_details_by_id_route()
    {
        $users = factory('App\User')->create();
        $response = $this->call('POST', '/findUser',$users->toArray());
        $this->assertEquals(200, $response->getStatusCode());
    }

     /**
     * test get user details by id
     *
     * @test 
     */
    public function test_get_user_details_by_id()
    {
        $users = factory('App\User')->create();
        $response = $this->post('/findUser',$users->toArray());
        $response->assertSee($users->name);
        $response->assertStatus(200);
    }


    /**
     * test create user route.
     *
     * @test 
     */
    public function test_create_user_route()
    {
        $response = $this->call('POST', '/createUser', []);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * request should fail when no password is provided
     *
     * @test 
     */
    public function request_should_fail_when_no_password_is_provided()
    {

        $requestData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
        $response = $this->post('/createUser', $requestData);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * request should fail when no email is provided
     *
     * @test 
     */
    public function request_should_fail_when_no_email_is_provided()
    {

        $requestData = [
            'name' => $this->faker->name(),
            'password' => $this->faker->password(),
        ];
        $response = $this->post('/createUser', $requestData);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * request should fail when no name is provided
     *
     * @test 
     */
    public function request_should_fail_when_no_name_is_provided()
    {

        $requestData = [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
        $response = $this->post('/createUser', $requestData);
        $response->assertSessionHasErrors(['name']);
    }

    /**
     * test create user success
     *
     * @test 
     */
    public function test_create_user_success()
    {

        $requestData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
        $response = $this->post('/createUser', $requestData);
        $response->assertStatus(200);
    }

    /**
     * test update user route.
     *
     * @test 
     */
    public function test_update_user_route()
    {
        
        $user = factory('App\User')->create();
        $response = $this->call('POST', '/updateUser/'.$user->id, array(
            'name' => $user->name,
            'email' => $user->email
        ));

        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * test update user success
     *
     * @test 
     */
    public function test_update_user_success()
    {

        $user = factory('App\User')->create();
        $response = $this->post('/updateUser/' . $user->id, $user->toArray());
        $response->assertStatus(200);
    }

    /**
     * test delete user success
     *
     * @test 
     */
    public function test_delete_user_success()
    {
        $user = factory('App\User')->create();
        $response = $this->post('/deleteUser/' . $user->id, $user->toArray());
        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}
