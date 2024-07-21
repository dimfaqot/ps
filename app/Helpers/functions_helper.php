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

    $q1[] = ['id' => 0, 'no_urut' => 0, 'role' => user()['role'], 'menu' => 'Home', 'tabel' => 'users', 'controller' => 'home', 'icon' => "fa-solid fa-earth-asia", 'url' => 'home', 'logo' => 'file_not_found.jpg'];
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

function gagal_with_button($url, $pesan)
{
    session()->setFlashdata('gagal_with_button', $pesan);
    header("Location: " . $url);
    die;
}

function sukses_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null)
{
    $data = [
        'status' => '200',
        'message' => $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4
    ];

    echo json_encode($data);
    die;
}

function gagal_js($pesan, $data = null, $data2 = null, $data3 = null, $data4 = null)
{
    $res = [
        'status' => '400',
        'message' =>  $pesan,
        'data' => $data,
        'data2' => $data2,
        'data3' => $data3,
        'data4' => $data4
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


function user()
{
    $db = db('users');

    $q = $db->where('id', session('id'))->get()->getRowArray();

    if (!$q) {
        gagal(base_url('landing/login'), 'Session expired!.');
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
    return 'Rp. ' . number_format($uang, 0, ",", ".");
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
