<?php

namespace App\Controllers;

class Finger extends BaseController
{
    public function get_booking()
    {
        $jwt = $this->request->getVar('jwt');
        decode_jwt_finger($jwt);

        $db = db('booking');
        $q = $db->get()->getRowArray();
        if ($q) {
            sukses_js('Silahkan sentuh fingerprint!.', $q['kategori'], $q['durasi']);
        } else {
            gagal_js('Silahkan pilih menu!');
        }
    }

    public function absen()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        $dbu = db('users');
        $q = $dbu->whereNotIn("role", ["Member"])->where('finger', $decode['uid'])->get()->getRowArray();

        if (!$q) {
            gagal_js("Finger tidak terdaftar!.");
        }

        $val = get_absen($q);

        $value = [
            'tgl' => date('d', $val['time_server']),
            'username' => $q["username"],
            'ket' => $val['ket'],
            'poin' => $val['poin'],
            'nama' => $q["nama"],
            'role' => $q["role"],
            'user_id' => $q["id"],
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
                $dbm = db("message");
                if ($val['ket'] == 'Terlambat') {
                    $message = ["message" => $val["msg"], "status" => "200", "kategori" => "Absen"];
                    if ($dbm->insert($message)) {
                        sukses_js($val['msg']);
                    }
                } else {
                    $message = ["message" => $val["msg"], "status" => "400", "kategori" => "Absen"];
                    if ($dbm->insert($message)) {
                        gagal_js($val['msg']);
                    }
                }
            } else {
                gagal_js("Insert notif gagal!.");
            }
        } else {
            gagal_js("Insert absen gagal!.");
        }
    }
    public function add_message()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        message($decode["uid"], $decode["data3"], $decode["data2"], $decode["data4"], $decode["data5"]);
        sukses_js("Add message sukses.");
    }
    public function add()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        // kalau dalam jwt ada keu topupId berarti kartu member yang ditap setelah kartu Root
        $member_uid = false;
        $dba = db('api');
        $qa = $dba->get()->getRowArray();
        if ($qa) {
            $member_uid = true;
        }


        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_js('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->whereNotIn("role", "Member")->where('finger', $decode['uid'])->get()->getRowArray();


        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            // api harus ada uid dan uid harus admin
            if ($qa) {
                $admin = $dbu->whereNotIn("role", "Member")->where('finger', $qa['status'])->get()->getRowArray();
                if (!$admin) {

                    message($q['kategori'], "Finger admin dibutuhkan!.", 400);
                    gagal_js('Finger admin dibutuhkan!.');
                } else {
                    if ($admin['role'] !== 'Root') {


                        message($q['kategori'], "Finger admin dibutuhkan!", 400);
                        gagal_js('Finger admin dibutuhkan!.');
                    }
                }
            } else {

                message($q['kategori'], "Finger admin dibutuhkan!.", 400);
                gagal_js('Finger admin dibutuhkan!.');
            }

            // check uid apakah sudah terdaftar/tidak boleh exist
            $uid_exist = $dbu->where('finger', $decode['uid'])->get()->getRowArray();
            konfirmasi_uid_exist_finger($uid_exist, $q);

            // check user member apakah ada/tidak boleh tidak ada
            $user_m = $dbu->where('id', $q["durasi"])->get()->getRowArray();
            konfirmasi_user_exist_finger($user_m, $q);

            $uid_member = $decode['uid'];
            if ($uid_member == '') {
                $uid_member = $decode("member_uid");
            }
            $user_m["finger"] = $uid_member;
            $dbu->where('id', $q['durasi']);
            if ($dbu->update($user_m)) {
                sukses_js($user_m['nama'] . " sukses didaftarkan.", "", $admin["nama"]);
            }
        } else {

            konfirmasi_root_finger($q, $user);
        }
    }
    public function delete()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);

        // kalau dalam jwt ada keu topupId berarti kartu member yang ditap setelah kartu Root
        $member_uid = false;
        $dba = db('api');
        $qa = $dba->get()->getRowArray();
        if ($qa) {
            $member_uid = true;
        }


        $db = db('booking');
        $q = $db->get()->getRowArray();

        if (!$q) {
            message($q['kategori'], "Data booking tidak ditemukan!.", 400);
            gagal_js('Data booking tidak ditemukan!');
        }

        $dbu = db('users');
        $user = $dbu->whereNotIn("role", "Member")->where('finger', $decode['uid'])->get()->getRowArray();


        if ($member_uid == true) {
            $dba = db('api');
            $qa = $dba->get()->getRowArray();
            // api harus ada uid dan uid harus admin
            if ($qa) {
                $admin = $dbu->whereNotIn("role", "Member")->where('finger', $qa['status'])->get()->getRowArray();
                if (!$admin) {

                    message($q['kategori'], "Finger admin dibutuhkan!.", 400);
                    gagal_js('Finger admin dibutuhkan!.');
                } else {
                    if ($admin['role'] !== 'Root') {


                        message($q['kategori'], "Finger admin dibutuhkan!", 400);
                        gagal_js('Finger admin dibutuhkan!.');
                    }
                }
            } else {

                message($q['kategori'], "Finger admin dibutuhkan!.", 400);
                gagal_js('Finger admin dibutuhkan!.');
            }

            // check uid apakah sudah terdaftar/tidak boleh exist
            $uid_exist = $dbu->where('finger', $decode['uid'])->get()->getRowArray();
            konfirmasi_uid_exist_finger($uid_exist, $q);

            // check user member apakah ada/tidak boleh tidak ada
            $user_m = $dbu->where('id', $q["durasi"])->get()->getRowArray();
            konfirmasi_user_exist_finger($user_m, $q);

            $uid_member = $decode['uid'];
            if ($uid_member == '') {
                $uid_member = $decode("member_uid");
            }
            $user_m["finger"] = time();
            $dbu->where('id', $q['durasi']);
            if ($dbu->update($user_m)) {
                sukses_js($user_m['nama'] . " sukses dihapus.", "", $admin["nama"]);
            }
        } else {

            konfirmasi_root_finger($q, $user);
        }
    }

    public function del_message()
    {
        $jwt = $this->request->getVar('jwt');
        $decode = decode_jwt_finger($jwt);
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
            $laporan = ['tgl' => time(), 'kategori' => $decode["data6"], 'message' => $decode['data3'], 'status' => $decode['data2'], 'uang' => $decode['data4'], 'admin' => $decode['data5']];
            $dbl->insert($laporan);
            sukses_js($decode["data3"]);
        }
        clear_tabel($decode);
        sukses_js('Booking dihapus!.');
    }
}
