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
        if ($data['role'] !== 'Root') {
            gagal(base_url('home'), 'Role harus root.');
        }
        session()->set($data);

        sukses(base_url('home'), 'Ok');
    }
    public function auth_jwt($jwt)
    {
        $data = decode_jwt($jwt);
        $db = db('users');

        $q = $db->where('id', $data['id'])->get()->getRowArray();

        if (!$q) {
            gagal(base_url(), 'User not found!.');
        }
        $val = [
            'id' => $data['id'],
            'role' => $data['role'],
            'latitude' => '',
            'longitude' => ''
        ];
        session()->set($val);

        sukses(base_url('home'), 'Ok');
    }
    public function menu()
    {
        $db = db('barang');
        $q = $db->orderBy('barang', 'ASC')->get()->getResultArray();
        return view('ext_menu', ['judul' => 'Daftar Menu', 'data' => $q]);
    }
    public function save_menu_pesanan()
    {
        $data = json_decode(json_encode($this->request->getVar('order_list')), true);
        $no_meja = clear($this->request->getVar('no_meja'));
        $user_id = clear($this->request->getVar('user_id'));
        $nama_pemesan = upper_first(clear($this->request->getVar('nama_pemesan')));
        $dbn = db('notif');

        $no_nota = no_nota('kantin', $no_meja);
        $tgl = time();
        $err = [];
        foreach ($data as $i) {
            $datan = [
                'no_nota' => $no_nota,
                'tgl' => $tgl,
                'menu' => $i['barang'],
                'harga' => $i['harga'],
                'qty' => $i['qty_item'],
                'total' => $i['total_item'],
                'meja' => $no_meja,
                'pemesan' => $nama_pemesan,
                'dibaca' => 'WAITING',
                'kategori' => 'Pesanan',
                'id_pemesan' => $user_id
            ];
            if (!$dbn->insert($datan)) {
                $err[] = $i['barang'];
            }
        }
        $res = [
            'no_nota' => $no_nota,
            'nama_pemesan' => $nama_pemesan,
            'no_meja' => $no_meja
        ];

        if (count($err) > 0) {
            gagal_js('Gagal disave: ' . implode(", ", $err), json_encode($res));
        } else {
            sukses_js('Pesanan sukses disimpan.', encode_jwt($res));
        }
    }

    public function pesanan($jwt)
    {
        $data = decode_jwt($jwt);
        $db = db('notif');
        $q = $db->where('no_nota', $data['no_nota'])->orderBy('menu', 'ASC')->get()->getResultArray();

        return view('ext_pesanan', ['judul' => 'Daftar Menu', 'data' => $q, 'no_meja' => $data['no_meja'], 'nama_pemesan' => $data['nama_pemesan']]);
    }
    public function invoice()
    {
        $kategori = clear($this->request->getVar('kategori'));
        $jenis = clear($this->request->getVar('jenis'));
        $no_nota = clear($this->request->getVar('no_nota'));

        $db = db('notif');
        $q = $db->where('no_nota', $no_nota)->whereNotIn('dibaca', [""])->get()->getRowArray();

        sukses_js('Ok', $q['dibaca']);
    }

    public function get_nama_pemesan()
    {
        $val = clear($this->request->getVar('val'));

        $db = db('users');

        $q = $db->where('role', 'Member')->like('nama', $val, 'both')->limit(5)->get()->getResultArray();
        sukses_js('Ok', $q);
    }

    public function qr()
    {
        helper('qr_code');
        return view('qr', ['judul' => 'Qr']);
    }

    public function add_uid()
    {
        $db = db('rfid');
        $jwt = $this->request->getVar('jwt');

        $decode = decode_jwt_fulus($jwt);
        $data = ['uid' => $decode['uid']];

        clear_rfid();

        if ($db->insert($data)) {
            sukses_js('Sukses!.');
        } else {
            gagal_js('Gagal!.');
        }
    }
}
