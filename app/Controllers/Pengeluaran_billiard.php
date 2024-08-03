<?php

namespace App\Controllers;

class Pengeluaran_billiard extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index($tahun = null, $bulan = null): string
    {
        if ($tahun == null) {
            $tahun = date('Y');
        }
        if ($bulan == null) {
            $bulan = date('m');
        }
        $db = db(menu()['tabel']);

        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

        $data = [];

        foreach ($q as $i) {
            if ($tahun == 'All' && $bulan == 'All') {
                $data[] = $i;
            } elseif ($tahun !== 'All' && $bulan !== 'All') {
                if (date('Y', $i['tgl']) == $tahun && date('m', $i['tgl']) == $bulan) {
                    $data[] = $i;
                }
            } else if ($tahun == 'All' && $bulan !== 'All') {
                if (date('m', $i['tgl']) == $bulan) {
                    $data[] = $i;
                }
            } elseif ($tahun !== 'All' && $bulan == 'All') {
                if (date('Y', $i['tgl']) == $tahun) {
                    $data[] = $i;
                }
            }
        }

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $data, 'tahun' => $tahun, 'bulan' => $bulan]);
    }

    public function add()
    {
        $barang = upper_first(clear($this->request->getVar('barang')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $qty = clear($this->request->getVar('qty'));
        $is_inv = ($this->request->getVar('is_inv') == null ? 0 : 1);
        $pj = upper_first(clear($this->request->getVar('pj')));
        $data = [
            'barang' => $barang,
            'harga' => $harga,
            'qty' => $qty,
            'is_inv' => $is_inv,
            'pj' => $pj,
            'petugas' => user()['nama'],
            'tgl' => time()
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
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $qty = clear($this->request->getVar('qty'));
        $is_inv = ($this->request->getVar('is_inv') == null ? 0 : 1);
        $pj = upper_first(clear($this->request->getVar('pj')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['barang'] = $barang;
        $q['harga'] = $harga;
        $q['qty'] = $qty;
        $q['is_inv'] = $is_inv;
        $q['pj'] = $pj;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }
}
