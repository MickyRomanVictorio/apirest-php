<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Registro::index');
$routes->resource('registro');
$routes->resource('cursos');