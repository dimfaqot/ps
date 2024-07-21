<?php

namespace App\Controllers;

class Options extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index($kategori = null): string
    {
        $db = db(menu()['tabel']);

        $db;
        if ($kategori !== null) {
            $db->where('kategori', $kategori);
        }
        $q = $db->orderBy('kategori', 'ASC')->get()->getResultArray();

        $kategoris = $db->groupBy('kategori')->orderBy('kategori', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q, 'kategori' => $kategoris]);
    }

    public function add()
    {
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $value = upper_first(clear($this->request->getVar('value')));

        $db = db(menu()['tabel']);
        $is_exist = $db->where('kategori', $kategori)->where('value', $value)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Data already exist!.');
        }

        $data = [
            'kategori' => $kategori,
            'value' => $value
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
        $kategori = upper_first(clear($this->request->getVar('kategori')));
        $value = upper_first(clear($this->request->getVar('value')));

        $db = db(menu()['tabel']);
        $is_exist = $db->whereNotIn('id', [$id])->where('kategori', $kategori)->where('value', $value)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Data already exist!.');
        }

        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['kategori'] = $kategori;
        $q['value'] = $value;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }
}
