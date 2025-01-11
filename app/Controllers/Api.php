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


    public function tap_booking_daftar()
    {
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


        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            // api harus ada uid dan uid harus admin
            if ($qa) {
                $admin = $dbu->where('uid', $qa['status'])->get()->getRowArray();
                if (!$admin) {
                    message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                    gagal_arduino('Akses admin dibutuhkan!.');
                } else {
                    if ($admin['role'] !== 'Root') {
                        message($q['kategori'], "Akses admin dibutuhkan!", 400);
                        gagal_arduino('Akses admin dibutuhkan!.');
                    }
                }
            } else {
                message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                gagal_arduino('Akses admin dibutuhkan!.');
            }

            // check uid apakah sudah terdaftar/tidak boleh exist
            $uid_exist = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
            konfirmasi_uid_exist($uid_exist, $q);

            // check user member apakah ada/tidak boleh tidak ada
            $user_m = $dbu->where('id', $q["durasi"])->where('role', 'Member')->get()->getRowArray();
            if ($user_m['uid'] !== '') {
                message($q['kategori'], "Kartu anggota sudah dibuat!.", 400);
                gagal_arduino('Kartu anggota sudah dibuat!.');
            }

            konfirmasi_user_exist($user_m, $q);

            $uid_member = $decode['uid'];
            if ($uid_member == '') {
                $uid_member = $decode("member_uid");
            }
            $user_m["uid"] = $uid_member;
            $dbu->where('id', $q['durasi']);
            if ($dbu->update($user_m)) {
                message($q['kategori'], $user_m['nama'] . " sukses didaftarkan.", "", $admin["nama"], "end");
                sukses_arduino($user_m['nama'] . " sukses didaftarkan.", "", $admin["nama"]);
            }
        } else {
            konfirmasi_root($q, $user);
        }
    }
    public function tap_booking_topup()
    {
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

        $petugas = [];

        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            // api harus ada uid dan uid harus admin
            if ($qa) {
                $admin = $dbu->where('uid', $qa['status'])->get()->getRowArray();
                if (!$admin) {
                    message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                    gagal_arduino('Akses admin dibutuhkan!.');
                } else {
                    if ($admin['role'] !== 'Root') {
                        message($q['kategori'], "Akses admin dibutuhkan!", 400);
                        gagal_arduino('Akses admin dibutuhkan!.');
                    }
                    $petugas = $admin;
                }
            } else {
                message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                gagal_arduino('Akses admin dibutuhkan!.');
            }


            $uid_member = $decode['uid'];
            if ($uid_member == '') {
                $uid_member = $decode("member_uid");
            }

            $user_m = $dbu->where('uid', $uid_member)->where('role', 'Member')->get()->getRowArray();
            if (!$user_m) {
                message($q['kategori'], "Kartu tidak dikenal!.", 400);
                gagal_arduino("Kartu tidak dikenal!.");
            }


            $fulus = saldo($user_m);
            $tp = ($q["durasi"] * 10000);
            $saldo = $fulus + $tp;
            $user_m["fulus"] = encode_jwt_fulus(["fulus" => $saldo]);

            $dbu->where('id', $user_m['id']);
            if ($dbu->update($user_m)) {
                saldo_tap($q['kategori'], '', $tp, $user_m, $petugas);
                message($q['kategori'], $user_m['nama'] . " sukses topup sebesar " . rupiah($tp), "end", rupiah($saldo), $admin['nama']);
                sukses_arduino($user_m['nama'] . " sukses topup sebesar " . rupiah($tp), rupiah($saldo), $admin['nama']);
            } else {
                message($q['kategori'], "Topup gagal!.", 400);
                gagal_arduino("Topup gagal!.");
            }
        } else {
            konfirmasi_root($q, $user);
        }
    }

    public function tap_booking_saldo()
    {

        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
        if (!$user) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            gagal_arduino('Kartu tidak dikenal!.');
        }

        $saldo = saldo($user);
        message($q['kategori'], $user['nama'] . " berhasil cek saldo.", "end", "Saldo " . rupiah($saldo));
        sukses_arduino($user['nama'] . " berhasil cek saldo.", rupiah($saldo));
    }

    public function tap_booking_hutang()
    {
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

        if (!$user) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            gagal_arduino('Kartu tidak dikenal!.');
        }

        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            if (!$qa) {
                message($q['kategori'], "Akses api tidak ditemukan!.", 400);
                gagal_arduino('Akses api tidak ditemukan!.');
            }

            $uid_member = $decode['uid'];
            if ($uid_member == '') {
                $uid_member = $decode("member_uid");
            }

            $check_role = $dbu->where('uid', $qa['status'])->get()->getRowArray();

            $dbu->where('uid', $uid_member);
            if ($check_role['role'] !== 'Root') {
                $dbu->where('role', 'Member');
            }
            $user_m = $dbu->get()->getRowArray();
            // sukses_js("Ok", $user_m);
            if (!$user_m) {
                message($q['kategori'], "Kartu tidak dikenal!.", 400);
                gagal_arduino('Kartu tidak dikenal!.');
            }

            if ($qa["status"] !== $uid_member) {
                message($q['kategori'], "Kartu berbeda!.", 400);
                gagal_arduino('Kartu berbeda!.');
            }

            $dbh = db('hutang');
            $qh = $dbh->where('user_id', $user_m['id'])->where('status', 0)->get()->getResultArray();
            $total = 0;
            if (!$qh) {
                message($q['kategori'], $user_m['nama'] . " tidak berhutang.", "end");
                sukses_arduino($user_m['nama'] . " tidak berhutang.");
            } else {
                foreach ($qh as $i) {
                    $total += $i['total_harga'];
                }
            }
            $saldo = saldo($user_m);

            if ($saldo < $total) {
                message($q['kategori'], $user_m["nama"] . ", saldo tidak cukup!.", "400", rupiah($saldo) . " < " . rupiah($total));
                gagal_arduino($user_m["nama"] . ", Saldo tidak cukup!.", rupiah($saldo) . " < " . rupiah($total), $user_m["nama"]);
            } else {
                $total_2 = 0;
                foreach ($qh as $i) {
                    $i['status'] = 1;
                    $i['tgl_lunas'] = time();
                    $i['dibayar_kpd'] = "Tap";

                    $dbh->where('id', $i['id']);
                    if ($dbh->update($i)) {
                        if ($i['kategori'] == "Billiard") {
                            $dbm = db('jadwal_2');
                            $mj = explode(" ", $i['barang']);
                            $meja = $dbm->where('meja', end($mj))->get()->getRowArray();

                            $dbb = db("billiard_2");
                            $exp_nota = explode("|", $i['no_nota']);
                            $value = [
                                'meja_id' => $meja['id'],
                                // 'no_nota' => $no_nota,
                                'meja' => 'Meja ' . $meja['meja'],
                                'tgl' => time(),
                                'durasi' => $i['qty'],
                                'petugas' => "Tap",
                                'biaya' => $i['total_harga'],
                                'diskon' => end($exp_nota),
                                'start' => $i['barang_id'],
                                'end' => $i['barang_id'] + ($i['qty'] * 60),
                                'is_active' => 0,
                                'harga' => $i['harga_satuan'],
                                "metode" => "Tap"
                            ];

                            if ($dbb->insert($value)) {
                                $total_2 += $i['total_harga'];
                                saldo_tap($i["kategori"], $value['meja'], $saldo["fulus"], $user_m);
                            }
                        }

                        if ($i['kategori'] == "Kantin") {
                            $no_nota = no_nota('Kantin');
                            $dbk = db('kantin');
                            $value = [
                                'barang_id' => $i['barang_id'],
                                'no_nota' => $no_nota,
                                'barang' => $i['barang'],
                                'harga_satuan' => $i['harga_satuan'],
                                'tgl' => time(),
                                'qty' => $i['qty'],
                                'diskon' => 0,
                                'metode' => "Tap",
                                'total_harga' => $i['total_harga'],
                                'petugas' => 'Tap'
                            ];

                            if ($dbk->insert($value)) {
                                saldo_tap($i["kategori"], $value['barang'], $saldo["fulus"], $user_m);
                                $total_2 += $i['total_harga'];
                            }
                        }
                        if ($i['kategori'] == "Barber") {
                            $value = [
                                'layanan_id' => $i['barang_id'],
                                'layanan' => $i['barang'],
                                'harga' => $i['harga_satuan'],
                                'qty' => $i['qty'],
                                "tgl" => time(),
                                'total_harga' => $i['total_harga'],
                                "user_id" => $i['user_id'],
                                "petugas" => 'Tap',
                                "diskon" => 0,
                                "metode" => "Tap",
                                "status" => 1,
                                "user_id" => $i['user_id']
                            ];
                            $dbb = db('barber');
                            if ($dbk->insert($value)) {
                                saldo_tap($i["kategori"], '', $saldo["layanan"], $user_m);
                                $total_2 += $i['total_harga'];
                            }
                        }
                    }
                }

                $sal = $saldo - $total_2;
                $user_m['fulus'] = encode_jwt_fulus(['fulus' => $sal]);
                $dbu->where('id', $user_m['id']);
                if ($dbu->update($user_m)) {
                    message($q['kategori'], $user['nama'] . " sukses bertransaksi sebesar " . rupiah($total_2), "end", "Saldo " . rupiah($sal));
                    sukses_arduino($user['nama'] . " sukses bertransaksi sebesar " . rupiah($total_2), rupiah($sal));
                }
            }
        } else {
            $db = db('api');
            $data = ['status' => $decode["uid"]];
            if ($db->insert($data)) {
                $dbh = db('hutang');
                $qh = $dbh->where('user_id', $user['id'])->where('status', 0)->get()->getResultArray();
                $total = 0;
                foreach ($qh as $i) {
                    $total += $i['total_harga'];
                }
                if ($total <= 0) {
                    message($q['kategori'], $user['nama'] . " tidak berhutang.", "end");
                    sukses_arduino($user['nama'] . " tidak berhutang.", "stop");
                } else {
                    message($q['kategori'], $user['nama'] . " berhasil mengakses data.", "200", "Tap untuk melunasi " . rupiah($total) . "...");
                    sukses_arduino($user["nama"] . ' berhasil mengakses data.', 'next', "Tap lagi untuk melunasi " . rupiah($total) . "...");
                }
            }
        }
    }
    public function tap_booking_load()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            gagal_arduino('Kartu tidak dikenal!.');
        }

        if ($user['role'] == "Member") {
            message($q['kategori'], "Butuh akses petugas!.", 400);
            gagal_arduino('Butuh akses petugas!.');
        }


        $dba = db('api');
        $qa = $dba->get()->getRowArray();
        if (!$qa) {
            message($q['kategori'], "Akses api tidak ditemukan!.", 400);
            gagal_arduino('Akses api tidak ditemukan!.');
        }

        $uid_member = $qa['status'];


        $user_m = $dbu->where('uid', $uid_member)->get()->getRowArray();


        $dbh = db('hutang');
        $qh = $dbh->where('user_id', $user_m['id'])->where('status', 0)->get()->getResultArray();

        $total = 0;
        foreach ($qh as $i) {
            $i['status'] = 1;
            $i['tgl_lunas'] = time();
            $i['dibayar_kpd'] = $user['nama'];

            $dbh->where('id', $i['id']);
            if ($dbh->update($i)) {
                if ($i['kategori'] == "Billiard") {
                    $dbm = db('jadwal_2');
                    $mj = explode(" ", $i['barang']);
                    $meja = $dbm->where('meja', end($mj))->get()->getRowArray();

                    $dbb = db("billiard_2");
                    $exp_nota = explode("|", $i['no_nota']);
                    $value = [
                        'meja_id' => $meja['id'],
                        // 'no_nota' => $no_nota,
                        'meja' => 'Meja ' . $meja['meja'],
                        'tgl' => time(),
                        'durasi' => $i['qty'],
                        'petugas' => $user['nama'],
                        'biaya' => $i['total_harga'],
                        'diskon' => end($exp_nota),
                        'start' => $i['barang_id'],
                        'end' => $i['barang_id'] + ($i['qty'] * 60),
                        'is_active' => 0,
                        'harga' => $i['harga_satuan'],
                        "metode" => "Cash"
                    ];

                    if ($dbb->insert($value)) {
                        $total += $i['total_harga'];
                    }
                }

                if ($i['kategori'] == "Kantin") {
                    $no_nota = no_nota('Kantin');
                    $dbk = db('kantin');
                    $value = [
                        'barang_id' => $i['barang_id'],
                        'no_nota' => $no_nota,
                        'barang' => $i['barang'],
                        'harga_satuan' => $i['harga_satuan'],
                        'tgl' => time(),
                        'qty' => $i['qty'],
                        'diskon' => 0,
                        'metode' => "Cash",
                        'total_harga' => $i['total_harga'],
                        'petugas' => $user['nama']
                    ];

                    if ($dbk->insert($value)) {
                        $total += $i['total_harga'];
                    }
                }
                if ($i['kategori'] == "Barber") {
                    $value = [
                        'layanan_id' => $i['barang_id'],
                        'layanan' => $i['barang'],
                        'harga' => $i['harga_satuan'],
                        'qty' => $i['qty'],
                        "tgl" => time(),
                        'total_harga' => $i['total_harga'],
                        "user_id" => $i['user_id'],
                        "petugas" => $user['nama'],
                        "diskon" => 0,
                        "metode" => "Cash",
                        "status" => 1,
                        "user_id" => $i['user_id']
                    ];
                    $dbb = db('barber');
                    if ($dbk->insert($value)) {
                        $total += $i['total_harga'];
                    }
                }
            }
        }

        message($q['kategori'], $user_m['nama'] . " sukses membayar hutang sebesar " . rupiah($total), "end", "kepada " . $user['nama']);
        sukses_arduino($user_m['nama'] . " sukses membayar hutang sebesar " . rupiah($total), "kepada " . $user['nama']);
    }
    public function tap_booking_ps()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();



        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino("Kartu tidak terdaftar!.");
        }
        if ($q['durasi'] == 0) {
            if ($user["role"] == "Member") {

                message($q['kategori'], "Butuh akses petugas!.", 400);
                gagal_arduino("Butuh akses petugas!.");
            }
        }
        $meja = "Meja " . $q['meja'];

        $dbun = db('unit');
        $unit = $dbun->where('unit', $meja)->get()->getRowArray();

        if (!$unit) {
            message($q['kategori'], "Unit tidak ditemukan!.", 400);
            gagal_arduino("Unit tidak ditemukan!.");
        }

        if ($unit['status'] == 'Maintenance') {
            message($q['kategori'], "Unit dalam perbaikan!.", 400);
            gagal_arduino("Unit dalam perbaikan!.");
        }

        $dbr = db('rental');
        $qr = $dbr->where('unit_id', $unit['id'])->where('is_active', 1)->get()->getRowArray();

        if ($qr) {
            message($q['kategori'], "Unit masih dalam permainan!.", 400);
            gagal_arduino("Unit masih dalam permainan!.");
        }

        $dbset = db('settings');
        $qs = $dbset->where('nama_setting', $unit['kode_harga'])->get()->getRowArray();
        if (!$qs) {
            message($q['kategori'], "Kode harga di unit tidak ada!.", 400);
            gagal_arduino("Kode harga di unit tidak ada!.");
        }
        $biaya = $qs['value_int'] * $q['durasi'];
        if ($user['role'] == "Root") {
            $biaya = 0;
        }
        $fulus = saldo($user);
        if ($biaya > $fulus) {
            message($q['kategori'], $user["nama"] . ", Saldo tidak cukup!.", 400, rupiah($fulus) . " < " . rupiah($biaya));
            gagal_arduino($user["nama"] . ", Saldo tidak cukup!.", rupiah($fulus) . " < " . rupiah($biaya));
        }

        $time = time();

        $durasi_main = $time + ((60 * 60) * $q['durasi']);
        $durasi_jam = $q["durasi"] * 60;
        if ($q['durasi'] == 0) {
            $durasi_jam = -1;
            $durasi_main = -1;
        }

        $datar = [
            'tgl' => $time,
            'unit_id' => $unit['id'],
            'meja' => $meja,
            'dari' => $time,
            'ke' => $durasi_main,
            'durasi' => $durasi_jam,
            'is_active' => 1,
            'biaya' => $biaya,
            'diskon' => 0,
            'metode' => "Tap",
            'harga' => harga_ps($unit['unit']),
            'petugas' => $user['nama']
        ];

        if ($dbr->insert($datar)) {
            $unit['status'] = 'In Game';
            $dbun->where('id', $unit['id']);
            $dbun->update($unit);

            if ($q["durasi"] == 0) {

                message($q['kategori'], $user['nama'] . " sukses open ps " . $meja . ".", 'end');
                sukses_arduino($user['nama'] . " sukses open billiard " . $meja . ".");
            } else {
                $sal = $fulus - $biaya;
                $user['fulus'] = encode_jwt_fulus(['fulus' => $sal]);

                $dbu->where('id', $user['id']);
                if (!$dbu->update($user)) {
                    message($q['kategori'], "Update saldo gagal!.", 400);
                    gagal_arduino("Update saldo gagal!.");
                } else {
                    saldo_tap($q['kategori'], $meja, $biaya, $user);
                    message($q['kategori'], $user['nama'] . " sukses bertansaksi " . rupiah($biaya), "end", "Saldo " . rupiah($sal));
                    sukses_arduino($user['nama'] . " sukses bertansaksi " . rupiah($biaya), rupiah($sal));
                }
            }
        } else {
            message($q['kategori'], "Update meja gagal!.", 400);
            gagal_arduino("Update meja gagal!.");
        }
    }
    public function tap_booking_billiard()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }

        if ($q['durasi'] == 0) {
            if ($user["role"] == "Member") {
                message($q['kategori'], "Butuh akses petugas!.", 400);
                gagal_arduino("Butuh akses petugas!.");
            }
        }


        $dbm = db('jadwal_2');
        $meja = $dbm->where('meja', $q['meja'])->get()->getRowArray();

        if (!$meja) {
            message($q['kategori'], "Meja tidak ditemukan!.", 400);
            gagal_arduino("Saldo", "Meja tidak ditemukan!.");
        }

        if ($meja['is_active'] == 1) {
            message($q['kategori'], "Meja aktif!.", 400);
            gagal_arduino("Meja aktif!.");
        }

        $harga = (int)$meja['harga'] * (int)$q['durasi'];
        if ($user['role'] == "Root") {
            $harga = 0;
        }

        $fulus = saldo($user);
        if ($fulus < $harga) {
            message($q['kategori'], $user["nama"] . ", Saldo tidak cukup!.", 400, rupiah($fulus) . " < " . rupiah($harga));
            gagal_arduino($user["nama"] . ", Saldo tidak cukup!.", rupiah($fulus) . " < " . rupiah($harga));
        }


        $time_now = time();


        $endtime = $time_now + ((60 * 60) * $q['durasi']);
        $durasi_jam = $q['durasi'] * 60;
        if ($q['durasi'] == 0) {
            $endtime = 0;
            $durasi_jam = 0;
        }

        $meja['is_active'] = 1;
        $meja['start'] = $time_now;

        $dbm->where('id', $meja['id']);
        if ($dbm->update($meja)) { //update meja
            $data = [
                'meja_id' => $meja['id'],
                'meja' => "Meja " . $meja['meja'],
                'tgl' => $time_now,
                'durasi' => $durasi_jam,
                'petugas' => $user['nama'],
                'biaya' => $harga,
                'diskon' => 0,
                'start' => $time_now,
                'end' => $endtime,
                'is_active' => 1,
                'harga' => $meja['harga'],
                'metode' => 'Tap'
            ];

            $dbb = db('billiard_2');
            if ($dbb->insert($data)) { //update billiard
                if ($q["durasi"] == 0) {

                    message($q['kategori'], $user['nama'] . " sukses open billiard " . $data['meja'] . ".", "end");
                    sukses_arduino($user['nama'] . " sukses open billiard " . $data['meja'] . ".");
                } else {
                    $sal = $fulus - $harga;
                    $user['fulus'] = encode_jwt_fulus(['fulus' => $sal]);
                    $dbu->where('id', $user['id']);
                    if ($dbu->update($user)) {
                        saldo_tap($q['kategori'], "Meja " . $q['meja'], $harga, $user);
                        message($q['kategori'], $user['nama'] . " sukses bertransaksi sebesar " . rupiah($harga), "end", "Saldo: " . rupiah($sal));
                        sukses_arduino($user['nama'] . " sukses bertransaksi sebesar " . rupiah($harga), "Saldo: " . rupiah($sal));
                    } else {
                        message($q['kategori'], "Update saldo gagal!.", 400);
                        gagal_arduino("Update saldo gagal!.");
                    }
                }
            } else {
                message($q['kategori'], "Insert billiard gagal!.", 400);
                gagal_arduino("Insert billiard gagal!.");
            }
        } else {
            message($q['kategori'], "Update meja gagal!.", 400);
            gagal_arduino("Update meja gagal!.");
        }
    }

    public function del_booking()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);
        if ($decode["uid"] == "message") {
            $db = db("message");
            $q = $db->get()->getRowArray();
            if ($q) {
                $q['status'] = "end";
                $q['message'] = $decode["data3"];
                $q['uang'] = $decode["data4"];
                $q['admin'] = $decode["data5"];
                $q['kategori'] = $decode["data6"];
                $db->where('id', $q['id']);
                $db->update($q);
            } else {
                $data = ['kategori' => $decode["data6"], 'message' => $decode['data3'], 'status' => "end", 'uang' => $decode['data4'], 'admin' => $decode['data5']];
                $db->insert($data);
            }

            $dbl = db("laporan");
            $laporan = ['tgl' => time(), 'kategori' => $decode["data6"], 'message' => $decode['data3'], 'status' => $decode['member_uid'], 'uang' => $decode['data4'], 'admin' => $decode['data5']];
            $dbl->insert($laporan);
            sukses_arduino($decode["data3"]);
        }
        clear_tabel(($decode == "msg" ? "message" : $decode));
        sukses_arduino('Booking dihapus!.');
    }

    public function tap_booking_barber()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }
        $dbb = db('barber');
        $barber = $dbb->where('user_id', $user['id'])->where('status', 0)->get()->getResultArray();
        if (!$barber) {
            message($q['kategori'], "Data transaksi belum dibuat!.", 400);
            gagal_arduino('Data transaksi belum dibuat!.');
        }

        $err = [];
        $total = 0;
        foreach ($barber as $i) {
            $total += $i['total_harga'];
        }
        $saldo = saldo($user);
        if ($saldo < $total) {
            message($q['kategori'], "Saldo tidak cukup!", 400, rupiah($saldo) . " < " . rupiah($total));
            gagal_arduino("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
        }

        $total2 = 0;
        foreach ($barber as $i) {
            $i['status'] = 1;
            $dbb->where('id', $i['id']);

            if ($dbb->update($i)) {
                $total2 += $i['total_harga'];
                saldo_tap($q['kategori'], $i['layanan'], $total2, $user);
            } else {
                $err[] = $i['id'];
            }
        }

        $saldo_akhir = $saldo - $total2;
        $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
        $dbu->where('id', $user['id']);
        if ($dbu->update($user)) {
            if (count($err) > 0) {
                message($q['kategori'], count($err) . ' barang' . " gagal!. " . $user['nama'] . " sukses sebesar " . rupiah($total2), "end", "Saldo: " . rupiah($saldo_akhir));
                sukses_arduino(count($err) . ' barang' . " gagal!.  " . $user['nama'] . " sukses sebesar " . rupiah($total2), "Saldo " . rupiah($saldo_akhir));
            } else {
                message($q['kategori'],  $user['nama'] . " berhasil transaksi sebesar " . rupiah($total2), "end", "Saldo: " . rupiah($saldo_akhir));
                sukses_arduino($user['nama'] . " berhasil transaksi sebesar " . rupiah($total2), "Saldo " . rupiah($saldo_akhir));
            }
        } else {
            message($q['kategori'], "Update saldo gagal!", 400, rupiah($saldo) . " < " . rupiah($total));
            gagal_arduino("Saldo tidak cukup!", rupiah($saldo) . " < " . rupiah($total));
        }
    }

    public function wabot()
    {
        return view('api/wabot', ['judul' => "WA BOT"]);
    }
    public function get_booking()
    {
        $jwt = $this->request->getVar('jwt');
        decode_jwt_fulus($jwt);
        tidak_absen();
        $db = db('booking');
        $q = $db->get()->getRowArray();
        if ($q) {
            sukses_arduino('Silahkan tap!.', $q['kategori'], $q['durasi']);
        } else {
            gagal_arduino('Silahkan pilih meja!');
        }
    }

    public function tap_booking_remove()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Akses kartu ditolakl!.", 400);
            gagal_arduino('Akses kartu ditolakl!.');
        }

        if ($user['role'] !== 'Root') {
            message($q['kategori'], "Akses kartu ditolakl!.", 400);
            gagal_arduino('Akses kartu ditolakl!.');
        }

        $user_m = $dbu->where('id', $q['durasi'])->whereNotIn('role', ["Root"])->get()->getRowArray();
        if (!$user_m) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            gagal_arduino("Kartu tidak dikenal!.");
        }
        $saldo = decode_jwt_fulus($user_m["fulus"]);
        $user_m["uid"] = '';
        $user_m["fulus"] = encode_jwt_fulus(["fulus" => 0]);

        $dbu->where('id', $user_m['id']);
        if ($dbu->update($user_m)) {
            saldo_tap($q["kategori"], '', $saldo["fulus"], $user_m);
            message($q['kategori'], "Kartu " . $user_m['nama'] . " sukses dihapus.", "end", $user['nama']);
            sukses_arduino("Kartu " . $user_m['nama'] . " sukses dihapus.", "", $user['nama']);
        } else {
            message($q['kategori'], "Hapus data gagal!.", 400);
            gagal_arduino("Hapus data gagal!.");
        }
    }


    public function tap_booking_cash()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $order = kode_bayar($q['durasi']);


        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400, $order);
            gagal_arduino('Kartu tidak terdaftar!.');
        }
        if ($user["role"] == "Member") {
            message($q['kategori'], "Butuh akses petugas!.", 400, $order);
            gagal_arduino("Butuh akses petugas!.");
        }


        if ($order == "Ps") {
            $dbr = db('rental');
            $qr = $dbr->where("id", $q["meja"])->where("is_active", 1)->where('durasi', -1)->get()->getRowArray();

            if (!$qr) {

                message($q['kategori'], "Data tabel rental tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel rental ditemukan!.");
            }

            $qr['is_active'] = 0;
            $qr['ke'] = time();
            $qr['biaya'] = $q['harga'];
            $qr['petugas'] = $user['nama'];
            $qr['metode'] = "Cash";
            $qr['durasi'] = round((time() - $qr['dari']) / 60);


            $dbr->where('id', $qr['id']);
            if ($dbr->update($qr)) {
                $dbu = db("unit");
                $qu = $dbu->where("id", $qr['unit_id'])->get()->getRowArray();
                if (!$qu) {

                    message($q['kategori'], "Data meja tidak ditemukan!.", 400, $order);
                    gagal_arduino("Data meja tidak ditemukan!.");
                }
                $qu['status'] = "Available";

                $dbu->where('id', $qu['id']);
                if ($dbu->update($qu)) {
                    message($q['kategori'], $user['nama'] . " menerima pembayaran " . $order . " " . $qr['meja'], "end", angka($q['harga']));
                    sukses_arduino($user['nama'] . " menerima pembayaran " . $order . " " . $qr['meja'], angka($q['harga']));
                } else {

                    message($q['kategori'], "Meja gagal diupdate!.", 400, $order);
                    gagal_arduino("Meja gagal diupdate!.");
                }
            } else {

                message($q['kategori'], "Update ps gagal!.", 400, $order);
                gagal_arduino("Update ps gagal!.");
            }
        }
        if ($order == "Billiard") {

            $dbb = db('billiard_2');
            $qb = $dbb->where("id", $q['meja'])->where("is_active", 1)->where('durasi', 0)->get()->getRowArray();
            if (!$qb) {

                message($q['kategori'], "Data tabel billiard tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel billiard ditemukan!.");
            }

            $qb['is_active'] = 0;
            $qb['end'] = time();
            $qb['biaya'] = $q['harga'];
            $qb['petugas'] = $user['nama'];
            $qb['metode'] = "Cash";
            $qb['durasi'] = round((time() - $qb['start']) / 60);


            $dbb->where('id', $qb['id']);
            if ($dbb->update($qb)) {
                $dbm = db("jadwal_2");
                $qm = $dbm->where("id", $qb['meja_id'])->get()->getRowArray();
                if (!$qm) {

                    message($q['kategori'], "Data meja tidak ditemukan!.", 400, $order);
                    gagal_arduino("Data meja tidak ditemukan!.");
                }
                $qm['is_active'] = 0;
                $qm['start'] = 0;
                $dbm->where('id', $qm['id']);
                if ($dbm->update($qm)) {
                    message($q['kategori'], $user['nama'] . " menerima pembayaran " . $order . " " . $qb['meja'], "end", angka($q['harga']));
                    sukses_arduino($user['nama'] . " menerima pembayaran " . $order . " " . $qb['meja'], angka($q['harga']));
                } else {

                    message($q['kategori'], "Meja gagal diupdate!.", 400, $order);
                    gagal_arduino("Meja gagal diupdate!.");
                }
            } else {

                message($q['kategori'], "Update billiard gagal!.", 400, $order);
                gagal_arduino("Update billiard gagal!.");
            }
        }
    }
    public function tap_booking_tap()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $order = kode_bayar($q['durasi']);


        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400, $order);
            gagal_arduino('Kartu tidak terdaftar!.');
        }

        if ($user["role"] !== "Member") {
            message($q['kategori'], "Kartu ditolak!.", 400, $order);
            gagal_arduino("Kartu ditolak!.");
        }

        $saldo = saldo($user);
        if ($saldo < $q['harga']) {
            message($q['kategori'], $user["nama"] . ", Saldo tidak cukup!.", 400, rupiah($saldo) . " < " . rupiah($q['harga']));
            gagal_arduino($user["nama"] . ", Saldo tidak cukup!.", rupiah($saldo) . " < " . rupiah($q['harga']));
        }

        if ($order == "Ps") {
            $dbr = db('rental');
            $qr = $dbr->where("id", $q['meja'])->where("is_active", 1)->where('durasi', -1)->get()->getRowArray();

            if (!$qr) {

                message($q['kategori'], "Data tabel rental tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel rental ditemukan!.");
            }

            $qr['is_active'] = 0;
            $qr['ke'] = time();
            $qr['biaya'] = $q['harga'];
            $qr['petugas'] = $user['nama'];
            $qr['metode'] = "Tap";
            $qr['durasi'] = round((time() - $qr['dari']) / 60);


            $dbr->where('id', $qr['id']);
            if ($dbr->update($qr)) {
                $sisa = $saldo - $qr['harga'];
                $user['fulus'] = encode_jwt_fulus(["fulus" => $sisa]);
                $dbu->where('id', $user['id']);
                if ($dbu->update($user)) {

                    $dbu = db("unit");
                    $qu = $dbu->where("id", $qr['unit_id'])->get()->getRowArray();
                    if (!$qu) {

                        message($q['kategori'], "Data meja tidak ditemukan!.", 400, $order);
                        gagal_arduino("Data meja tidak ditemukan!.");
                    }
                    $qu['status'] = "Available";

                    $dbu->where('id', $qu['id']);
                    if ($dbu->update($qu)) {
                        saldo_tap($order, $qr["meja"], $q['harga'], $user);

                        message($q['kategori'], $user['nama'] . " sukses bertransaksi sebesar " . rupiah($q['harga']), "end", "Saldo: " . rupiah($sisa));
                        sukses_arduino($user['nama'] . " sukses bertransaksi sebesar " . rupiah($q['harga']), "Saldo: " . rupiah($sisa));
                    } else {

                        message($q['kategori'], "Meja gagal diupdate!.", 400, $order);
                        gagal_arduino("Meja gagal diupdate!.");
                    }
                } else {

                    message($q['kategori'], "Update saldo gagal!.", 400, $order);
                    gagal_arduino("Update saldo gagal!.");
                }
            } else {

                message($q['kategori'], "Update ps gagal!.", 400, $order);
                gagal_arduino("Update ps gagal!.");
            }
        }
        if ($order == "Billiard") {

            $dbb = db('billiard_2');
            $qb = $dbb->where("id", $q['meja'])->where("is_active", 1)->where('durasi', 0)->get()->getRowArray();

            if (!$qb) {

                message($q['kategori'], "Data tabel billiard tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel billiard ditemukan!.");
            }

            $qb['is_active'] = 0;
            $qb['end'] = time();
            $qb['biaya'] = $q['harga'];
            $qb['petugas'] = $user['nama'];
            $qb['metode'] = "Tap";
            $qb['durasi'] = round((time() - $qb['start']) / 60);


            $dbb->where('id', $qb['id']);
            if ($dbb->update($qb)) {
                $sisa = $saldo - $q['harga'];
                $user['fulus'] = encode_jwt_fulus(["fulus" => $sisa]);
                $dbu->where('id', $user['id']);
                if ($dbu->update($user)) {
                    $dbm = db("jadwal_2");
                    $qm = $dbm->where("id", $qb['meja_id'])->get()->getRowArray();
                    if (!$qm) {

                        message($q['kategori'], "Data meja tidak ditemukan!.", 400, $order);
                        gagal_arduino("Data meja tidak ditemukan!.");
                    }
                    $qm['is_active'] = 0;
                    $qm['start'] = 0;
                    $dbm->where('id', $qm['id']);
                    if ($dbm->update($qm)) {
                        saldo_tap($order, $qb['meja'], $q['harga'], $user);
                        message($q['kategori'], $user['nama'] . " sukses bertransaksi sebesar " . rupiah($q['harga']), "end", "Saldo: " . rupiah($sisa));
                        sukses_arduino($user['nama'] . " sukses bertransaksi sebesar " . rupiah($q['harga']), "Saldo: " . rupiah($sisa));
                    } else {

                        message($q['kategori'], "Meja gagal diupdate!.", 400, $order);
                        gagal_arduino("Meja gagal diupdate!.");
                    }
                } else {

                    message($q['kategori'], "Update saldo gagal!.", 400, $order);
                    gagal_arduino("Update saldo gagal!.");
                }
            } else {

                message($q['kategori'], "Update billiard gagal!.", 400, $order);
                gagal_arduino("Update billiard gagal!.");
            }
        }
    }

    public function tap_booking_panel()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $order = kode_bayar($q['durasi']);

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400, $order);
            gagal_arduino('Kartu tidak terdaftar!.');
        }
        if ($user["role"] == "Member") {
            message($q['kategori'], "Butuh akses petugas!.", 400, $order);
            gagal_arduino("Butuh akses petugas!.");
        }
        if ($order == "Ps") {
            $dbu = db("unit");
            $qu = $dbu->where("unit", "Meja " . $q['meja'])->where('is_active', 1)->get()->getRowArray();
            if (!$qu) {

                message($q['kategori'], "Data tabel unit tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel unit ditemukan!.");
            }

            $dbr = db('rental');
            $qr = $dbr->where("unit_id", $qu["id"])->where("is_active", 1)->get()->getRowArray();

            if (!$qr) {

                message($q['kategori'], "Ps open tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Ps open ditemukan!.");
            }

            $qr['is_active'] = 0;

            $dbr->where('id', $qr['id']);
            if ($dbr->update($qr)) {
                $qu['status'] = "Available";

                $dbu->where('id', $qu['id']);
                if ($dbu->update($qu)) {
                    message($q['kategori'], $user['nama'] . " mematikan tv " . $order . " " . $qr['meja'], "end");
                    sukses_arduino($user['nama'] . " mematikan tv " . $order . " " . $qr['meja']);
                } else {

                    message($q['kategori'], "Meja gagal diupdate!.", 400, $order);
                    gagal_arduino("Meja gagal diupdate!.");
                }
            } else {

                message($q['kategori'], "Update ps gagal!.", 400, $order);
                gagal_arduino("Update ps gagal!.");
            }
        }
        if ($order == "Billiard") {

            $dbm = db('jadwal_2');
            $qm = $dbm->where("meja", $q['meja'])->where('is_active', 1)->get()->getRowArray();
            if (!$qm) {

                message($q['kategori'], "Data tabel jadwal tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel jadwal ditemukan!.");
            }
            $dbb = db('billiard_2');
            $qb = $dbb->where("meja_id", $qm['id'])->where("is_active", 1)->get()->getRowArray();
            if (!$qb) {

                message($q['kategori'], "Data tabel billiard tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data tabel billiard ditemukan!.");
            }

            $qb['is_active'] = 0;


            $dbb->where('id', $qb['id']);
            if ($dbb->update($qb)) {
                $qm['is_active'] = 0;
                $qm['start'] = 0;
                $dbm->where('id', $qm['id']);
                if ($dbm->update($qm)) {
                    message($q['kategori'], $user['nama'] . " mematikan lampu " . $order . " " . $qb['meja'], "end", angka($q['harga']));
                    sukses_arduino($user['nama'] . " mematikan lampu " . $order . " " . $qb['meja'], angka($q['harga']));
                } else {

                    message($q['kategori'], "Meja gagal diupdate!.", 400, $order);
                    gagal_arduino("Meja gagal diupdate!.");
                }
            } else {

                message($q['kategori'], "Update billiard gagal!.", 400, $order);
                gagal_arduino("Update billiard gagal!.");
            }
        }
        if ($order == "Others") {

            $dbo = db('perangkat');
            $qo = $dbo->where("id", $q['meja'])->get()->getRowArray();
            if (!$qo) {
                message($q['kategori'], "Data perangkat tidak ditemukan!.", 400, $order);
                gagal_arduino("Saldo", "Data perangkat tidak ditemukan!.");
            }

            $qo['status'] = ($qo['status'] == 0 ? 1 : 0);

            $dbo->where('id', $qo['id']);
            if ($dbo->update($qo)) {
                message($q['kategori'], $user['nama'] . " mematikan " . $qo['jenis'] . " " . $qo['nama'] . ".", "end");
                sukses_arduino($user['nama'] . " mematikan " . $qo['jenis'] . " " . $qo['nama'] . ".");
            } else {
                message($q['kategori'], "Update perangkat gagal!.", 400, $order);
                gagal_arduino("Update perangkat gagal!.");
            }
        }
    }

    public function tap_booking_reload()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }
        if ($user["role"] !== "Root") {
            message($q['kategori'], "Harus Mbahdim!.", 400);
            gagal_arduino("Harus Mbahdim!.");
        }

        message($q['kategori'], "Reload sukses!.", "end");
        sukses_arduino("Reload sukses.");
    }

    public function tap_booking_absen()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message('Gagal', "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->whereNotIn("role", ["Member"])->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }

        $val = get_absen($user);

        $value = [
            'tgl' => date('d', $val['time_server']),
            'username' => $user["username"],
            'ket' => $val['ket'],
            'poin' => $val['poin'],
            'nama' => $user["nama"],
            'role' => $user["role"],
            'user_id' => $user["id"],
            'shift' => $val['shift'],
            'jam' => $val['jam'],
            'absen' => $val['time_server'],
            'terlambat' => $val['menit']
        ];
        $db = db('absen');
        if ($db->insert($value)) {
            $dbn = db('notif');
            $datan = [
                'kategori' => 'Absen',
                'pemesan' => $value['nama'],
                'tgl' => $value['absen'],
                'harga' => time(),
                'menu' => ($value['ket'] == 'Ontime' ? 'Absen pada ' . date('H:i', $val['time_server']) : $val['diff']),
                'meja' => $value['ket'],
                'qty' => $value['poin']
            ];

            if ($dbn->insert($datan)) {
                if ($val['ket'] == 'Terlambat') {
                    message($q['kategori'], $val['msg'], "400");


                    sukses_js($val['msg']);
                } else {
                    message($q['kategori'], $val['msg'], "end");


                    sukses_js($val['msg']);
                }
            } else {

                message($q['kategori'], "Insert notif gagal!", 400);
                gagal_js("Insert notif gagal!.");
            }
        } else {
            message($q['kategori'], "Insert absen gagal!", 400);
            gagal_js("Insert absen gagal!.");
        }
    }
    public function tap_booking_poin()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_fulus($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message('Gagal', "Data booking tidak ditemukan!.", 400);
            gagal_arduino('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->whereNotIn("role", ["Member"])->where('uid', $decode['uid'])->get()->getRowArray();

        if (!$user) {
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }

        $q['durasi'] = $user['id'];
        $db->where('id', $q['id']);
        if ($db->update($q)) {
            message($q['kategori'], 'Id detected.', '200', $user['nama']);
            sukses_js('Id detected. ' . $user['nma']);
        } else {
            message($q['kategori'], 'Insert id failded.', '400');
            gagal_js('Insert id failed.');
        }
    }
    public function get_perangkat()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
        $nama = $decode['uid'];

        $db = db('perangkat');
        $q = $db->get()->getResultArray();

        $jam = (int)date('H');

        $res = [];
        foreach ($q as $i) {
            if ($jam == 0 && $i['otomatis'] == 1) {
                $i['otomatis'] = 0;
                $db->where('nama', $nama);
                $db->update($i);
            } else {
                if ($jam == $i['nyala'] && $i['otomatis'] == 0 && $i['status'] == 0) {
                    $i['status'] = 1;
                    $i['otomatis'] = 1;
                    $db->where('nama', $nama);
                    $db->update($i);
                }
                if ($jam == $i['mati'] && $i['otomatis'] == 0 && $i['status'] == 1) {
                    $i['otomatis'] = 1;
                    $i['status'] = 0;
                    $db->where('nama', $nama);
                    $db->update($i);
                }
            }

            if ($i['nama'] == $nama) {
                $res = $i;
            }
        }

        sukses_js("Sukses", $res['status'], $jam);
    }
    public function itag_press()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
        $nama = $decode['uid'];

        $db = db('perangkat');
        $q = $db->where('nama', $nama)->get()->getRowArray();

        if (!$q) {
            gagal_arduino('Perangkat tidak ditemukan!.');
        }

        $q['status'] = ($q['status'] == 0 ? 1 : 0);
        $db->where('id', $q['id']);

        if ($db->update($q)) {
            sukses_js("Sukses", $q['status']);
        } else {
            gagal_js("Gagal", $q['status']);
        }
    }
}
