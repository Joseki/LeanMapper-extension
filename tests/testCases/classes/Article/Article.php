<?php

namespace App\Tables;

use DateTime;
use Joseki\LeanMapper\BaseEntity;



/**
 * @property string $name
 * @property string $linkName
 * @property string $text
 * @property Tag[] $tags m:hasMany
 * @property DateTime|NULL $date
 * @property string $link
 */
class Article extends BaseEntity
{
	public function getTagNames()
	{
		$names = array();
		foreach ($this->tags as $tag) {
			$names[$tag->id] = $tag->id;
		}
		ksort($names);
		return $names;
	}
}
