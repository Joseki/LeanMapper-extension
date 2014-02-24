<?php

namespace App\Tables;

use Joseki\LeanMapper\Repository;



/**
 * @method \App\Tables\Tag   get($id)
 * @method \App\Tables\Tag   findOneBy($condition)
 * @method \App\Tables\Tag[] findAll($limit = NULL, $offset = NULL)
 * @method \App\Tables\Tag[] findBy($condition)
 */
class TagRepository extends Repository
{
	const ARTICLE_RELATION_TABLE = 'article_tag';



	public function getTagsByFrequency()
	{
		$query = $this->createQueryObject()->removeClause('select')->select($this->getTable() . '.*, count(tag) as frequency')
			->join(self::ARTICLE_RELATION_TABLE)
			->on($this->getTable() . '.id = ' . self::ARTICLE_RELATION_TABLE . '.tag')
			->groupBy('tag')
			->orderBy('frequency DESC, id ASC');
		return $this->createEntities($this->prepare($query)->fetchAll(), '\App\Tables\TagFrequency');
	}
}
