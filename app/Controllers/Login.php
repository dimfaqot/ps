<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index(): string
    {
        return view('login', ['judul' => 'Login - PS']);
    }
    public function landing(): string
    {
        $db = db('unit');

        $rental = $db->orderBy('id', 'ASC')->get()->getResultArray();

        $db = db('jadwal');

        $hari = hari(date('l'))['indo'];

        $billiard = $db->where('hari', $hari)->orderBy('meja', 'ASC')->orderBy('jam', 'ASC')->get()->getResultArray();
        $meja = $db->groupBy('meja')->orderBy('meja', 'ASC')->get()->getResultArray();
        return view('landing', ['judul' => 'Landing - PS', 'rental' => $rental, 'meja' => $meja, 'billiard' => $billiard]);
    }

    public function auth()
    {
        $username = clear($this->request->getVar('username'));
        $password = clear($this->request->getVar('password'));


        $db = db('users');
        $data = [
            'username' => $username,
            'password' => $password
        ];

        $q = $db->where('username', $username)->get()->getRowArray();

        if (!$q) {
            gagal(base_url('login'), 'Username not found!.');
        }

        if (!password_verify($password, $q['password'])) {
            gagal(base_url('login'), 'Password wrong!.');
        }

        $data = [
            'id' => $q['id'],
            'role' => $q['role']
        ];

        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }

    public function logout()
    {
        session()->remove('id');
        session()->remove('role');

        sukses(base_url('login'), 'Logout sukses!.');
    }
}
