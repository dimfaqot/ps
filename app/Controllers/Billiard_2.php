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

        $db_b = db('billiard_2');
        $q = $db_b->orderBy('tgl', 'DESC')->get()->getResultArray();
        $data = [];
        foreach ($q as $i) {
            if (date('n', $i['tgl']) == date('n') || date('n', $i['tgl']) == (date('n') - 1)) {
                $data[] = $i;
            }
        }

        return view('billiard_2', ['judul' => menu()['menu'] . ' - PLAYGROUND', 'meja' => $meja, 'data' => $data]);
    }

    public function start_stop()
    {
        $id = clear($this->request->getVar('id'));
        $billiard_id = clear($this->request->getVar('billiard_id'));
        $order = clear($this->request->getVar('order'));
        $durasi = clear($this->request->getVar('durasi'));

        $db = db('jadwal_2');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        }

        if ($order == 'end') {
            $db_bill = db('billiard_2');
            $data = $db_bill->where('id', $billiard_id)->get()->getRowArray();

            if ($data['durasi'] == 0) {
                $data['durasi_waktu'] = ceil((time() - $data['start']) / 60);
                $data['biaya'] = biaya_per_menit($data['harga'], $data['start'], time());
            } else {
                $data['biaya'] = (int)$data['harga'] * (int)((int)$data['durasi'] / 60);
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
                        'harga' => $q['harga'],
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
        $uang = (int)rp_to_int(clear($this->request->getVar('uang')));
        $durasi = clear($this->request->getVar('durasi'));
        $diskon = (int)rp_to_int(clear($this->request->getVar('diskon')));
        $biaya = (int)clear($this->request->getVar('biaya'));
        $nama_user = clear($this->request->getVar('nama_user'));
        $user_id = clear($this->request->getVar('user_id'));
        $hutang = $this->request->getVar('hutang');

        if ($biaya == "" || $biaya == 0) {
            gagal_js('Total biaya belum diisi!.');
        }

        if ($diskon > $biaya) {
            gagal_js('Diskon tidak boleh lebih besar dari total biaya!.');
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

        if ($hutang) {
            $dbh = db('hutang');
            $value = [
                'barang_id' => $q['start'], //start dan end. end=start+durasi*60
                'user_id' => $user_id,
                'kategori' => 'Billiard',
                'nama' => $nama_user,
                'no_nota' => no_nota('billiard', $qj['meja']) . '|' . $diskon,
                'barang' => 'Billiard ' . $q['meja'], //meja diexplode. dan meja id dicari dari jadwal2
                'harga_satuan' => $qj['harga'], //harga
                'tgl_lunas' => 0,
                'status' => 0,
                'tgl' => time(), //tgl
                'qty' => $durasi, //durasi
                'total_harga' => ($biaya - $diskon), //biaya. diskon dicari dari harga_satuan-total_harga
                'teller' => $q['petugas'] //petugas
            ];
            if ($dbh->insert($value)) {
                $db->where('id', $id);
                if ($db->delete()) {
                    $qj['is_active'] = 0;
                    $qj['start'] = 0;

                    $dbj->where('id', $meja_id);
                    if ($dbj->update($qj)) {
                        sukses_js('Sukses', $uang - $value['total_harga']);
                    } else {
                        gagal_js('Update meja gagal!.');
                    }
                } else {
                    gagal_js('Data billiard gagal dihapus!.');
                }
            }
        }

        if ($uang == "" || $uang == 0) {
            gagal_js('Uang belum diisi!.');
        }
        if ($uang < ($biaya - $diskon)) {
            gagal_js('Uang kurang!.');
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

    public function get_user()
    {
        $user = clear($this->request->getVar('user'));
        $db = db('users');

        $q = $db->whereIn('role', ['Member'])->like('nama', $user, 'both')->orderBy('nama', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js('Koneksi ok', $q);
    }
}
