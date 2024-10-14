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

        $db = db('jadwal_2');
        $meja = $db->orderBy('meja', 'ASC')->get()->getResultArray();


        $billiard = [];

        foreach ($meja as $i) {

            if ($i['is_active'] == 0) {
                $i['status'] = 'Available';
                $i['paket'] = 'Available';
                $i['durasi'] = 'Available';
                $i['end'] = 0;
            } else {
                $i['status'] = 'In Game';
                $dbb = db('billiard_2');
                $q = $dbb->where('meja_id', $i['id'])->where('is_active', 1)->get()->getRowArray();
                if ($q['durasi'] == 0) {
                    $i['paket'] = "Reguler";
                    $i['durasi'] = date('H:i', $i['end']);
                } else {
                    $i['paket'] = "Open";
                    $i['durasi'] = durasi($q['start'], $q['end']);
                }
            }
            $billiard[] = $i;
        }

        return view('landing', ['judul' => 'Landing - PS', 'rental' => $rental, 'billiard' => $billiard]);
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
