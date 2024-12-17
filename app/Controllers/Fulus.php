<?php

namespace App\Controllers;

class Fulus extends BaseController
{

    public function get()
    {
        $db = db('fulus');
        $q = $db->get()->getRowArray();
        sukses_js('Ok', $q);
    }
    public function add()
    {
        // $key = clear($this->request->getVar('key'));
        // $data = [
        //     "uid" => clear($this->request->getVar('uid')),
        //     "kategori" => clear($this->request->getVar('kategori')),
        //     "meja" => clear($this->request->getVar('meja')),
        //     "durasi" => clear($this->request->getVar('durasi'))
        // ];

        $jwt = $this->request->getVar('jwt');
        $key = $this->request->getVar('key');

        $encode = encode_jwt_fulus($key, $jwt);
        $dbu = db('users');
        $usr = $dbu->where("uid", $encode['uid'])->get()->getRowArray();

        if (!$usr) {
            gagal_js('Kartu tidak terdaftar!.');
        }

        $data = [
            "uid" => $encode['uid'],
            "kategori" => $encode['kategori'],
            "meja" => $encode['meja'],
            "durasi" => $encode['durasi']
        ];

        $db = db('fulus');
        if ($db->insert($data)) {
            $rand = generateRandomString();
            $res = ['key' => $rand, 'saldo' => $usr];

            sukses_js('Ok', encode_jwt_fulus($rand, $res));
        } else {
            gagal_js('Salah!');
        }
    }
}
