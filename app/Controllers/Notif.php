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
            if ($i['kategori'] == 'Pesanan') {
                if ($i['dibaca'] !== 'DONE') {
                    $belum_dibaca++;
                }
            } else {
                $exp = explode(",", $i['dibaca']);
                if (!in_array(session('id'), $exp)) {
                    $err[] = $i;
                    $belum_dibaca++;
                }
            }
        }

        sukses_js('Koneksi sukses.', $q, $belum_dibaca, $err);
    }
    public function detail_pesanan()
    {
        $db = db('notif');
        $q = $db->orderBy('harga', 'DESC')->get()->getResultArray();

        $data = [];
        $no_nota_exist = [];
        foreach ($q as $i) {
            if ($i['kategori'] == 'Pesanan') {
                if ($i['dibaca'] == 'DONE' || $i['dibaca'] == 'MOVE') {
                    $i['read'] = 1;
                } else {
                    $i['read'] = 0;
                }
                if (in_array($i['no_nota'], $no_nota_exist)) {
                    continue;
                } else {
                    $no_nota_exist[] = $i['no_nota'];
                }
            } else {
                $i['read'] = 0;
                $exp = explode(",", $i['dibaca']);
                if (in_array(session('id'), $exp)) {
                    $i['read'] = 1;
                }
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
    public function notif_detail_pesanan()
    {
        $no_nota = clear($this->request->getVar('no_nota'));
        $db = db('notif');
        $q = $db->where('no_nota', $no_nota)->get()->getResultArray();

        if (!$q) {
            gagal_js('No. nota not found!.');
        }

        sukses_js('Koneksi sukses.', $q);
    }
    public function kerjakan_pesanan()
    {
        $no_nota = clear($this->request->getVar('no_nota'));
        $db = db('notif');
        $q = $db->where('no_nota', $no_nota)->get()->getResultArray();

        if (!$q) {
            gagal_js('No. nota not found!.');
        }

        foreach ($q as $i) {
            $i['dibaca'] = 'PROCESS';
            $db->where('id', $i['id']);
            $db->update($i);
        }

        sukses_js('Pesanan dikerjakan.', $q);
    }
    public function selesaikan_pesanan()
    {
        $no_nota = clear($this->request->getVar('no_nota'));
        $db = db('notif');
        $q = $db->where('no_nota', $no_nota)->get()->getResultArray();

        if (!$q) {
            gagal_js('No. nota not found!.');
        }

        sukses_js('Pesanan selesai.', $q);
    }
}
