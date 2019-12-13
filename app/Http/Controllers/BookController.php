<?php

namespace App\Http\Controllers;

use DB;
use App\Book;
use App\photo;
use Redirect;
use Storage;
use Illuminate\Http\UploadedFile;
use Upload;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookController extends Controller
{
    /**
     * book constructor.
     * @param Carousel $book
     */
    public function __construct(Book $book)
    {
        $this->book = $book;
    }
    /**
     * create book detail
     * @param array $data
     * @return bookData
     */
    public function createBook($data)
    {

        $bookId = DB::table('books')->insertGetId(
            [
                'title' => $data['title'],
                'price' => $data['price'],
            ]
        );
        if ($bookId) {
            $bookData = Book::findOrFail($bookId);
            return $bookData;
        } else {
            return "";
        }
    }

    /**
     * find book detail
     * @param array $data
     * @return bookDetail
     */
    public function findBook($id)
    {
        $bookDetail = Book::findOrFail($id);
        if ($bookDetail) {
            return $bookDetail;
        }
    }

    /**
     * update  book detail
     * @param array $data
     * @return updated
     */
    public function updateBook($id, $data)
    {
        $bookDetail = Book::findOrFail($id['id']);
        $bookDetail->title = $data['title'];
        $bookDetail->price = $data['price'];
        $updated = $bookDetail->save();
        if ($updated) {
            return $updated;
        }
    }

    /**
     * delete book detail
     * @param array $data
     * @return deleted
     */
    public function deleteBook($id)
    {
        $deleteBookDetail = Book::findOrFail($id);
        $deleted = $deleteBookDetail->delete();
        if ($deleted) {
            return $deleted;
        }
    }

    /**
     * upload file
     * @param array $data
     * @return saved
     */
    public function uploadFile(Request $request)
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

    public function exportExcel() 
    {
        // return Excel::download(new Book, 'formal.xlsx');
        $list = Book::get()->toArray();
        $file = Excel::create('Excel File', function ($excel) use ($list) {
            $excel->sheet('Sheet 1', function ($sheet) use ($list) {
                $sheet->fromArray($list);
            });
        })->export('xls');
        return $file;

    }

}
