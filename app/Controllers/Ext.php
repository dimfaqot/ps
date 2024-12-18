<?php

namespace App\Controllers;

class Ext extends BaseController
{
    public function auth($jwt)
    {
        $data = decode_jwt($jwt);
        $data['id'] = 'temp';

        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }
    public function auth_root($jwt)
    {
        $data = decode_jwt($jwt);
        if ($data['role'] !== 'Root') {
            gagal(base_url('home'), 'Role harus root.');
        }
        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }
    public function auth_jwt($jwt)
    {
        $data = decode_jwt($jwt);
        $db = db('users');

        $q = $db->where('id', $data['id'])->get()->getRowArray();

        if (!$q) {
            gagal(base_url(), 'User not found!.');
        }
        $val = [
            'id' => $data['id'],
            'role' => $data['role'],
            'latitude' => '',
            'longitude' => ''
        ];
        session()->set($val);

        sukses(base_url('home'), 'Ok');
    }
    public function menu()
    {
        $db = db('barang');
        $q = $db->orderBy('barang', 'ASC')->get()->getResultArray();
        return view('ext_menu', ['judul' => 'Daftar Menu', 'data' => $q]);
    }
    public function save_menu_pesanan()
    {
        $data = json_decode(json_encode($this->request->getVar('order_list')), true);
        $no_meja = clear($this->request->getVar('no_meja'));
        $user_id = clear($this->request->getVar('user_id'));
        $nama_pemesan = upper_first(clear($this->request->getVar('nama_pemesan')));
        $dbn = db('notif');

        $no_nota = no_nota('kantin', $no_meja);
        $tgl = time();
        $err = [];
        foreach ($data as $i) {
            $datan = [
                'no_nota' => $no_nota,
                'tgl' => $tgl,
                'menu' => $i['barang'],
                'harga' => $i['harga'],
                'qty' => $i['qty_item'],
                'total' => $i['total_item'],
                'meja' => $no_meja,
                'pemesan' => $nama_pemesan,
                'dibaca' => 'WAITING',
                'kategori' => 'Pesanan',
                'id_pemesan' => $user_id
            ];
            if (!$dbn->insert($datan)) {
                $err[] = $i['barang'];
            }
        }
        $res = [
            'no_nota' => $no_nota,
            'nama_pemesan' => $nama_pemesan,
            'no_meja' => $no_meja
        ];

        if (count($err) > 0) {
            gagal_js('Gagal disave: ' . implode(", ", $err), json_encode($res));
        } else {
            sukses_js('Pesanan sukses disimpan.', encode_jwt($res));
        }
    }

    public function pesanan($jwt)
    {
        $data = decode_jwt($jwt);
        $db = db('notif');
        $q = $db->where('no_nota', $data['no_nota'])->orderBy('menu', 'ASC')->get()->getResultArray();

        return view('ext_pesanan', ['judul' => 'Daftar Menu', 'data' => $q, 'no_meja' => $data['no_meja'], 'nama_pemesan' => $data['nama_pemesan']]);
    }
    public function invoice()
    {
        $kategori = clear($this->request->getVar('kategori'));
        $jenis = clear($this->request->getVar('jenis'));
        $no_nota = clear($this->request->getVar('no_nota'));

        $db = db('notif');
        $q = $db->where('no_nota', $no_nota)->whereNotIn('dibaca', [""])->get()->getRowArray();

        sukses_js('Ok', $q['dibaca']);
    }

    public function get_nama_pemesan()
    {
        $val = clear($this->request->getVar('val'));

        $db = db('users');

        $q = $db->where('role', 'Member')->like('nama', $val, 'both')->limit(5)->get()->getResultArray();
        sukses_js('Ok', $q);
    }

    public function qr()
    {
        helper('qr_code');
        return view('qr', ['judul' => 'Qr']);
    }

    public function add_uid()
    {
        $db = db('rfid');
        $jwt = $this->request->getVar('jwt');

        $decode = decode_jwt_fulus($jwt);
        $data = ['uid' => $decode['uid']];

        clear_tabel('rfid');

        if ($db->insert($data)) {
            sukses_js('Sukses!.');
        } else {
            gagal_js('Gagal!.');
        }
    }

    public function booking()
    {
        return view('ext_booking', ['judul' => 'BOOKING']);
    }
    public function add_booking()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        clear_tabel('booking');
        $db = db('booking');
        if ($db->insert($data)) {
            sukses_js('Sukses. Silahkan tap dalam 20 detik!.', $data);
        } else {
            gagal_js('Order gagal!.');
        }
    }
    public function get_durasi()
    {
        $db = db('billiard_2');
        $q = $db->where('is_active', 1)->get()->getResultArray();

        $res = [];
        foreach ($q as $i) {
            $exp = explode(" ", $i['meja']);
            $dur = explode(":", durasi($i['end'], time()));
            $val = [
                'meja' => end($exp),
                'durasi' => ($i['durasi'] == 0 ? "Open" : $dur[0] . "h " . $dur[1] . "m")
            ];

            $res[] = $val;
        }

        sukses_js('Ok', $res);
    }
    public function tap_booking()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
        if (!$user) {
            gagal_js('Kartu tidak dikenal!.');
        }

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            gagal_js('Booking gagal!.');
        }

        $dbm = db('jadwal_2');
        $meja = $dbm->where('meja', $q['meja'])->get()->getRowArray();

        if (!$meja) {
            gagal_js('Meja tidak ditemukan!.');
        }

        if ($meja['is_active'] == 1) {
            gagal_js('Meja aktif!.');
        }

        $harga = (int)$meja['harga'] * (int)$q['durasi'];
        $decode_fulus = decode_jwt_fulus($user['fulus']);
        $fulus = (int)$decode_fulus['fulus'];
        if ($fulus < $harga) {
            gagal_js('Saldo tidak cukup!.');
        }

        $time_now = time();
        $meja['is_active'] = 1;
        $meja['start'] = $time_now;

        $dbm->where('id', $meja['id']);
        if ($dbm->update($meja)) {
            $data = [
                'meja_id' => $meja['id'],
                'meja' => "Meja " . $meja['meja'],
                'tgl' => $time_now,
                'durasi' => $q['durasi'] * 60,
                'petugas' => $user['nama'],
                'biaya' => $harga,
                'diskon' => 0,
                'start' => $time_now,
                'end' => $time_now + ((60 * 60) * $q['durasi']),
                'is_active' => 1,
                'harga' => $meja['harga'],
                'metode' => 'Tap'
            ];

            $dbb = db('billiard_2');
            if ($dbb->insert($data)) {
                $sal = $fulus - $harga;
                $user['fulus'] = encode_jwt_fulus(['fulus' => $sal]);
                $dbu->where('id', $user['id']);
                if ($dbu->update($user)) {
                    clear_tabel('booking');
                    sukses_js('Sukses. Saldo: ' . $sal, $data['durasi']);
                } else {
                    clear_tabel('booking');
                    gagal_js('Update saldo gagal!.');
                }
            } else {
                clear_tabel('booking');
                gagal_js('Insert billiard gagal!.');
            }
        } else {
            clear_tabel('booking');
            gagal_js('Update meja gagal!.');
        }
    }

    public function del_booking()
    {
        $jwt = $this->request->getVar('jwt');
        decode_jwt_fulus($jwt);

        clear_tabel('booking');
        sukses_js('Booking dihapus!.');
    }

    public function get_booking()
    {
        $db = db('booking');
        $q = $db->get()->getRowArray();
        if ($q) {
            sukses_js('Silahkan tap!.');
        } else {
            gagal_js('Silahkan pilih meja!');
        }
    }
    public function hasil_tap()
    {
        $meja = "Meja " . clear($this->request->getVar('meja'));
        $durasi = (int)clear($this->request->getVar('durasi'));
        $durasi *= 60;
        $dbb = db('billiard_2');
        $bil = $dbb->where('is_active', 1)->where('metode', 'Tap')->get()->getRowArray();

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if ($q) {
            gagal_js('Belum ditap!.');
        } else {

            if (!$bil) {
                sukses_js("Tap gagal!.");
            } else {
                $dbu = db('users');
                $user = $dbu->where('nama', $bil['petugas'])->get()->getRowArray();
                $saldo = "Saldo tidak terbaca!.";
                if ($user) {
                    $sal = decode_jwt_fulus($user['fulus']);
                    $saldo = rupiah($sal['fulus']);
                }
                sukses_js('Tap berhasil.', $saldo);
            }
        }
    }
}
