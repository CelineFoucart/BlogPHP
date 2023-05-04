<?php

declare(strict_types=1);

namespace App\Manager;

use App\Database\QueryBuilder;
use App\Entity\Comment;
use App\Manager\AbstractManager;
use App\Service\Pagination;
use App\Service\Paginator;

/**
 * CommentManager handles requests to the comment table.
 */
class CommentManager extends AbstractManager
{
    /**
     * Gets a pagination of comments.
     */
    public function findPaginated(int $postId, string $link, int $page = 1): Pagination
    {
        $queryBuilder = $this->getDefaultQuery()->where('c.post_id = ? AND c.is_validated != 0')->setParams([$postId]);
        $paginator = new Paginator($queryBuilder, $this->getBuilder());

        return $paginator->getPagination($link, $page, PER_PAGE);
    }

    /**
     * Gets a pagination of comment with validation filter.
     */
    public function findPaginatedWithFilter(string $link, int $page = 1, string $option = 'all'): Pagination
    {
        $queryBuilder = $this->getDefaultQuery()
            ->select('b.id AS post_id', 'b.title AS post_title', 'b.slug AS post_slug')
            ->leftJoin('blog_post b', 'b.id = c.post_id')
        ;

        if ('validated' === $option) {
            $queryBuilder->where('c.is_validated != 0');
        } elseif ('notValidated' === $option) {
            $queryBuilder->where('c.is_validated = 0');
        }

        $paginator = new Paginator($queryBuilder, $this->getBuilder());

        return $paginator->getPagination($link, $page, PER_PAGE);
    }

    /**
     * Finds last not validated comments.
     */
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

    /**
     * Gets a queryBuilder with select field used in all methods.
     */
    private function getDefaultQuery(): QueryBuilder
    {
        return $this->getQuery()
            ->select('c.content', 'c.created_at', 'c.updated_at', 'c.id', 'c.is_validated')
            ->select('u.username AS author_username', 'u.id AS author_id')
            ->leftJoin('blog_user u', 'u.id = c.author_id')
            ->orderBy('c.created_at', 'DESC')
        ;
    }

    /**
     * Creates a comment.
     */
    public function create(Comment $comment, int $userId): int
    {
        $query = $this->getQuery();
        $insertSQL = $query->select('content', 'updated_at', 'is_validated', 'created_at', 'author_id', 'post_id')
            ->setParams([
                'content' => htmlspecialchars($comment->getContent()),
                'updated_at' => $comment->getUpdatedAt()->format('Y-m-d H:i:s'),
                'is_validated' => $comment->getIsValidated(),
                'created_at' => $comment->getCreatedAt()->format('Y-m-d H:i:s'),
                'author_id' => $userId,
                'post_id' => $comment->getPost()->getId(),
            ])
            ->toSQL('insert')
        ;

        return $this->getBuilder()->alter($insertSQL, $query->getParams());
    }

    /**
     * Updates a comment.
     */
    public function update(Comment $comment): int
    {
        $query = $this->getQuery();
        $updateSQL = $query->select('content', 'updated_at', 'is_validated')
            ->setParams([
                'content' => htmlspecialchars($comment->getContent()),
                'updated_at' => $comment->getUpdatedAt()->format('Y-m-d H:i:s'),
                'is_validated' => $comment->getIsValidated(),
                'id' => $comment->getId(),
            ])
            ->toSQL('update')
        ;

        return $this->getBuilder()->alter($updateSQL, $query->getParams());
    }

    /**
     * Deletes a comment.
     */
    public function delete(Comment $comment): int
    {
        $query = $this->getQuery();
        $deleteSQL = $query->toSQL('delete');

        return $this->getBuilder()->alter($deleteSQL, ['id' => $comment->getId()]);
    }

    /**
     * Finds a comment by id.
     */
    public function findById(int $id): ?Comment
    {
        $sql = $this->getDefaultQuery()
            ->select('b.id AS post_id', 'b.title AS post_title', 'b.slug AS post_slug')
            ->leftJoin('blog_post b', 'b.id = c.post_id')
            ->where('c.id = ?')
            ->toSQL()
        ;

        return $this->getBuilder()->fetch($sql, [$id]);
    }
}
