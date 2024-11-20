<?php

namespace App\Controllers;

class Barang extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db(menu()['tabel']);

        $q = $db->orderBy('barang', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
    }

    public function add()
    {
        $barang = upper_first(clear($this->request->getVar('barang')));
        $stok = clear($this->request->getVar('stok'));
        $jenis = upper_first(clear($this->request->getVar('jenis')));
        $harga_satuan = rp_to_int(clear($this->request->getVar('harga_satuan')));
        $data = [
            'barang' => $barang,
            'harga_satuan' => $harga_satuan,
            'jenis' => $jenis,
            'stok' => $stok
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
        $barang = upper_first(clear($this->request->getVar('barang')));
        $jenis = upper_first(clear($this->request->getVar('jenis')));
        $harga_satuan = rp_to_int(clear($this->request->getVar('harga_satuan')));
        $stok = clear($this->request->getVar('stok'));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['barang'] = $barang;
        $q['harga_satuan'] = $harga_satuan;
        $q['jenis'] = $jenis;
        $q['stok'] = $stok;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }


    public function cari_barang()
    {
        $value = clear($this->request->getVar('value'));

        $db = db('pengeluaran_kantin');
        $q = $db->like('barang', $value, 'both')->orderBy('barang', 'ASC')->get()->getResultArray();
        sukses_js('Koneksi sukses', $q);
    }
}
