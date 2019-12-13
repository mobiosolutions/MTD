<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use App\Http\Controllers\BookController;

class UpdateDataTest extends TestCase
{

    /**
     * update book detail.
     *
     * @return void
     */
    /** @test */
    public function updateBookDetail()
    {
        $book_id = \factory(Book::class)->create();
        $faker = \Faker\Factory::create();
        $data = [
            'title' => $faker->word,
            'price' => $faker->randomFloat(2, 0, 1000),
        ];

        $books = new BookController(new Book);
        $update = $books->updateBook($book_id, $data);

        $this->assertTrue($update);
    }

    /**
     * updated show book detail.
     *
     * @return void
     */
    /** @test */
    public function showBook()
    {
        $book_id = \factory(Book::class)->create();
        $books = new BookController(new Book);
        $found = $books->findBook($book_id->id);

        $this->assertInstanceOf(Book::class, $found);
        $this->assertEquals($found->title, $book_id->title);
        $this->assertEquals($found->price, $book_id->price);
    }
}
