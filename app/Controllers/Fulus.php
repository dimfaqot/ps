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
        $decode = decode_jwt_fulus($jwt);
        $dbu = db('users');
        $usr = $dbu->where("uid", $decode['uid'])->get()->getRowArray();

        if (!$usr) {
            gagal_js('Kartu tidak terdaftar!.');
        }

        $data = [
            "uid" => $decode['uid'],
            "kategori" => $decode['kategori'],
            "meja" => $decode['meja'],
            "durasi" => $decode['durasi']
        ];

        $db = db('fulus');
        $q = $db->get()->getResultArray();

        if ($q) {
            $db->delete();
        }
        if ($db->insert($data)) {
            sukses_js('Ok', rupiah($usr['fulus']));
        } else {
            gagal_js('Salah!');
        }
    }
}
