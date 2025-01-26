<?php

namespace App\Controllers;

class Rfid extends BaseController
{
    public function index($lokasi)
    {
        return view('rfid/home', ['judul' => 'RFID', 'lokasi' => upper_first($lokasi)]);
    }

    public function start()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
        $uid = $decode['uid'];

        $dbs = db('session');
        $qs = $dbs->get()->getResultArray();
        if ($qs) {
            foreach ($qs as $i) {
                $dbs->where('id', $i['id']);
                $dbs->delete();
            }
        }

        $db = db('users');
        $q = $db->where('uid', $uid)->get()->getRowArray();

        if (!$q) {
            $data = [
                'lokasi' => $decode['data3'],
                'status' => "400",
                'message' => "Kartu tidak terdaftar!."
            ];

            if ($dbs->insert($data)) {
                gagal_js("Kartu tidak terdaftar!.");
            }
        }


        $data = [
            'lokasi' => $decode['data3'],
            'uid' => $q['uid'],
            'uid_member' => $decode['data2'],
            'status' => "200",
            'url' => encode_jwt_fulus(['uid' => $uid, 'lokasi' => $decode['data3'], 'exp' => (time() + 60)]),
            'message' => "Hai " . $q['nama'] . "...."
        ];

        if ($dbs->insert($data)) {
            sukses_js("Hai " . $q['nama'] . "....");
        }
    }

    public function session()
    {
        $lokasi = clear($this->request->getVar('lokasi'));
        $dbs = db('session');
        $q = $dbs->where("lokasi", $lokasi)->get()->getRowArray();

        if ($q) {
            if ($q['url'] !== "") {
                $exp = decode_jwt_fulus($q['url']);
                if ((time() + 60) > $exp['exp']) {
                    $q['status'] = '400';
                    $q['message'] = 'Time expired!.';
                    $dbs->update($q);
                }
            }


            session()->set($q);

            $dbs->where('id', $q['id']);
            $dbs->delete();
            sukses_js("Ok", $q);
        }

        gagal_js("Session not found!.");
    }
    public function logout()
    {
        session()->remove('lokasi');
        session()->remove('status');
        session()->remove('message');
        session()->remove('uid');
        session()->remove('url');
        session()->remove('uid_member');

        sukses_js('Sukses.');
    }
    // halaman eksekusi melalui jwt
    public function execute($jwt)
    {
        $decode = decode_jwt_fulus($jwt);


        if ((time() + 60) > $decode['exp']) {
            gagal_rfid(base_url('rfid'), "Time expired!.");
        }

        $uid = $decode['uid'];

        $db = db('users');
        $q = $db->where('uid', $uid)->get()->getRowArray();

        if (!$q) {
            gagal_rfid(base_url('rfid'), "Kartu tidak terdaftar!.");
        }

        $saldo = saldo($q);
        return view("rfid/execute", ['judul' => 'EXECUTE', 'nama' => $q['nama'], 'saldo' => $saldo, 'role' => $q['role'], 'user_id' => $q['id']]);
    }

    // menampilkan data hutang member
    public function hutang()
    {
        $user_id = clear($this->request->getVar("user_id"));
        $db = db("hutang");
        $q = $db->where("user_id", $user_id)->where('status', 0)->get()->getResultArray();

        sukses_js("Sukses", $q);
    }

    // melunasi hutang member secara mandiri melalui tap
    public function lunasi_hutang()
    {
        $user_id = clear($this->request->getVar("user_id"));
        $dbu = db('users');
        $user = $dbu->where('uid', session('uid'))->get()->getRowArray();

        if (!$user) {
            gagal_js("User tidak ditemukan!.");
        }
        $saldo = decode_jwt_fulus($user['fulus']);
        $saldo = $saldo['fulus'];
        $dbh = db("hutang");
        $hutang = $dbh->where("user_id", $user_id)->where('status', 0)->get()->getResultArray();

        if (!$hutang) {
            gagal_js("Kamu tidak berhutang!.");
        }

        $total_hutang = 0;
        foreach ($hutang as $i) {
            $total_hutang += (int)$i['total_harga'];
        }

        if ($saldo < $total_hutang) {
            gagal_js("Saldo tidak cukup!.");
        }

        $total = 0;
        foreach ($hutang as $i) {
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
                        $total += $i['total_harga'];
                        saldo_tap($i["kategori"], $value['meja'], $i['total_harga'], $user);
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
                        saldo_tap($i["kategori"], $value['barang'], $i['total_harga'], $user);
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
                        "petugas" => 'Tap',
                        "diskon" => 0,
                        "metode" => "Tap",
                        "status" => 1,
                        "user_id" => $i['user_id']
                    ];
                    $dbb = db('barber');
                    if ($dbk->insert($value)) {
                        saldo_tap($i["kategori"], $value['layanan'], $i["total_harga"], $user);
                        $total += $i['total_harga'];
                    }
                }
            }
        }

        $saldo_akhir = $saldo - $total;

        $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
        $dbu->where("id", $user_id);
        if ($dbu->update($user)) {
            sukses_js("Transaksi sukses: " . angka($total) . ". Saldo: " . angka($saldo_akhir));
        } else {
            gagal_js("Update saldo gagal: " . angka($total) . ". Saldo: " . angka($saldo));
        }
    }

    // membayar tagihan yang dibuat admin lalu dibayar dengan tap member secara mandiri
    // setelah member tap maka langsung menampilkan data tagihan barber member
    public function lunasi_barber()
    {
        $user_id = clear($this->request->getVar("user_id"));
        $dbu = db('users');
        $user = $dbu->where('uid', session('uid'))->get()->getRowArray();

        if (!$user) {
            gagal_js("User tidak ditemukan!.");
        }
        $saldo = decode_jwt_fulus($user['fulus']);
        $saldo = $saldo['fulus'];

        $dbb = db("barber");
        $barber = $dbb->where("user_id", $user_id)->where('status', 0)->get()->getResultArray();

        if (!$barber) {
            gagal_js("Kamu tidak berhutang!.");
        }
        $err = 0;
        $total = 0;
        foreach ($barber as $i) {
            $total += $i['total_harga'];
        }

        if ($saldo < $total) {
            gagal_js("Saldo tidak cukup!.");
        }

        $total2 = 0;
        foreach ($barber as $i) {
            $i['status'] = 1;
            $dbb->where('id', $i['id']);

            if ($dbb->update($i)) {
                $total2 += $i['total_harga'];
                saldo_tap(session('lokasi'), $i['layanan'], $i['harga'], $user);
            } else {
                $err++;
            }
        }

        $saldo_akhir = $saldo - $total2;
        $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
        $dbu->where('id', $user['id']);
        if ($dbu->update($user)) {
            if ($err > 0) {
                gagal_js($err . " gagal: " . angka($total2) . ". Saldo: " . angka($saldo_akhir));
            } else {
                sukses_js("Transaksi sukses: " . angka($total2) . ". Saldo: " . angka($saldo_akhir));
            }
        } else {
            gagal_js("Update saldo gagal!.");
        }
    }

    // transaksi billiard dan ps oleh member secara mandiri melalui tap
    public function transaksi()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);

        if ($data['menu'] == "topup" || $data['menu'] == "rfid" || $data['menu'] == "finger") {
            $fun = $data['menu'];
            $this->$fun(($data));
        }

        $dbu = db('users');
        $user = $dbu->where('uid', session('uid'))->get()->getRowArray();

        if (!$user) {
            gagal_js("User tidak ditemukan!.");
        }
        $saldo = decode_jwt_fulus($user['fulus']);
        $saldo = $saldo['fulus'];

        if (session('lokasi') == "Billiard") {
            $no_meja = $data['sub_menu'];
            $durasi = $data['durasi'];

            $dbm = db("jadwal_2");
            $meja = $dbm->where('meja', $no_meja)->get()->getRowArray();

            if (!$meja) {
                gagal_js("Meja tidak ditemukan!.");
            }
            if ($meja['is_active'] == 1) {
                gagal_js("Meja sedang digunakan!.");
            }

            $harga = (int)$meja['harga'] * (int)$durasi;

            if ($user['role'] == "Gus" || $user['role'] == "Root") {
                $harga = 0;
            }
            if ($user['role'] == "Member" && $durasi > 0) {
                if ($saldo < $harga) {
                    gagal_js("Saldo tidak cukup!.");
                }
            }

            $time_now = time();


            $endtime = $time_now + ((60 * 60) * $durasi);
            $durasi_jam = $durasi * 60;

            if ($durasi == 0) {
                $endtime = 0;
                $durasi_jam = 0;
            }

            $meja['is_active'] = 1;
            $meja['start'] = $time_now;

            $dbm->where('id', $meja['id']);
            if ($dbm->update($meja)) { //update meja
                $data_billiard = [
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
                if ($dbb->insert($data_billiard)) { //update billiard
                    $saldo_akhir = $saldo - $harga;
                    $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
                    $dbu->where('id', $user['id']);
                    if ($dbu->update($user)) {
                        saldo_tap(session('lokasi'), "Meja " . $meja['meja'], $harga, $user);
                        sukses_js("Transaksi sukses: " . angka($harga) . ". Saldo: " . angka($saldo_akhir));
                    } else {
                        gagal_js("Update saldo gagal!.");
                    }
                } else {
                    gagal_js("Insert " . session("lokasi") . " gagal!.");
                }
            } else {
                gagal_js("Update meja gagal!.");
            }
        }
        if (session('lokasi') == "Ps") {
            $no_meja = $data['sub_menu'];
            $durasi = $data['durasi'];

            $dbu = db("unit");
            $meja = $dbu->where('meja', "Meja " . $no_meja)->get()->getRowArray();

            if (!$meja) {
                gagal_js("Meja tidak ditemukan!.");
            }

            $harga = harga_ps($meja['unit']) * (int)$durasi;
            if ($user['role'] == "Gus" || $user['role'] == "Root") {
                $harga = 0;
            }

            if ($saldo < $harga) {
                gagal_js("Saldo tidak cukup!.");
            }

            if ($meja['status'] !== "Available") {
                gagal_js("Meja sedang digunakan!.");
            }


            $time_now = time();


            $endtime = $time_now + ((60 * 60) * $durasi);
            $durasi_jam = $durasi * 60;

            if ($durasi == 0) {
                $endtime = -1;
                $durasi_jam = -1;
            }

            $meja['status'] = 'In Game';
            $dbu->where('id', $meja['id']);
            if ($dbu->update($meja)) {
                $data_ps = [
                    'tgl' => $time_now,
                    'unit_id' => $meja['id'],
                    'meja' => $meja,
                    'dari' => $time_now,
                    'ke' => $endtime,
                    'durasi' => $durasi_jam,
                    'is_active' => 1,
                    'biaya' => $harga,
                    'diskon' => 0,
                    'metode' => "Tap",
                    'harga' => harga_ps($meja['unit']),
                    'petugas' => $user['nama']
                ];

                $dbr = db("rental");
                if ($dbr->insert($data_ps)) {
                    $saldo_akhir = $saldo - $harga;
                    $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
                    $dbu->where('id', $user['id']);
                    if ($dbu->update($user)) {
                        saldo_tap(session('lokasi'), "Meja " . $meja['meja'], $harga, $user);
                        sukses_js("Transaksi sukses: " . angka($harga) . ". Saldo: " . angka($saldo_akhir));
                    } else {
                        gagal_js("Update saldo gagal!.");
                    }
                } else {
                    gagal_js("Insert " . session("lokasi") . " gagal!.");
                }
            } else {
                gagal_js("Update meja gagal!.");
            }
        }
    }

    // turunan dari route transaksi
    // topup hanya bisa dilakukan role root
    function topup($data)
    {
        $dbu = db("users");
        $admin = $dbu->where('uid', session('uid'))->get()->getRowArray();
        if (!$admin) {
            gagal_js("Admin tidak ditemukan!.");
        }
        if ($admin['role'] !== "Root") {
            gagal_js("Role ditolak!.");
        }

        $jml_topup = (int)$data['sub_menu'] * 10000;
        $member_id = $data['durasi'];

        $member = $dbu->where('role', 'Member')->where('id', $member_id)->get()->getRowArray();

        if (!$member) {
            gagal_js("Data member tidak ditemukan!.");
        }

        if ($member['uid'] == "") {
            gagal_js("Member belum punya kartu!.");
        }
        $saldo_awal = saldo($member);
        $saldo_akhir = $saldo_awal + $jml_topup;

        $member['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
        $dbu->where('id', $member['id']);
        if ($dbu->update($member)) {
            saldo_tap("Topup", session('lokasi'), $jml_topup, $member, $admin['nama']);
            sukses_js("Topup sukses: " . angka($saldo_awal) . " + " . angka($jml_topup) . "|a/n " . $member['nama'] . "|Saldo: " . angka($saldo_akhir));
        } else {
            gagal_js("Topup gagal!.");
        }
    }

    // turunan dari route transaksi
    // hanya bisa dilakukan oleh role Root
    function rfid($data)
    {
        $dbu = db("users");
        $admin = $dbu->where('uid', session('uid'))->get()->getRowArray();
        if (!$admin) {
            gagal_js("Admin tidak ditemukan!.");
        }
        if ($admin['role'] !== "Root") {
            gagal_js("Role ditolak!.");
        }

        $order = $data['sub_menu'];
        $member_id = $data['durasi'];

        $member = $dbu->where('role', 'Member')->where('id', $member_id)->get()->getRowArray();

        if (!$member) {
            gagal_js("Data member tidak ditemukan!.");
        }

        if ($order == "add" && $member['uid'] !== "") {
            gagal_js("Member sudah punya kartu: " . $member['uid']);
        }

        if ($order == "update" || $order == "delete") {
            if ($member['uid'] == "") {
                gagal_js("Member belum punya kartu!.");
            }
        }

        if ($order == "add" || $order == "update") {
            if (!session('uid_member')) {
                gagal_js("Kartu baru tidak terbaca!.");
            }

            $is_exist = $dbu->where('uid', session('uid_member'))->get()->getRowArray();
            if ($is_exist) {
                gagal_js("Kartu sudah terdaftar|a/n " . $is_exist['nama']);
            }
        }

        if ($order == "add") {
            $member['uid'] = session('uid_member');
            $dbu->where("id", $member['id']);
            if ($dbu->update($member)) {
                sukses_js("Kartu berhasil didaftarkan.|a/n " . $member['nama'] . "|uid: " . session('uid_member'));
            } else {
                gagal_js("Add uid gagal!.");
            }
        }

        if ($order == "update") {
            $member['uid'] = session('uid_member');
            $member['fulus'] = encode_jwt_fulus(['fulus' => 0]);
            $dbu->where("id", $member['id']);
            if ($dbu->update($member)) {
                saldo_tap("Remove", session('lokasi'), saldo($member), $member, $admin['nama']);
                sukses_js("Kartu berhasil diupdate.|a/n " . $member['nama'] . "|uid: " . session('uid_member'));
            } else {
                gagal_js("Update uid gagal!.");
            }
        }
        if ($order == "delete") {
            $member['uid'] = "";
            $member['fulus'] = encode_jwt_fulus(['fulus' => 0]);
            $dbu->where("id", $member['id']);
            if ($dbu->update($member)) {
                saldo_tap("Remove", session('lokasi'), saldo($member), $member, $admin['nama']);
                sukses_js("Kartu berhasil dihapus.|a/n " . $member['nama']);
            } else {
                gagal_js("Delete uid gagal!.");
            }
        }
    }

    // hanya untuk admin
    public function absen()
    {
        $dbu = db('users');
        $user = $dbu->whereNotIn('role', ["Member"])->where('uid', session('uid'))->get()->getRowArray();

        if (!$user) {
            gagal_js("User tidak ditemukan!.");
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
                    gagal_js($val['msg']);
                } else {
                    sukses_js($val['msg']);
                }
            } else {
                gagal_js("Insert notif gagal!.");
            }
        } else {
            gagal_js("Insert absen gagal!.");
        }
    }

    // hanya untuk admin
    public function poin()
    {
        $db = db('users');
        $user = $db->whereNotIn('role', ["Member"])->where('uid', session('uid'))->get()->getRowArray();

        if (!$user) {
            gagal_js('User tidak ditemukan!.');
        }
        $data = poin_absen($user['id'], 'Tap');

        sukses_js("Sukses", $data['data'], $data['poin']);
    }

    // hanya untuk admin sesuai lokasi
    public function perangkat()
    {
        $db = db('users');
        $user = $db->whereNotIn('role', ["Member"])->where('uid', session('uid'))->get()->getRowArray();

        if (!$user) {
            gagal_js('Akses ditolak!.');
        }

        $id = clear($this->request->getVar('id'));
        $dbp = db('perangkat');
        $q = $dbp->where('id', $id)->get()->getRowArray();

        $q['status'] = ($q['status'] == 1 ? 0 : 1);
        $dbp->where('id', $id);
        if ($dbp->update($q)) {
            sukses_js("Update " . $q['nama'] . " sukses.");
        } else {
            gagal_js("Update " . $q['nama'] . " gagal!.");
        }
    }

    // admin mengakhiri permainan.
    // Jika open maka mengembalikan harga dan durasi lalu selanjutnya muncul opsi tap atau cash
    // Jika tidak open maka fungsinya menjadi hanya mematikan lampu
    public function akhiri_permainan()
    {
        $order = clear($this->request->getVar('order'));
        $id = clear($this->request->getVar('id'));
        $dbu = db("users");
        $user = $dbu->where('uid', session('uid'))->get()->getRowArray();
        if (!$user) {
            gagal_js("Admin tidak ditemukan!.");
        }
        if ($user['role'] == "Member") {
            gagal_js("Role ditolak!.");
        }
        if (session('lokasi') == "Billiard") {
            $dbm = db('jadwal_2');
            $meja = $dbm->where("meja", $id)->where('is_active', 1)->get()->getRowArray();

            if (!$meja) {
                gagal_js("Meja " . $id . " tidak sedang aktif!.");
            }

            $dbb = db("billiard_2");
            $billiard = $dbb->where('meja', "Meja " . $id)->where("is_active", 1)->get()->getRowArray();

            if (!$billiard) {
                gagal_js("Data di tabel billiard tidak ditemukan!.");
            }
            $is_open = ($billiard['durasi'] == 0 && $billiard['end'] == 0 ? 'open' : null);
            $time_now = time();
            if ($is_open) {
                $dur = explode(":", durasi($billiard['start'], $time_now));
                $durasi = $dur[0] . "h " . $dur[1] . "m";
                $biaya = biaya_per_menit($meja['harga'], $billiard['start'], $time_now);
                sukses_js($durasi, $biaya, 'open', $billiard['id']);
            } else {
                $meja['is_active'] = 0;
                $meja['start'] = 0;

                $dbm->where('id', $meja['id']);

                if ($dbm->update($meja)) {
                    $billiard['is_active'] = 0;
                    $dbb->where('id', $billiard['id']);
                    if ($dbb->update($billiard)) {
                        sukses_js("Permainan berhasil dihentikan.");
                    } else {
                        gagal_js("Update billiard gagal!.");
                    }
                } else {
                    gagal_js("Update meja gagal!.");
                }
            }
        }
    }

    // admin menerima pembayaran open dengan cara cash
    public function bayar_permainan()
    {
        $id = clear($this->request->getVar('id'));
        $order = clear($this->request->getVar('order'));
        $biaya = clear($this->request->getVar('biaya'));

        $dbu = db("users");
        $user = $dbu->where('uid', session('uid'))->get()->getRowArray();
        if (!$user) {
            gagal_js("Admin tidak ditemukan!.");
        }
        if ($user['role'] == "Member") {
            gagal_js("Role ditolak!.");
        }

        if (session('lokasi') == "Billiard") {
            $dbb = db('billiard_2');
            $billiard = $dbb->where('id', $id)->get()->getRowArray();
            if (!$billiard) {
                gagal_js("Data di tabel billiard tidak ditemukan!.");
            }
            if ($billiard['is_active'] == 0) {
                gagal_js("Billiard tidak sedang aktif!.");
            }

            if ($billiard['durasi'] > 0) {
                gagal_js("Billiard tidak open!.");
            }

            $dbm = db('jadwal_2');
            $no_meja = explode(" ", $billiard['meja']);
            $no_meja = end($no_meja);
            $meja = $dbm->where('meja', $no_meja)->get()->getRowArray();

            if (!$meja) {
                gagal_js("Meja tidak aktif!.");
            }

            $time_now = time();
            $biaya_saat_ini = biaya_per_menit($meja['harga'], $billiard['start'], $time_now);
            $biaya_berlaku = (((int)$biaya_saat_ini - (int)$biaya) < 1001 ? $biaya : $biaya_saat_ini);
            if ($order == "cash") {
                $meja['is_active'] = 0;
                $meja['start'] = 0;

                $dbm->where('id', $meja['id']);
                if ($dbm->update($meja)) {
                    $billiard['durasi'] = durasi_dalam_menit($billiard['start'], $time_now);
                    $billiard['biaya'] = $biaya_berlaku;
                    $billiard['is_active'] = 0;
                    $billiard['metode'] = 'Cash';
                    $billiard['end'] = $time_now;
                    $dbb->where('id', $billiard['id']);
                    if ($dbb->update($billiard)) {
                        sukses_js("Transaksi sukses: " . angka($biaya_berlaku) . ".");
                    } else {
                        gagal_js("Transaksi gagal!.");
                    }
                } else {
                    gagal_js("Update meja gagal!.");
                }
            }
        }
    }

    // hanya bisa dilakukan admin dan root
    // admin mencari member sedangkan root mencari semuanya
    public function search_user()
    {
        $value = clear($this->request->getVar('value'));
        $db = db('users');
        $admin = $db->where('uid', session('uid'))->get()->getRowArray();

        if (!$admin) {
            gagal_js("Admin tidak ditemukan!.");
        }
        if ($admin['role'] == "Member") {
            gagal_js("Role ditolak!.");
        }

        $db->select('id,nama,role');
        if ($admin['role'] !== "Root") {
            $db->whereIn('role', ['Member']);
        }
        $q = $db->like('nama', $value, 'both')->limit(8)->orderBy('role', "ASC")->orderBy('nama', 'ASC')->get()->getResultArray();

        sukses_js('Sukses', $q);
    }

    // admin menerima pembayaran open dengan cara tap
    public function transaksi_tap()
    {
        $member_id = clear($this->request->getVar('member_id'));
        $biaya = clear($this->request->getVar('biaya'));
        $billiard_id = clear($this->request->getVar('billiard_id'));
        $no_meja = clear($this->request->getVar('meja'));
        $dbu = db('users');

        $admin = $dbu->where('uid', session('uid'))->get()->getRowArray();
        if (!$admin) {
            gagal_js("Admin tidak ditemukan!.");
        }
        if ($admin['role'] == "Member") {
            gagal_js("Role ditolak!.");
        }
        $user = $dbu->whereIn('role', ['Member'])->where('id', $member_id)->get()->getRowArray();


        if (session('lokasi') == "Billiard") {
            $dbb = db('billiard_2');
            $billiard = $dbb->where('id', $billiard_id)->get()->getRowArray();
            if (!$billiard) {
                gagal_js("Data di tabel billiard tidak ditemukan!.");
            }
            if ($billiard['is_active'] == 0) {
                gagal_js("Billiard tidak sedang aktif!.");
            }

            if ($billiard['durasi'] > 0) {
                gagal_js("Billiard tidak open!.");
            }

            $dbm = db('jadwal_2');

            $meja = $dbm->where('meja', $no_meja)->get()->getRowArray();

            if (!$meja) {
                gagal_js("Meja tidak aktif!.");
            }

            $time_now = time();
            $biaya_saat_ini = biaya_per_menit($meja['harga'], $billiard['start'], $time_now);
            $biaya_berlaku = (((int)$biaya_saat_ini - (int)$biaya) < 1001 ? $biaya : $biaya_saat_ini);

            $saldo = saldo($user);

            if ($saldo < $biaya_berlaku) {
                gagal_js("Saldo tidak cukup: " . angka($saldo) . ".");
            }

            $meja['is_active'] = 0;
            $meja['start'] = 0;

            $dbm->where('id', $meja['id']);
            if ($dbm->update($meja)) {
                $billiard['durasi'] = durasi_dalam_menit($billiard['start'], $time_now);
                $billiard['biaya'] = $biaya_berlaku;
                $billiard['is_active'] = 0;
                $billiard['metode'] = 'Tap';
                $billiard['end'] = $time_now;
                $dbb->where('id', $billiard['id']);
                if ($dbb->update($billiard)) {
                    $saldo_akhir = $saldo - $biaya_berlaku;
                    $user['fulus'] = encode_jwt_fulus(['fulus' => $saldo_akhir]);
                    $dbu->where('id', $user['id']);
                    if ($dbu->update($user)) {
                        saldo_tap(session('lokasi'), $billiard["meja"], $biaya_berlaku, $user, $admin['nama']);
                        sukses_js("Transaksi sukses: " . angka($biaya_berlaku) . ". Saldo: " . angka($saldo_akhir));
                    } else {
                        gagal_js("Update saldo gagal!. " . angka($biaya_berlaku));
                    }
                } else {
                    gagal_js("Transaksi gagal!.");
                }
            } else {
                gagal_js("Update meja gagal!.");
            }
        }
    }
}
