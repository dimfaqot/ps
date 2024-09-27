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
        return view('home', ['judul' => 'Home - PS']);
    }

    public function get_pendapatan()
    {
        $tabel = clear($this->request->getVar('tabel'));
        $tahun = clear($this->request->getVar('tahun'));

        $db = db($tabel);

        $q = $db->get()->getResultArray();

        $data_tahun = [];
        $data_tahun_p = [];

        $tbl_pengeluaran = ($tabel == 'rental' ? 'inventaris' : 'pengeluaran_' . $tabel);
        $dbp = db($tbl_pengeluaran);
        $p = $dbp->get()->getResultArray();

        // mencari tahun pemasukan
        foreach ($q as $i) {
            if ($tabel == 'rental') {
                $i['biaya'] = $i['biaya'] - $i['diskon'];
            }
            if ($tahun == 'All') {
                $data_tahun[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_tahun[] = $i;
                }
            }
        }
        // mencari tahun pengeluaran
        foreach ($p as $i) {
            if ($tahun == 'All') {
                $data_tahun_p[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_tahun_p[] = $i;
                }
            }
        }




        // mencari bulan

        $res = [];

        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_tahun as $i) {
                $i['tanggal'] = date('d/m/Y', $i['tgl']);
                if ($b['angka'] == date('m', $i['tgl'])) {
                    if ($tabel == 'kantin') {
                        $i['biaya'] = $i['total_harga'];
                    }
                    $temp[] = $i;
                    $total +=  $i['biaya'];
                }
            }
            $res[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        $res_p = [];

        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_tahun_p as $i) {
                $i['tanggal'] = date('d/m/Y', $i['tgl']);
                if ($b['angka'] == date('m', $i['tgl'])) {
                    $temp[] = $i;
                    $total +=  $i['harga'];
                }
            }
            $res_p[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        sukses_js('Connection success.', $res, $res_p);
    }
}
