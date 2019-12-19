<?php

namespace Tests\Controllers;

use Mockery;
use App\User;
use Tests\DuskTestCase;
use App\Http\Controllers\ApiController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;


class ApiControllerTest extends DuskTestCase
{
    use WithFaker;
    use DatabaseTransactions;

    /**
     * setup test environment
     * 
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->mockApiController = Mockery::mock(ApiController::class)->makePartial();
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
            'getAllUsers',
            'createUser',
            'getUser',
            'updateUser',
            'deleteUser',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockApiController, $method);
        }
    }

    /**
     * Test case for Check route get all user detail.
     *
     * @test
     */
    public function test_get_all_users_route()
    {
        $response = $this->call('GET', '/api/getAllUsers');
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test case for Check get all user detail.
     *
     * @test
     */
    public function test_get_all_users()
    {
        $this->json('GET', '/api/getAllUsers')
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'message'
            ]);
    }


    /**
     * Test case for Check route of get user detail by id.
     *
     * @test
     */
    public function test_get_user_by_id_route()
    {
        $user = factory(User::class)->create();
        $response = $this->call('GET', '/api/getUser/'.$user->id);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test case for Check get user detail by id.
     *
     * @test
     */
    public function test_get_user_by_id()
    {
        $user = factory(User::class)->create();
        $this->json('GET', '/api/getUser/'.$user->id)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'created_at',
                        'updated_at',
                    ]
                ],
                'message'
            ]);
    }


    /**
     * Test case for Check route exist for create user.
     *
     * @test
     */
    public function test_create_user_route()
    {
        $response = $this->call('POST', '/api/createUser', []);
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test case for required fields for create user
     *
     * @test
     */
    public function tests_requires_password_email_and_name()
    {
        $this->json('POST', '/api/createUser', [])
            ->assertStatus(422)
            ->assertJson(["errors" => [
                'name' => ['The name field is required.'],
                'email' => ['The email field is required.'],
                'password' => ['The password field is required.'],
            ]]);
    }

    /**
     * Test case for required password for create user
     *
     * @test
     */
    public function tests_requires_password()
    {
        $requestData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
        ];
        $this->json('POST', '/api/createUser', $requestData)
            ->assertStatus(422)
            ->assertJson(["errors" => [
                'password' => ['The password field is required.'],
            ]]);
    }

    /**
     * Test case for required email for create user
     *
     * @test
     */
    public function tests_requires_email()
    {
        $requestData = [
            'name' => $this->faker->name(),
            'password' => $this->faker->password(),
        ];
        $this->json('POST', '/api/createUser', $requestData)
            ->assertStatus(422)
            ->assertJson(["errors" => [
                'email' => ['The email field is required.'],
            ]]);
    }

    /**
     * Test case for required name for create user
     *
     * @test
     */
    public function tests_requires_name()
    {
        $requestData = [
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
        $this->json('POST', '/api/createUser', $requestData)
            ->assertStatus(422)
            ->assertJson(["errors" => [
                'name' => ['The name field is required.'],
            ]]);
    }

    /**
     * Test case for create user successfully
     *
     * @test
     */
    public function tests_create_user_successfully()
    {
        $requestData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
        $this->json('POST', '/api/createUser', $requestData)
            ->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'message'
            ]);
    }


    /**
     * Check route for update user detail.
     *
     * @test
     */
    public function test_update_user_detail_route()
    {
        $user = factory(User::class)->create();
        $requestData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];
        $response = $this->call('POST', '/api/updateUser/'.$user->id, $requestData);
        $this->assertEquals(200, $response->getStatusCode());
    }

    /**
     * Test case for update user successfully
     *
     * @test
     */
    public function tests_update_user_successfully()
    {
        $user = factory(User::class)->create();
        $requestData = [
            'name' => $this->faker->name(),
            'email' => $this->faker->email(),
            'password' => $this->faker->password(),
        ];

        $this->json('POST', '/api/updateUser/' . $user->id, $requestData)
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'message'
            ]);
    }

    /**
     * check route for delete user
     *
     * @test
     */
    public function tests_delete_user_route()
    {
        $user = factory(User::class)->create();
        $this->json('POST', '/api/deleteUser/' . $user->id)
            ->assertStatus(200);
    }

    /**
     * Test case for delete user successfully
     *
     * @test
     */
    public function tests_delete_user_successfully()
    {
        $user = factory(User::class)->create();
        $this->json('POST', '/api/deleteUser/' . $user->id)
            ->assertStatus(200);
    }
}
