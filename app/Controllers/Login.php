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
        return view('landing', ['judul' => 'Landing - PS']);
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
            'id' => $q['id']
        ];

        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }

    public function logout()
    {
        session()->remove('id');

        sukses(base_url('login'), 'Logout sukses!.');
    }
}
