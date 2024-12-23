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
                    clear_tabel('booking');
                    message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                    gagal_arduino('Akses admin dibutuhkan!.');
                } else {
                    if ($admin['role'] !== 'Root') {
                        clear_tabel('booking');
                        clear_tabel('api');
                        message($q['kategori'], "Akses admin dibutuhkan!", 400);
                        gagal_arduino('Akses admin dibutuhkan!.');
                    }
                }
            } else {
                clear_tabel('booking');
                message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                gagal_arduino('Akses admin dibutuhkan!.');
            }

            // check uid apakah sudah terdaftar/tidak boleh exist
            $uid_exist = $dbu->where('uid', $decode['uid'])->get()->getRowArray();
            konfirmasi_uid_exist($uid_exist, $q);

            // check user member apakah ada/tidak boleh tidak ada
            $user_m = $dbu->where('id', $q["durasi"])->where('role', 'Member')->get()->getRowArray();
            konfirmasi_user_exist($user_m, $q);

            $uid_member = $decode['uid'];
            if ($uid_member == '') {
                $uid_member = $decode("member_uid");
            }
            $user_m["uid"] = $uid_member;
            $dbu->where('id', $q['durasi']);
            if ($dbu->update($user_m)) {
                sukses_arduino($user_m['nama'] . " sukses didaftarkan.", "", $admin["nama"]);
            }
        } else {
            clear_tabel('api');
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


        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            // api harus ada uid dan uid harus admin
            if ($qa) {
                $admin = $dbu->where('uid', $qa['status'])->get()->getRowArray();
                if (!$admin) {
                    clear_tabel('booking');
                    message($q['kategori'], "Akses admin dibutuhkan!.", 400);
                    gagal_arduino('Akses admin dibutuhkan!.');
                } else {
                    if ($admin['role'] !== 'Root') {
                        clear_tabel('booking');
                        clear_tabel('api');
                        message($q['kategori'], "Akses admin dibutuhkan!", 400);
                        gagal_arduino('Akses admin dibutuhkan!.');
                    }
                }
            } else {
                clear_tabel('booking');
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
                clear_tabel('booking');
                clear_tabel('api');
                gagal_arduino("Kartu tidak dikenal!.");
            }


            $fulus = saldo($user_m);
            $tp = ($q["durasi"] * 10000);
            $saldo = $fulus + $tp;
            $user_m["fulus"] = encode_jwt_fulus(["fulus" => $saldo]);

            $dbu->where('id', $user_m['id']);
            if ($dbu->update($user_m)) {
                sukses_arduino($user_m['nama'] . " sukses topup sebesar " . rupiah($tp), rupiah($saldo), $admin['nama']);
            }
        } else {
            clear_tabel('api');
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
        $user = $dbu->where('uid', $decode['uid'])->where('role', 'Member')->get()->getRowArray();
        if (!$user) {
            message($q['kategori'], "Kartu tidak dikenal!.", 400);
            clear_tabel('booking');
            gagal_arduino('Kartu tidak dikenal!.');
        }

        $saldo = saldo($user);
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
            clear_tabel('booking');
            gagal_arduino('Kartu tidak dikenal!.');
        }

        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            if (!$qa) {
                message($q['kategori'], "Akses api tidak ditemukan!.", 400);
                clear_tabel('booking');
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
                clear_tabel('booking');
                clear_tabel('api');
                gagal_arduino('Kartu tidak dikenal!.');
            }

            if ($qa["status"] !== $uid_member) {
                message($q['kategori'], "Kartu berbeda!.", 400);
                clear_tabel('booking');
                clear_tabel('api');
                gagal_arduino('Kartu berbeda!.');
            }

            $dbh = db('hutang');
            $qh = $dbh->where('user_id', $user_m['id'])->where('status', 0)->get()->getResultArray();
            $total = 0;
            if (!$qh) {
                sukses_arduino($user_m['nama'] . " tidak berhutang.");
            } else {
                foreach ($qh as $i) {
                    $total += $i['total_harga'];
                }
            }
            $saldo = saldo($user_m);

            if ($saldo < $total) {
                message($q['kategori'], $user_m["nama"] . ", saldo tidak cukup!.", 400, rupiah($saldo) . " < " . rupiah($total));
                clear_tabel('booking');
                clear_tabel('api');
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
                                'total_harga' => $i['total_harga'],
                                'petugas' => 'Tap'
                            ];

                            if ($dbk->insert($value)) {
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
                                $total_2 += $i['total_harga'];
                            }
                        }
                    }
                }

                $sal = $saldo - $total_2;
                $user_m['fulus'] = encode_jwt_fulus(['fulus' => $sal]);
                $dbu->where('id', $user_m['id']);
                if ($dbu->update($user_m)) {
                    sukses_arduino($user['nama'] . " sukses bertransaksi sebesar " . rupiah($total_2), rupiah($sal));
                }
            }
        } else {
            clear_tabel('api');
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
                    sukses_arduino($user['nama'] . " tidak berhutang.", "stop");
                } else {
                    message($q['kategori'], $user['nama'] . " berhasil mengakses data.", 200, "Tap untuk melunasi " . rupiah($total) . "...");
                    sukses_arduino($user["nama"] . ' berhasil mengakses data.', 'next', "Tap lagi untuk melunasi " . rupiah($total) . "...");
                }
            }
        }
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
            clear_tabel('booking');
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino("Kartu tidak terdaftar!.");
        }

        $meja = "Meja " . $q['meja'];

        $dbun = db('unit');
        $unit = $dbun->where('unit', $meja)->get()->getRowArray();

        if (!$unit) {
            clear_tabel('booking');
            message($q['kategori'], "Unit tidak ditemukan!.", 400);
            gagal_arduino("Unit tidak ditemukan!.");
        }

        if ($unit['status'] == 'Maintenance') {
            clear_tabel('booking');
            message($q['kategori'], "Unit dalam perbaikan!.", 400);
            gagal_arduino("Unit dalam perbaikan!.");
        }

        $dbr = db('rental');
        $qr = $dbr->where('unit_id', $unit['id'])->where('is_active', 1)->get()->getRowArray();

        if ($qr) {
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
        if ($user['role'] == "Root") {
            $biaya = 0;
        }
        $fulus = saldo($user);
        if ($biaya > $fulus) {
            clear_tabel('booking');
            message($q['kategori'], $user["nama"] . ", Saldo tidak cukup!.", 400, rupiah($fulus) . " < " . rupiah($biaya));
            gagal_arduino($user["nama"] . ", Saldo tidak cukup!.", rupiah($fulus) . " < " . rupiah($biaya));
        }

        $time = time();

        $durasi_main = $time + ((60 * 60) * $q['durasi']);
        $durasi_jam = $q["durasi"] * 60;

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
            'petugas' => $user['nama']
        ];

        if ($dbr->insert($datar)) {
            $unit['status'] = 'In Game';
            $dbun->where('id', $unit['id']);
            $dbun->update($unit);
            $sal = $fulus - $biaya;
            $user['fulus'] = encode_jwt_fulus(['fulus' => $sal]);

            $dbu->where('id', $user['id']);
            if (!$dbu->update($user)) {
                clear_tabel('booking');
                message($q['kategori'], "Update saldo gagal!.", 400);
                gagal_arduino("Update saldo gagal!.");
            } else {
                clear_tabel('booking');
                message($q['kategori'], $user['nama'] . " sukses bertansaksi " . rupiah($biaya), 200, rupiah($sal));
                sukses_arduino($user['nama'] . " sukses bertansaksi " . rupiah($biaya), rupiah($sal));
            }
        } else {
            clear_tabel('booking');
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
            clear_tabel('booking');
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }


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
        if ($user['role'] == "Root") {
            $harga = 0;
        }

        $fulus = saldo($user);
        if ($fulus < $harga) {
            clear_tabel('booking');
            message($q['kategori'], $user["nama"] . ", Saldo tidak cukup!.", 400, rupiah($fulus) . " < " . rupiah($harga));
            gagal_arduino($user["nama"] . ", Saldo tidak cukup!.", rupiah($fulus) . " < " . rupiah($harga));
        }

        $time_now = time();


        $endtime = $time_now + ((60 * 60) * $q['durasi']);
        $durasi_jam = $q['durasi'] * 60;

        $meja['is_active'] = 1;
        $meja['start'] = $time_now;

        $dbm->where('id', $meja['id']);
        if ($dbm->update($meja)) {
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
            if ($dbb->insert($data)) {
                $sal = $fulus - $harga;
                $user['fulus'] = encode_jwt_fulus(['fulus' => $sal]);
                $dbu->where('id', $user['id']);
                if ($dbu->update($user)) {
                    clear_tabel('booking');
                    message($q['kategori'], $user['nama'] . " sukses bertransaksi sebesar " . rupiah($harga), 200, "Saldo: " . rupiah($sal));
                    sukses_arduino($user['nama'] . " sukses bertransaksi sebesar " . rupiah($harga), "Saldo: " . rupiah($sal));
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
        clear_tabel($decode);
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
            clear_tabel('booking');
            message($q['kategori'], "Kartu tidak terdaftar!.", 400);
            gagal_arduino('Kartu tidak terdaftar!.');
        }
        $dbb = db('barber');
        $barber = $dbb->where('user_id', $user['id'])->where('status', 0)->get()->getResultArray();

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
}
