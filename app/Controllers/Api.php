<?php

namespace App\Controllers;

class Api extends BaseController
{

    public function index()
    {
        $db = db('api');
        $q = $db->get()->getRowArray();
        return view('uiapi', ['judul' => 'UI API', 'data' => $q]);
    }
    public function lampu($tabel, $jwt)
    {

        $db = db('settings');
        $data = decode_jwt($jwt);

        $q = $db->where('nama_setting', upper_first($tabel))->get()->getRowArray();


        sukses_js($q['value_str'], $data['lampu']);
    }
    public function update($value)
    {
        $db = db('settings');

        $q = $db->where('nama_setting', 'Billiard')->get()->getRowArray();
        $q['value_str'] = $value;

        $db->where('id', $q['id']);
        $db->update($q);
    }


    public function iot_notif_pesanan()
    {
        $db = db('notif');
        $q = $db->where('kategori', 'Pesanan')->whereIn('dibaca', ['WAITING', 'PROCESS'])->countAllResults();
        sukses_js(($q > 0 ? 'on' : 'off'));
    }
    public function tes_update_iot_rental()
    {
        $id = clear($this->request->getVar('id'));
        $db = db('api');
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Id not found!.');
        }
        $q['status'] = ($q['status'] == 1 ? 0 : 1);

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js('Sukses.');
        } else {
            gagal_js('Gagal update!.');
        }
    }
    public function tes_iot_rental()
    {
        $db = db('api');
        $q = $db->get()->getRowArray();
        $q['status'] = ($q['status'] == 1 ? 0 : 1);
        $db->where('id', $q['id']);
        $db->update($q);
        sukses_js($q['id'], $q['kategori'], $q['meja'], $q['status'], $q['durasi']);
    }
    public function iot_rental($kategori, $meja)
    {
        if ($kategori == 'Billiard') {
            $db = db('billiard_2');
            $q = $db->where('meja', "Meja " . $meja)->where('is_active', 1)->get()->getRowArray();
            if (!$q) {
                gagal_js("Not active!.");
            }
            if ($q['end'] == 0) {
                sukses_iot(1);
            }
            $kode = 1;
            if (time() > $q['end']) {
                $kode = 2;
            }
            if ($q['metode'] == 'Tap' && $kode == 2) {
                $dbm = db('jadwal_2');
                $meja = $dbm->where('id', $q['meja_id'])->get()->getRowArray();

                if ($meja) {
                    $meja['is_active'] = 0;
                    $meja['start'] = 0;

                    $dbm->where('id', $meja['id']);
                    if ($dbm->update($meja)) {
                        $q['is_active'] = 0;
                        $db->where('id', $q['id']);
                        $db->update($q);
                    }
                }
            }
            sukses_iot($kode);
        }
        if ($kategori == 'Ps') {
            $db = db('rental');
            $q = $db->where('meja', "Meja " . $meja)->where('is_active', 1)->get()->getRowArray();
            if (!$q) {
                gagal_js("Unit tidak aktif!.");
            }
            if ($q['ke'] == -1) {
                sukses_iot(1);
            }

            $kode = 1;
            if (time() > $q['ke']) {
                $kode = 2;
            }

            if ($q['metode'] == 'Tap' && $kode == 2) {
                $q['is_active'] = 0;
                $db->where('id', $q['id']);
                $db->update($q);

                $dbu = db('unit');
                $qu = $dbu->where('meja', "Meja " . $meja)->get()->getRowArray();

                if (!$qu) {
                    gagal_js("Not active!.");
                }

                $qu['status'] = 'Available';
                $dbu->where('id', $qu['id']);
                if ($dbu->update($qu)) {
                    sukses_js('Sukses.');
                } else {
                    gagal_js('Update data to unit failed!.');
                }
            }
            sukses_iot($kode);
        }

        // $start = date_create(date('Y-m-d H:i:s', $q['end']));
        // $end = date_create(date('Y-m-d H:i:s', time()));

        // $diff  = date_diff($end, $start);
        // $durasi = $diff->h * (60 * 60);
        // $durasi += $diff->i * 60;
        // $durasi += $diff->s;

    }



    public function tap_booking()
    {
        clear_tabel('message');
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        // kalau dalam jwt ada keu topupId berarti kartu member yang ditap setelah kartu Root
        $member_uid = key_exists("member_uid", $decode);


        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');

        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if ($q['kategori'] == "Daftar") {
            sukses_arduino("Masuk", $q);
            if ($member_uid) {
                sukses_arduino("Ok", $q);
                $user_m = $dbu->where('id', $q['durasi'])->get()->getRowArray();
                if (!$user_m) {
                    clear_tabel('booking');
                    message($q['kategori'], "User tidak ada!.", 400);
                    gagal_arduino("User tidak ada!.");
                }
                $user_m["uid"] = $decode['uid'];
                $dbu->where('id', $q['durasi']);
                if ($dbu->update($user_m)) {
                    clear_tabel('booking');
                    message($q['kategori'], "Pendaftaran sukses.", 200);
                    sukses_arduino("Pendaftaran sukses.");
                }
            } else {
                sukses_arduino("jos");
                konfirmasi_root($q, $user);
            }
        }
        sukses_arduino("Los", $q, $user);

        if (!$user) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            gagal_arduino('Kartu tidak dikenal!.');
        }

        if ($q['kategori'] == 'Topup') {
            if ($member_uid) {
                if (topup($user, $q)) {
                    $sal = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
                    $decode_fulus = decode_jwt_fulus($sal['fulus']);

                    $fulus = $decode_fulus['fulus'];
                    clear_tabel('booking');
                    message($q['kategori'], "Topup berhasil.", 200, rupiah($fulus));
                    sukses_arduino("Topup berhasil.", rupiah($fulus));
                } else {
                    clear_tabel('booking');
                    message($q['kategori'], "Topup gagal!.", 400);
                    sukses_arduino("Topup gagal!.");
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
            sukses_arduino("Saldo", rupiah($fulus));
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
                gagal_arduino("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
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
                    sukses_arduino(count($err) . ' barang' . " gagal!.", "Saldo " . rupiah($saldo_akhir));
                } else {
                    message($q['kategori'], "Berhasil", 200, "Saldo: " . rupiah($saldo_akhir));
                    sukses_arduino("Berhasil", "Saldo " . rupiah($saldo_akhir));
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update saldo gagal!", 400, rupiah($saldo) . " < " . rupiah($total));
                gagal_arduino("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
            }
        }

        if ($q['kategori'] == 'Billiard') {
            $dbm = db('jadwal_2');
            $meja = $dbm->where('meja', $q['meja'])->get()->getRowArray();

            if (!$meja) {
                clear_tabel('booking');
                message($q['kategori'], "Meja tidak ditemukan!.", 400);
                gagal_arduino("Saldo", "Meja tidak ditemukan!.");
            }

            if ($meja['is_active'] == 1) {
                clear_tabel('booking');
                message($q['kategori'], "Meja aktif!.", 400);
                gagal_arduino("Meja aktif!.");
            }

            $harga = (int)$meja['harga'] * (int)$q['durasi'];
            $decode_fulus = decode_jwt_fulus($user['fulus']);
            $fulus = (int)$decode_fulus['fulus'];
            if ($fulus < $harga) {
                clear_tabel('booking');
                message($q['kategori'], "Saldo tidak cukup!.", 400);
                gagal_arduino("Saldo tidak cukup!.");
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
                        sukses_arduino("Transaksi sukses.", "Saldo: " . rupiah($sal));
                    } else {
                        clear_tabel('booking');
                        message($q['kategori'], "Update saldo gagal!.", 400);
                        gagal_arduino("Update saldo gagal!.");
                    }
                } else {
                    clear_tabel('booking');
                    message($q['kategori'], "Insert billiard gagal!.", 400);
                    gagal_arduino("Insert billiard gagal!.");
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update meja gagal!.", 400);
                gagal_arduino("Update meja gagal!.");
            }
        }

        if ($q['kategori'] == 'Ps') {
            $meja = "Meja " . $q['meja'];

            $dbu = db('unit');
            $unit = $dbu->where('meja', $meja)->get()->getRowArray();

            if (!$unit) {
                clear_tabel('booking');
                message($q['kategori'], "Unit tidak ditemukan!.", 400);
                gagal_arduino("Unit tidak ditemukan!.");
            }

            if ($unit['status'] !== 'Maintenance') {
                clear_tabel('booking');
                message($q['kategori'], "Unit dalam perbaikan!.", 400);
                gagal_arduino("Unit dalam perbaikan!.");
            }

            $dbr = db('rental');
            $q = $dbr->where('unit_id', $unit['id'])->where('is_active', 1)->get()->getRowArray();

            if ($q) {
                clear_tabel('booking');
                message($q['kategori'], "Unit masih dalam permainan!.", 400);
                gagal_arduino("Unit masih dalam permainan!.");
            }

            $dbset = db('settings');
            $qs = $dbset->where('nama_setting', $unit['kode_harga'])->get()->getRowArray();
            if (!$qs) {
                clear_tabel('booking');
                message($q['kategori'], "Kode harga di unit tidak ada!.", 400);
                gagal_arduino("Kode harga di unit tidak ada!.");
            }
            $biaya = $qs['value_int'] * $q['durasi'];
            $fulus = saldo($user);
            if ($biaya < $fulus) {
                clear_tabel('booking');
                message($q['kategori'], "Saldo tidak cukup!.", 400);
                gagal_arduino("Saldo tidak cukup!.");
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
                    gagal_arduino("Update saldo gagal!.");
                } else {
                    clear_tabel('booking');
                    message($q['kategori'], "Transaksi sukses", 200, "Saldo: " . rupiah($sal));
                    sukses_arduino("Transaksi sukses.", "Saldo: " . rupiah($sal));
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update meja gagal!.", 400);
                gagal_arduino("Update meja gagal!.");
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
                gagal_arduino("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
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
                    sukses_arduino(count($err) . ' barang' . " gagal!.", "Saldo " . rupiah($saldo_akhir));
                } else {
                    message($q['kategori'], "Berhasil", 200, "Saldo: " . rupiah($saldo_akhir));
                    sukses_arduino("Berhasil", "Saldo " . rupiah($saldo_akhir));
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Update saldo gagal!", 400, rupiah($saldo) . " < " . rupiah($total));
                gagal_arduino("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
            }
        }
    }
    public function tap_booking_daftar()
    {
        clear_tabel('message');
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        // kalau dalam jwt ada keu topupId berarti kartu member yang ditap setelah kartu Root
        $member_uid = key_exists("member_uid", $decode);


        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');

        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if ($member_uid) {
            $uid_exist = $dbu->where('id', $q['durasi'])->where('uid', $decode['uid'])->get()->getRowArray();
            if ($uid_exist) {
                clear_tabel('booking');
                message($q['kategori'], "Uid sudah terdaftar!.", 400);
                gagal_arduino("Uid sudah terdaftar!.");
            }
            $user_m = $dbu->where('id', $q['durasi'])->get()->getRowArray();
            if (!$user_m) {
                clear_tabel('booking');
                message($q['kategori'], "User tidak ada!.", 400);
                gagal_arduino("User tidak ada!.");
            }
            $user_m["uid"] = $decode['uid'];
            $dbu->where('id', $q['durasi']);
            if ($dbu->update($user_m)) {
                clear_tabel('booking');
                message($q['kategori'], "Pendaftaran sukses.", 200);
                sukses_arduino("Pendaftaran sukses.");
            }
        } else {
            konfirmasi_root($q, $user);
        }
    }

    public function del_booking()
    {
        $jwt = $this->request->getVar('jwt');
        decode_jwt_fulus($jwt);

        clear_tabel('booking');
        sukses_arduino('Booking dihapus!.');
    }

    public function get_booking()
    {
        $jwt = $this->request->getVar('jwt');
        decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();
        if ($q) {
            sukses_arduino('Silahkan tap!.', $q['kategori']);
        } else {
            gagal_arduino('Silahkan pilih meja!');
        }
    }

    public function add_message()
    {
        $jwt = $this->request->getVar('jwt');
        $encode = decode_jwt_fulus($jwt);

        $db = db('message');

        $data = [
            'status' => $encode['status'],
            'message' => $encode['message'],
            'message_2' => $encode['message_2'],
            'kategori' => $encode['kategori']
        ];
        if ($db->insert($data)) {
            sukses_arduino("Insert sukses.", $data);
        } else {
            gagal_arduino("Insert gagal.", $data);
        }
    }
}
