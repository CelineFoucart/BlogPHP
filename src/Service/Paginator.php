<?php

namespace App\Service;

use App\Database\QueryBuilder;
use App\Database\StatementBuilder;

/**
 * Paginator generates a pagination.
 */
class Paginator
{
    /**
     * @var int The current page
     */
    private int $current;

    /**
     * @var int The total number of pages
     */
    private int $totalPage;
    /**
     * @var int the total number of items
     */
    private int $numberItems;
    /**
     * @var int the number of items per page
     */
    private int $perPage;
    /**
     * @var QueryBuilder An instance of QueryBuilder to build a SQL query
     */
    private QueryBuilder $queryBuilder;

    /**
     * @var StatementBuilder an instance of StatementBuilder to perform the query
     */
    private StatementBuilder $statementBuilder;

    /**
     * A QueryBuilder and a StatementBuilder are required to perform the query to build the pagination.
     */
    public function __construct(QueryBuilder $queryBuilder, StatementBuilder $statementBuilder)
    {
        $this->queryBuilder = $queryBuilder;
        $this->statementBuilder = $statementBuilder;
    }

    /**
     * Gets a pagination.
     */
    public function getPagination(string $link, int $current, int $perPage = 3): Pagination
    {
        $countSQL = $this->queryBuilder->count($this->queryBuilder->getAlias().'.id');
        $this->numberItems = $this->statementBuilder->fetch($countSQL, $this->queryBuilder->getParams(), 'num')[0];

        $this->perPage = $perPage;
        $this->totalPage = ceil($this->numberItems / $this->perPage);
        $this->current = $this->setCurrent((int) $current);
        $offset = $this->perPage * ($this->current - 1);
        $offset = ($offset < 0) ? 0 : $offset;
        $sql = $this->queryBuilder->setLimit($this->perPage)->setOffset($offset)->toSQL();
        $elements = $this->statementBuilder->fetchAll($sql, $this->queryBuilder->getParams());

        return (new Pagination())
            ->setPreviousLink($this->previousLink($link))
            ->setNextLink($this->nextLink($link))
            ->setPages($this->getPages($link))
            ->setPerPage($this->perPage)
            ->setCurrent($this->current)
            ->setNumberItems($this->numberItems)
            ->setElements($elements);
    }

    /**
     * Sets the value of the current page.
     */
    private function setCurrent(int $current): int
    {
        if ($current <= 0) {
            return 1;
        }
        
        if ($current > $this->totalPage) {
            return $this->totalPage;
        } 
        
        return $current;
    }

    /**
     * Gets the previous link.
     */
    private function previousLink(string $link, array $params = []): ?string
    {
        $currentPage = $this->current;
        if ($currentPage <= 1) {
            return null;
        }
        $params['page'] = $currentPage - 1;
        $link = $this->setLink($link, $params);

        return <<<HTML
        <a href="{$link}" class="btn button-outline init">&laquo; Précédente</a>
HTML;
    }

    /**
     * Gets the next link.
     */
    private function nextLink(string $link, array $params = []): ?string
    {
        $currentPage = $this->current;
        if ($currentPage >= $this->totalPage) {
            return null;
        }
        $params['page'] = $currentPage + 1;
        $link = $this->setLink($link, $params);

        return <<<HTML
        <a href="{$link}" class="btn button-outline init">Suivant &raquo;</a>
HTML;
    }

    /**
     * Gets the links of the pages.
     */
    private function getPages(string $link, array $params = []): array
    {
        $total = $this->totalPage;
        $current = $this->current;
        if ($current > $total) {
            return [];
        } elseif ($total <= 7) {
            $selectedPages = [];
            for ($i = 1; $i <= $total; ++$i) {
                $selectedPages[] = $i;
            }
        } elseif ($current < 5) {
            $selectedPages = [1, 2, 3, 4, 5, $total];
        } elseif ($total - 4 < $current && $current <= $total) {
            $selectedPages = [1, $total - 4, $total - 3, $total - 2, $total - 1, $total];
        } else {
            $selectedPages = [1, $current - 2, $current - 1, $current, $current + 1, $current + 2, $total];
        }
        $pages = [];
        foreach ($selectedPages as $page) {
            if ($page === $current) {
                $pages[] = "<strong class=\"btn outline-active number\">{$page}</strong>";
            } else {
                $params['page'] = $page;
                $pageLink = $this->setLink($link, $params);
                $pages[] = "<a href=\"{$pageLink}\" class=\"btn button-outline number\">{$page}</a>";
            }
        }

        return $pages;
    }

    /**
     * Formats a link for the pagination.
     */
    private function setLink(string $link, array $params): string
    {
        $parts = parse_url($link);
        $query = isset($parts['query']) ? $parts['query'] : [];

        if (isset($parts['query'])) {
            parse_str($query, $query);
            $parts['query'] = $query;
        } else {
            $parts['query'] = $query;
        }
        $parts['query']['page'] = $params['page'];

        return $parts['scheme'].'://'.$parts['host'].$parts['path'].'?'.http_build_query($parts['query']);
    }
}
