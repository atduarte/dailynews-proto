<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router(false);
$router->removeExtraSlashes(true);
$router->setDefaultNamespace('Notnull\DailyNews\Controllers');

// Home
$router->add('/user/{username}', "User::add", ['POST'])->setName('add-user');

$router->add('/user/{username}/source', "Source::add", ['POST'])->setName('add-source');
$router->add('/user/{username}/source/delete/{id}', "Source::delete", ['POST'])->setName('delete-source');
$router->add('/user/{username}/source', "Source::list", ['GET'])->setName('list-sources');
$router->add('/fetch', "Source::list", ['GET'])->setName('list-sources');

$router->add('/user/{username}/news', "News::list", ['GET'])->setName('list-news');

// 404 Not Found
$router->notFound(array(
    'controller' => 'base',
    'action' => 'notFound'
));

return $router;
