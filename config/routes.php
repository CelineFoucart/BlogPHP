<?php

/**
 * @var array list of the blog routes: [ 'get|mixed|post', 'url', 'controller#method', ['param1' => '([0-9]+)'], 'route_name' ]
 */
return [
    ['get', '/', 'App\\Controller\\HomeController#index', [], 'app_home'],
    ['get', '/privacy', 'App\\Controller\\HomeController#privacy', [], 'app_privacy'],
    ['get', '/articles', 'App\\Controller\\ArticleController#index', [], 'app_article_index'],
    ['mixed', '/articles/:slug', 'App\\Controller\\ArticleController#show', ['slug' => "([a-z-]+)"], 'app_article_show'],
    ['mixed', '/category/:slug', 'App\\Controller\\ArticleController#category', ['slug' => "([a-z-]+)"], 'app_category_show'],
    ['mixed', '/login', 'App\\Controller\\UserController#login', [], 'app_login'],
    ['mixed', '/register', 'App\\Controller\\UserController#register', [], 'app_register'],
];
