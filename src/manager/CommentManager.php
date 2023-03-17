<?php

declare(strict_types=1);

namespace App\manager;

use App\Service\Paginator;
use App\Service\Pagination;
use App\Manager\AbstractManager;

class CommentManager extends AbstractManager
{
    public function findPaginated(int $postId, string $link, int $page = 1): Pagination
    {
        $queryBuilder = $this->getQuery()
            ->select('c.content', 'c.created_at', 'c.updated_at')
            ->select('u.username AS author_username', 'u.id AS author_id')
            ->leftJoin('blog_user u', 'u.id = c.author_id')
            ->orderBy('c.created_at', 'DESC')
            ->where('c.post_id = ?')
            ->setParams([$postId])
        ;

        $paginator = new Paginator($queryBuilder, $this->getBuilder());

        return $paginator->getPagination($link, $page, PER_PAGE);
    }
}