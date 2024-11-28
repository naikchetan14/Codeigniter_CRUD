<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->group('',['filter' =>'auth'] ,function (RouteCollection $routes) {
    $routes->get('/', 'Home::index');
    $routes->post('add','Home::add');
    $routes->get('/delete/(:num)','Home::delete/$1');
    $routes->post('edit/(:num)','Home::edit/$1');
    $routes->post('/todofilter','Home::filter');
    $routes->get('/download','Home::download');
});

$routes->get('/register','Home::register');
$routes->get('login','Home::login');
$routes->post('/addUser', 'User::addNewUser'); 
$routes->post('/logout','User::LogOut');
$routes->post('/getloginUser','User::getLoginUser');

