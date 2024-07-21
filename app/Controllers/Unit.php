<?php

namespace App\Controllers;

class Unit extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {

        $db = db(menu()['tabel']);

        $q = $db->orderBy('id', 'ASC')->get()->getResultArray();
        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
    }

    public function add()
    {
        $unit = upper_first(clear($this->request->getVar('unit')));
        $desc = upper_first(clear($this->request->getVar('desc')));
        $status = upper_first(clear($this->request->getVar('status')));
        $kode_harga = upper_first(clear($this->request->getVar('kode_harga')));

        $data = [
            'unit' => $unit,
            'kode_harga' => $kode_harga,
            'desc' => $desc,
            'status' => $status
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
        $unit = upper_first(clear($this->request->getVar('unit')));
        $desc = upper_first(clear($this->request->getVar('desc')));
        $status = upper_first(clear($this->request->getVar('status')));
        $kode_harga = upper_first(clear($this->request->getVar('kode_harga')));

        $db = db(menu()['tabel']);
        $q = $db->where('id', $id)->get()->getRowArray();
        if (!$q) {
            gagal(base_url(menu()['controller']), 'Id not found!.');
        }

        if ($status == 'Maintenance') {
            $dbr = db('rental');
            $qr = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
            if ($qr) {
                gagal(base_url(menu()['controller']), 'Unit is in game!.');
            }
        }

        $q['unit'] = $unit;
        $q['kode_harga'] = $kode_harga;
        $q['desc'] = $desc;
        $q['status'] = $status;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses(base_url(menu()['controller']), 'Update data success.');
        } else {
            gagal(base_url(menu()['controller']), 'Update data failed!.');
        }
    }

    public function detail_unit()
    {
        $id = clear($this->request->getVar('id'));

        $db = db('detail_unit');
        $q = $db->select('detail_unit.id as id,barang,harga,kondisi,lokasi,pembeli,tgl,ket,unit_id,inv_id,catatan')->join('inventaris', 'inv_id=inventaris.id')->where('unit_id', $id)->orderBy('id', 'ASC')->get()->getResultArray();

        sukses_js('Query success.', $q);
    }


    public function select_inv()
    {
        $value = clear($this->request->getVar('value'));
        $col = clear($this->request->getVar('col'));

        $db = db('inventaris');

        $q = $db->like($col, $value, 'both')->orderBy('barang', 'ASC')->limit(10)->get()->getResultArray();

        sukses_js('Query sukses.', $q);
    }
    public function add_unit_inv()
    {
        $unit_id = clear($this->request->getVar('unit_id'));
        $inv_id = clear($this->request->getVar('inv_id'));

        $db = db('detail_unit');

        $q = $db->where('unit_id', $unit_id)->where('inv_id', $inv_id)->get()->getRowArray();
        if ($q) {
            gagal_js('Data already exist!.');
        }

        $data = [
            'unit_id' => $unit_id,
            'inv_id' => $inv_id,
            'catatan' => ''
        ];

        if ($db->insert($data)) {
            $res = $db->select('detail_unit.id as id,barang,harga,kondisi,lokasi,pembeli,tgl,ket,unit_id,inv_id,catatan')->join('inventaris', 'inv_id=inventaris.id')->where('unit_id', $unit_id)->orderBy('barang', 'ASC')->get()->getResultArray();
            sukses_js('Query sukses.', $res);
        } else {
            gagal_js('Add data failed!.');
        }
    }

    public function detail_inv()
    {
        $id = clear($this->request->getVar('id'));
        $unit_id = clear($this->request->getVar('unit_id'));

        $db = db('inventaris');

        $q = $db->where('id', $id)->get()->getRowArray();

        $db2 = db('detail_unit');
        $data = $db2->where('unit_id', $unit_id)->where('inv_id', $id)->get()->getRowArray();


        if ($q) {
            sukses_js('Query sukses.', $q, $data);
        } else {
            gagal_js("Data not found!.");
        }
    }
    public function update_catatan()
    {
        $id = clear($this->request->getVar('id'));
        $catatan = upper_first(clear($this->request->getVar('catatan')));

        $db = db('detail_unit');

        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        }

        $q['catatan'] = $catatan;

        $db->where('id', $id);
        if ($db->update($q)) {
            sukses_js('Update data success.');
        } else {
            gagal_js("Update data failed!.");
        }
    }
}
