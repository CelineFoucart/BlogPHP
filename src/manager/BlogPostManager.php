<?php

declare(strict_types=1);

namespace App\manager;

use App\Database\QueryBuilder;
use App\Entity\BlogPost;
use App\Service\Pagination;
use App\Service\Paginator;

/**
 * BlogPostManager handles requests to the blog post table.
 */
final class BlogPostManager extends AbstractManager
{
    /**
     * Returns a pagination of blog post.
     */
    public function findPaginated(string $link, int $page = 1): Pagination
    {
        $queryBuilder = $this->getDefaultQuery()->orderBy('b.updated_at', 'DESC');
        $paginator = new Paginator($queryBuilder, $this->getBuilder());

        return $paginator->getPagination($link, $page, PER_PAGE);
    }

    /**
     * Returns a blog post if the slug is found in the database.
     */
    public function findBySlug(string $slug): ?BlogPost
    {
        $sql = $this->getDefaultQuery()->where('b.slug = ?')->toSQL();

        return $this->getBuilder()->fetch($sql, [$slug]);
    }

    /**
     * Returns a blog post if the id is found in the database.
     */
    public function findById(int $id): ?BlogPost
    {
        $sql = $this->getDefaultQuery()->where('b.id = ?')->toSQL();

        return $this->getBuilder()->fetch($sql, [$id]);
    }

    /**
     * Updates a blog post.
     */
    public function update(BlogPost $post): int
    {
        $query = $this->getQuery();
        $updateSQL = $query->select('title', 'slug', 'content', 'updated_at', 'description', 'author_id')
            ->setParams([
                'title' => $post->getTitle(),
                'slug' => $post->getSlug(),
                'description' => $post->getDescription(),
                'content' => htmlspecialchars($post->getContent()),
                'updated_at' => $post->getUpdatedAt()->format('Y-m-d H:i:s'),
                'id' => $post->getId(),
                'author_id' => (string) $post->getAuthor()->getId(),
            ])
            ->toSQL('update')
        ;

        return $this->getBuilder()->alter($updateSQL, $query->getParams());
    }

    /**
     * Deletes a blog post.
     */
    public function delete(BlogPost $post): int
    {
        $query = $this->getQuery();
        $deleteSQL = $query->toSQL('delete');

        return $this->getBuilder()->alter($deleteSQL, ['id' => $post->getId()]);
    }

    /**
     * Inserts in the database a new blog post.
     */
    public function insert(BlogPost $post): int
    {
        $query = $this->getQuery();
        $insertSQL = $query->select('title', 'slug', 'description', 'content', 'updated_at', 'created_at', 'author_id')
            ->setParams([
                'title' => $post->getTitle(),
                'slug' => $post->getSlug(),
                'description' => $post->getDescription(),
                'content' => htmlspecialchars($post->getContent()),
                'updated_at' => $post->getUpdatedAt()->format('Y-m-d H:i:s'),
                'created_at' => $post->getCreatedAt()->format('Y-m-d H:i:s'),
                'author_id' => (string) $post->getAuthor()->getId(),
            ])
            ->toSQL('insert')
        ;

        return $this->getBuilder()->alter($insertSQL, $query->getParams());
    }

    /**
     * Returns the default query with a join with the author.
     */
    private function getDefaultQuery(): QueryBuilder
    {
        return $this->getQuery()
            ->select('b.title', 'b.slug', 'b.title', 'b.id', 'b.content', 'b.description', 'b.created_at', 'b.updated_at')
            ->select('u.id AS author_id', 'u.username AS author_username')
            ->leftJoin('blog_user u', 'u.id = b.author_id')
        ;
    }
}
