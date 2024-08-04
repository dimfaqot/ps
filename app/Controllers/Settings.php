<?php

namespace App\Controllers;

class Settings extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db(menu()['tabel']);

        $q = $db->orderBy('nama_setting', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
    }

    public function add()
    {
        $nama_setting = upper_first(clear($this->request->getVar('nama_setting')));
        $value_int = rp_to_int(clear($this->request->getVar('value_int')));
        $value_str = clear($this->request->getVar('value_str'));

        $data = [
            'nama_setting' => $nama_setting,
            'value_int' => $value_int,
            'value_str' => $value_str
        ];

        $db = db(menu()['tabel']);
        if ($db->insert($data)) {
            sukses(base_url(menu()['controller']), 'Save data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Save data failed!.');
        }
    }
    public function update()
    {
        $id = clear($this->request->getVar('id'));
        $nama_setting = upper_first(clear($this->request->getVar('nama_setting')));
        $value_int = rp_to_int(clear($this->request->getVar('value_int')));
        $value_str = clear($this->request->getVar('value_str'));

        $db = db(menu()['tabel']);

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['nama_setting'] = $nama_setting;
        $q['value_int'] = $value_int;
        $q['value_str'] = $value_str;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }

    public function make_user_jwt()
    {
        $role = clear($this->request->getVar('role'));
        $data = [
            'role' => $role,
            'nama' => 'Temp User'
        ];

        sukses_js('Koneksi sukses.', encode_jwt($data));
    }
}
