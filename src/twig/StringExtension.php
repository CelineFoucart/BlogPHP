<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class StringExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('truncate', [$this, 'truncate'], ['is_safe' => ['html']]),
            new TwigFunction('uniqid', [$this, 'getUniqid'])
        ];
    }

    public function truncate(?string $text, int $length = 150): string
    {
        $text = strip_tags($text);
        
        if (strlen($text) <= $length) {
            return $text;
        }
        $excerpt = mb_substr($text, 0, $length);
        $last = mb_strripos($excerpt, ' ');

        return mb_substr($excerpt, 0, $last).'[...]';
    }

    public function getUniqid(string $prefix = ""): string
    {
        return uniqid($prefix);
    }
}
