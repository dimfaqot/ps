<?php

namespace App\Controllers;

class Layanan extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db(menu()['tabel']);

        $q = $db->orderBy('layanan', 'ASC')->get()->getResultArray();


        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - BARBER', 'data' => $q]);
    }

    public function add()
    {
        $layanan = upper_first(clear($this->request->getVar('layanan')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $data = [
            'layanan' => $layanan,
            'harga' => $harga
        ];

        $db = db(menu()['tabel']);
        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), 'Save data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Save data failed!.');
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $layanan = upper_first(clear($this->request->getVar('layanan')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['layanan'] = $layanan;
        $q['harga'] = $harga;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }


    public function cari_layanan()
    {
        $value = clear($this->request->getVar('value'));

        $db = db('pengeluaran_barber');
        $q = $db->like('layanan', $value, 'both')->orderBy('layanan', 'ASC')->get()->getResultArray();
        sukses_js('Koneksi sukses', $q);
    }
}
