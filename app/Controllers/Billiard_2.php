<?php

namespace App\Controllers;

class Billiard_2 extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db('jadwal_2');
        $meja = $db->orderBy('meja', 'ASC')->get()->getResultArray();

        return view('billiard_2', ['judul' => menu()['menu'] . ' - PLAYGROUND', 'meja' => $meja]);
    }

    public function start_stop()
    {
        $id = clear($this->request->getVar('id'));
        $order = clear($this->request->getVar('order'));
        $durasi = clear($this->request->getVar('durasi'));

        $db = db('jadwal_2');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        }

        if ($order == 'end') {
            $db_bill = db('billiard_2');
            $data = $db_bill->where('meja_id', $id)->get()->getRowArray();
            if ($data['durasi'] == 0) {
                $data['durasi_waktu'] = ceil((time() - $data['start']) / 60);
                $data['biaya'] = biaya_per_menit($data['harga'], $data['start'], time());
            } else {
                $data['biaya'] = $data['harga'] * ($data['durasi'] / 60);
                $data['durasi_waktu'] = $data['durasi'];
            }
            sukses_js('Koneksi sukses.', $data);
        } else {
            if ($q['is_active'] == 1) {
                gagal_js('Meja masih dimainkan!.');
            } else {
                if ($durasi > 0 && $durasi < 60) {
                    gagal_js('Durasi 0 untul OPEN, selain itu minimal 60 menit!.');
                }
                $q['is_active'] = 1;
                $q['start'] = time();

                $db->where('id', $id);
                if ($db->update($q)) {
                    $data = [
                        'meja_id' => $id,
                        'durasi' => $durasi,
                        'petugas' => user()['nama'],
                        'is_active' => 1,
                        'meja' => 'Meja ' . $q['meja'],
                        'tgl' => time(),
                        'start' => time(),
                        'end' => ($durasi == 0 ? 0 : time() + ($durasi * 60))
                    ];
                    $db_bill = db('billiard_2');

                    if ($db_bill->insert($data)) {
                        sukses_js('Time started.');
                    } else {
                        gagal_js('Update billiard gagal!.');
                    }
                } else {
                    gagal_js('Update meja gagal!.');
                }
            }
        }
    }

    public function pembayaran()
    {
        $id = clear($this->request->getVar('id'));
        $meja_id = clear($this->request->getVar('meja_id'));
        $uang = rp_to_int(clear($this->request->getVar('uang')));
        $durasi = clear($this->request->getVar('durasi'));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $biaya = clear($this->request->getVar('biaya'));


        if ($uang == "" || $uang == 0) {
            gagal_js('Uang belum diisi!.');
        }
        if ($biaya == "" || $biaya == 0) {
            gagal_js('Total biaya belum diisi!.');
        }

        if ($diskon > $biaya) {
            gagal_js('Diskon tidak boleh lebih besar dari total biaya!.');
        }

        if ($uang < ($biaya - $diskon)) {
            gagal_js('Uang kurang!.');
        }

        $db = db('billiard_2');
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Meja belum dimulai!.');
        }

        $dbj = db('jadwal_2');
        $qj = $dbj->where('id', $meja_id)->get()->getRowArray();
        if (!$qj) {
            gagal_js('Id meja tidak ditemukan!.');
        }

        $q['biaya'] = $biaya - $diskon;
        $q['diskon'] = $diskon;
        if ($q['durasi'] == 0) {
            $q['end'] = time();
            $q['durasi'] = $durasi;
        }
        $q['is_active'] = 0;

        $db->where('id', $id);
        if ($db->update($q)) {
            $qj['is_active'] = 0;
            $qj['start'] = 0;

            $dbj->where('id', $meja_id);
            if ($dbj->update($qj)) {
                sukses_js('End time success', $uang - $q['biaya']);
            } else {
                gagal_js('Update meja gagal!.');
            }
        } else {
            gagal_js('End billiard gagal!.');
        }
    }
}
