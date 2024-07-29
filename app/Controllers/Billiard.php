<?php

namespace App\Controllers;

class Billiard extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index($tgl = null): string
    {
        $db = db('jadwal');

        $hari = hari(date('l'))['indo'];

        $q = $db->where('hari', $hari)->orderBy('meja', 'ASC')->orderBy('jam', 'ASC')->get()->getResultArray();
        $meja = $db->groupBy('meja')->orderBy('meja', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q, 'meja' => $meja]);
    }

    public function pembayaran()
    {
        $id = clear($this->request->getVar('id'));
        $uang = rp_to_int(clear($this->request->getVar('uang')));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $total_biaya = clear($this->request->getVar('total_biaya'));

        $ids = explode(",", $id);

        if ($uang == "" || $uang == 0) {
            gagal_js('Uang belum diisi!.');
        }
        if ($total_biaya == "" || $total_biaya == 0) {
            gagal_js('Total biaya belum diisi!.');
        }

        if ($diskon > $total_biaya) {
            gagal_js('Diskon tidak boleh lebih besar dari total biaya!.');
        }

        if ($uang < ($total_biaya - $diskon)) {
            gagal_js('Uang harus lebih besar dari total biaya!.');
        }

        $db = db(menu()['tabel']);

        $err = [];
        foreach ($ids as $i) {
            $dbj = db('jadwal');
            $qj = $dbj->where('id', $i)->get()->getRowArray();
            if (!$qj) {
                $err[] = 'Id ' . $i . ' not found.';
            }
            $data = [
                'jadwal_id' => $i,
                'diskon' => $diskon,
                'pemesan' => $qj['pemesan'],
                'meja' => $qj['meja'],
                'dari' => $qj['jam'],
                'ke' => ($qj['jam'] == 24 ? 1 : $qj['jam'] + 1),
                'durasi' => '1 Jam',
                'biaya' => get_harga_billiard(),
                'petugas' => user()['nama'],
                'tgl' => time()
            ];

            if (!$db->insert($data)) {
                $err[] = 'Pembayaran ' . $qj['nama'] . ' gagal!.';
            }
        }

        if (count($err) > 0) {
            gagal_js('Gagal: ' . implode(",", $err));
        } else {
            sukses_js('Pembayaran sukses.', $uang - ($total_biaya - $diskon));
        }
    }
}
