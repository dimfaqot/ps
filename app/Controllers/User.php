<?php

namespace App\Controllers;

class User extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db(menu()['tabel']);

        $q = $db->select('id,nama,hp,img,role,username,bidang,uid,fulus')->orderBy('nama', 'ASC')->get()->getResultArray();

        $data = [];

        foreach ($q as $i) {
            $i['fulus'] = decode_jwt_fulus($i['fulus'])['fulus'];

            $data[] = $i;
        }

        return view('user', ['judul' => menu()['menu'] . ' - PS', 'data' => $data]);
    }

    public function add()
    {
        $nama = upper_first(clear($this->request->getVar('nama')));
        $username = strtolower(clear($this->request->getVar('username')));
        $role = upper_first(clear($this->request->getVar('role')));
        $hp = upper_first(clear($this->request->getVar('hp')));
        $uid = generateRandomString(4);
        $fulus = rp_to_int($this->request->getVar('fulus'));
        $img = 'file_not_found.jpg';
        $password = password_hash(getenv('default_password'), PASSWORD_DEFAULT);

        if ($fulus == "") {
            $fulus = 0;
        }

        $db = db(menu()['tabel']);
        $is_exist = $db->where('username', $username)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Username already taken!.');
        }
        $is_exist_uid = $db->where('uid', $uid)->get()->getRowArray();
        if ($is_exist_uid) {
            clear_tabel('rfid');
            gagal(base_url(menu()['controller']), 'Uid already exist!.');
        }

        $data = [
            'nama' => $nama,
            'username' => $username,
            'fulus' => encode_jwt_fulus(['fulus' => $fulus]),
            'uid' => $uid,
            'role' => $role,
            'password' => $password,
            'img' => $img,
            'hp' => $hp
        ];


        if ($db->insert($data)) {
            clear_tabel('rfid');
            sukses(base_url(menu()['controller']), 'Save data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Save data failed!.');
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $nama = upper_first(clear($this->request->getVar('nama')));
        $username = strtolower(clear($this->request->getVar('username')));
        $role = upper_first(clear($this->request->getVar('role')));
        $hp = upper_first(clear($this->request->getVar('hp')));
        $bidang = upper_first(clear($this->request->getVar('bidang')));
        $img = 'file_not_found.jpg';
        $password = clear($this->request->getVar('password'));
        $uid = clear($this->request->getVar('uid'));
        $fulus = rp_to_int($this->request->getVar('fulus'));
        if ($fulus == "") {
            $fulus = 0;
        }

        $db = db(menu()['tabel']);
        $is_exist = $db->where('username', $username)->whereNotIn('id', [$id])->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Username already taken!.');
        }
        $is_exist_uid = $db->where('uid', $uid)->whereNotIn('id', [$id])->get()->getRowArray();
        if ($is_exist_uid) {
            clear_tabel('rfid');
            gagal(base_url(menu()['controller']), 'Uid already exist!.');
        }

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['nama'] = $nama;
        $q['bidang'] = $bidang;
        $q['username'] = $username;
        $q['uid'] = $uid;
        $q['fulus'] = encode_jwt_fulus(['fulus' => $fulus]);
        $q['role'] = $role;
        if ($password !== '') {
            $q['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $q['img'] = $img;
        $q['hp'] = $hp;

        $db->where('id', $id);
        if ($db->update($q)) {
            clear_tabel('rfid');
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }

    public function get_uid()
    {
        $db = db('rfid');
        $q = $db->get()->getRowArray();

        if (!$q) {
            gagal_js('Kosong!.');
        } else {
            sukses_js('Isi', $q);
        }
    }
    public function update_santri()
    {
        $dbs = db('santri', 'santri');

        $res = [];
        $qs = $dbs->where('status', 'Aktif')->where('deleted', 0)->whereNotIn("uid", [""])->limit(10)->get()->getResultArray();

        if ($qs) {
            $dbu = db('users');
            foreach ($qs as $i) {
                $continue = $dbu->where('uid', $i['uid'])->where('no_id', $i['no_id'])->get()->getRowArray();
                if ($continue) {
                    continue;
                }
                $qu = $dbu->where('uid', $i['uid'])->get()->getRowArray();
                if ($qu) {
                    $res[] = ['nama' => $i['nama'] . ' = ' . $qu['nama'] . " (TB)", 'ket' => 'Exist'];
                } else {
                    $qu_exist = $dbu->where('no_id', $i['no_id'])->get()->getRowArray();

                    if ($qu_exist) {
                        $res[] = ['nama' => $i['nama'], 'ket' => 'Changed'];
                    } else {
                        $data = [
                            'nama' => $i['nama'],
                            'hp' => '',
                            'img' => 'file_not_found.jpg',
                            'role' => 'Member',
                            'bidang' => "Santri",
                            'no_id' => $i['no_id'],
                            'username' => time(),
                            'password' => password_hash("santri_" . $i['no_id'], PASSWORD_DEFAULT),
                            'uid' => $i['uid'],
                            'fulus' => encode_jwt_fulus(['fulus' => 0]),
                            'finger' => 0
                        ];

                        if ($dbu->insert($data)) {
                            $res[] = ['nama' => $i['nama'], 'ket' => 'Success'];
                        } else {
                            $res[] = ['nama' => $i['nama'], 'ket' => 'Failed'];
                        }
                    }
                }
            }
        }
        sukses_js("Ok", $res);
    }
}
