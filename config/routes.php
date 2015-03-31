<?php
use Cake\Routing\Router;

Router::plugin('Automation', function ($routes) {
    $routes->fallbacks();
});
