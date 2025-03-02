<?php

namespace App\Controllers;

class Basil extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function basil_kotor($tahun = "", $bulan = "", $order = "hide")
    {

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'bulan' => ($bulan == "" ? date("m") : $bulan), 'tahun' => ($tahun == "" ? date("Y") : $tahun), 'order' => $order]);
    }
    public function basil_bersih($tahun = "", $bulan = "")
    {
        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'bulan' => ($bulan == "" ? date("m") : $bulan), 'tahun' => ($tahun == "" ? date("Y") : $tahun)]);
    }
    public function save()
    {
        $bulan = clear($this->request->getVar('bulan'));
        $tahun = clear($this->request->getVar('tahun'));

        if ($bulan == "All") {
            gagal(base_url('laba'), "Bulan atau Tahun tidak boleh: All");
        }

        $bulan = ($bulan == "" ? date('m') : $bulan);
        $tahun = ($tahun == "" ? date('Y') : $tahun);

        $db = db('saham');
        $q = $db->where('tahun', $tahun)->where('bulan', $bulan)->get()->getResultArray();

        if ($q) {
            gagal_js(base_url('laba'), "Tahun dan bulan tersebut telah diinput!.");
        }

        $laba = basil_bersih($bulan, $tahun);

        $jenis = $laba['orders'];
        $kepada = $laba['grup'];
        $data = [];
        foreach ($jenis as $j) {
            $data[] = [
                'tahun' => $tahun,
                'bulan' => $bulan,
                'kategori' => $j,
                'kepada' => "Masuk",
                'persen' => 0,
                'jml' => $laba['data'][$j]['Masuk']
            ];
        }

        foreach ($jenis as $j) {
            foreach ($kepada as $i) {
                $data[] = [
                    'tahun' => $tahun,
                    'bulan' => $bulan,
                    'kategori' => $j,
                    'kepada' => $i['kepada'],
                    'persen' => 0,
                    'jml' => $laba['data'][$j][$i['kepada']]
                ];
            }
        }

        dd($data);
    }

    public function basil($tahun = "", $bulan = "")
    {
        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'bulan' => ($bulan == "" ? date("m") : $bulan), 'tahun' => ($tahun == "" ? date("Y") : $tahun)]);
    }

    public function data_pengeluaran()
    {
        $kepada = clear($this->request->getVar('kepada'));

        $db = db('basil_keluar');
        $q = $db->where('kepada', $kepada)->orderBy('tgl', 'DESC')->get()->getResultArray();

        sukses_js('Sukses', $q);
    }
    public function add_pengeluaran()
    {
        $kepada = clear($this->request->getVar('kepada'));
        $jml = (int)clear($this->request->getVar('jml'));
        $jml_keluar = (int)clear($this->request->getVar('jml_keluar'));
        $penerima_keluar = upper_first(clear($this->request->getVar('penerima_keluar')));
        if ($jml_keluar > $jml) {
            gagal_js("Melebihi limit!.");
        }
        $data = [
            'tgl' => time(),
            'kepada' => $kepada,
            'jml' => $jml_keluar,
            'penerima' => $penerima_keluar
        ];

        $db = db('basil_keluar');

        if ($db->insert($data)) {
            sukses_js("Data berhasil disimpan!.");
        } else {
            gagal_js("Data gagal disimpan!.");
        }
    }
}
