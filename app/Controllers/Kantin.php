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

        $q = $db->orderBy('tgl', 'DESC')->orderBy('barang', 'DESC')->get()->getResultArray();
        $data = [];
        foreach ($q as $i) {
            if (date('n', $i['tgl']) == date('n') || date('n', $i['tgl']) == (date('n') - 1)) {
                $data[] = $i;
            }
        }
        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $data]);
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
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $uang = rp_to_int(clear($this->request->getVar('uang')));

        $db = db('barang');
        $dbk = db('kantin');

        $err = [];
        $total_harga = 0;
        foreach ($data as $i) {
            $q = $db->where('id', $i['barang_id'])->get()->getRowArray();
            if (!$q) {
                $err[] = 'Id ' . $i['barang_id'] . ' err';
                continue;
            }
            $value = [
                'barang_id' => $i['barang_id'],
                'barang' => $q['barang'],
                'harga_satuan' => $q['harga_satuan'],
                'tgl' => time(),
                'qty' => $i['qty'],
                'diskon' => $i['diskon'],
                'total_harga' => ($q['harga_satuan'] * $i['qty']) - $i['diskon'],
                'petugas' => user()['nama']
            ];
            $total_harga += $value['total_harga'];
            if ($dbk->insert($value)) {
                $q['stok'] = $q['stok'] - $i['qty'];
                $db->where('id', $q['id']);
                if (!$db->update($q)) {
                    $err[] = 'Update stock err';
                }
            } else {
                $err[] = 'Insert to kantin err';
            }
        }
        if (count($err) <= 0) {
            sukses_js('Save data success!.', ($uang - $total_harga));
        } else {
            gagal_js(implode(", ", $err));
        }
    }
}
