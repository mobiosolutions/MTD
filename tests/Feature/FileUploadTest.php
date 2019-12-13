<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileUploadTest extends TestCase
{

    /**
     * Test For file upload.
     *
     * @return void
     */
    /** @test */
    function testFileUpload()
    {
        Storage::fake('public');

        $response = $this->json('post', '/upload', [
            'file' => $file = UploadedFile::fake()->image('wifi.jpeg')
        ]);

        $response->assertStatus(200);
    }
}
