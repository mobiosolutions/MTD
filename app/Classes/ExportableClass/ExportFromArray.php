<?php

namespace App\Classes\ExportableClass;

use Maatwebsite\Excel\Excel;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;

class ExportFromArray implements FromArray
{
    use Exportable;

    protected $fileName   = 'name.xls';
    protected $writerType = Excel::XLSX;
    protected $headers    = [
        'Content-Type' => 'text/xlsx',
    ];

    /**
     * @return array
     */
    public function array(): array
    {
        return [
            ['test', 'test'],
            ['test', 'test'],
        ];
    }
}
