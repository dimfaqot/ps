<?php

namespace App\Controllers;

class Hutang extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }
    public function index(): string
    {
        return view('hutang/hutang', ['judul' => menu()['menu'] . ' - PS']);
    }

    public function pembeli()
    {
        $db = db('users');
        $user = $db->where('role', 'Member')->orderBy('nama', 'ASC')->get()->getResultArray();

        $data = [];

        foreach ($user as $i) {
            $jwt = [
                'id' => $i['id'],
                'role' => $i['role'],
            ];
            $i['jwt'] = base_url('login/a/member/') . encode_jwt($jwt);
            $data[] = $i;
        }

        sukses_js('Koneksi sukses.', $data);
    }
    public function add_pembeli()
    {
        $nama = upper_first(clear($this->request->getVar('nama')));
        $hp = clear($this->request->getVar('hp'));

        $db = db('users');
        $nam = $db->where('role', 'Member')->where('nama', $nama)->get()->getRowArray();
        if ($nam) {
            gagal_js('Nama sudah ada!.');
        }
        $db_hp = $db->where('role', 'Member')->where('hp', $hp)->get()->getRowArray();
        if ($db_hp) {
            gagal_js('Hp sudah ada!.');
        }
        $data = [
            'nama' => $nama,
            'hp' => $hp,
            'img' => 'file_not_found.jpg',
            'role' => 'Member',
            'bidang' => '',
            'username' => time(),
            'password' => password_hash(getenv('default_password'), PASSWORD_DEFAULT)
        ];

        if ($db->insert($data)) {
            sukses_js('Berhasil.');
        } else {
            gagal_js('Data gagal disimpan!.');
        }
    }
    public function update_pembeli()
    {

        $id = clear($this->request->getVar('id'));
        $col = clear($this->request->getVar('col'));
        $val = clear($this->request->getVar('val'));

        $db = db('users');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        }

        $exist = $db->where('role', 'Member')->where($col, $val)->whereNotIn('id', [$id])->get()->getRowArray();

        if ($exist) {
            gagal_js(upper_first($col) . ' sudah ada!.');
        }

        $q[$col] = $val;
        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js('Berhasil.');
        } else {
            gagal_js('Data gagal disimpan!.');
        }
    }
    public function data_hutang()
    {

        $id = clear($this->request->getVar('id'));

        $db = db('hutang');
        $db->where('user_id', $id);
        if (session('role') !== 'Root') {
            $db->where('kategori', explode(" ", session('role'))[1]);
        }
        $q = $db->groupBy('no_nota')->orderBy('tgl', 'ASC')->get()->getResultArray();

        $dbu = db('users');
        $usr = $dbu->where('id', $id)->get()->getRowArray();
        $jwt = [
            'id' => $usr['id'],
            'role' => $usr['role'],
        ];
        $jwt = base_url('login/a/member/') . encode_jwt($jwt);
        $data = [];
        $total = 0;
        foreach ($q as $i) {
            $val = $db->where('no_nota', $i['no_nota'])->orderBy('barang', 'ASC')->get()->getResultArray();


            foreach ($val as $v) {
                if ($v['status'] == 0) {
                    $total += $v['total_harga'];
                }
            }
            $temp = ['no_nota' => $i['no_nota'], 'tgl' => $i['tgl'], 'status' => $i['status'], 'nama' => $i['nama'], 'data' => $val];

            $data[] = $temp;
        }
        sukses_js('Koneksi sukses.', $data, $total, $id, ($usr ? $usr['hp'] : ''), $jwt);
    }

    public function add()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $user_id = clear($this->request->getVar('user_id'));
        $kategori = clear($this->request->getVar('kategori'));

        $db = db('barang');
        $dbh = db('hutang');

        $no_nota = no_nota(strtolower($kategori));
        $dbu = db('users');
        $user = $dbu->where('id', $user_id)->get()->getRowArray();
        $err = [];
        foreach ($data as $i) {
            $q = $db->where('id', $i['barang_id'])->get()->getRowArray();
            if (!$q) {
                $err[] = 'Id ' . $i['barang_id'] . ' err';
                continue;
            }
            $value = [
                'barang_id' => $i['barang_id'],
                'user_id' => $user_id,
                'kategori' => $kategori,
                'nama' => $user['nama'],
                'no_nota' => $no_nota,
                'barang' => $q['barang'],
                'harga_satuan' => $q['harga_satuan'],
                'tgl_lunas' => 0,
                'status' => 0,
                'tgl' => time(),
                'qty' => $i['qty'],
                'total_harga' => ($q['harga_satuan'] * $i['qty']),
                'teller' => user()['nama']
            ];
            if ($dbh->insert($value)) {
                $q['stok'] = $q['stok'] - $i['qty'];
                $db->where('id', $q['id']);
                if (!$db->update($q)) {
                    $err[] = 'Update stock err';
                }
            } else {
                $err[] = 'Insert to kantin err';
            }
        }


        if (count($err) <= 0) {
            sukses_js('Save data success!.');
        } else {
            gagal_js(implode(", ", $err));
        }
    }

    public function lunas()
    {

        $no_nota = clear($this->request->getVar('no_nota'));

        $db = db('hutang');
        $q = $db->where('no_nota', $no_nota)->get()->getResultArray();

        $data = [];
        $total = 0;
        foreach ($q as $i) {
            $val = $db->where('no_nota', $i['no_nota'])->orderBy('barang', 'ASC')->get()->getResultArray();


            foreach ($val as $v) {
                $total += $v['total_harga'];
            }
            $temp = ['no_nota' => $i['no_nota'], 'tgl' => $i['tgl'], 'nama' => $i['nama'], 'data' => $val];

            $data[] = $temp;
        }
        sukses_js('Koneksi sukses.', $data, $total);
    }

    public function bayar_lunas()
    {
        $user_id = clear($this->request->getVar('user_id'));
        $kategori = clear($this->request->getVar('kategori'));

        $uang = (int)clear($this->request->getVar('uang'));
        $diskon = (int)clear($this->request->getVar('diskon'));
        $total_setelah_diskon = (int)clear($this->request->getVar('total_setelah_diskon'));

        $db_tujuan = db(($kategori == 'Kantin' ? 'kantin' : ($kategori == 'Billiard' ? 'billiard_2' : strtolower($kategori))));
        $dbh = db('hutang');
        $data_hutang = $dbh->where('user_id', $user_id)->where('kategori', $kategori)->where('status', 0)->get()->getResultArray();

        $err = [];
        $diskon_item = floor($diskon / count($data_hutang));
        $total_diskon = $diskon - ($diskon_item * count($data_hutang));

        $no_nota = no_nota(strtolower($kategori));
        $bill = 0;
        foreach ($data_hutang as $k => $i) {
            if ($kategori == 'Kantin') {
                $value = [
                    'barang_id' => $i['barang_id'],
                    'no_nota' => $no_nota,
                    'barang' => $i['barang'],
                    'harga_satuan' => $i['harga_satuan'],
                    'tgl' => time(),
                    'qty' => $i['qty'],
                    'diskon' => $diskon_item,
                    'total_harga' => $i['total_harga'] - $diskon_item,
                    'petugas' => user()['nama']
                ];
                if (($k + 1) == count($data_hutang)) {
                    if ($total_diskon > 0) {
                        $value['diskon'] = $value['diskon'] + $total_diskon;
                        $value['total_harga'] = ($value['total_harga'] + $diskon_item) - $value['diskon'];
                    }
                }
            }

            if ($kategori == 'Billiard') {
                $dbm = db('jadwal_2');
                $mj = explode(" ", $i['barang']);
                $meja = $dbm->where('meja', end($mj))->get()->getRowArray();

                $exp_nota = explode("|", $i['no_nota']);
                $value = [
                    'meja_id' => $meja['id'],
                    // 'no_nota' => $no_nota,
                    'meja' => 'Meja ' . $meja['meja'],
                    'tgl' => time(),
                    'durasi' => $i['qty'],
                    'petugas' => user()['nama'],
                    'biaya' => $i['total_harga'],
                    'diskon' => end($exp_nota),
                    'start' => $i['barang_id'],
                    'end' => $i['barang_id'] + ($i['qty'] * 60),
                    'is_active' => 0,
                    'harga' => $i['harga_satuan']
                ];
                $i['no_nota'] = $exp_nota[0];
            }

            if ($db_tujuan->insert($value)) {
                $dbh->where('id', $i['id']);
                $i['status'] = 1;
                $i['dibayar_kpd'] = user()['nama'];
                $i['tgl_lunas'] = time();
                $dbh->update($i);
            } else {
                $err[] = $i['barang'];
            }
        }

        if (count($err) <= 0) {
            sukses_js('Save data success!. ' . $uang . ' | ' . $total_setelah_diskon, ((int)$uang - (int)$total_setelah_diskon));
        } else {
            gagal_js('Gagal diinput: ' . implode(", ", $err));
        }
    }
}
