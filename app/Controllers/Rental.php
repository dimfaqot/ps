<?php

namespace App\Controllers;

class Rental extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index(): string
    {
        $db = db('unit');

        $q = $db->orderBy('id', 'ASC')->get()->getResultArray();

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS', 'data' => $q]);
    }

    public function start_play()
    {
        $id = clear($this->request->getVar('id'));
        $durasi = clear($this->request->getVar('durasi'));

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();

        if ($qu['status'] == 'Maintenance') {
            gagal_js('Unit dalam perbaiakan!.');
        }


        $dbr = db('rental');
        $q = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();

        if ($q) {
            gagal_js('Rental masih dalam permainan!.', $id, $durasi, 'ubah/tambah');
        }

        if ($qu['status'] == 'In Game') {
            gagal_js('Unit masih dalam permainan!.');
        }

        if ($qu['status'] == 'Booked') {
            gagal_js('Unit telah dibooking. Yakin lanjutkan!.', $id, $durasi, menu()['controller'] . '/confirm_start_play');
        }

        $time = time();
        $datar = [
            'tgl' => $time,
            'meja' => $qu['unit'],
            'unit_id' => $id,
            'dari' => $time,
            'ke' => ($durasi == -1 ? $durasi : $time + (60 * $durasi)),
            'durasi' => $durasi,
            'metode' => 'Cash',
            'harga' => harga_ps($qu['unit']),
            'is_active' => 1,
            'petugas' => user()['nama']
        ];

        if ($dbr->insert($datar)) {
            $qu['status'] = 'In Game';
            $dbu->where('id', $id);
            if ($dbu->update($qu)) {
                sukses_js('Input data success.');
            } else {
                gagal_js('Update data to unit failed!.');
            }
        } else {
            gagal_js('Input data to rental failed!.');
        }
    }
    public function confirm_start_play()
    {
        $id = clear($this->request->getVar('id'));
        $durasi = clear($this->request->getVar('durasi'));

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();

        if ($qu['status'] == 'Maintenance') {
            gagal_js('Unit dalam perbaiakan!.');
        }

        $dbr = db('rental');
        $q = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();

        if ($q) {
            gagal_js('Rental masih dalam permainan!.');
        }

        $time = time();
        $datar = [
            'tgl' => $time,
            'unit_id' => $id,
            'meja' => $qu['unit'],
            'dari' => $time,
            'ke' => ($durasi == -1 ? $durasi : $time + (60 * $durasi)),
            'durasi' => $durasi,
            'metode' => 'Cash',
            'harga' => harga_ps($qu['unit']),
            'is_active' => 1,
            'petugas' => user()['nama']
        ];

        if ($dbr->insert($datar)) {
            $qu['status'] = 'In Game';
            $dbu->where('id', $id);
            if ($dbu->update($qu)) {
                sukses_js('Input data success.');
            } else {
                gagal_js('Update data to unit failed!.');
            }
        } else {
            gagal_js('Input data to rental failed!.');
        }
    }
    public function end_play()
    {
        $id = clear($this->request->getVar('id'));
        $durasi = clear($this->request->getVar('durasi'));

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();
        if (!$qu) {
            gagal_js('Id unit in unit not found!.');
        }

        $dbr = db('rental');
        $q = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$q) {
            gagal_js('Unit id in rental not found!.');
        }

        $time = time();
        $sisa = $q['ke'] - $time;

        $dbs = db('settings');
        $qs = $dbs->where('nama_setting', $qu['kode_harga'])->get()->getRowArray();
        if ($qs) {
            $q['biaya'] = $qs['value_int'];
        }
        $q['durasi_realtime'] = floor(($time - $q['dari']) / 60);

        if ($sisa > 0) {
            gagal_js('Waktu belum habis [' . floor($sisa / 60) . ' m]. Yakin lanjutkan!.', $id, $durasi, menu()['controller'] . '/confirm_end_play', $q);
        } else {

            gagal_js('Yakin masuk pembayaran?.', $id, $durasi, menu()['controller'] . '/confirm_end_play', $q);
        }
    }
    public function confirm_end_play()
    {
        $id = clear($this->request->getVar('id'));
        $biaya = rp_to_int(clear($this->request->getVar('biaya')));
        $diskon = rp_to_int(clear($this->request->getVar('diskon')));
        $uang = rp_to_int(clear($this->request->getVar('uang')));

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();
        if (!$qu) {
            gagal_js('Id unit in unit not found!.');
        }
        $dbr = db('rental');
        $q = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$q) {
            gagal_js('Unit id in rental not found!.');
        }
        $time = time();
        if ($q['durasi'] == -1) {
            $q['durasi'] = floor(($time - $q['dari']) / 60);
        }
        $q['ke'] = $time;
        $q['is_active'] = 0;
        $q['biaya'] = $biaya;
        $q['diskon'] = $diskon;
        $q['petugas'] = user()['nama'];

        $dbr->where('id', $q['id']);
        if ($dbr->update($q)) {
            $qu['status'] = 'Available';
            $dbu->where('id', $id);
            if ($dbu->update($qu)) {
                sukses_js('Payment Success.', $uang - ($biaya - $diskon));
            } else {
                gagal_js('Payment failed!.');
            }
        } else {
            gagal_js('Input data to rental failed!.');
        }
    }
    public function confirm_tambah()
    {
        $id = clear($this->request->getVar('id'));
        $durasi = clear($this->request->getVar('durasi'));

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();
        if (!$qu) {
            gagal_js('Id unit in unit not found!.');
        }
        $dbr = db('rental');
        $qr = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$qr) {
            gagal_js('Unit id in rental not found!.');
        }

        $q = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();

        $sisa = floor(($q['ke'] - time()) / 60);

        if ($sisa > 10) {
            gagal_js('Tunggu sampai 10 menit terakhir!.');
        }

        $q['ke'] = $q['ke'] + (60 * $durasi);
        $q['durasi'] = $q['durasi'] + $durasi;
        $q['petugas'] = user()['nama'];

        $dbr->where('id', $q['id']);
        if ($dbr->update($q)) {
            sukses_js('Update duration data success.');
        } else {
            gagal_js('Update duration failed!.');
        }
    }
    public function confirm_ubah()
    {
        $id = clear($this->request->getVar('id'));
        $durasi = clear($this->request->getVar('durasi'));

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();
        if (!$qu) {
            gagal_js('Id unit in unit not found!.');
        }
        $dbr = db('rental');
        $qr = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$qr) {
            gagal_js('Unit id in rental not found!.');
        }

        $q = $dbr->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        $berjalan = floor((time() - $q['dari']) / 60);

        if ($berjalan > 10) {
            gagal_js('Hanya bisa sebelum 10 menit!.');
        }
        $time = time();
        $q['dari'] = $time;
        $q['ke'] = $time + (60 * $durasi);
        $q['durasi'] = $durasi;
        $q['petugas'] = user()['nama'];

        $dbr->where('id', $q['id']);
        if ($dbr->update($q)) {
            sukses_js('Update duration data success.');
        } else {
            gagal_js('Update duration failed!.');
        }
    }
    public function reset_play()
    {
        $id = clear($this->request->getVar('id'));

        $db = db('rental');
        $q = $db->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$q) {
            gagal_js('Unit id in rental not found!.');
        }

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();
        if (!$qu) {
            gagal_js('Id unit in unit not found!.');
        }

        $berjalan = floor((time() - $q['dari']) / 60);

        if ($berjalan > 10) {
            gagal_js('Reset hanya bisa di 10 menit awal!.');
        }

        gagal_js('Yakin reset data rental?', $id, null, menu()['controller'] . '/confirm_reset');
    }
    public function confirm_reset()
    {
        $id = clear($this->request->getVar('id'));

        $db = db('rental');
        $q = $db->where('unit_id', $id)->where('is_active', 1)->get()->getRowArray();
        if (!$q) {
            gagal_js('Unit id in rental not found!.');
        }

        $dbu = db('unit');
        $qu = $dbu->where('id', $id)->get()->getRowArray();
        if (!$qu) {
            gagal_js('Id unit in unit not found!.');
        }
        $db->where('id', $q['id']);
        if ($db->delete()) {
            $qu['status'] = 'Available';
            $dbu->where('id', $id);
            if ($dbu->update($qu)) {
                sukses_js('Reset data success.');
            } else {
                gagal_js('Update data unit failed!.');
            }
        } else {
            gagal_js('Reset data rental failed!.');
        }
    }
}
