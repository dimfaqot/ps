<?php

namespace App\Controllers;

class Notif extends BaseController
{

    public function pesanan()
    {
        $db = db('notif');
        $q = $db->orderBy('harga', 'DESC')->get()->getResultArray();
        $belum_dibaca = 0;
        $err = [];
        foreach ($q as $i) {

            $exp = explode(",", $i['dibaca']);
            if (!in_array(session('id'), $exp)) {
                $err[] = $i;
                $belum_dibaca++;
            }
        }

        sukses_js('Koneksi sukses.', $q, $belum_dibaca, $err);
    }
    public function detail_pesanan()
    {
        $db = db('notif');
        $q = $db->orderBy('harga', 'DESC')->get()->getResultArray();

        $data = [];

        foreach ($q as $i) {
            $i['read'] = 0;
            $exp = explode(",", $i['dibaca']);
            if (in_array(session('id'), $exp)) {
                $i['read'] = 1;
            }
            $data[] = $i;
        }

        sukses_js('Koneksi sukses.', $data);
    }
    public function read_notif_pesanan()
    {
        $id = clear($this->request->getVar('id'));
        $db = db('notif');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        }

        $exp = explode(",", $q['dibaca']);
        if (in_array(session('id'), $exp)) {
            sukses_js('Ok');
        } else {
            $q['dibaca'] = $q['dibaca'] . session('id') . ',';
            $db->where('id', $id);
            if ($db->update($q)) {
                sukses_js('Ok');
            } else {
                gagal_js('Update read gagal!.');
            }
        }


        sukses_js('Koneksi sukses.', $q);
    }
}
