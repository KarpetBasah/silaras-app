<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Input Program Routes
$routes->group('input-program', static function ($routes) {
    $routes->get('', 'InputProgram::index');
    $routes->get('create', 'InputProgram::create');
    $routes->post('store', 'InputProgram::store');
    $routes->get('edit/(:num)', 'InputProgram::edit/$1');
    $routes->post('update/(:num)', 'InputProgram::update/$1');
    $routes->post('delete/(:num)', 'InputProgram::delete/$1');
    $routes->post('validate-location', 'InputProgram::validateLocation');
});

// Peta Program Routes
$routes->group('peta-program', static function ($routes) {
    $routes->get('', 'PetaProgram::index');
    $routes->get('getProgramData', 'PetaProgram::getProgramData');
    $routes->get('getProgramDetail/(:num)', 'PetaProgram::getProgramDetail/$1');
});

// Placeholder routes for other modules
$routes->get('rpjmd', 'RPJMD::index');
$routes->get('analisis', 'Analisis::index');
$routes->get('monitoring', 'Monitoring::index');
