<?php

namespace App\Controllers;

class General extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role('login');
    }
    public function delete()
    {
        $data = json_decode(json_encode($this->request->getVar('data')), true);
        $db = db($data['tabel']);
        $q = $db->where($data['col'], $data['id'])->get()->getRowArray();

        $res = null;
        if (array_key_exists('function', $data)) {
            if (!array_key_exists('id2', $data)) {
                $data['id2'] = '';
            }
            $fun = $data['function'];
            $res = $this->$fun($data['id'], $data['id2']);
        }

        if (!$q) {
            gagal_js('Id not found!.');
        }

        $db->where($data['col'], $data['id']);
        if ($db->delete()) {
            if (array_key_exists('function', $data)) {
                if (!array_key_exists('id2', $data)) {
                    $data['id2'] = '';
                }
                $fun = $data['function'];
                $res = $this->$fun($data['id'], $data['id2']);
            }
            sukses_js('Delete data success.', $res);
        } else {
            gagal_js('Delete data failed!.');
        }
    }

    public function add_unit_inv($id, $id2)
    {
        $db = db('detail_unit');
        $res = $db->select('detail_unit.id as id,barang,harga,kondisi,lokasi,pembeli,tgl,ket,unit_id,inv_id,catatan')->join('inventaris', 'inv_id=inventaris.id')->where('unit_id', $id2)->orderBy('id', 'ASC')->get()->getResultArray();
        return $res;
    }
    public function check_unit_item($id, $id2)
    {
        $db = db('detail_unit');
        $res = $db->where('unit_id', $id)->get()->getResultArray();
        if ($res) {
            gagal_js('Unit contains items!.');
        }
    }
    public function delete_meja($id, $id2)
    {
        $db = db('jadwal_2');

        $db->where('id', $id);
        if ($db->delete()) {
            sukses_js('Delete data success.');
        } else {
            gagal_js('Delete data failed!.');
        }
        // $db = db('jadwal');
        // $q = $db->where('id', $id)->get()->getRowArray();

        // $db->whereIn('meja', ['meja' => $q['meja']]);

        // if ($db->delete()) {
        //     sukses_js('Delete data success.');
        // } else {
        //     gagal_js('Delete data failed!.');
        // }
    }
}
