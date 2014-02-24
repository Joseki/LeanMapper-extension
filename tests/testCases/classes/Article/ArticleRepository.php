<?php

namespace App\Tables;

use Joseki\LeanMapper\Repository;
use Nette\Utils\Strings;



/**
 * @method \App\Tables\Article get($id)
 * @method \App\Tables\Article findOneBy($condition)
 * @method \App\Tables\Article[] findAll($limit = NULL, $offset = NULL)
 * @method \App\Tables\Article[] findBy($condition)
 */
class ArticleRepository extends Repository
{
	const TAG_RELATION_TABLE = 'article_tag';



	public function findByTag($tag)
	{
		$query = $this->createQueryObject()->removeClause('select')->select($this->getTable() . '.*')
			->join(self::TAG_RELATION_TABLE)
			->on($this->getTable() . '.id = ' . self::TAG_RELATION_TABLE . '.article')
			->where([[self::TAG_RELATION_TABLE . '.tag = %s', $tag]])
			->orderBy('[date] DESC');
		return $this->createEntities($this->prepare($query)->fetchAll());
	}



	function initEvents()
	{
		parent::initEvents();
		$this->onBeforePersist[] = function (Article $article) {
			$article->link = Strings::webalize($article->name);
		};
	}
}




