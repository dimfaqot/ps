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

        $q = $db->select('id,nama,hp,img,role,username,bidang')->orderBy('nama', 'ASC')->get()->getResultArray();

        return view('user', ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
    }

    public function add()
    {
        $nama = upper_first(clear($this->request->getVar('nama')));
        $username = strtolower(clear($this->request->getVar('username')));
        $role = upper_first(clear($this->request->getVar('role')));
        $hp = upper_first(clear($this->request->getVar('hp')));
        $img = 'file_not_found.jpg';
        $password = password_hash(getenv('default_password'), PASSWORD_DEFAULT);

        $db = db(menu()['tabel']);
        $is_exist = $db->where('username', $username)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Username already taken!.');
        }

        $data = [
            'nama' => $nama,
            'username' => $username,
            'role' => $role,
            'password' => $password,
            'img' => $img,
            'hp' => $hp
        ];


        if ($db->insert($data)) {
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

        $db = db(menu()['tabel']);
        $is_exist = $db->where('username', $username)->whereNotIn('id', [$id])->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Username already taken!.');
        }

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['nama'] = $nama;
        $q['bidang'] = $bidang;
        $q['username'] = $username;
        $q['role'] = $role;
        if ($password !== '') {
            $q['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        $q['img'] = $img;
        $q['hp'] = $hp;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }
}
