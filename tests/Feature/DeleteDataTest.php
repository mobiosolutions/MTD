<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use App\Http\Controllers\BookController;

class DeleteDataTest extends TestCase
{

    /**
     * delete book detail.
     *
     * @return void
     */
    /** @test */
    public function deleteBooks()
    {
        $book = \factory(Book::class)->create();

        $books = new BookController(new Book);
        $delete = $books->deleteBook($book['id']);

        $this->assertTrue($delete);
    }
}
