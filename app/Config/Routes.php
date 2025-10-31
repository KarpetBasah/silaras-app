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
    $routes->group('api', static function ($routes) {
        $routes->get('programs', 'PetaProgram::getProgramData');
    });
});

// RPJMD Routes
$routes->group('rpjmd', static function ($routes) {
    $routes->get('', 'RPJMD::index');
    $routes->group('api', static function ($routes) {
        $routes->get('priority-layers', 'RPJMD::getPriorityLayers');
        $routes->get('alignment-analysis', 'RPJMD::getAlignmentAnalysis');
        $routes->get('programs', 'RPJMD::getPrograms');
    });
});
// Analisis Routes
$routes->group('analisis', static function ($routes) {
    $routes->get('', 'Analisis::index');
    $routes->group('api', static function ($routes) {
        $routes->get('tumpang-tindih', 'Analisis::getTumpangTindih');
        $routes->get('kesenjangan', 'Analisis::getKesenjangan');
        $routes->get('keselarasan-rpjmd', 'Analisis::getKeselarasanRPJMD');
        $routes->get('statistik', 'Analisis::getStatistikAnalisis');
        $routes->get('rpjmd-zones', 'Analisis::getRpjmdZones');
    });
});

// Monitoring Routes
$routes->group('monitoring', static function ($routes) {
    $routes->get('', 'Monitoring::index');
    $routes->get('map', 'Monitoring::map');
    $routes->get('input-progress/(:num)', 'Monitoring::inputProgress/$1');
    $routes->post('saveProgress', 'Monitoring::saveProgress');
    $routes->get('getMapData', 'Monitoring::getMapData');
    $routes->get('getStatistics', 'Monitoring::getStatistics');
    $routes->get('getPrograms', 'Monitoring::getPrograms');
});

// Test Routes (remove in production)
$routes->get('test-analisis/methods', 'TestAnalisis::testMethods');
$routes->get('test-analisis/statistik', 'TestAnalisis::testStatistik');
