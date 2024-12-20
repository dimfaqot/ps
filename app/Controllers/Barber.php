<?php

namespace App\Controllers;

class Barber extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db(menu()['tabel']);

        $q = $db->orderBy('tgl', 'DESC')->orderBy('layanan', 'DESC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
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

    public function pembayaran()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $uang = rp_to_int(clear($this->request->getVar('uang')));

        $db = db('layanan');
        $dbk = db('barber');

        $err = [];
        $total_harga = 0;
        foreach ($data as $i) {
            $q = $db->where('id', $i['layanan_id'])->get()->getRowArray();
            if (!$q) {
                $err[] = 'Id ' . $i['layanan_id'] . ' err';
                continue;
            }
            $value = [
                'layanan_id' => $i['layanan_id'],
                'qty' => $i['qty'],
                'layanan' => $q['layanan'],
                'harga' => $q['harga'],
                'tgl' => time(),
                'diskon' => $i['diskon'],
                'total_harga' => ($q['harga'] * $i['qty']) - $i['diskon'],
                'metode' => 'Cash',
                'status' => 1,
                'petugas' => user()['nama']
            ];
            $total_harga += $value['total_harga'];
            if (!$dbk->insert($value)) {
                $err[] = 'Insert to barber err';
            }
        }
        if (count($err) <= 0) {
            sukses_js('Save data success!.', ($uang - $total_harga));
        } else {
            gagal_js(implode(", ", $err));
        }
    }
    public function pembayaran_tap()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);

        $db = db('layanan');
        $dbk = db('barber');

        $err = [];
        $total_harga = 0;
        foreach ($data as $i) {
            $q = $db->where('id', $i['layanan_id'])->get()->getRowArray();
            if (!$q) {
                $err[] = 'Id ' . $i['layanan_id'] . ' err';
                continue;
            }
            $value = [
                'layanan_id' => $i['layanan_id'],
                'qty' => $i['qty'],
                'layanan' => $q['layanan'],
                'harga' => $q['harga'],
                'tgl' => time(),
                'diskon' => $i['diskon'],
                'total_harga' => ($q['harga'] * $i['qty']) - $i['diskon'],
                'metode' => 'Tap',
                'status' => 0,
                'petugas' => user()['nama']
            ];
            $total_harga += $value['total_harga'];
            if (!$dbk->insert($value)) {
                $err[] = 'Insert to barber err';
            }
        }
        if (count($err) <= 0) {
            sukses_js('Sukses!. Segera tap kartu. Total: ' . $total_harga);
        } else {
            gagal_js(implode(", ", $err));
        }
    }
}
