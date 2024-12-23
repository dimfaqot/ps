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
    public function hutang()
    {
        $data = json_decode(json_encode($this->request->getVar("data")), true);
        $user_id = clear($this->request->getVar("user_id"));
        $dbl = db('layanan');
        $dbh = db('hutang');
        $dbu = db("users");
        $user = $dbu->where('id', $user_id)->get()->getRowArray();
        if (!$user) {
            gagal_js("User id not found!.");
        }
        $no_nota = no_nota("barber");
        foreach ($data as $i) {
            $q = $dbl->where('id', $i['layanan_id'])->get()->getRowArray();
            if (!$q) {
                $err[] = 'Id ' . $i['layanan_id'] . ' err';
                continue;
            }
            $value = [
                "kategori" => "Barber",
                "no_nota" => $no_nota,
                "user_id" => $user['id'],
                "nama" => $user['nama'],
                "tgl" => time(),
                "teller" => user()['nama'],
                "status" => 0,
                'barang_id' => $i['layanan_id'],
                'barang' => $q['layanan'],
                'harga_satuan' => $q['harga'],
                'qty' => $i['qty'],
                'total_harga' => $q['harga'] * $i['qty']
            ];
            $dbh->insert($value);
        }

        sukses_js("Sukses.");
    }
}
