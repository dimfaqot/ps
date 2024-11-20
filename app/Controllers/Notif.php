<?php

namespace App\Controllers;

class Notif extends BaseController
{

    public function pesanan()
    {
        $db = db('notif');
        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
        $belum_dibaca = 0;

        foreach ($q as $i) {

            $exp = explode(",", $i['dibaca']);
            if (!in_array(session('id'), $exp)) {
                $belum_dibaca++;
            }
        }

        sukses_js('Koneksi sukses.', $q, $belum_dibaca);
    }
    public function detail_pesanan()
    {
        $db = db('notif');
        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

        sukses_js('Koneksi sukses.', $q);
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
            $q['dibaca'] = session('id') . ',';
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
