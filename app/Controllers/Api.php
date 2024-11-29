<?php

namespace App\Controllers;

class Api extends BaseController
{

    public function index()
    {
        $db = db('settings');
        $q = $db->where('nama_setting', 'Billiard')->get()->getRowArray();

        return view('uiapi', ['judul' => 'UI API', 'data' => $q['value_str']]);
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

    public function update_by_js()
    {
        $val = clear($this->request->getVar('val'));
        $db = db('settings');

        $q = $db->where('nama_setting', 'Billiard')->get()->getRowArray();
        $q['value_str'] = $val;

        $db->where('id', $q['id']);

        if ($db->update($q)) {
            sukses_js('Sukses');
        } else {
            sukses_js('Gagal!.');
        }
    }
}
