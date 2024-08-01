<?php

namespace App\Controllers;

class Kantin extends BaseController
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
        $harga_satuan = rp_to_int(clear($this->request->getVar('harga_satuan')));
        $data = [
            'barang' => $barang,
            'harga_satuan' => $harga_satuan,
            'stok' => $stok
        ];

        $db = db(menu()['tabel']);
        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), 'Save data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Save data failed!.');
        }
    }
    public function pembayaran()
    {
        $barang = upper_first(clear($this->request->getVar('barang')));
        $barang_id = clear($this->request->getVar('id'));
        $qty = clear($this->request->getVar('qty'));
        $stok = clear($this->request->getVar('stok'));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $harga_satuan = rp_to_int(clear($this->request->getVar('harga_satuan')));
        $total_harga = rp_to_int(clear($this->request->getVar('total_biaya')));
        $uang = rp_to_int(clear($this->request->getVar('uang')));

        if ($qty < $stok) {
            gagal_js('ml. tidak boleh melebihi stok!.');
        }
        if ($uang < (($harga_satuan * $qty) - $diskon)) {
            gagal_js('Uang tidak boleh lebih kecil dari harga!.');
        }
        if ($diskon > ($harga_satuan * $qty)) {
            gagal_js('Diskon tidak boleh lebih besar dari harga!.');
        }

        if (($total_harga - $diskon) !== (($harga_satuan * $qty) - $diskon)) {
            gagal_js('Jumlah harga beda dengan yang seharusnya!.');
        }

        $data = [
            'barang_id' => $barang_id,
            'barang' => $barang,
            'qty' => $qty,
            'diskon' => $diskon,
            'harga_satuan' => $harga_satuan,
            'total_harga' => (($harga_satuan * $qty) - $diskon),
            'petugas' => user()['nama'],
            'tgl' => time()
        ];

        $db = db(menu()['tabel']);
        if ($db->insert($data)) {
            $dbb = db('barang');
            $q = $dbb->where('id', $barang_id)->get()->getRowArray();

            if (!$q) {
                gagal_js('Id barang tidak ditemukan!.');
            }
            $q['stok'] = $q['stok'] - $qty;

            $dbb->where('id', $barang_id);

            if ($dbb->update($q)) {
                sukses_js('Pembayaran sukses.', ($uang - (($harga_satuan * $qty) - $diskon)));
            } else {
                sukses_js('Update stock failed!.', ($uang - (($harga_satuan * $qty) - $diskon)));
            }
        } else {
            gagal(base_url(menu()['controller']), 'Save data failed!.');
        }
    }
}
