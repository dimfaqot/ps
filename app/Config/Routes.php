<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::landing');

$routes->get('/login', 'Login::index');

$routes->post('/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

// home
$routes->get('/home', 'Home::index');
$routes->post('/home/get_pendapatan', 'Home::get_pendapatan');
$routes->post('/home/koperasi', 'Home::koperasi');
$routes->post('/home/add_tabungan', 'Home::add_tabungan');
// $routes->get('/home/replace', 'Home::replace');

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
$routes->post('/inv/add_pengeluaran_ps', 'Inventaris::add_pengeluaran_ps');
$routes->post('/inv/update', 'Inventaris::update');

// inv __________________________________
$routes->get('/settings', 'Settings::index');
$routes->post('/settings/add', 'Settings::add');
$routes->post('/settings/update', 'Settings::update');
$routes->post('/settings/make_user_jwt', 'Settings::make_user_jwt');

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
$routes->post('/js/select_barang', 'Js::select_barang');
$routes->post('/js/select_layanan', 'Js::select_layanan');

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
// $routes->get('/jadwal', 'Jadwal::index');
// $routes->post('/jadwal/add', 'Jadwal::add');
// $routes->post('/jadwal/update_jadwal', 'Jadwal::update_jadwal');
$routes->get('/jadwal', 'Jadwal_2::index');
$routes->post('/jadwal/add', 'Jadwal_2::add');
$routes->post('/jadwal/update_jadwal', 'Jadwal_2::update_jadwal');

// billiard __________________________________
// $routes->get('/billiard', 'Billiard::index');
// $routes->post('/billiard/add', 'Billiard::add');
// $routes->post('/billiard/pembayaran', 'Billiard::pembayaran');
$routes->get('/billiard', 'Billiard_2::index');
$routes->post('/billiard/add', 'Billiard_2::add');
$routes->post('/billiard/pembayaran', 'Billiard_2::pembayaran');
$routes->post('/billiard/start_stop', 'Billiard_2::start_stop');

// pengeluaran billiard __________________________________
$routes->get('/pengeluaran_billiard', 'Pengeluaran_billiard::index');
$routes->get('/pengeluaran_billiard/(:num)/(:num)', 'Pengeluaran_billiard::index/$1/$2');
$routes->post('/pengeluaran_billiard/add', 'Pengeluaran_billiard::add');
$routes->post('/pengeluaran_billiard/update', 'Pengeluaran_billiard::update');

// KANTIN
// pengeluaran kantin __________________________________
$routes->get('/pengeluaran_kantin', 'Pengeluaran_kantin::index');
$routes->get('/pengeluaran_kantin/(:num)/(:num)', 'Pengeluaran_kantin::index/$1/$2');
$routes->post('/pengeluaran_kantin/add', 'Pengeluaran_kantin::add');
$routes->post('/pengeluaran_kantin/update', 'Pengeluaran_kantin::update');

// barang __________________________________
$routes->get('/barang', 'Barang::index');
$routes->post('/barang/cari_barang', 'Barang::cari_barang');
$routes->post('/barang/add', 'Barang::add');
$routes->post('/barang/update', 'Barang::update');

// kantin __________________________________
$routes->get('/kantin', 'Kantin::index');
$routes->post('/kantin/add', 'Kantin::add');
$routes->post('/kantin/pembayaran', 'Kantin::pembayaran');

// public
$routes->get('/ext/a/(:any)', 'Ext::auth/$1');
$routes->get('/login/a/(:any)', 'Ext::auth_root/$1');

// BARBER
// layanan __________________________________
$routes->get('/layanan', 'Layanan::index');
$routes->post('/layanan/cari_layanan', 'Layanan::cari_layanan');
$routes->post('/layanan/add', 'Layanan::add');
$routes->post('/layanan/update', 'Layanan::update');

// pengeluaran barber __________________________________
$routes->get('/pengeluaran_barber', 'Pengeluaran_barber::index');
$routes->get('/pengeluaran_barber/(:num)/(:num)', 'Pengeluaran_barber::index/$1/$2');
$routes->post('/pengeluaran_barber/add', 'Pengeluaran_barber::add');
$routes->post('/pengeluaran_barber/update', 'Pengeluaran_barber::update');

// barber __________________________________
$routes->get('/barber', 'Barber::index');
$routes->post('/barber/add', 'Barber::add');
$routes->post('/barber/pembayaran', 'Barber::pembayaran');
