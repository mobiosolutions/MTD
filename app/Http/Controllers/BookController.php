<?php

namespace App\Http\Controllers;

use App\Book;
use App\photo;
use Illuminate\Http\Request;
use App\Http\Requests\FileRequest;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{

    /**
     * upload file
     * @param request
     * @return saved
     */
    public function uploadFile(FileRequest $request)
    {

        if ($request->file('file')) {
            $destinationPath = 'photo/';
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $extension = $request->file('file')->getClientOriginalExtension();
            $fileOriginalName = $request->file('file')->getClientOriginalName();
            $fileOriginalName = basename($fileOriginalName, ".jpeg");
            $fileName = $fileOriginalName . '.' . $extension;
            $request->file('file')->move($destinationPath, $fileName);

            $img = new photo();
            $img->photo = $fileName;
            $img->save();
        }
    }

    /**
     * search data filter
     * @param request
     * @return json
     */
    public function searchData(Request $request)
    {
        $query = Book::where('title', 'LIKE', '%' . $request->title . '%')
            ->orWhere('price',  'LIKE', '%' . $request->price . '%');
        $data = $query->get();
        if (count($data) == 0) {
            return response()->json(['data' => $data], 404);
        }
        return response()->json(['data' => $data], 200);
    }

    /**
     * export excel file
     * 
     * @return file
     */
    public function exportExcel()
    {
        $list = Book::get()->toArray();
        $file = Excel::create('Excel File', function ($excel) use ($list) {
            $excel->sheet('Sheet 1', function ($sheet) use ($list) {
                $sheet->fromArray($list);
            });
        })->export('xls');
        return $file;
    }
}
