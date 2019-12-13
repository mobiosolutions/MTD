<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\BookController;

class ExcelGenerateTest extends TestCase
{
     /**
     * Test For generate export.
     *
     * @return void
     */
    /** @test */
    public function generateExcel() 
    {
        $response = $this->post('/generateExcel'.[]);
        $response->assertStatus(200);

    }
    
}
