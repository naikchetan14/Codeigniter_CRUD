<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->post('add','Home::add');
$routes->get('/delete/(:num)','Home::delete/$1');
$routes->post('edit/(:num)','Home::edit/$1');
