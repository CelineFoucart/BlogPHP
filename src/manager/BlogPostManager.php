<?php

declare(strict_types=1);

namespace App\manager;

use App\Entity\BlogPost;
use App\Service\Pagination;
use App\Service\Paginator;

final class BlogPostManager extends AbstractManager
{
    public function findPaginated(string $link, int $page = 1): Pagination
    {
        $this->getQuery()
            ->select('b.title', 'b.slug', 'b.title', 'b.id', 'b.description', 'b.created_at', 'b.updated_at')
            ->select('u.id AS author_id', 'u.username AS author_username')
            ->leftJoin('blog_user u', 'u.id = b.author_id')
            ->orderBy('b.updated_at', 'DESC')
        ;
        $paginator = new Paginator($this->getQuery(), $this->getBuilder());

        return $paginator->getPagination($link, $page, PER_PAGE);
    }

    public function findBySlug(string $slug): ?BlogPost
    {
        $sql = $this->getQuery()
            ->select('b.title', 'b.slug', 'b.title', 'b.id', 'b.content', 'b.description', 'b.created_at', 'b.updated_at')
            ->select('u.id AS author_id', 'u.username AS author_username')
            ->leftJoin('blog_user u', 'u.id = b.author_id')
            ->where('b.slug = ?')
            ->toSQL()
        ;

        return $this->getBuilder()->fetch($sql, [$slug]);
    }
}