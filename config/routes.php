<?php

/**
 * @var array list of the blog routes: [ 'get|mixed|post', 'url', 'controller#method', ['param1' => '([0-9]+)'], 'route_name' ]
 */
return [
    ['get', '/', 'App\\Controller\\HomeController#index', [], 'app_home'],
];
