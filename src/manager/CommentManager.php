<?php

declare(strict_types=1);

namespace App\manager;

use App\Database\QueryBuilder;
use App\Manager\AbstractManager;
use App\Service\Pagination;
use App\Service\Paginator;

class CommentManager extends AbstractManager
{
    public function findPaginated(int $postId, string $link, int $page = 1): Pagination
    {
        $queryBuilder = $this->getDefaultQuery()->where('c.post_id = ? AND c.is_validated != 0')->setParams([$postId]);
        $paginator = new Paginator($queryBuilder, $this->getBuilder());

        return $paginator->getPagination($link, $page, PER_PAGE);
    }

    public function findLastNotValidated(int $limit = 5): array
    {
        $sql = $this->getDefaultQuery()
            ->select('b.id AS post_id', 'b.title AS post_title', 'b.slug AS post_slug')
            ->leftJoin('blog_post b', 'b.id = c.post_id')
            ->limit($limit)
            ->where(('c.is_validated = 0'))
            ->toSQL()
        ;

        return $this->getBuilder()->fetchAll($sql);
    }

    private function getDefaultQuery(): QueryBuilder
    {
        return $this->getQuery()
            ->select('c.content', 'c.created_at', 'c.updated_at')
            ->select('u.username AS author_username', 'u.id AS author_id')
            ->leftJoin('blog_user u', 'u.id = c.author_id')
            ->orderBy('c.created_at', 'DESC')
        ;
    }
}
