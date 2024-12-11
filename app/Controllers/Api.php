<?php

namespace App\Controllers;

class Api extends BaseController
{

    public function index()
    {
        $db = db('api');
        $q = $db->orderBy('kategori', 'ASC')->orderBy('meja', 'ASC')->get()->getResultArray();

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
    public function tes_iot_rental($kategori, $meja)
    {
        $db = db('api');
        $q = $db->where('kategori', $kategori)->where('meja', $meja)->get()->getRowArray();
        sukses_js($q['id'], $q['kategori'], $q['meja'], $q['status'], $q['durasi']);
    }
    public function iot_rental($kategori, $meja)
    {
        $db = db(($kategori == 'Billiard' ? 'billiard_2' : ($kategori == 'Ps' ? 'rental' : 'kantin')));
        $q = $db->where('meja', "Meja " . $meja)->where('is_active', 1)->get()->getRowArray();
        if (!$q) {
            gagal_js("Not active!.");
        }

        $start = date_create(date('Y-m-d H:i:s', $q['end']));
        $end = date_create(date('Y-m-d H:i:s', time()));

        $diff  = date_diff($end, $start);
        $durasi = $diff->h * (60 * 60);
        $durasi += $diff->i * 60;
        $durasi += $diff->s;

        sukses_iot($q['is_active'], $durasi * 1000);
    }
}
