<?php

namespace App\Controllers;

class Wifi extends BaseController
{
    public function settings()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
        $nama_server = $decode['uid'];

        $db = db('ble');

        $q = $db->where('nama_server', $nama_server)->get()->getRowArray();

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

        $data = [];
        $pin = [];
        $macs = [];

        $db = db('perangkat');
        $qp = $db->where('grup', $nama_server)->orderBy('no_urut', 'ASC')->get()->getResultArray();
        $macQp = $db->where('grup', $nama_server)->whereNotIn('mac', [''])->get()->getRowArray();
        if (!$qp) {
            gagal_js("Nama server tidak ditemukan!.");
        }

        if ($nama_server == "Billiard") {
            $dbj = db('jadwal_2');
            if ($status_esp == "") {
                $macs[] = $macQp['mac'];
                foreach ($qp as $i) {
                    $pin[] = $i['pin'];
                    $data[] = ['mac' => $macQp['mac'], 'pin' => $i['pin'], 'status' => $i['status']];
                }

                $qb = $dbj->orderBy('meja', 'ASC')->get()->getResultArray();
                if (!$qb) {
                    gagal_js("Nama server tidak ditemukan!.");
                }
                $pin[] = 21;
                foreach ($qb as $i) {
                    if (!in_array($i['mac'], $macs) && $i['mac'] !== "") {
                        $macs[] = $i['mac'];
                    }
                    $data[] = ['mac' => $i['mac'], 'pin' => 21, 'status' => $i['is_active']];
                }
            } else {
                $statusArr = stringArr_to_arr($status_esp);
                $jml_mej = [];

                $dbb = db('jadwal_2');
                foreach ($statusArr as $i) {
                    $no_urut = "";
                    // $q = $dbb->where('meja', $mej)->get()->getRowArray();
                    // if ($i['perangkat'] >= 10) {
                    //     if ($q && $mej == $q['meja'] && $q['is_active'] != $i['status']) {
                    //         $meja = ($q['meja'] == 1 ? 10 : ($q['meja'] == 2 ? 11 : $q['meja']));
                    //         $hasil = $meja . $q['is_active'];
                    //         $jml_mej[] = $hasil;
                    //         $data[] =  (int)$hasil;
                    //     }
                    // }
                }
                $jml_meja = count($jml_mej);

                $jml_per = [];
                foreach ($statusArr as $i) {
                    $q = $db->where('grup', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->where("no_urut", $i['perangkat'])->get()->getRowArray();
                    // if ($q && $q['status'] != $i['status']) {
                    // }

                    // dd("tidak masuk");
                    if ($q && $q['status'] != $i['status']) {
                        $hasil = $q['no_urut'] . $q['status'];
                        $jml_per[] = $hasil;
                        $data[] =  (int)$hasil;
                    }
                }
                $jml_perangkat = count($jml_per);
            }
            sukses_js("sukses", $data, $pin, count($data), $macs);
        }
    }
}
