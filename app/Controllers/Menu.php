<?php

namespace App\Controllers;

class Menu extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index($role = null): string
    {
        $db = db(menu()['tabel']);

        $db;
        if ($role !== null) {
            $db->where('role', $role);
        }
        $q = $db->orderBy('urutan', 'ASC')->get()->getResultArray();

        $dbo = db('options');

        $dbm = db('menus');
        $menus = $db->where('role', 'Root')->get()->getResultArray();
        $roles = $dbo->where('kategori', 'Role')->orderBy('id', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q, 'role' => $roles, 'menus' => $menus]);
    }

    public function add()
    {
        $role = upper_first(clear($this->request->getVar('role')));
        $menu = upper_first(clear($this->request->getVar('menu')));
        $tabel = strtolower(clear($this->request->getVar('tabel')));
        $controller = strtolower(clear($this->request->getVar('controller')));
        $icon = strtolower(clear($this->request->getVar('icon')));

        $db = db(menu()['tabel']);
        $is_exist = $db->where('role', $role)->where('menu', $menu)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Data already exist!.');
        }

        $urut = $db->where('role', $role)->orderBy('urutan', 'DESC')->get()->getRowArray();

        $data = [
            'role' => $role,
            'menu' => $menu,
            'tabel' => $tabel,
            'controller' => $controller,
            'icon' => $icon,
            'urutan' => ($urut ? $urut['urutan'] + 1 : 1)
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
        $role = upper_first(clear($this->request->getVar('role')));
        $menu = upper_first(clear($this->request->getVar('menu')));
        $tabel = strtolower(clear($this->request->getVar('tabel')));
        $controller = strtolower(clear($this->request->getVar('controller')));
        $icon = strtolower(clear($this->request->getVar('icon')));
        $urutan = clear($this->request->getVar('urutan'));

        $db = db(menu()['tabel']);
        $is_exist = $db->whereNotIn('id', [$id])->where('role', $role)->where('menu', $menu)->get()->getRowArray();
        if ($is_exist) {
            gagal(base_url(menu()['controller']), 'Data already exist!.');
        }


        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }


        $q['role'] = $role;
        $q['menu'] = $menu;
        $q['tabel'] = $tabel;
        $q['controller'] = $controller;
        $q['icon'] = $icon;
        $q['urutan'] = $urutan;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }
    public function copy_menu()
    {
        $menu_id = clear($this->request->getVar('menu_id'));
        $tujuan = clear($this->request->getVar('tujuan'));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $menu_id)->get()->getRowArray();
        if (!$q) {
            gagal_js('Menu id not found!.');
        }

        $is_exist = $db->where('id', $menu_id)->where('role', $tujuan)->get()->getRowArray();
        if ($is_exist) {
            gagal_js('Menu already exist!.');
        }
        unset($q['id']);
        $q['role'] = $tujuan;

        if ($db->insert($q)) {
            sukses_js('Copy data success.');
        } else {
            gagal_js('Copy data failed!.');
        }
    }
}
