<?php

namespace App\Controllers;

class Api extends BaseController
{

    public function index()
    {
        $db = db('api');
        $q = $db->get()->getRowArray();
        return view('uiapi', ['judul' => 'UI API', 'data' => $q]);
    }
    public function lampu($tabel, $jwt)
    {

        $db = db('settings');
        $data = decode_jwt($jwt);

        $q = $db->where('nama_setting', upper_first($tabel))->get()->getRowArray();


        sukses_js($q['value_str'], $data['lampu']);
    }
    public function update($value)
    {
        $db = db('settings');

        $q = $db->where('nama_setting', 'Billiard')->get()->getRowArray();
        $q['value_str'] = $value;

        $db->where('id', $q['id']);
        $db->update($q);
    }


    public function iot_notif_pesanan()
    {
        $db = db('notif');
        $q = $db->where('kategori', 'Pesanan')->whereIn('dibaca', ['WAITING', 'PROCESS'])->countAllResults();
        sukses_js(($q > 0 ? 'on' : 'off'));
    }
    public function tes_update_iot_rental()
    {
        $id = clear($this->request->getVar('id'));
        $db = db('api');
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Id not found!.');
        }
        $q['status'] = ($q['status'] == 1 ? 0 : 1);

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js('Sukses.');
        } else {
            gagal_js('Gagal update!.');
        }
    }
    public function tes_iot_rental()
    {
        $db = db('api');
        $q = $db->get()->getRowArray();
        $q['status'] = ($q['status'] == 1 ? 0 : 1);
        $db->where('id', $q['id']);
        $db->update($q);
        sukses_js($q['id'], $q['kategori'], $q['meja'], $q['status'], $q['durasi']);
    }
    public function iot_rental($kategori, $meja)
    {
        if ($kategori == 'Billiard') {
            $db = db('billiard_2');
            $q = $db->where('meja', "Meja " . $meja)->where('is_active', 1)->get()->getRowArray();
            if (!$q) {
                gagal_js("Not active!.");
            }
            if ($q['end'] == 0) {
                sukses_iot(1);
            }
            $kode = 1;
            if (time() > $q['end']) {
                $kode = 2;
            }
            if ($q['metode'] == 'Tap' && $kode == 2) {
                $dbm = db('jadwal_2');
                $meja = $dbm->where('id', $q['meja_id'])->get()->getRowArray();

                if ($meja) {
                    $meja['is_active'] = 0;
                    $meja['start'] = 0;

                    $dbm->where('id', $meja['id']);
                    if ($dbm->update($meja)) {
                        $q['is_active'] = 0;
                        $db->where('id', $q['id']);
                        $db->update($q);
                    }
                }
            }
            sukses_iot($kode);
        }
        if ($kategori == 'Ps') {
            $db = db('rental');
            $q = $db->where('meja', "Meja " . $meja)->where('is_active', 1)->get()->getRowArray();
            if (!$q) {
                gagal_js("Not active!.");
            }
            if ($q['ke'] == -1) {
                sukses_iot(1);
            }

            sukses_iot((time() > $q['ke'] ? 2 : $q['is_active']));
        }

        // $start = date_create(date('Y-m-d H:i:s', $q['end']));
        // $end = date_create(date('Y-m-d H:i:s', time()));

        // $diff  = date_diff($end, $start);
        // $durasi = $diff->h * (60 * 60);
        // $durasi += $diff->i * 60;
        // $durasi += $diff->s;

    }
}
