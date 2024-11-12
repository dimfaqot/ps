<?php

namespace App\Controllers;

class Ext extends BaseController
{
    public function auth($jwt)
    {
        $data = decode_jwt($jwt);
        $data['id'] = 'temp';

        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }
    public function auth_root($jwt)
    {
        $data = decode_jwt($jwt);

        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }
}
