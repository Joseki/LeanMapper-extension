<?php

namespace JosekiTests\Tables;

use Joseki\LeanMapper\BaseEntity;



/**
 * @property string $longName
 * @property string $text
 * @property string $link
 * @property Tag[] $tags m:hasMany
 */
class Article extends BaseEntity
{
}
