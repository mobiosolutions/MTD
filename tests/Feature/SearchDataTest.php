<?php

namespace Tests\Feature;

use Str;
use App\Book;
use Tests\TestCase;
use App\Http\Controllers\BookController;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SearchDataTest extends TestCase
{

    /**
     * Test For Search Data if found.
     *
     * @return void
     */
    /** @test */
    function testSearchDataIfFound()
    {
        $response = $this->post('/searchData', [
            'title' => 'aliquam',
            'price' => '218.7',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test For Search Data if not found.
     *
     * @return void
     */
    /** @test */
    function testSearchDataIfNotFound()
    {
        $response = $this->post('/searchData', [
            'title' => 'abc',
            'price' => '123',
        ]);

        $response->assertStatus(404);
    }
}
