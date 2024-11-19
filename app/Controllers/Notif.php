<?php

namespace App\Controllers;

class Notif extends BaseController
{

    public function pesanan()
    {
        $db = db('notif_pesanan');
        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();
        $belum_dibaca = 0;

        foreach ($q as $i) {
            if ($i['dibaca'] == 0) {
                $belum_dibaca++;
            }
        }

        sukses_js('Koneksi sukses.', $q, $belum_dibaca);
    }
    public function detail_pesanan()
    {
        $db = db('notif_pesanan');
        $q = $db->orderBy('tgl', 'DESC')->get()->getResultArray();

        sukses_js('Koneksi sukses.', $q);
    }
    public function read_notif_pesanan()
    {
        $id = clear($this->request->getVar('id'));
        $db = db('notif_pesanan');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        }

        if ($q['dibaca'] == 1) {
            sukses_js('Ok');
        } else {
            $q['dibaca'] = 1;

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
