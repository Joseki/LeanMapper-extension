<?php

namespace JosekiTests\LeanMapperExtension\Tables;

use LeanMapper\Entity;

/**
 * @property int $id
 * @property Author $author m:hasOne {N:1}
 * @property Author $reviewer m:hasOne(reviewer) {N:1}
 * @property Tag[] $tags m:hasMany {N:M}
 * @property Borrowing[] $borrowing m:belongsToMany {1:N}
 * @property string $pubDate
 * @property string $name
 * @property string|null $description
 * @property string|null $website
 * @property bool $available = true
 */
class Book extends Entity
{

}
