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
                $qb = $db->orderBy('meja', 'ASC')->get()->getResultArray();
                if (!$qb) {
                    gagal_js("Nama server tidak ditemukan!.");
                }

                foreach ($qb as $i) {
                    $meja = ($i['meja'] == 1 ? 10 : ($i['meja'] == 2 ? 11 : $i['meja']));
                    $hasil = $meja . $i['is_active'];
                    $data[] =  (int)$hasil;
                }
            } else {
                $statusArr = stringArr_to_arr($status_esp);
                $qb = [];

                $dbb = db('jadwal_2');
                foreach ($statusArr as $i) {
                    $q = $dbb->where('meja', $i['perangkat'])->get()->getRowArray();
                    if ($q && $q['is_active'] != $i['status']) {
                        $hasil = $q['meja'] . $q['is_active'];
                        $qb[] = $hasil;
                        $data[] =  (int)$hasil;
                    }
                }

                $qp = [];
                foreach ($statusArr as $i) {
                    $q = $db->where('grup', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->where("no_urut", $i['perangkat'])->get()->getRowArray();
                    if ($q && $q['status'] != $i['status']) {
                        $hasil = $q['no_urut'] . $q['status'];
                        $qp[] = $hasil;
                        $data[] =  (int)$hasil;
                    }
                }
            }


            sukses_js("sukses", $data, count($qp), count($qb), count($qp) + count($qb));
        }
    }
}
