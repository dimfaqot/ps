<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Login::landing');
$routes->get('/api/iot_rental/(:any)/(:num)', 'Api::iot_rental/$1/$2');
$routes->get('/api/tes_iot_rental', 'Api::tes_iot_rental');
$routes->get('/api/(:any)/(:any)', 'Api::lampu/$1/$2');
$routes->get('/api/(:any)/(:any)', 'Api::lampu/$1/$2');
$routes->get('/uiapi', 'Api::index');
$routes->post('/api/update_iot_rental', 'Api::update_iot_rental');
$routes->post('/api/tes_update_iot_rental', 'Api::tes_update_iot_rental');
$routes->get('/api/iot_notif_pesanan', 'Api::iot_notif_pesanan');

$routes->get('/login', 'Login::index');

$routes->post('/auth', 'Login::auth');
$routes->get('/logout', 'Login::logout');

// Absen
$routes->get('/absen', 'Absen::index');
$routes->get('/qrcode', 'Absen::qrcode');
$routes->get('/cetak_absen_qrcode', 'Absen::cetak_absen_qrcode');
$routes->get('/presentation/(:any)', 'Absen::presentation/$1');
$routes->post('/absen/encode', 'Absen::encode');
$routes->post('/absen/poin_absen', 'Absen::poin_absen');
$routes->get('/absen/reset_absen', 'Absen::reset_absen');
$routes->post('/absen/update_poin', 'Absen::update_poin');
$routes->post('/absen/perizinan', 'Absen::perizinan');

// notif
$routes->post('/notif/pesanan', 'Notif::pesanan');
$routes->post('/notif/detail_pesanan', 'Notif::detail_pesanan');
$routes->post('/notif/read_notif_pesanan', 'Notif::read_notif_pesanan');
$routes->post('/notif/notif_detail_pesanan', 'Notif::notif_detail_pesanan');
$routes->post('/notif/kerjakan_pesanan', 'Notif::kerjakan_pesanan');
$routes->post('/notif/selesaikan_pesanan', 'Notif::selesaikan_pesanan');



// home
$routes->get('/home', 'Home::index');
$routes->post('/home/get_pendapatan', 'Home::get_pendapatan');
$routes->post('/home/koperasi', 'Home::koperasi');
$routes->post('/home/add_tabungan', 'Home::add_tabungan');
$routes->post('/home/pembayaran_kantin_barcode', 'Home::pembayaran_kantin_barcode');
$routes->post('/home/pindah_ke_hutang', 'Home::pindah_ke_hutang');
$routes->post('/home/hapus_pesanan', 'Home::hapus_pesanan');
// $routes->get('/home/replace', 'Home::replace');

// users ____________________________________
$routes->get('/users', 'User::index');
$routes->post('/users/add', 'User::add');
$routes->post('/users/update', 'User::update');
$routes->post('/users/get_uid', 'User::get_uid');


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
$routes->post('/billiard/get_user', 'Billiard_2::get_user');
$routes->post('/billiard/update', 'Billiard_2::update');

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
$routes->get('/ext/menu', 'Ext::menu');
$routes->post('/ext/save_menu_pesanan', 'Ext::save_menu_pesanan');
$routes->get('/ext/pesanan/(:any)', 'Ext::pesanan/$1');
$routes->post('/ext/invoice', 'Ext::invoice');
$routes->get('/login/a/member/(:any)', 'Ext::auth_jwt/$1');
$routes->get('/login/a/(:any)', 'Ext::auth_root/$1');
$routes->post('/ext/get_nama_pemesan', 'Ext::get_nama_pemesan');
$routes->get('/ext/qr', 'Ext::qr');
$routes->post('/ext/add_uid', 'Ext::add_uid');
$routes->get('/booking', 'Ext::booking');
$routes->post('/get_durasi', 'Ext::get_durasi');
$routes->post('/get_booking', 'Ext::get_booking');
$routes->post('/add_booking', 'Ext::add_booking');
$routes->post('/end_booking', 'Ext::end_booking');
$routes->post('/del_booking', 'Ext::del_booking');
$routes->post('/hasil_tap', 'Ext::hasil_tap');
$routes->post('/hasil_tap_2', 'Ext::hasil_tap_2');
$routes->post('/daftar/search_db', 'Ext::search_db');
$routes->post('/tap_booking', 'Ext::tap_booking');
$routes->post('/tap_booking/Daftar', 'Ext::tap_booking_daftar');


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
$routes->post('/barber/pembayaran_tap', 'Barber::pembayaran_tap');

// aturan __________________________________
$routes->get('/aturan', 'Aturan::index');
$routes->post('/aturan/add', 'Aturan::add');
$routes->post('/aturan/update', 'Aturan::update');

// Hutang __________________________________
$routes->get('/hutang', 'Hutang::index');
$routes->post('/hutang/update', 'Hutang::update');
$routes->post('/hutang/pembeli', 'Hutang::pembeli');
$routes->post('/hutang/add_pembeli', 'Hutang::add_pembeli');
$routes->post('/hutang/update_pembeli', 'Hutang::update_pembeli');
$routes->post('/hutang/data_hutang', 'Hutang::data_hutang');
$routes->post('/hutang/add', 'Hutang::add');
$routes->post('/hutang/lunas', 'Hutang::lunas');
$routes->post('/hutang/bayar_lunas', 'Hutang::bayar_lunas');

// Notif __________________________________
$routes->get('/firebase_notif', 'Firebase_notif::index');

// fulus
$routes->get('/fulus/get', 'Fulus::get');
$routes->post('/fulus/add', 'Fulus::add');
