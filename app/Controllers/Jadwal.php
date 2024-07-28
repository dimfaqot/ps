<?php

namespace App\Controllers;

class Jadwal extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db(menu()['tabel']);
        $q = $db->orderBy('meja', 'ASC')->orderBy('jam', 'ASC')->get()->getResultArray();
        $meja = $db->groupBy('meja')->orderBy('meja', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - Billiard', 'data' => $q, 'meja' => $meja]);
    }

    public function add()
    {
        $meja = upper_first(clear($this->request->getVar('meja')));

        $db = db(menu()['tabel']);
        $is_exist = $db->where('meja', $meja)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Data already exist!.');
        }

        $err = [];
        for ($i = 1; $i < 25; $i++) {
            foreach (hari() as $h) {
                $data = [
                    'meja' => $meja,
                    'jam' => $i,
                    'hari' => $h['indo'],
                    'pemesan' => '',
                    'ket' => '',
                    'petugas' => user()['nama']
                ];
                if (!$db->insert($data)) {
                    $err[] = 'jam ' . $i . ' hari ' . $h['indo'];
                }
            }
        }

        if (count($err) > 0) {
            gagal_with_button(base_url(menu()['controller']), 'Gagal: ' . implode(",", $err));
        } else {
            sukses(base_url(menu()['controller']), 'Save data success.');
        }
    }
    public function update_jadwal()
    {
        $id = clear($this->request->getVar('id'));
        $val = upper_first(clear($this->request->getVar('val')));
        $col = clear($this->request->getVar('col'));
        $meja = clear($this->request->getVar('meja'));

        $db = db(menu()['tabel']);

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Id not found!.');
        }


        $q[$col] = $val;
        $q['petugas'] = user()['nama'];

        $db->where('id', $id);
        if ($db->update($q)) {
            $data = $db->where('meja', $meja)->orderBy('meja', 'ASC')->orderBy('jam', 'ASC')->get()->getResultArray();
            sukses_js('Update data success.', $data);
        } else {
            gagal_js('Update data failed!.');
        }
    }
}
