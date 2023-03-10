<?php

declare(strict_types=1);

namespace App\manager;

use App\Entity\BlogPost;

final class BlogPostManager extends AbstractManager
{
    public function findPaginated(int $offset = 0)
    {
        $sql = $this->getQuery()
            ->select('b.title', 'b.slug', 'b.title', 'b.id', 'b.description', 'b.created_at', 'b.updated_at')
            ->select('u.id AS author_id', 'u.username AS author_username')
            ->leftJoin('blog_user u', 'u.id = b.author_id')
            ->limit(PER_PAGE)
            ->offset($offset)
            ->orderBy('b.updated_at', 'DESC')
            ->toSQL()
        ;

        return $this->getBuilder()->fetchAll($sql);
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