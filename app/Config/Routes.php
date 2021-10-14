<?php

namespace Config;

Services::debug_start();

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Api');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override('App\Controllers\Api::show404');
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

$routes->addPlaceholder('uuid', '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}');

$routes->get('/', 'Api::index');
$routes->get('/swagger', 'Api::swagger');

$routes->options('(:any)', 'Api::cors');

$routes->group(
	'api',
	function ($routes) {
		$routes->group(
			'v2',
			function ($routes) {
				$routes->get('ping', 'Api::ping');

				$routes->group(
					'auth',
					[
						'namespace' => 'App\Api'
					],
					function ($routes) {
						$routes->post('login', 'Auth::login');
						$routes->get('profile', 'Auth::profile', ['filter' => 'api-auth']);
						$routes->get('refresh', 'Auth::refresh', ['filter' => 'api-auth']);
						$routes->get('access', 'Auth::access', ['filter' => 'api-auth']);
						$routes->get('logout', 'Auth::logout', ['filter' => 'api-auth']);
					}
				);

				$routes->group(
					'net',
					[
						'namespace' => 'App\Api\Net'
					],
					function ($routes) {
						/// Dhcpsnoop
						$routes->get('dhcpsnoop', 'Dhcpsnoop::index', ['filter' => 'api-auth']);
						$routes->post('dhcpsnoop/create', 'Dhcpsnoop::create', ['filter' => 'api-auth']);

						/// Device
						$routes->get('device', 'Device::index', ['filter' => 'api-auth']);
						$routes->get('device/(:num)', 'Device::getitem/$1', ['filter' => 'api-auth']);
						$routes->get('device/(:num)/module', 'Device::getitem_modules/$1', ['filter' => 'api-auth']);
					}
				);
			}
		);
	}
);
