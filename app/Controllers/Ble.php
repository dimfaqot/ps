<?php

namespace App\Controllers;

class Ble extends BaseController
{
    public function settings()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
        $nama_server = $decode['uid'];

        $db = db('ble');

        $q = $db->where('nama_server', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->get()->getRowArray();

        if (!$q) {
            gagal_js("Nama server tidak ditemukan!.");
        }

        sukses_js("sukses", $q['uuid'], $q['karakteristik']);
    }
    public function perangkat()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
        $nama_server = $decode['uid'];
        $status_esp = $decode['data2'];

        $jml_perangkat = 0;
        $jml_meja = 0;

        $db = db('perangkat');
        $qp = $db->where('grup', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->orderBy('no_urut', 'ASC')->get()->getResultArray();
        if (!$qp) {
            gagal_js("Nama server tidak ditemukan!.");
        }
        $data = [];

        if ($nama_server == "Billiards") {
            $db = db('jadwal_2');
            if ($status_esp == "") {
                foreach ($qp as $i) {
                    $hasil = $i['no_urut'] . $i['status'];
                    $data[] = (int)$hasil;
                }
                $jml_perangkat = count($qp);
                $qb = $db->orderBy('meja', 'ASC')->get()->getResultArray();
                if (!$qb) {
                    gagal_js("Nama server tidak ditemukan!.");
                }
                $jml_meja = count($qb);
                foreach ($qb as $i) {
                    $meja = ($i['meja'] == 1 ? 10 : ($i['meja'] == 2 ? 11 : $i['meja']));
                    $hasil = $meja . $i['is_active'];
                    $data[] =  (int)$hasil;
                }
            } else {
                $statusArr = stringArr_to_arr($status_esp);
                $jml_mej = [];

                $dbb = db('jadwal_2');
                foreach ($statusArr as $i) {
                    $q = $dbb->where('meja', $i['perangkat'])->get()->getRowArray();
                    if ($q && $q['is_active'] != $i['status']) {
                        $hasil = $q['meja'] . $q['is_active'];
                        $jml_mej[] = $hasil;
                        $data[] =  (int)$hasil;
                    }
                }
                $jml_meja = count($jml_mej);
                $jml_per = [];
                foreach ($statusArr as $i) {
                    $q = $db->where('grup', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->where("no_urut", $i['perangkat'])->get()->getRowArray();
                    if ($q && $q['status'] != $i['status']) {
                        $hasil = $q['no_urut'] . $q['status'];
                        $jml_per[] = $hasil;
                        $data[] =  (int)$hasil;
                    }
                }
                $jml_perangkat = count($jml_per);
            }


            sukses_js("sukses", (count($data) == 1 ? "" : $data), $jml_perangkat, $jml_meja, $jml_perangkat + $jml_meja);
        }
    }
}
