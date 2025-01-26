<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function db($tabel, $db = null)
{
    if ($db == null || $db == 'ps') {
        $db = \Config\Database::connect();
    } else {
        $db = \Config\Database::connect(strtolower(str_replace(" ", "_", $db)));
    }
    $db = $db->table($tabel);

    return $db;
}

function menus()
{

    $q1[] = ['id' => 0, 'no_urut' => 0, 'role' => user()['role'], 'menu' => 'Home', 'tabel' => 'users', 'controller' => 'home', 'icon' => "fa-solid fa-earth-asia", 'url' => 'home', 'logo' => 'file_not_found.jpg', 'grup' => ''];
    $db = db('menus');
    $q2 = $db->where('role', user()['role'])->orderBy('urutan', 'ASC')->get()->getResultArray();
    $menus = array_merge($q1, $q2);
    return $menus;
}


function menu($req = null)
{
    $res = [];
    if ($req == null) {

        foreach (menus() as $i) {
            if ($i['controller'] == url()) {
                $res = $i;
            }
        }
    } else {
        foreach (menus() as $i) {
            if ($i['controller'] == $req) {
                $res = $i;
            }
        }
    }

    return $res;
}

function url($req = null)
{

    $url = service('uri');
    $res = $url->getPath();
    $val = '';
    if ($req == null) {
        if (getenv('is_online') == 0) {
            $req = 2;
        } else {
            $req = 3;
        }
    } else {
        if (getenv('is_online') == 0) {
            $req = $req - 1;
        }
    }


    $exp = explode("/", $res);

    if (array_key_exists($req, $exp)) {
        $val = $exp[$req];
    }

    return $val;
}

function check_role($order = null)
{

    if (!session('id')) {
        gagal(base_url('login'), 'You are not login. Login first!.');
    }

    if ($order == null) {
        if (!menu()) {
            gagal(base_url('home'), 'You are not allowed!.');
        }
    }
}

function check_session_tap()
{
    if (!session('lokasi') || !session('status') || session('message')) {
        session()->remove('lokasi');
        session()->remove('status');
        session()->remove('message');
        session()->remove('uid');
        session()->remove('url');
        session()->remove('uid_member');
        gagal_js("Session expired!.");
    }
}


function angka($uang)
{
    return number_format($uang, 0, ",", ".");
}

function sukses($url, $pesan)
{
    session()->setFlashdata('sukses', $pesan);
    header("Location: " . $url);
    die;
}

function gagal($url, $pesan)
{
    session()->setFlashdata('gagal', $pesan);
    header("Location: " . $url);
    die;
}
function gagal_rfid($url, $pesan)
{
    session()->remove('lokasi');
    session()->remove('status');
    session()->remove('message');
    session()->remove('url');
    session()->remove('uid');
    session()->remove('uid_member');
    session()->setFlashdata('gagal_rfid', $pesan);
    header("Location: " . $url . "/" . strtolower(session('lokasi')));
    die;
}

function gagal_with_button($url, $pesan)
{
    session()->setFlashdata('gagal_with_button', $pesan);
    header("Location: " . $url);
    die;
}

function sukses_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null, $data5 = null)
{
    $data = [
        'status' => '200',
        'message' => $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($data);
    die;
}
function sukses_arduino($pesan, $data = "", $data2 = "", $data3 = "", $data4 = "", $data5 = "")
{
    // nama harus di data2, uang harus data
    $data = [
        'status' => '200',
        'message' => $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($data);
    die;
}
function gagal_arduino($pesan, $data = "", $data2 = "", $data3 = "", $data4 = "", $data5 = "")
{
    $data = [
        'status' => '400',
        'message' => $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($data);
    die;
}
function sukses_iot($is_active)
{
    $data = [
        'status' => '200',
        'is_active' => $is_active
    ];

    echo json_encode($data);
    die;
}

function gagal_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null, $data5 = null)
{
    $res = [
        'status' => '400',
        'message' =>  $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4,
        'data5' => $data5
    ];

    echo json_encode($res);
    die;
}

function clear($text)
{
    $text = trim($text);
    $text = htmlspecialchars($text);
    return $text;
}



function upper_first($text)
{
    $text = clear($text);
    $exp = explode(" ", $text);

    $val = [];
    foreach ($exp as $i) {
        $lower = strtolower($i);
        $val[] = ucfirst($lower);
    }

    return implode(" ", $val);
}

function encode_jwt($data)
{

    $jwt = JWT::encode($data, getenv('key_jwt'), 'HS256');

    return $jwt;
}

function decode_jwt($encode_jwt)
{
    try {

        $decoded = JWT::decode($encode_jwt, new Key(getenv('key_jwt'), 'HS256'));
        $arr = (array)$decoded;

        return $arr;
    } catch (\Exception $e) { // Also tried JwtException
        $data = [
            'status' => '400',
            'message' => $e->getMessage()
        ];

        echo json_encode($data);
        die;
    }
}
function encode_jwt_santri($data)
{
    $jwt = JWT::encode($data, getenv('jwt_santri'), 'HS256');

    return $jwt;
}

function decode_jwt_santri($encode_jwt)
{
    try {

        $decoded = JWT::decode($encode_jwt, new Key(getenv('jwt_santri'), 'HS256'));
        $arr = (array)$decoded;

        return $arr;
    } catch (\Exception $e) { // Also tried JwtException
        $data = [
            'status' => '400',
            'message' => $e->getMessage()
        ];

        echo json_encode($data);
        die;
    }
}
function encode_jwt_fulus($data)
{

    $jwt = JWT::encode($data, getenv("jwt_fulus"), 'HS256');

    return $jwt;
}
function decode_jwt_fulus($encode_jwt)
{
    try {

        $decoded = JWT::decode($encode_jwt, new Key(getenv("jwt_fulus"), 'HS256'));
        $arr = (array)$decoded;

        return $arr;
    } catch (\Exception $e) { // Also tried JwtException
        $data = [
            'status' => '300',
            'message' => $e->getMessage(),
            'data' => "",
            'data2' => "",
            'data3' => "",
            'data4' => "",
            'data5' => ""
        ];
        echo json_encode($data);
        die;
    }
}
function encode_jwt_finger($data)
{

    $jwt = JWT::encode($data, getenv("jwt_finger"), 'HS256');

    return $jwt;
}
function decode_jwt_finger($encode_jwt)
{
    try {

        $decoded = JWT::decode($encode_jwt, new Key(getenv("jwt_finger"), 'HS256'));
        $arr = (array)$decoded;

        return $arr;
    } catch (\Exception $e) { // Also tried JwtException
        $data = [
            'status' => '300',
            'message' => $e->getMessage(),
            'data' => "",
            'data2' => "",
            'data3' => "",
            'data4' => "",
            'data5' => ""
        ];
        echo json_encode($data);
        die;
    }
}


function user()
{
    $db = db('users');

    $q = $db->where('id', session('id'))->get()->getRowArray();
    if (!$q) {
        if (session('id')) {
            $q['nama'] = session('nama');
            $q['role'] = session('role');
            $q['id'] = 0;
            $q['img'] = 'file_not_found.jpg';
        } else {
            gagal(base_url('login'), 'Session expired!.');
        }
    }
    return $q;
}

function remove_char($string)
{
    // return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.

    return preg_replace('/[^\da-z ]/i', '', $string); //except space
}

function get_files($dir)
{
    $val = [];

    $dir_name = $dir;
    $data = glob($dir_name . "*.*");

    foreach ($data as $i) {

        $name = explode("/", $i);
        $name = end($name);
        $name = explode(".", $name);

        if (end($name) == 'mp3') {
            $val[] = ['data' => upper_first(str_replace("-", " ", $name)[0]), 'url_audio' => base_url($i), 'url_img' => img_vocabs($name[0])];
        }
    }

    return $val;
}

// memanggil gambar vocab
function img_vocabs($text)
{
    $file = strtolower(str_replace(" ", "-", $text)) . '.jpg';

    $res = base_url('files/vocabs/') . $file;
    if (!file_exists('files/vocabs/' . $file)) {
        $res = base_url('file_not_found.jpg');
    }

    return $res;
}


function get_last_no_inv()
{
    $th = date('Y');
    $db = db('inventaris');
    $no = substr($th, 2) . '001';
    $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

    if ($q) {
        for ($i = 1; $i < 100; $i++) {
            if (strlen($i) == 1) {
                $temp = '00' . $i;
            }
            if (strlen($i) == 2) {
                $temp = '0' . $i;
            }
            if (strlen($i) == 3) {
                $temp = $i;
            }

            $temp_no = substr($th, 2) . $temp;

            $is_exist = $db->where('id', $temp_no)->get()->getRowArray();

            if (!$is_exist) {
                $no = $temp_no;
                break;
            }
        }
    }

    return $no;
}

function rp_to_int($uang)
{
    $uang = str_replace("Rp. ", "", $uang);
    $uang = str_replace(".", "", $uang);
    return $uang;
}

function rupiah($uang)
{
    return number_format($uang, 0, ",", ".");
}

function get_rental($data)
{
    $db = db('rental');
    $q = $db->where('unit_id', $data['id'])->where('is_active', 1)->get()->getRowArray();
    return ($q ? $q : null);
}

function get_harga_rental()
{
    $db = db('settings');

    $q = $db->get()->getResultArray();

    $res = [];
    foreach ($q as $i) {
        $exp = explode(" ", $i['nama_setting']);

        if ($exp[0] == 'Ps') {
            $i['harga_permenit'] = round($i['value_int'] / 60);
            $res[] = $i;
        }
    }

    return $res;
}
function harga_ps($meja)
{
    $db = db("unit");
    $res = 0;
    $q = $db->where("unit", $meja)->get()->getRowArray();

    if ($q) {
        $dbs = db('settings');
        $qs = $dbs->where('nama_setting', $q['kode_harga'])->get()->getRowArray();
        if ($qs) {
            $res = $qs['value_int'];
        }
    }

    return $res;
}
function get_harga_billiard()
{
    $db = db('settings');

    $q = $db->where('nama_setting', 'Billiard')->get()->getRowArray();
    return $q['value_int'];
}

function hari($req = null)
{
    $hari = [
        ['inggris' => 'Monday', 'indo' => 'Senin', 'singkatan' => 'Sn'],
        ['inggris' => 'Tuesday', 'indo' => 'Selasa', 'singkatan' => 'Sl'],
        ['inggris' => 'Wednesday', 'indo' => 'Rabu', 'singkatan' => 'Rb'],
        ['inggris' => 'Thursday', 'indo' => 'Kamis', 'singkatan' => 'Km'],
        ['inggris' => 'Friday', 'indo' => 'Jumat', 'singkatan' => 'Jm'],
        ['inggris' => 'Saturday', 'indo' => 'Sabtu', 'singkatan' => 'Sb'],
        ['inggris' => 'Sunday', 'indo' => 'Ahad', 'singkatan' => 'Mg']
    ];

    if ($req == null) {
        return $hari;
    }
    $res = [];
    foreach ($hari as $i) {
        if ($i['inggris'] == $req) {
            $res = $i;
        } elseif ($i['indo'] == $req) {
            $res = $i;
        }
    }

    return $res;
}

function bulan($req = null)
{
    $bulan = [
        ['romawi' => 'I', 'bulan' => 'Januari', 'angka' => '01', 'satuan' => 1],
        ['romawi' => 'II', 'bulan' => 'Februari', 'angka' => '02', 'satuan' => 2],
        ['romawi' => 'III', 'bulan' => 'Maret', 'angka' => '03', 'satuan' => 3],
        ['romawi' => 'IV', 'bulan' => 'April', 'angka' => '04', 'satuan' => 4],
        ['romawi' => 'V', 'bulan' => 'Mei', 'angka' => '05', 'satuan' => 5],
        ['romawi' => 'VI', 'bulan' => 'Juni', 'angka' => '06', 'satuan' => 6],
        ['romawi' => 'VII', 'bulan' => 'Juli', 'angka' => '07', 'satuan' => 7],
        ['romawi' => 'VIII', 'bulan' => 'Agustus', 'angka' => '08', 'satuan' => 8],
        ['romawi' => 'IX', 'bulan' => 'September', 'angka' => '09', 'satuan' => 9],
        ['romawi' => 'X', 'bulan' => 'Oktober', 'angka' => '10', 'satuan' => 10],
        ['romawi' => 'XI', 'bulan' => 'November', 'angka' => '11', 'satuan' => 11],
        ['romawi' => 'XII', 'bulan' => 'Desember', 'angka' => '12', 'satuan' => 12]
    ];

    $res = $bulan;
    foreach ($bulan as $i) {
        if ($i['bulan'] == $req) {
            $res = $i;
        } elseif ($i['angka'] == $req) {
            $res = $i;
        } elseif ($i['satuan'] == $req) {
            $res = $i;
        } elseif ($i['romawi'] == $req) {
            $res = $i;
        }
    }
    return $res;
}

// apakah pembayaran hari ini sudah dibayar
function billiard_paid($id)
{
    $db = db('billiard');
    $q = $db->where('jadwal_id', $id)->get()->getResultArray();

    $res = null;

    foreach ($q as $i) {
        if (date('d/m/Y', $i['tgl']) == date('d/m/Y')) {
            $res[] = $i;
        }
    }

    return $res;
}

function is_menu_active($grup)
{
    $db = db('menus');
    $res = null;
    $q = $db->where('controller', menu()['controller'])->where('grup', $grup)->get()->getRowArray();
    if ($q) {
        $res = 1;
    }
    return $res;
}

function get_tahuns($tabel)
{

    $db = db($tabel);
    $q = $db->get()->getResultArray();

    $res = [];

    foreach ($q as $i) {
        if (!in_array(date('Y', $i['tgl']), $res)) {
            $res[] = date('Y', $i['tgl']);
        }
    }

    return $res;
}

function get_detail_billiard($meja_id)
{

    $db = db('billiard_2');
    $q = $db->where('is_active', 1)->where('meja_id', $meja_id)->get()->getRowArray();

    return $q;
}


function durasi($start, $end)
{
    $start = date_create(date('Y-m-d H:i:s', $start));
    $end = date_create(date('Y-m-d H:i:s', $end));

    $diff  = date_diff($end, $start);

    return $diff->h . ':' . $diff->i;
}

function durasi_dalam_menit($start, $end)
{
    // Hitung selisih waktu dalam detik
    $diffInSeconds = abs($end - $start); // Menggunakan abs untuk memastikan hasil positif
    // Konversi selisih waktu ke menit
    $diffInMinutes = round($diffInSeconds / 60);
    return $diffInMinutes;
}

function biaya_per_menit($harga, $start, $end)
{

    $diff = $end - $start;
    $menit = ceil($diff / 60);
    $harga_per_menit = ceil($harga / 60);
    $harga = $harga_per_menit * $menit;


    if ($harga < 1000) {
        return 1000;
    } else {
        $res = 0;
        $exp = explode('.', rupiah($harga));
        if (count($exp) >= 2) {
            if (end($exp) !== '000') {
                $temp = (int)$exp[0] + 1;
                $temp .= ".000";
                $res = rp_to_int($temp);
            }
        }
        return $res;
    }
}

function get_closest($search, $arr)
{
    $closest = null;
    foreach ($arr as $item) {
        if ($closest === null || abs($search - $closest) > abs($item - $search)) {
            $closest = $item;
        }
    }
    return $closest;
}

function get_absen($user)
{

    // $sess = 'Admin Kantin';
    $sess = $user['role'];
    $dbs = db('shift');
    $s = $dbs->where('kategori', $sess)->get()->getResultArray();
    if (!$s) {
        gagal_js("Data shift tidak ada.");
    }

    // $time_shift = strtotime('2024-11-22 00:00:00');
    // $time_server = strtotime('2024-11-23 01:00:00');
    $time_server = time();

    $datas = [];
    $nums = [];
    $date_server = date_create(date('Y-m-d H:i:s', $time_server)); //jam server


    foreach ($s as $i) {
        $time_shift = strtotime(date('Y-m-d') . ' ' . $i['jam'] . ':00');
        $shift = date('Y-m-d') . ' ' . $i['jam'] . ':00';

        $df = $time_server - $time_shift;
        $mnt = round($df / 60);

        $date_shift = date_create($shift); // jam shift
        $diff = date_diff($date_shift, ($date_server));
        $i['diff'] = $diff->h . ' jam ' . $diff->i . ' menit';
        $i['time_shift'] = $time_shift;
        $i['time_server'] = $time_server;
        $i['diff_time'] = $df;
        $i['menit'] = $mnt;
        $nums[] = $time_shift;
        $datas[] = $i;
    }


    $closest = get_closest($time_server, $nums);

    $data = [];

    foreach ($datas as $i) {
        if ($i['time_shift'] == $closest) {
            $data = $i;
        }
    }
    // $tes = [$date_server, $time_server, date('d/m/Y H:i:s', $time_server)];

    $db = db('absen');
    $q = $db->where('role', $sess)->where('tgl', date('d'))->where('shift', $data['shift'])->whereIn('ket', ['Terlambat', 'Ontime'])->get()->getRowArray();

    if ($q) {
        gagal_js('Absen shift ' . $data['shift'] . ' sudah dilakukan.');
    }

    if ($data['menit'] < 0) {
        gagal_js('Belum waktunya absen untuk shift ' . $data['shift']  . '.');
    } else if (round($data['menit'] / 60) > 2) {
        gagal_js('Absen shift ' . $data['shift'] . ' ditutup.');
    }


    $msg = "Kamu tepat waktu.";


    $dbp = db('aturan');
    if ($data['menit'] < 16) {
        $data['ket'] = 'Ontime';
        $qp = $dbp->where('aturan', $data['ket'])->get()->getRowArray();
        if ($qp) {
            $data['poin'] = $qp['poin'];
        } else {
            gagal_js("Data poin ontime tidak ada.");
        }
    } else {
        $qat = $dbp->where("aturan", "Terlambat")->get()->getRowArray();
        if (!$qat) {
            gagal_js("Data poin terlambat tidak ada.");
        }
        $data['ket'] = 'Terlambat';
        $po = (round(($data['menit'] - 15) / 10)) + abs($qat["poin"]);
        $data['poin'] = -$po;
        $msg = 'Ente terlambat ' . $data['diff'] . ' untuk shift ' . $data['shift'] . '.';
    }
    $data['msg'] = $msg;
    return $data;
}

function poin_absen($id, $order = null)
{
    $db = db('absen');

    $q = $db->where('user_id', $id)->orderBy('absen', 'ASC')->get()->getResultArray();
    if ($order !== null) {
        if (!$q) {
            gagal_js("Data tidak ditemukan!.");
        }
    }
    $poin = 100;
    foreach ($q as $i) {
        $poin = $poin + $i['poin'];
    }

    $data = ['data' => $q, 'poin' => $poin];

    return $data;
}

function options($kategori)
{
    $db = db('options');

    $q = $db->where('kategori', $kategori)->orderBy('value', 'ASC')->get()->getResultArray();
    return $q;
}

function barang($jenis = null)
{
    $db = db('barang');
    $db;
    if ($jenis !== null) {
        $db->where('jenis', $jenis);
    }

    $q = $db->orderBy('barang', 'ASC')->get()->getResultArray();

    return $q;
}

function no_nota($tabel, $meja = 14)
{

    $db = db(($tabel == 'kantin' ? $tabel : 'hutang'));
    $no_nota = strtoupper(substr($tabel, 0, 1)) . '/' . $meja . '/' . date('dmY/His');
    $q = $db->where('no_nota', $no_nota)->get()->getRowArray();

    if (!$q) {
        return $no_nota;
    } else {
        return strtoupper(substr($tabel, 0, 1)) . '/' . $meja . '/' . date('dmY/His') . '/0001';
    }
}

function get_jml_hutang($id)
{
    $db = db('hutang');
    $res = 0;
    $db->where('user_id', $id);
    if (session('role') !== 'Root') {
        $db->where('kategori', explode(" ", session('role'))[1]);
    }
    $q = $db->where('status', 0)->get()->getResultArray();

    if ($q) {
        $res = count($q);
    }

    return $res;
}

function data_pembeli()
{

    $dbu = db('users');

    $q = $dbu->where('role', 'Member')->get()->getResultArray();

    $data = [];
    foreach ($q as $i) {
        $i['status'] = get_jml_hutang($i['id']);
        $data[] = $i;
    }

    $short_by = SORT_DESC;

    $keys = array_column($data, 'status');
    array_multisort($keys, $short_by, $data);

    return $data;
}

function generateRandomString($length = 14)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function clear_tabel($tabel)
{
    $db = db($tabel);
    $q = $db->get()->getResultArray();
    if ($q) {
        foreach ($q as $i) {
            $db->where('id', $i['id']);
            $db->delete();
        }
    }
}

function message($kategori, $msg, $status, $msg2 = "", $msg3 = "")
{
    $db = db('message');
    $q = $db->get()->getRowArray();
    if ($q) {
        $q['kategori'] = $kategori;
        $q['message'] = $msg;
        $q['uang'] = $msg2;
        $q['status'] = $status;
        $q['admin'] = $msg3;
        $db->where('id', $q['id']);
        $db->update($q);
    } else {
        $data = ['kategori' => $kategori, 'message' => $msg, 'status' => $status, 'uang' => $msg2, 'admin' => $msg3];
        $db->insert($data);
    }
}

function topup($user, $booking)
{
    $jml = $booking['durasi'] * 10000;

    $decode_fulus = decode_jwt_fulus($user['fulus']);

    $fulus = $decode_fulus['fulus'];

    $user['fulus'] = encode_jwt_fulus(['fulus' => $fulus + $jml]);

    $db = db('users');
    $db->where('id', $user['id']);
    if ($db->update($user)) {
        return true;
    } else {
        return false;
    }
}
function saldo($user)
{
    $decode_fulus = decode_jwt_fulus($user['fulus']);
    $fulus = ($decode_fulus['fulus'] == "" ? 0 : $decode_fulus['fulus']);
    $fulus = (int)$fulus;
    return $fulus;
}

function konfirmasi_root($booking, $user)
{
    if (!$user) {
        clear_tabel('booking');
        message($booking['kategori'], "Akses kartu ditolakl!.", 400);
        gagal_arduino('Akses kartu ditolakl!.');
    }

    if ($user['role'] !== 'Root') {
        clear_tabel('booking');
        message($booking['kategori'], "Akses kartu ditolakl!.", 400);
        gagal_arduino('Akses kartu ditolakl!.');
    } else {
        $db = db('api');
        $data = ['status' => $user["uid"]];
        if ($db->insert($data)) {
            message($booking['kategori'], $user['nama'] . " akses diterima.", 200, "Tap rfid member...");
            sukses_arduino('Akses diterima.', 'next');
        } else {
            clear_tabel('booking');
            message($booking['kategori'], "Gagal api!.", 400);
            gagal_arduino('Gagal api!.');
        }
    }
}
function konfirmasi_root_finger($booking, $user)
{
    if (!$user) {
        clear_tabel('booking');
        message($booking['kategori'], "Finger tidak ditemukan!.", 400);
        gagal_js('Finger tidak ditemukan!.');
    }

    if ($user['role'] !== 'Root') {
        clear_tabel('booking');
        message($booking['kategori'], "Finger ditolakl!.", 400);
        gagal_js('Finger ditolakl!.');
    } else {
        $db = db('api');
        $data = ['status' => $user["finger"]];
        if ($db->insert($data)) {
            message($booking['kategori'], $user['nama'] . " akses diterima.", 200, "Sentuh finger member...");
            sukses_js($user['nama'] . ' akses diterima.', 'next');
        } else {
            clear_tabel('booking');
            message($booking['kategori'], "Gagal api!.", 400);
            gagal_js('Gagal api!.');
        }
    }
}


function konfirmasi_uid_exist($uid_exist, $booking)
{
    if ($uid_exist) {
        message($booking['kategori'], "Uid sudah terdaftar!.", 400);
        clear_tabel('booking');
        clear_tabel('api');
        gagal_arduino("Uid sudah terdaftar!.");
    }
}
function konfirmasi_user_exist($user_m, $booking)
{
    if (!$user_m) {
        message($booking['kategori'], "User tidak ada!.", 400);
        clear_tabel('booking');
        clear_tabel('api');
        gagal_arduino("User tidak ada!.");
    }
}
function konfirmasi_uid_exist_finger($uid_exist, $booking)
{
    if ($uid_exist) {
        message($booking['kategori'], "Finger sudah terdaftar!.", 400);
        clear_tabel('booking');
        clear_tabel('api');
        gagal_js("Finger sudah terdaftar!.");
    }
}
function konfirmasi_user_exist_finger($user_m, $booking)
{
    if (!$user_m) {
        message($booking['kategori'], "User tidak ada!.", 400);
        clear_tabel('booking');
        clear_tabel('api');
        gagal_js("User tidak ada!.");
    }
}

function saldo_tap($kategori, $barang, $uang, $user, $petugas = "")
{
    $dbt = db("topup");
    $topup = [
        "tgl" => time(),
        "jenis" => ($kategori == "Remove" ? 'hapus' : ($kategori == "Topup" ? "in" : "out")),
        "kategori" => $kategori,
        "petugas" => ($petugas == "" ? $user['nama'] : $petugas),
        "jml" => $uang,
        "barang" => $barang,
        "user_id" => $user["id"],
        "uid" => $user["uid"],
        "user" => $user["nama"]

    ];
    if (!$dbt->insert($topup)) {
        gagal_js("Insert ke tabel topup gagal!.");
    }
}

function kode_bayar($order = null)
{
    $data = options("Kode Bayar");
    $res = [];

    foreach ($data as $i) {
        $exp = explode("_", $i['value']);
        $res[$exp[0]] = $exp[1];
    }
    if ($order == null) {
        return $res;
    } else {
        foreach ($res as $k => $i) {
            if ($i == $order) {
                return $k;
            }
        }
    }
}

function nama_tabel($order)
{
    $res = 'rental';
    if ($order == "Billiard") {
        $res = 'billiard_2';
    }

    return $res;
}

function tidak_absen()
{
    $dba = db('absen');
    $dbs = db('shift');
    $qs = $dbs->whereNotIn("kategori", ["Root"])->get()->getResultArray();

    $jam_now = (int)date("H");
    $tgl = (int)date("j");
    $res = null;
    foreach ($qs as $i) {
        $jam = explode(".", $i['jam']);
        $jam_terlambat = (int)$jam[0] + 3;

        if ($jam_now > $jam_terlambat) {
            $qa = $dba->where('tgl', $tgl)->where('role', $i['kategori'])->where('shift', $i['shift'])->get()->getRowArray();
            if (!$qa) {
                $dbu = db('users');
                $qu = $dbu->where('uid', $i['uid'])->get()->getRowArray();

                $dbat = db('aturan');
                $qat = $dbat->where('aturan', "Ghoib")->get()->getRowArray();
                if (!$qu) {
                    dd("User tidak ditemukan!.");
                }
                if (!$qat) {
                    dd("Aturan tidak ditemukan!.");
                }

                $value = [
                    'tgl' => $tgl,
                    'username' => $qu["username"],
                    'ket' => "Ghoib",
                    'poin' => $qat['poin'],
                    'nama' => $qu["nama"],
                    'role' => $qu["role"],
                    'user_id' => $qu["id"],
                    'shift' => $i['shift'],
                    'jam' => $i['jam'],
                    'absen' => time(),
                    'terlambat' => 180
                ];


                if ($dba->insert($value)) {
                    $dbn = db('notif');
                    $datan = [
                        'kategori' => 'Absen',
                        'pemesan' => $value['nama'],
                        'tgl' => $value['absen'],
                        'harga' => time(),
                        'menu' => "Tidak Absen",
                        'meja' => $value['ket'],
                        'qty' => $value['poin']
                    ];

                    if ($dbn->insert($datan)) {
                        $res[] = $qu['nama'];
                    }
                }
            }
        }
    }
}

function laporan_arduino()
{
    $dbb = db('booking');
    $qb = $dbb->get()->getRowArray();
    $data = [];
    $data['tgl'] = time();
    if ($qb) {
        $data['kategori'] = $qb['kategori'];
    }
    $dbm = db('message');
    $qm = $dbm->get()->getRowArray();
    if ($qm) {
        $data['message'] = $qm['message'];
        $data['status'] = $qm['status'];
        $data['uang'] = $qm['uang'];
        $data['admin'] = $qm['admin'];
    }
    if (count($data) > 1) {
        $db = db('laporan');
        $db->insert($data);
    }
}

function get_itag_addr()
{
    $db = db("users");
}
function stringArr_to_arr($string)
{
    $string = str_replace("[", "", str_replace("]", "", $string));
    $arr = explode(",", $string);

    $hasil = [];
    foreach ($arr as $i) {
        $angka_terakhir = $i % 10;
        $sisa_angka = substr($i, 0, -1);

        $hasil[] = ['status' => (int)$angka_terakhir, 'perangkat' => (int)$sisa_angka];
    }

    return $hasil;
}
