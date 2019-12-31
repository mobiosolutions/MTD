<?php

namespace Tests\Controllers;

use Mockery;
use App\User;
use App\photo;
use Tests\DuskTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\BookController;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserControllerTest extends DuskTestCase
{
    use WithFaker;
    use DatabaseTransactions;

    public $mockphoto;
    public $mockValidator;
    public $mockUploadedFile;
    public $mockBookController;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->mockValidator = $this->app['validator'];
        $this->mockphoto = Mockery::mock(photo::class)->makePartial();
        $this->mockUploadedFile = Mockery::mock(UploadedFile::class)->makePartial();
        $this->mockBookController = Mockery::mock(BookController::class)->makePartial();
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
            'uploadFile',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockBookController, $method);
        }
    }

    /**
     * Validate file upload type
     *
     * @test
     */
    public function test_file_upload_request_data_invalid()
    {

        $this->user = factory(User::class)->create();
        $response = $this->actingAs($this->user)
            ->post('/upload', [
                'photo' => 'wifi.pnj',
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * test no file selected
     *
     * @test
     */
    public function test_no_file_selected()
    {
        $this->user = factory(\App\User::class)->create();
        Storage::fake('public');

        $response = $this->actingAs($this->user)->post('/upload', ['photo'=>'']);
        $response->assertSessionHasErrors();
    }

    /**
     * test file upload successfully
     *
     * @test
     */
    public function test_file_upload_success()
    {

        $this->user = factory(\App\User::class)->create();
        Storage::fake('public');

        $data = [
            'photo' => $file = $this->mockUploadedFile::fake()->image('wifi.jpg', 1, 1)
        ];

        $response = $this->actingAs($this->user)->post('/upload', $data);
        $response->assertStatus(200);
    }
}
