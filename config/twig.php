<?php
/*
 * This file provides global variables for Twig.
 */

return [
    'website_name' => 'Céline Foucart',
    'twig_variables' => [
        'website_name' => 'Céline Foucart',
        'website_description' => "Développeuse web fullstack",
        'request_uri' => (isset($_SERVER['REQUEST_URI']) === true) ? $_SERVER['REQUEST_URI'] : '/',
    ],
];
