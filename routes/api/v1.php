<?php

$router->post('/register', ['uses' => 'TestController@register']);
$router->post('/login', ['uses' => 'TestController@login']);
$router->get('/users', ['uses' => 'TestController@users']);
$router->get('/users/{id}', ['uses' => 'TestController@userDetail']);
// $router->get('/file/{path:.*}', ['uses' => 'FilestoreController@public']);
