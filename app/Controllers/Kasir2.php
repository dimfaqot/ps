<?php

namespace App\Controllers;

class Kasir2 extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index()
    {
        return view('kasir2', ['judul' => menu()['menu'] . ' - PS']);
    }
    public function get_data()
    {
        $menu = clear($this->request->getVar('menu'));

        $res = [];
        if ($menu == "billiard") {
            $db_meja = db('jadwal_2');
            $meja = $db_meja->orderBy('meja', 'ASC')->get()->getResultArray();
            $db_billiard = db('billiard_2');

            foreach ($meja as $i) {
                $q = $db_billiard->where('meja', "Meja " . $i['meja'])->where('is_active', 1)->get()->getRowArray();

                $temp = [
                    'id' => $i['id'],
                    'meja_id' => 0,
                    'durasi' => 0,
                    'petugas' => user()['nama'],
                    'tgl' => time(),
                    'meja' => "Meja " . $i['meja'],
                    'harga' => $i['harga'],
                    'biaya' => 0,
                    'diskon' => 0,
                    'start' => $i['start'],
                    'end' => 0,
                    'is_active' => 0,
                    'metode' => 'Cash',
                    'ket' => 'Available',
                    'jam' => '',
                    'waktu' => '',
                    'text' => 'text-success',
                    'durasi' => 0
                ];

                if ($q) {
                    $temp = $q;
                    $temp['jam'] = $q['durasi'] / 60;
                    $temp['meja_id'] = $i['id'];
                    if ($q['durasi'] == 0) {
                        $temp['ket'] = "Open";
                        $temp['text'] = "text-secondary";
                        $temp['waktu'] = durasi_waktu($q['start']);
                    } else {
                        $temp['ket'] = (time() > $q['end'] ? "Over" : "In Game");
                        $temp['text'] = ($temp['ket'] == "Over" ? "text-danger" : "text-secondary");
                        $temp['waktu'] = (time() > $q['end'] ? -1 : "-" . durasi(time(), $q['end']));
                    }
                }

                $res[] = $temp;
            }
        }

        sukses_js("Ok", $res);
    }

    public function execute()
    {
        $menu = clear($this->request->getVar('menu'));
        $durasi = clear($this->request->getVar('durasi'));
        $order = clear($this->request->getVar('order'));
        $id = clear($this->request->getVar('id'));

        $now = time();

        if ($menu == "billiard") {
            $db_meja = db('jadwal_2');
            $db_billiard = db('billiard_2');
            if ($order == "play") {
                $meja = $db_meja->where('id', $id)->get()->getRowArray();
                $meja['start'] = $now;
                $meja['is_active'] = 1;

                $db_meja->where('id', $id);
                if ($db_meja->update($meja)) {
                    $data = [
                        'meja_id' => $meja['id'],
                        'meja' => "Meja " . $meja['meja'],
                        'tgl' => $now,
                        'durasi' => 0,
                        'petugas' => user()['nama'],
                        'biaya' => 0,
                        'diskon' => 0,
                        'start' => $now,
                        'end' => 0,
                        'is_active' => 1,
                        'harga' => $meja['harga'],
                        'metode' => "Cash"
                    ];

                    if ($durasi > 0) {
                        $data['end'] = $now + ((60 * $durasi) * 60);
                        $data['durasi'] = 60 * $durasi;
                        $data['biaya'] = $durasi * $meja['harga'];
                    }

                    if ($db_billiard->insert($data)) {
                        sukses_js("Berhasil");
                    } else {
                        gagal_js("Gagal");
                    }
                }
            }
        }
    }

    public function add_change()
    {
        $meja = clear($this->request->getVar('meja'));
        $val = clear($this->request->getVar('val'));
        $menu = clear($this->request->getVar('menu'));
        $order = clear($this->request->getVar('order'));
        $id = clear($this->request->getVar('id'));

        if ($menu == "billiard") {
            $db = db('billiard_2');
            $q = $db->where('id', $id)->where('is_active', 1)->get()->getRowArray();
            if (!$q) {
                gagal_js("Id tidak ditemukan");
            }
            if ($order == "add") {
                $q['durasi'] += $val * 60;
                $q['end'] += (60 * $val) * 60;
            }
            if ($order == "change") {
                $exp = explode(" ", $val);

                $db_meja = db('jadwal_2');

                $meja = $db_meja->where('meja', end($exp))->get()->getRowArray(); //meja tujuan
                $meja_awal = $db_meja->where('id', $q['meja_id'])->where('is_active', 1)->get()->getRowArray();

                if (!$meja_awal) {
                    gagal_js("Id meja tidak ditemukan");
                }
                if (!$meja) {
                    gagal_js("Id meja tujuan tidak ditemukan");
                }

                // tabel billiard 2
                $q['meja_id'] = $meja['id'];
                $q['meja'] = "Meja " . $meja['meja'];

                // meja tujuan
                $meja['start'] = $q['start'];
                $meja['is_active'] = 1;

                $db_meja->where('id', $meja['id']); //meja tujuan
                if ($db_meja->update($meja)) {
                    $meja_awal['start'] = 0;
                    $meja_awal['is_active'] = 0;

                    $db_meja->where('id', $meja_awal['id']);

                    if ($db_meja->update($meja_awal)) {
                        $db->where('id', $id);
                        if ($db->update($q)) {
                            sukses_js("Sukses");
                        }
                    }
                }
            }

            $db->where('id', $id);
            if ($db->update($q)) {
                sukses_js("Sukses");
            }
        }
    }

    public function bayar()
    {
        $biaya = rp_to_int(clear($this->request->getVar('biaya')));
        $uang = rp_to_int(clear($this->request->getVar('uang')));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $menu = clear($this->request->getVar('menu'));
        $id = clear($this->request->getVar('id'));

        if ($uang < $biaya) {
            gagal_js('Uang kurang.');
        }
        if ($diskon > $biaya) {
            gagal_js("Diskon melebihi biaya.");
        }

        if ($menu == "billiard") {
            $db = db('billiard_2');
            $q = $db->where('id', $id)->get()->getRowArray();
            if (!$q) {
                gagal_js("Data id tidak ditemukan.");
            }

            $db_meja = db('jadwal_2');
            $meja = $db_meja->where('id', $q['meja_id'])->get()->getRowArray();
            if (!$meja) {
                gagal_js("Data id meja tidak ditemukan.");
            }

            $meja['is_active'] = 0;
            $meja['start'] = 0;

            $q['is_active'] = 0;
            $q['diskon'] = 0;
            $q['biaya'] = $biaya;
            $q['petugas'] = user()['nama'];

            if ($q['end'] == 0) {
                $q['end'] = time();
                $q['durasi'] = durasi_dalam_menit($q['start'], time());
            }

            $kembalian = $uang - $biaya;

            $db_meja->where('id', $meja['id']);
            if ($db_meja->update($meja)) {
                $db->where('id', $q['id']);
                if ($db->update($q)) {
                    sukses_js("Sukses", $kembalian);
                } else {
                    gagal_js("Gagal");
                }
            }
        }
    }
}
