<?php

$loader = require_once __DIR__.'/vendor/autoload.php';
$loader->add('SWProject', __DIR__.'/src');

use SWProject\Controller\FilmControllerProvider;
use SWProject\Controller\PersonControllerProvider;
use SWProject\Controller\DBpediaControllerProvider;

$app = new Silex\Application();

$app['debug'] = true;

$app->mount('/film', new FilmControllerProvider());
$app->mount('/person', new PersonControllerProvider());
$app->mount('/dbpedia', new DBpediaControllerProvider());

$app->run();