<?php

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

$author = $authorRepository->get(1);
\Tester\Assert::equal(' SELECT `author`.* FROM `author` WHERE (`author`.`id` = 1) ORDER BY `id` LIMIT 1', \dibi::$sql);
$book = $bookRepository->get(2);
\Tester\Assert::equal(' SELECT `book`.* FROM `book` WHERE (`book`.`id` = 2) ORDER BY `id` LIMIT 1', \dibi::$sql);

\Tester\Assert::true(true);
