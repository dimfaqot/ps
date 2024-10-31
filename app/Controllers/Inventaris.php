<?php

namespace App\Controllers;

class Inventaris extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index($role = null): string
    {
        $d = db('inventaris');
        $qq = $d->get()->getResultArray();

        foreach ($qq as $i) {
            $i['jenis'] = 'Inv';
            $d->where('id', $i['id']);
            $d->update($i);
        }

        $db = db(menu()['tabel']);

        $db;
        if ($role !== null) {
            $db->where('role', $role);
        }
        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

        $dbk = db('options');
        $kondisi = $dbk->where('kategori', 'Kondisi')->orderBy('value', 'ASC')->get()->getResultArray();

        $data = [];
        foreach ($q as $i) {
            $i['tgl_str'] = date('Y-m-d', $i['tgl']);
            $data[] = $i;
        }

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $data, 'kondisi' => $kondisi]);
    }

    public function add()
    {
        $barang = upper_first(clear($this->request->getVar('barang')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $kondisi = upper_first(clear($this->request->getVar('kondisi')));
        $lokasi = upper_first(clear($this->request->getVar('lokasi')));
        $pembeli = upper_first(clear($this->request->getVar('pembeli')));
        $qty = clear($this->request->getVar('qty'));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $tgl = strtotime(clear($this->request->getVar('tgl')));

        $data = [
            'id' => get_last_no_inv(),
            'barang' => $barang,
            'jenis' => 'Inv',
            'harga' => $harga,
            'kondisi' => $kondisi,
            'lokasi' => $lokasi,
            'pembeli' => $pembeli,
            'qty' => $qty,
            'ket' => $ket,
            'tgl' => $tgl
        ];

        $db = db(menu()['tabel']);
        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), 'Save data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Save data failed!.');
        }
    }
    public function add_pengeluaran_ps()
    {
        $barang = upper_first(clear($this->request->getVar('barang')));
        $harga = rp_to_int(clear($this->request->getVar('harga')));
        $kondisi = '';
        $lokasi = '';
        $pembeli = upper_first(clear($this->request->getVar('pembeli')));
        $qty = clear($this->request->getVar('qty'));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $tgl = strtotime(clear($this->request->getVar('tgl')));

        $data = [
            'id' => get_last_no_inv(),
            'barang' => $barang,
            'jenis' => 'Pengeluaran',
            'harga' => $harga,
            'kondisi' => $kondisi,
            'lokasi' => $lokasi,
            'pembeli' => $pembeli,
            'qty' => $qty,
            'ket' => $ket,
            'tgl' => $tgl
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
        $kondisi = upper_first(clear($this->request->getVar('kondisi')));
        $lokasi = upper_first(clear($this->request->getVar('lokasi')));
        $pembeli = upper_first(clear($this->request->getVar('pembeli')));
        $qty = clear($this->request->getVar('qty'));
        $ket = upper_first(clear($this->request->getVar('ket')));
        $tgl = strtotime(clear($this->request->getVar('tgl')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['barang'] = $barang;
        $q['harga'] = $harga;
        $q['kondisi'] = $kondisi;
        $q['lokasi'] = $lokasi;
        $q['pembeli'] = $pembeli;
        $q['qty'] = $qty;
        $q['ket'] = $ket;
        $q['tgl'] = $tgl;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }
}
