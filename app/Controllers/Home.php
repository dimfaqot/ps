<?php

namespace App\Controllers;

class Home extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }
    public function index(): string
    {
        // $db = db('rental');
        // $q = $db->get()->getResultArray();

        // $dbu = db('unit');
        // foreach ($q as $i) {
        //     $qu = $dbu->where('id', $i['unit_id'])->get()->getRowArray();

        //     $i['meja'] = $qu['unit'];
        //     $db->where('id', $i['id']);
        //     $db->update($i);
        // }

        $db = db('billiard');
        $q = $db->get()->getResultArray();

        $dbu = db('jadwal');
        foreach ($q as $i) {
            $qu = $dbu->where('id', $i['jadwal_id'])->get()->getRowArray();
            $i['meja'] = $qu['meja'];
            $db->where('id', $i['id']);
            $db->update($i);
        }
        return view('home', ['judul' => 'Home - PS']);
    }

    public function get_pendapatan()
    {
        $tabel = clear($this->request->getVar('tabel'));
        $tahun = clear($this->request->getVar('tahun'));

        $db = db($tabel);

        $q = $db->get()->getResultArray();

        $data_tahun = [];

        // mencari tahun
        foreach ($q as $i) {
            if ($tahun == 'All') {
                $data_tahun[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_tahun[] = $i;
                }
            }
        }

        // mencari bulan

        $res = [];

        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_tahun as $i) {
                if ($b['angka'] == date('m', $i['tgl'])) {
                    $temp[] = $i;
                    $total += $i['biaya'];
                }
            }
            $res[] = ['bulan' => $b['satuan'], 'data' => $temp, 'total' => $total];
        }

        sukses_js('Connection success.', $res);
    }
}
