<?php

namespace App\Controllers;

class Finger extends BaseController
{
    public function get_booking()
    {
        $jwt = $this->request->getVar('jwt');
        decode_jwt_finger($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();
        if ($q) {
            sukses_js('Silahkan sentuh fingerprint!.', $q['kategori']);
        } else {
            gagal_js('Silahkan pilih menu!');
        }
    }

    public function absen()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        $dbu = db('users');
        $q = $dbu->whereNotIn("role", ["Member"])->where('finger', $decode['uid'])->get()->getRowArray();

        if (!$q) {
            gagal_js("Finger tidak terdaftar!.");
        }

        $val = get_absen($q);

        $value = [
            'tgl' => date('d', $val['time_server']),
            'username' => $q["username"],
            'ket' => $val['ket'],
            'poin' => $val['poin'],
            'nama' => $q["nama"],
            'role' => $q["role"],
            'user_id' => $q["id"],
            'shift' => $val['shift'],
            'jam' => $val['jam'],
            'absen' => $val['time_server'],
            'terlambat' => $val['menit']
        ];
        $db = db('absen');
        if ($db->insert($value)) {
            $dbn = db('notif');
            $datan = [
                'kategori' => 'Absen',
                'pemesan' => $value['nama'],
                'tgl' => $value['absen'],
                'harga' => time(),
                'menu' => ($value['ket'] == 'Ontime' ? 'Absen pada ' . date('H:i', $val['time_server']) : $val['diff']),
                'meja' => $value['ket'],
                'qty' => $value['poin']
            ];

            if ($dbn->insert($datan)) {
                $dbm = db("message");
                if ($val['ket'] == 'Terlambat') {
                    $message = ["message" => $val["msg"], "status" => "200", "kategori" => "Absen"];
                    if ($dbm->insert($message)) {
                        clean_path('booking');
                        clean_path('api');
                        sukses_js($val['msg']);
                    }
                } else {
                    $message = ["message" => $val["msg"], "status" => "400", "kategori" => "Absen"];
                    if ($dbm->insert($message)) {
                        clean_path('booking');
                        clean_path('api');
                        gagal_js($val['msg']);
                    }
                }
            } else {
                gagal_js("Insert notif gagal!.");
            }
        } else {
            gagal_js("Insert absen gagal!.");
        }
    }
    public function add_message()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        message($decode["uid"], $decode["data3"], $decode["data2"], $decode["data4"], $decode["data5"]);
        sukses_js("Add message sukses.");
    }
    public function add()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        message($decode["uid"], $decode["data3"], $decode["data2"], $decode["data4"], $decode["data5"]);
        sukses_js("Add message sukses.");
    }
}
