<?php

namespace App\Controllers;

class Jadwal_2 extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db('jadwal_2');
        $q = $db->orderBy('meja', 'ASC')->get()->getResultArray();

        return view('jadwal_2', ['judul' => menu()['menu'] . ' - Billiard', 'data' => $q]);
    }

    public function add()
    {
        $meja = upper_first(clear($this->request->getVar('meja')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));

        $db = db('jadwal_2');
        $is_exist = $db->where('meja', $meja)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Data already exist!.');
        }

        $data = [
            'meja' => $meja,
            'harga' => $harga
        ];

        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), 'Save data success.');
        } else {
            gagal_with_button(base_url(menu()['controller']), 'Save data failed!');
        }
    }
    public function update_jadwal()
    {
        $id = clear($this->request->getVar('id'));
        $meja = clear($this->request->getVar('meja'));
        $harga = clear(rp_to_int($this->request->getVar('harga')));

        $db = db('jadwal_2');

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Id not found!.');
        }


        $q['meja'] = $meja;
        $q['harga'] = $harga;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js('Update data success.');
        } else {
            gagal_js('Update data failed!.');
        }
    }
}
