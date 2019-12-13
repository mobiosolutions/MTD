<?php

namespace Tests\Feature;

use App\Book;
use Tests\TestCase;
use App\Http\Controllers\BookController;

class InsertDataTest extends TestCase
{

    /**
     * show book detail.
     *
     * @return void
     */
    /** @test */
    public function showBookDetail()
    {
        $book_id = \factory(Book::class)->create();
        $books = new BookController(new Book);
        $found = $books->findBook($book_id->id);

        $this->assertInstanceOf(Book::class, $found);
        $this->assertEquals($found->title, $book_id->title);
        $this->assertEquals($found->price, $book_id->price);
    }

    /**
     * insert book detail.
     *
     * @return void
     */
    /** @test */
    public function insertData()
    {
        $faker = \Faker\Factory::create();
        $data = [
            'title' => $faker->word,
            'price' => $faker->randomFloat(2, 0, 1000),
        ];

        $books = new BookController(new Book);
        $saveBook = $books->createBook($data);

        $this->assertInstanceOf(Book::class, $saveBook);
        $this->assertEquals($data['title'], $saveBook->title);
        $this->assertEquals($data['price'], $saveBook->price);
    }
}
