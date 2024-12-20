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
        $db = db('barber');
        $q = $db->get()->getResultArray();
        foreach ($q as $i) {
            $i['metode'] = 'Cash';
            $i['status'] = 1;
            $db->where('id', $i['id']);
            $db->update($i);
        }
        return view('ext_booking', ['judul' => 'BOOKING']);
    }
    public function add_booking()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        clear_tabel('booking');
        $db = db('booking');
        if ($db->insert($data)) {
            sukses_js('Sukses. Silahkan tap dalam 20 detik!.');
        } else {
            gagal_js('Order gagal!.');
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
                } else {
                    $dur = explode(":", durasi($i['end'], time()));
                    $val['durasi'] = ($i['durasi'] == 0 ? "Open" : $dur[0] . "h " . $dur[1] . "m");
                }

                $res[] = $val;
            }
        }

        sukses_js('Ok', $res);
    }
    public function tap_booking()
    {
        clear_tabel('message');
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        // kalau dalam jwt ada keu topupId berarti kartu member yang ditap setelah kartu Root
        $member_id = key_exists("member_id", $decode);


        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_js('Data booking tidak ditemukan!');
        }

        $dbu = db('users');

        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if ($q['kategori'] == 'Daftar') {
            if ($member_id) {
                $user_m = $dbu->where('id', $q['durasi'])->get()->getRowArray();
                if (!$user_m) {
                    clear_tabel('booking');
                    message($q['kategori'], "User tidak ada!.", 400);
                    gagal_js("User tidak ada!.");
                }
                $user_m["uid"] = $decode['uid'];
                $dbu->where('id', $q['durasi']);
                if ($dbu->update($user_m)) {
                    clear_tabel('booking');
                    message($q['kategori'], "Pendaftaran sukses.", 200);
                    sukses_js("Pendaftaran sukses.");
                }
            } else {
                konfirmasi_root($q, $user);
            }
        }


        if (!$user) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            gagal_js('Kartu tidak dikenal!.');
        }

        if ($q['kategori'] == 'Topup') {
            if ($member_id) {
                if (topup($user, $q)) {
                    $sal = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
                    $decode_fulus = decode_jwt_fulus($sal['fulus']);

                    $fulus = $decode_fulus['fulus'];
                    clear_tabel('booking');
                    message($q['kategori'], "Topup berhasil.", 200, rupiah($fulus));
                    sukses_js("Topup berhasil.", rupiah($fulus));
                } else {
                    clear_tabel('booking');
                    message($q['kategori'], "Topup gagal!.", 400);
                    sukses_js("Topup gagal!.");
                }
            } else {
                konfirmasi_root($q, $user);
            }
        }

        if ($q['kategori'] == 'Saldo') {
            $decode_fulus = decode_jwt_fulus($user['fulus']);
            $fulus = $decode_fulus['fulus'];
            clear_tabel('booking');
            message($q['kategori'], "Saldo", 200, rupiah($fulus));
            sukses_js("Saldo", rupiah($fulus));
        }

        if ($q['kategori'] == 'Hutang') {
            $dbh = db('hutang');
            $hutang = $dbh->where('user_id', $user['id'])->where('status', 0)->get()->getResultArray();

            $err = [];
            $total = 0;
            foreach ($hutang as $i) {
                $total += $i['total_harga'];
            }
            $saldo = saldo($user);
            if ($saldo < $total) {
                clear_tabel('booking');
                message($q['kategori'], "Saldo tidak cukup!", 400, rupiah($saldo) . " < " . rupiah($total));
                gagal_js("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
            }

            $total2 = 0;
            foreach ($hutang as $i) {
                $i['status'] = 1;
                $dbh->where('id', $i['id']);

                if ($dbh->update($i)) {
                    $total2 += $i['total_harga'];
                } else {
                    $err[] = $i['id'];
                }
            }

            $saldo_akhir = $saldo - $total2;
            $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
            $dbu->where('id', $user['id']);
            if ($dbu->update($user)) {
                clear_tabel('booking');
                if (count($err) > 0) {
                    message($q['kategori'], count($err) . ' barang' . " gagal!.", 200, "Saldo: " . rupiah($saldo_akhir));
                    sukses_js(count($err) . ' barang' . " gagal!.", "Saldo " . rupiah($saldo_akhir));
                } else {
                    message($q['kategori'], "Berhasil", 200, "Saldo: " . rupiah($saldo_akhir));
                    sukses_js("Berhasil", "Saldo " . rupiah($saldo_akhir));
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update saldo gagal!", 400, rupiah($saldo) . " < " . rupiah($total));
                gagal_js("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
            }
        }

        if ($q['kategori'] == 'Billiard') {
            $dbm = db('jadwal_2');
            $meja = $dbm->where('meja', $q['meja'])->get()->getRowArray();

            if (!$meja) {
                clear_tabel('booking');
                message($q['kategori'], "Meja tidak ditemukan!.", 400);
                gagal_js("Saldo", "Meja tidak ditemukan!.");
            }

            if ($meja['is_active'] == 1) {
                clear_tabel('booking');
                message($q['kategori'], "Meja aktif!.", 400);
                gagal_js("Meja aktif!.");
            }

            $harga = (int)$meja['harga'] * (int)$q['durasi'];
            $decode_fulus = decode_jwt_fulus($user['fulus']);
            $fulus = (int)$decode_fulus['fulus'];
            if ($fulus < $harga) {
                clear_tabel('booking');
                message($q['kategori'], "Saldo tidak cukup!.", 400);
                gagal_js("Saldo tidak cukup!.");
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
                        message($q['kategori'], "Transaksi sukses", 200, "Saldo: " . rupiah($sal));
                        sukses_js("Transaksi sukses.", "Saldo: " . rupiah($sal));
                    } else {
                        clear_tabel('booking');
                        message($q['kategori'], "Update saldo gagal!.", 400);
                        gagal_js("Update saldo gagal!.");
                    }
                } else {
                    clear_tabel('booking');
                    message($q['kategori'], "Insert billiard gagal!.", 400);
                    gagal_js("Insert billiard gagal!.");
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update meja gagal!.", 400);
                gagal_js("Update meja gagal!.");
            }
        }

        if ($q['kategori'] == 'Ps') {
            $meja = "Meja " . $q['meja'];

            $dbu = db('unit');
            $unit = $dbu->where('meja', $meja)->get()->getRowArray();

            if (!$unit) {
                clear_tabel('booking');
                message($q['kategori'], "Unit tidak ditemukan!.", 400);
                gagal_js("Unit tidak ditemukan!.");
            }

            if ($unit['status'] !== 'Maintenance') {
                clear_tabel('booking');
                message($q['kategori'], "Unit dalam perbaikan!.", 400);
                gagal_js("Unit dalam perbaikan!.");
            }

            $dbr = db('rental');
            $q = $dbr->where('unit_id', $unit['id'])->where('is_active', 1)->get()->getRowArray();

            if ($q) {
                clear_tabel('booking');
                message($q['kategori'], "Unit masih dalam permainan!.", 400);
                gagal_js("Unit masih dalam permainan!.");
            }

            $dbset = db('settings');
            $qs = $dbset->where('nama_setting', $unit['kode_harga'])->get()->getRowArray();
            if (!$qs) {
                clear_tabel('booking');
                message($q['kategori'], "Kode harga di unit tidak ada!.", 400);
                gagal_js("Kode harga di unit tidak ada!.");
            }
            $biaya = $qs['value_int'] * $q['durasi'];
            $fulus = saldo($user);
            if ($biaya < $fulus) {
                clear_tabel('booking');
                message($q['kategori'], "Saldo tidak cukup!.", 400);
                gagal_js("Saldo tidak cukup!.");
            }

            $time = time();

            $datar = [
                'tgl' => $time,
                'unit_id' => $unit['id'],
                'meja' => $meja,
                'dari' => $time,
                'ke' => $time + ((60 * 60) * $q['durasi']),
                'durasi' => $q["durasi"],
                'is_active' => 1,
                'biaya' => $biaya,
                'diskon' => 0,
                'metode' => "Tap",
                'petugas' => $user['nama']
            ];

            if ($dbr->insert($datar)) {
                $unit['status'] = 'Available';
                $dbu->where('id', $unit['id']);
                $dbu->update($unit);
                $sal = $fulus - $biaya;
                $user['fulus'] = encode_jwt_fulus(['fulus' => $sal]);
                $db->where('id', $user['id']);
                if (!$dbu->update($user)) {
                    clear_tabel('booking');
                    message($q['kategori'], "Update saldo gagal!.", 400);
                    gagal_js("Update saldo gagal!.");
                } else {
                    clear_tabel('booking');
                    message($q['kategori'], "Transaksi sukses", 200, "Saldo: " . rupiah($sal));
                    sukses_js("Transaksi sukses.", "Saldo: " . rupiah($sal));
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update meja gagal!.", 400);
                gagal_js("Update meja gagal!.");
            }
        }


        if ($q['kategori'] == 'Barber') {
            $dbb = db('barber');
            $barber = $dbb->get()->getResultArray();

            $err = [];
            $total = 0;
            foreach ($barber as $i) {
                $total += $i['total_harga'];
            }
            $saldo = saldo($user);
            if ($saldo < $total) {
                clear_tabel('booking');
                message($q['kategori'], "Saldo tidak cukup!", 400, rupiah($saldo) . " < " . rupiah($total));
                gagal_js("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
            }

            $total2 = 0;
            foreach ($barber as $i) {
                $i['status'] = 1;
                $dbb->where('id', $i['id']);

                if ($dbb->update($i)) {
                    $total2 += $i['total_harga'];
                } else {
                    $err[] = $i['id'];
                }
            }

            $saldo_akhir = $saldo - $total2;
            $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
            $dbu->where('id', $user['id']);
            if ($dbu->update($user)) {
                clear_tabel('booking');
                if (count($err) > 0) {
                    message($q['kategori'], count($err) . ' barang' . " gagal!.", 200, "Saldo: " . rupiah($saldo_akhir));
                    sukses_js(count($err) . ' barang' . " gagal!.", "Saldo " . rupiah($saldo_akhir));
                } else {
                    message($q['kategori'], "Berhasil", 200, "Saldo: " . rupiah($saldo_akhir));
                    sukses_js("Berhasil", "Saldo " . rupiah($saldo_akhir));
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update saldo gagal!", 400, rupiah($saldo) . " < " . rupiah($total));
                gagal_js("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
            }
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
        $db = db('message');
        $q = $db->get()->getRowArray();

        $dbb = db('booking');
        $qb = $dbb->get()->getRowArray();

        if ($qb) {
            if ($q) {
                sukses_js("Proses...");
            } else {
                sukses_js($q);
            }
        } else {
            gagal_js('Gagal!.');
        }
    }
}
