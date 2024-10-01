<?php

namespace App\Controllers;

class Js extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role('login');
    }
    public function select()
    {

        $col = clear($this->request->getVar('col'));
        $where = clear($this->request->getVar('where'));
        $value = clear($this->request->getVar('value'));
        $tabel = clear($this->request->getVar('tabel'));
        $orderby = clear($this->request->getVar('orderby'));

        $db = db($tabel);
        $db;

        $exp_where = explode("=", $where);
        if (count($exp_where) == 2) {
            $db->whereIn($exp_where[0], [$exp_where[1]]);
        }
        $exp = explode("=", $orderby);
        $q = $db->like($col, $value, 'both')->orderBy($exp[0], $exp[1])->get()->getResultArray();

        sukses_js('Query sukses', $q);
    }

    public function check_is_exist()
    {
        $col = clear($this->request->getVar('col'));
        $order = clear($this->request->getVar('order'));
        $value = clear($this->request->getVar('value'));
        $id = clear($this->request->getVar('id'));
        $db = db(clear($this->request->getVar('tabel')));

        if (str_contains($value, " ")) {
            sukses_js('Space is not allowed!.', 'text_danger', 'text_success');
        }

        if (strlen($value) < 6) {
            sukses_js('Username min 5 chars!.', 'text_danger', 'text_success');
        }


        if ($order == 'add') {
            $q = $db->where($col, $value,)->get()->getRowArray();
        }
        if ($order == 'update') {
            $q = $db->whereNotIn('id', [$id])->where($col, $value)->get()->getRowArray();
        }

        if ($q) {
            sukses_js('Username already taken!.', 'text_danger', 'text_success');
        } else {
            sukses_js('Username available.', 'text_success', 'text_danger');
        }
    }
    public function select_barang()
    {
        $value = clear($this->request->getVar('value'));
        $db = db('barang');
        $q = $db->like('barang', $value, 'both')->orderBy('barang', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js('Koneksi sukses.', $q);
    }
    public function select_layanan()
    {
        $value = clear($this->request->getVar('value'));
        $db = db('layanan');
        $q = $db->like('layanan', $value, 'both')->orderBy('layanan', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js('Koneksi sukses.', $q);
    }
}
