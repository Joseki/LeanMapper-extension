<?php

use Tester\Assert;
use UnitTests\Tables\Person;

require_once __DIR__ . '/../bootstrap.php';

\Tester\Environment::lock('database', LOCK_DIR);

$connection->loadFile(__DIR__ . '/db/book_author.sql');

/**
 * @property int $id
 * @property string $name
 */
class Author extends \Joseki\LeanMapper\BaseEntity
{

}

/**
 * @property int $id (id)
 * @property string $name
 * @property Author $author m:hasOne(author:)
 */
class Book extends \Joseki\LeanMapper\BaseEntity
{

}

class AuthorRepository extends \Joseki\LeanMapper\Repository
{

}

class BookRepository extends \Joseki\LeanMapper\Repository
{

}

$mapper->registerTable('author', 'AuthorRepository');
$mapper->registerTable('book', 'BookRepository');
$authorRepository = new AuthorRepository($connection, $mapper, $entityFactory);
$bookRepository = new BookRepository($connection, $mapper, $entityFactory);

Assert::noError(
    function () {
        $person = new Person();
        $person->id = 'name';
        $person->person1 = 'John';
        $person->person1 = null;
        $person->integer = 5;
    }
);

$author = $authorRepository->get(1);
$book = new Book();
$book->name = 'foo';
$book->author = $author;

Assert::equal('Terry Goodkind', $book->author->name);
