<?php

use Tests\DuskTestCase;
use Maatwebsite\Excel\Excel;
use Illuminate\Support\Collection;
use App\Classes\ExportableClass\ExportFromArray;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class ExcelGenerateTest extends DuskTestCase
{

    public $mockExcelGenerate;
    public $mockExportFromArray;
    public $mockHeadersExportClass;

    /**
     * Setup test environment
     */
    public function setUp(): void
    {
        parent::setUp();
       
        $this->mockExcelGenerate = Mockery::mock(Excel::class)->makePartial();
        $this->mockExportFromArray = Mockery::mock(ExportFromArray::class)->makePartial();
        
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
            'download',
            'store',
            'queue',
            'raw',
            'import',
            'toArray',
            'toCollection',
            'queueImport',
            'export',
        ];

        foreach ($methodsToCheck as $method) {
            $this->checkMethodExist($this->mockExcelGenerate, $method);
        }
    }

    /**
     * can fake an export
     * 
     * @test
     */
    public function can_fake_an_export()
    {
        ExcelFacade::fake();
        $this->assertInstanceOf('Maatwebsite\Excel\Fakes\ExcelFake', $this->app->make('excel'));
    }

    /**
     * can set custom headers in export class
     * 
     * @test
     */
    public function can_set_custom_headers_in_export_class()
    {
        $response = $this->mockExportFromArray->toResponse(request());
        $this->assertEquals('text/xlsx', $response->headers->get('Content-Type'));
    }

    /**
     * can export from array
     * 
     * @test
     */
    public function can_export_from_array()
    {
        $response = $this->mockExportFromArray->store('from-array-store.xlsx');
        $this->assertTrue($response);
    }

    /**
     * can download collection as excel
     * 
     * @test
     */
    public function can_download_a_collection_as_excel()
    {
        $collection = new Collection([
            ['column_1' => 'test', 'column_2' => 'test'],
            ['column_1' => 'test2', 'column_2' => 'test2'],
        ]);
        $response = $collection->downloadExcel('collection-download.xlsx', Excel::XLSX);

        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $response);
        $this->assertEquals(
            'attachment; filename=collection-download.xlsx',
            str_replace('"', '', $response->headers->get('Content-Disposition'))
        );
    }

    /**
     * can download an export object with facade
     * 
     * @test
     */
    public function can_download_an_export_object_with_facade()
    {
        $response = ExcelFacade::download('', 'filename.xlsx');
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $response);
        $this->assertEquals('attachment; filename=filename.xlsx', str_replace('"', '', $response->headers->get('Content-Disposition')));
    }

}
