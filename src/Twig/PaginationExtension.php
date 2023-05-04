<?php

declare(strict_types=1);

namespace App\Twig;

use App\Service\Pagination;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * PaginationExtension creates a twig function which generate a pagination navigation.
 */
class PaginationExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('pagination', [$this, 'getPagination'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Formats a pagination navigation.
     */
    public function getPagination(?Pagination $pagination): string
    {
        if (null === $pagination) {
            return '';
        }

        $previous = $pagination->getPreviousLink();
        $next = $pagination->getNextLink();
        if (null === $next && null === $previous) {
            return '';
        }
        $pagination = (empty($pagination->getPages())) ? '' : implode('', $pagination->getPages());

        return <<<HTML
        <div class="navigation">
            <div class="btn-group" role="group" aria-label="Pagination">
                {$previous}
                {$pagination}
                {$next}
            </div>
        </div>
HTML;
    }
}
