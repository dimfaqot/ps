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
        $data = [
            "uid" => clear($this->request->getVar('uid')),
            "kategori" => clear($this->request->getVar('kategori')),
            "meja" => clear($this->request->getVar('meja')),
            "durasi" => clear($this->request->getVar('durasi'))
        ];
        $db = db('fulus');
        if ($db->insert($data)) {
            sukses_js('Ok');
        } else {
            gagal_js('Salah!');
        }
    }
}
