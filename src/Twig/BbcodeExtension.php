<?php

declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * BbcodeExtension creates a twig filter which convert a bbcode text into html.
 */
class BbcodeExtension extends AbstractExtension
{
    /**
     * @var array the tags with a different name in HTML
     */
    private array $alignTags = [
        [
            'bbcode' => 'justify',
            'htmlLeft' => '<div style="text-align:justify">',
            'htmlRight' => '</div>',
        ],
        [
            'bbcode' => 'left',
            'htmlLeft' => '<div style="text-align:left">',
            'htmlRight' => '</div>',
        ],
        [
            'bbcode' => 'center',
            'htmlLeft' => '<div style="text-align:center">',
            'htmlRight' => '</div>',
        ],
        [
            'bbcode' => 'right',
            'htmlLeft' => '<div style="text-align:right">',
            'htmlRight' => '</div>',
        ],
        [
            'bbcode' => 'b',
            'htmlLeft' => '<span style="font-weight:bold">',
            'htmlRight' => '</span>',
        ],
        [
            'bbcode' => 'i',
            'htmlLeft' => '<span style="font-style:italic">',
            'htmlRight' => '</span>',
        ],
        [
            'bbcode' => 'u',
            'htmlLeft' => '<span style="text-decoration-line:underline">',
            'htmlRight' => '</span>',
        ],
        [
            'bbcode' => 's',
            'htmlLeft' => '<span style="text-decoration-line:line-through">',
            'htmlRight' => '</span>',
        ],
        [
            'bbcode' => 'table',
            'htmlLeft' => '<table class="table table-bordered">',
            'htmlRight' => '</table>',
        ],
        [
            'bbcode' => 'quote',
            'htmlLeft' => '<blockquote>',
            'htmlRight' => '</blockquote>',
        ],
    ];

    /**
     * @var array the tags with the same name
     */
    private array $tags = ['ol', 'li', 'ul', 'tr', 'td', 'th', 'code'];

    /**
     * @var array the available font sizes
     */
    private array $fontSizes = [
        'size=1' => '10px',
        'size=2' => '13px',
        'size=3' => '16px',
        'size=4' => '18px',
        'size=5' => '24px',
        'size=6' => '32px',
        'size=7' => '48px',
    ];

    public function getFilters()
    {
        return [
            new TwigFilter('parse_bbcode', [$this, 'parseBbcode'], ['is_safe' => ['html']]),
        ];
    }

    /**
     * Parses a bbcode text to html text.
     */
    public function parseBbcode(string $text): string
    {
        foreach ($this->alignTags as $tag) {
            $pattern = '#\['.$tag['bbcode'].'\](.{0,})\[\/'.$tag['bbcode'].'\]#iUs';
            $replacement = $tag['htmlLeft'].'$1'.$tag['htmlRight'];
            $text = preg_replace($pattern, $replacement, $text);
        }

        foreach ($this->tags as $tag) {
            $pattern = '#\['.$tag.'\](.{0,})\[\/'.$tag.'\]#iUs';
            $replacement = '<'.$tag.'>$1</'.$tag.'>';
            $text = preg_replace($pattern, $replacement, $text);
        }

        $text = preg_replace('#\[color=(.+)\](.+)\[/color\]#isU', '<span style="color:$1">$2</span>', $text);
        $text = preg_replace('#\[url\=(.+)\](.+)\[\/url\]#iUs', '<a href="$1">$2</a>', $text);
        $text = preg_replace('#\[img\](.+)\[\/img\]#iUs', '<img src="$1" class="img-fluid">', $text);
        $text = preg_replace('#\[img\=(\d+)x(\d+)\](.+)\[\/img\]#iUs', '<img src="$3" style="width:$1px; height:$2px; overflow:hidden;">', $text);

        foreach ($this->fontSizes as $key => $size) {
            $text = preg_replace('#\['.$key.'\](.+)\[/size\]#isU', '<span style="font-size:'.$size.'">$1</span>', $text);
        }
        $text = preg_replace('#\[font=(.+)\](.+)\[/font\]#isU', '<span style="font-family:$1">$2</span>', $text);
        $text = preg_replace('#\[hr\]#iUs', '<hr>', $text);

        $text = nl2br($text);

        foreach (['div', 'ul', 'li', 'td', 'table', 'ol', 'tr', 'th', 'blockquote'] as $value) {
            $text = preg_replace('#\/'.$value.'\>\<br \/\>#iUs', '/'.$value.'>', $text);
        }

        $text = preg_replace('#\<hr\>\<br \/\>#iUs', '<hr>', $text);

        return $text;
    }
}
