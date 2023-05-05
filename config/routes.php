<?php

/**
 * @var array list of the blog routes: [ 'get|mixed|post', 'url', 'controller#method', ['param1' => '([0-9]+)'], 'route_name' ]
 */
return [
    ['mixed', '/', 'App\\Controller\\HomeController#index', [], 'app_home'],
    ['get', '/privacy', 'App\\Controller\\HomeController#privacy', [], 'app_privacy'],
    ['get', '/articles', 'App\\Controller\\ArticleController#index', [], 'app_article_index'],
    ['mixed', '/articles/:slug', 'App\\Controller\\ArticleController#show', ['slug' => '([a-z-]+)'], 'app_article_show'],
    ['mixed', '/login', 'App\\Controller\\UserController#login', [], 'app_login'],
    ['mixed', '/register', 'App\\Controller\\UserController#register', [], 'app_register'],
    ['mixed', '/logout', 'App\\Controller\\UserController#logout', [], 'app_logout'],
    ['get', '/profile', 'App\\Controller\\UserController#profile', [], 'app_profile'],
    ['get', '/admin', 'App\\Controller\\Admin\\AdminDashboardController#dashboard', [], 'app_admin'],
    ['get', '/admin/posts', 'App\\Controller\\Admin\\AdminPostController#index', [], 'app_admin_post_index'],
    ['get', '/admin/posts/:id', 'App\\Controller\\Admin\\AdminPostController#show', ['id' => '([0-9-]+)'], 'app_admin_post_show'],
    ['mixed', '/admin/posts/:id/edit', 'App\\Controller\\Admin\\AdminPostController#edit', ['id' => '([0-9-]+)'], 'app_admin_post_edit'],
    ['mixed', '/admin/posts/create', 'App\\Controller\\Admin\\AdminPostController#create', [], 'app_admin_post_create'],
    ['post', '/admin/posts/:id/delete', 'App\\Controller\\Admin\\AdminPostController#delete', ['id' => '([0-9-]+)'], 'app_admin_post_delete'],
    ['get', '/admin/comments', 'App\\Controller\\Admin\\AdminCommentController#index', [], 'app_admin_comment_index'],
    ['get', '/admin/comments/:id', 'App\\Controller\\Admin\\AdminCommentController#show', ['id' => '([0-9-]+)'], 'app_admin_comment_show'],
    ['mixed', '/admin/comments/:id/edit', 'App\\Controller\\Admin\\AdminCommentController#edit', ['id' => '([0-9-]+)'], 'app_admin_comment_edit'],
    ['post', '/admin/comments/:id/delete', 'App\\Controller\\Admin\\AdminCommentController#delete', ['id' => '([0-9-]+)'], 'app_admin_comment_delete'],
    ['post', '/admin/comments/:id/status', 'App\\Controller\\Admin\\AdminCommentController#updateStatus', ['id' => '([0-9-]+)'], 'app_admin_comment_status'],
];
