<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::landing');

$routes->get('/login', 'Login::index');

$routes->post('/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

$routes->get('/home', 'Home::index');

// users ____________________________________
$routes->get('/users', 'User::index');
$routes->post('/users/add', 'User::add');
$routes->post('/users/update', 'User::update');

// options __________________________________
$routes->get('/options', 'Options::index');
$routes->get('/options/(:any)', 'Options::index/$1');
$routes->post('/options/add', 'Options::add');
$routes->post('/options/update', 'Options::update');

// menu __________________________________
$routes->get('/menu', 'Menu::index');
$routes->get('/menu/(:any)', 'Menu::index/$1');
$routes->post('/menu/add', 'Menu::add');
$routes->post('/menu/update', 'Menu::update');
$routes->post('/menu/copy_menu', 'Menu::copy_menu');

// inv __________________________________
$routes->get('/inv', 'Inventaris::index');
$routes->get('/inv/(:any)', 'Inventaris::index/$1');
$routes->post('/inv/add', 'Inventaris::add');
$routes->post('/inv/update', 'Inventaris::update');

// inv __________________________________
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/add', 'Settings::add');
$routes->post('/settings/update', 'Settings::update');

// inv __________________________________
$routes->get('/unit', 'Unit::index');
$routes->get('/unit/(:any)', 'Unit::index/$1');
$routes->post('/unit/add', 'Unit::add');
$routes->post('/unit/update', 'Unit::update');
$routes->post('/unit/detail_unit', 'Unit::detail_unit');
$routes->post('/unit/select_inv', 'Unit::select_inv');
$routes->post('/unit/add_unit_inv', 'Unit::add_unit_inv');
$routes->post('/unit/detail_inv', 'Unit::detail_inv');
$routes->post('/unit/update_catatan', 'Unit::update_catatan');

$routes->post('/js/select', 'Js::select');
$routes->post('/js/check_is_exist', 'Js::check_is_exist');

$routes->post('/general/delete', 'General::delete');

// inv __________________________________
$routes->get('/rental', 'Rental::index');
$routes->post('/rental/start_play', 'Rental::start_play');
$routes->post('/rental/confirm_start_play', 'Rental::confirm_start_play');
$routes->post('/rental/end_play', 'Rental::end_play');
$routes->post('/rental/confirm_end_play', 'Rental::confirm_end_play');
$routes->post('/rental/confirm_tambah', 'Rental::confirm_tambah');
$routes->post('/rental/confirm_ubah', 'Rental::confirm_ubah');
$routes->post('/rental/reset_play', 'Rental::reset_play');
$routes->post('/rental/confirm_reset', 'Rental::confirm_reset');

// jadwal __________________________________
$routes->get('/jadwal', 'Jadwal::index');
$routes->post('/jadwal/add', 'Jadwal::add');
$routes->post('/jadwal/update_jadwal', 'Jadwal::update_jadwal');

// billiard __________________________________
$routes->get('/billiard', 'Billiard::index');
$routes->post('/billiard/add', 'Billiard::add');
$routes->post('/billiard/pembayaran', 'Billiard::pembayaran');
