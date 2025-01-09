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

        // dd(decode_jwt_fulus("eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmdWx1cyI6MH0.xMR3V0PQ703flTaEkOgrnUiKC76BHAeptjAtj323ohk"));

        // $db = db("users");
        // $q = $db->get()->getResultArray();

        // foreach ($q as $i) {
        //     $i['fulus'] = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmdWx1cyI6MH0.xMR3V0PQ703flTaEkOgrnUiKC76BHAeptjAtj323ohk";
        //     $db->where('id', $i['id']);
        //     $db->update($i);
        // }
        // $db = db('users');
        // $q = $db->whereNotIn("role", ["Member"])->where('finger', 1)->get()->getRowArray();

        // if (!$q) {
        //     gagal_js("Finger tidak terdaftar!.");
        // }

        // $val = get_absen($q);
        // dd($val['msg']);

        // dd(decode_jwt_fulus(""));
        // $dbu = db('users');
        // $user = $dbu->where('uid', "098979")->get()->getRowArray();
        // $decode_fulus = decode_jwt_fulus($user['fulus']);
        // $fulus = $decode_fulus['fulus'];
        // dd($fulus);
        // dd(decode_jwt_finger("eyJhbGciOiAiSFMyNTYiLCJ0eXAiOiJKV1QifQ.eyJ1aWQiOiIxIiwiZGF0YTMiOiIiLCJkYXRhNCI6IiIsImRhdGE1IjoiIiwiZGF0YTYiOiIifQ.A_dvGajjO6CkJZfAE2Rs9bFD1VlPBWONV2Q0bxbfO60"));

        // $db = db('barber');
        // $q = $db->get()->getResultArray();
        // foreach ($q as $i) {
        //     $i['metode'] = 'Cash';
        //     $i['status'] = 1;
        //     $db->where('id', $i['id']);
        //     $db->update($i);
        // }
        return view('ext_booking', ['judul' => 'BOOKING']);
    }
    public function add_booking()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $db = db('booking');
        $qb = $db->get()->getResultArray();
        if ($data['kategori'] == "Reload") {
            if ($qb) {
                $qb['kategori'] = "Reload";
                $db->where('id', $qb['id']);
                if ($db->update($qb)) {
                    sukses_js('Reload sukses.');
                } else {
                    gagal_js('Reload gagal!.');
                }
            } else {
                if ($db->insert($data)) {

                    sukses_js('Reload sukses.');
                } else {
                    gagal_js('Reload gagal!.');
                }
            }
        } else {
            if ($qb) {
                gagal_js("Mohon tunggu, transaksi lain sedang berlangsung!.", 1);
            }
            $dba = db("api");
            $qa = $dba->get()->getResultArray();
            if ($qa) {
                gagal_js("Mohon tunggu, transaksi lain sedang berlangsung!.", 1);
            }
            $dbm = db("message");
            $qm = $dbm->get()->getResultArray();
            if ($qm) {
                gagal_js("Mohon tunggu, transaksi lain sedang berlangsung!.", 1);
            }

            if ($db->insert($data)) {
                sukses_js('Sukses. Silahkan tap dalam 20 detik!.');
            } else {
                gagal_js('Order gagal!.');
            }
        }
    }

    public function get_durasi()
    {
        $kategori = clear(strtolower($this->request->getVar('kategori')));
        $db = db(($kategori == "billiard" ? "billiard_2" : 'rental'));

        $q = $db->where('is_active', 1)->get()->getResultArray();

        $res = [];
        if ($q) {
            foreach ($q as $i) {
                $exp = explode(" ", $i['meja']);
                $val = [
                    'meja' => end($exp),
                    'is_active' => $i['is_active']
                ];
                if ($kategori == 'ps') {
                    $dur = explode(":", durasi($i['ke'], time()));
                    $val['durasi'] = ($i['durasi'] == -1 ? "Open" : $dur[0] . "h " . $dur[1] . "m");
                    $val['id'] = biaya_per_menit($i['harga'], $i['dari'], time());
                    $val['id'] = $i['id'];
                } else {
                    $dur = explode(":", durasi($i['end'], time()));
                    $val['durasi'] = ($i['durasi'] == 0 ? "Open" : $dur[0] . "h " . $dur[1] . "m");
                    $val['harga'] = biaya_per_menit($i['harga'], $i['start'], time());
                    $val['id'] = $i['id'];
                }

                $res[] = $val;
            }
        }

        sukses_js('Ok', $res);
    }
    public function search_db()
    {
        $kategori = clear($this->request->getVar('kategori'));
        $value = clear($this->request->getVar('value'));
        $db = db('users');

        $db;
        if ($kategori == "Add" || $kategori == "Delete") {
            $db->whereNotIn("role", ["Member"]);
        } else {
            $db->whereIn('role', ['Member']);
        }
        $q = $db->like('nama', $value, "both")->limit(8)->get()->getResultArray();
        sukses_js('Ok', $q);
    }

    public function message_server()
    {
        $db = db('message');
        $q = $db->get()->getRowArray();

        if (!$q) {
            gagal_js("Tidak ada pesan.!");
        } else {
            sukses_js("Sukses.", $q);
        }
    }
    public function data_hutang()
    {
        $res = null;
        $db = db('api');
        $q = $db->get()->getRowArray();
        $uid = null;
        if ($q) {
            $dbu = db('users');
            $user = $dbu->where('uid', $q['status'])->get()->getRowArray();
            if ($user) {
                $uid = $user['uid'];
                $dbh = db("hutang");
                $qh = $dbh->select("tgl,barang,total_harga,kategori")->where('status', 0)->where('user_id', $user['id'])->get()->getResultArray();
                if (count($qh) > 0) {
                    $res = $qh;
                }
            }
        }
        if (!$res) {
            gagal_js("Kosong");
        } else {
            sukses_js("Sukses.", $res, $uid);
        }
    }
    public function bayar_hutang_cash()
    {
        $uid = clear($this->request->getVar('uid'));
        $db = db('booking');
        $q = $db->get()->getRowArray();

        $dbu = db('users');
        $user = $dbu->where('uid', $uid)->get()->getRowArray();
        if ($user) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            if (!$qa) {
                clear_tabel('booking');
                clear_tabel('message');
                gagal_js("Akses api tidak ditemukan!.");
            }

            if ($qa["status"] !== $uid) {
                clear_tabel('booking');
                clear_tabel('api');
                clear_tabel('message');
                gagal_js("Data uid api berbeda!.");
            }

            $q['kategori'] = "Loan";

            $db->where('id', $q['id']);
            if ($db->update($q)) {
                sukses_js("Silahkan tap kartu petugas.");
            } else {
                clear_tabel('booking');
                clear_tabel('api');
                clear_tabel('message');
                gagal_js("Update kategori gagal!.");
            }
        } else {
            clear_tabel('booking');
            clear_tabel('api');
            clear_tabel('message');
            gagal_js("User tidak ditemukan!.");
        }
    }
    public function del_message()
    {
        laporan_arduino();
        clear_tabel("message");
        clear_tabel("api");
        clear_tabel("booking");
        sukses_js("Delete sukses!.");
    }
    public function data_poin()
    {
        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message('Gagal', "Data booking tidak ditemukan!.", 400);
        }

        $data = poin_absen($q['durasi'], 'Tap');
        sukses_js('Data ditemukan.', $data['data'], $data['poin']);
    }
}
