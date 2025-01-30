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
        $macs = [];
        $cek_perubahan = 0;
        $no_urut_status = [];

        $db = db('perangkat');
        $qp = $db->where('grup', $nama_server)->orderBy('no_urut', 'ASC')->get()->getResultArray();
        $macQp = $db->where('grup', $nama_server)->whereNotIn('mac', [''])->get()->getRowArray();
        if (!$qp) {
            gagal_js("Nama server tidak ditemukan!.");
        }

        if ($nama_server == "Billiard") {
            $dbj = db('jadwal_2');
            if ($status_esp == "" || $status_esp == "1") {
                $macs[] = $macQp['mac'];
                foreach ($qp as $i) {
                    $no_urut_status[] = $i['no_urut'] . '|' . $i['is_active'];
                    $data[] = ['mac' => $macQp['mac'], 'pin' => $i['pin'], 'status' => $i['status'], 'no_urut' => $i['no_urut']];
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
                    $no_urut_status[] = $i['no_urut'] . '|' . $i['is_active'];
                    $data[] = ['mac' => $i['mac'], 'pin' => 21, 'status' => $i['is_active'], 'no_urut' => $i['no_urut']];
                }

                if ($status_esp == "") {
                    sukses_js("Mulai", $macs);
                }
                if ($status_esp == "1") {
                    sukses_js("Berubah", $data, $no_urut_status);
                }
            } else {
                $statusArr = stringArr_to_arr($status_esp);

                foreach ($statusArr as $i) {
                    $no_urut = $i['no_urut'];
                    if ($no_urut > 10) {
                        $qm = $dbj->where('no_urut', $no_urut)->get()->getRowArray();
                        if ($qm['is_active'] !== $i['status']) {
                            $cek_perubahan++;
                            $no_urut_status[] = $i['no_urut'] . '|' . $qm['is_active'];
                            $data[] = ['mac' => $qm['mac'], 'pin' => 21, 'status' => $qm['is_active'], 'no_urut' => $i['no_urut']];
                        }
                    } else {
                        foreach ($qp as $p) {
                            if ($p['no_urut'] == $i['no_urut']) {
                                if ($i['status'] !== $p['status']) {
                                    $no_urut_status[] = $i['no_urut'] . '|' . $p['status'];
                                    $cek_perubahan++;
                                    $data[] = ['mac' => $macQp['mac'], 'pin' => $p['pin'], 'status' => $p['status'], 'no_urut' => $i['no_urut']];
                                }
                            }
                        }
                    }
                }
                if ($cek_perubahan == 0) {
                    sukses_js("Sama", $data);
                }
                if ($cek_perubahan > 0) {
                    sukses_js("Berubah", $data, $no_urut_status);
                }
            }
        }
    }
}
