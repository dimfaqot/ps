<?php

namespace App\Controllers;

class Aturan extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }
    public function index(): string
    {
        $db = db(menu()['tabel']);

        $q = $db->orderBy('poin', 'DESC')->get()->getResultArray();


        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
    }

    public function add()
    {
        $aturan = clear($this->request->getVar('aturan'));
        $poin = clear($this->request->getVar('poin'));
        $data = [
            'aturan' => $aturan,
            'poin' => $poin
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
        $aturan = clear($this->request->getVar('aturan'));
        $poin = clear($this->request->getVar('poin'));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['aturan'] = $aturan;
        $q['poin'] = $poin;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }

    // public function tap()
    // {
    //     $tahun = clear($this->request->getVar("tahun"));
    //     $bulan = clear($this->request->getVar("bulan"));
    //     $kategori = clear($this->request->getVar("kategori"));

    //     $tabel = "billiard_2";
    //     if ($kategori !== "Billiard") {
    //         if ($kategori == "Ps") {
    //             $tabel = "rental";
    //         } else {
    //             $tabel = strtolower($kategori);
    //         }
    //     }

    //     $db = db($tabel);
    //     $q = $db->where("metode", "Tap")->get()->getResultArray();
    //     $data = [];
    //     foreach ($q as $i) {
    //         if (date("Y", $i['tgl']) == $tahun && date("m", $i['tgl']) == $bulan) {
    //             $val = [
    //                 "tgl" => date("d/m/Y", $i['tgl']),
    //                 "barang" => ($kategori == "Barber" ? $i["layanan"] : ($kategori == "Kantin" ? $i["barang"] : $i["meja"])),
    //                 "harga" => ($kategori == "Billiard" || $kategori == "Ps" ? $i["biaya"] : $i["total_harga"])
    //             ];

    //             $data[] = $val;
    //         }
    //     }

    //     sukses_js("Ok", $data);
    // }
}
