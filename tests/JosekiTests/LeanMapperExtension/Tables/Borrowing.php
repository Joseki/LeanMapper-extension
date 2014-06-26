<?php

namespace JosekiTests\LeanMapperExtension\Tables;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property Book $book {N:1}
 * @property Borrower $borrower {N:1}
 * @property string $date
 */
class Borrowing extends Entity
{

}
