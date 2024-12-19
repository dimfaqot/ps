<?php

namespace App\Controllers;

class Home extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }
    public function index(): string
    {
        $meja = "Meja " . 1;
        $durasi = 1;
        $durasi *= 60;
        $dbb = db('billiard_2');
        $bil = $dbb->where('meja', $meja)->where('is_active', 1)->where('metode', 'Tap')->get()->getRowArray();
        dd($bil);
        $db = db('users');
        $q = $db->orderBy('nama', 'ASC')->get()->getResultArray();

        $users = [];

        foreach ($q as $i) {
            $data = [
                'id' => $i['id'],
                'role' => $i['role'],
                'ip' => getenv('IP')
            ];

            $i['jwt'] = encode_jwt($data);
            $users[] = $i;
        }

        return view('home', ['judul' => 'Home - PS', 'users' => $users]);
    }

    public function get_pendapatan()
    {
        $tabel = clear($this->request->getVar('tabel'));
        $tahun = clear($this->request->getVar('tahun'));

        $db = db(($tabel == 'billiard' ? 'billiard_2' : $tabel));

        $q = $db->get()->getResultArray();

        $data_tahun = [];
        $data_tahun_p = [];

        $tbl_pengeluaran = ($tabel == 'rental' ? 'inventaris' : 'pengeluaran_' . $tabel);
        $dbp = db($tbl_pengeluaran);
        $p = $dbp->get()->getResultArray();

        // mencari tahun pemasukan
        foreach ($q as $i) {
            if ($tabel == 'rental') {
                $i['biaya'] = $i['biaya'] - $i['diskon'];
            }
            if ($tahun == 'All') {
                $data_tahun[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_tahun[] = $i;
                }
            }
        }
        // mencari tahun pengeluaran
        foreach ($p as $i) {
            if ($tahun == 'All') {
                $data_tahun_p[] = $i;
            } else {
                if ($tahun == date('Y', $i['tgl'])) {
                    $data_tahun_p[] = $i;
                }
            }
        }




        // mencari bulan

        $res = [];

        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_tahun as $i) {
                $i['tanggal'] = date('d/m/Y', $i['tgl']);
                if ($b['angka'] == date('m', $i['tgl'])) {
                    if ($tabel == 'kantin') {
                        $i['biaya'] = $i['total_harga'];
                    }
                    $temp[] = $i;
                    $total +=  ($tabel == 'barber' ? $i['total_harga'] : $i['biaya']);
                }
            }
            $res[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        $res_p = [];

        foreach (bulan() as $b) {
            $temp = [];
            $total = 0;
            foreach ($data_tahun_p as $i) {
                $i['tanggal'] = date('d/m/Y', $i['tgl']);
                if ($b['angka'] == date('m', $i['tgl'])) {
                    $temp[] = $i;
                    $total +=  $i['harga'];
                }
            }
            $res_p[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
            // $res_p[] = ['bulan' => $b['satuan'], 'bln' => $b['bulan'], 'data' => $temp, 'total' => $total];
        }

        sukses_js('Connection success.', $res, $res_p);
    }

    public function koperasi()
    {
        $usaha = clear($this->request->getVar('usaha'));

        $db = db('koperasi');

        $q = $db->where('usaha', $usaha)->orderBy('tgl', 'ASC')->get()->getResultArray();

        sukses_js('Connection success.', $q);
    }
    public function add_tabungan()
    {
        $usaha = clear($this->request->getVar('usaha'));
        $tabungan = rp_to_int($this->request->getVar('tabungan'));

        $db = db('koperasi');

        $data = [
            'tgl' => time(),
            'usaha' => $usaha,
            'tabungan' => $tabungan
        ];

        if ($db->insert($data)) {
            sukses_js('Save data success.');
        } else {
            gagal_js('Save data failed!.');
        }
    }

    public function replace()
    {
        $db = db('billiard');
        $billiard = $db->orderBy('tgl', 'ASC')->get()->getResultArray();

        $err = [];
        foreach ($billiard as $i) {
            $meja = 15;
            if ($i['meja'] !== "") {
                $meja = explode(" ", $i['meja'])[1];
            }
            $db_m = db('jadwal_2');
            $m = $db_m->where('meja', $meja)->get()->getRowArray();
            $data = [
                'meja_id' => $m['id'],
                'meja' => ($i['meja'] == "" ? 'Meja 15' : $i['meja']),
                'tgl' => $i['tgl'],
                'diskon' => $i['diskon'],
                'petugas' => $i['petugas'],
                'durasi' => $i['durasi'] * 60,
                'harga' => $i['diskon'] + $i['biaya'],
                'biaya' => (int)$i['biaya'],
                'is_active' => 0,
                'start' => 1728835189,
                'end' => 1728838789
            ];

            $db_2 = db('billiard_2');
            if (!$db_2->insert($data)) {
                $err[] = $i['id'];
            }
        }

        if (count($err) > 0) {
            gagal_with_button(base_url('biiliard'), implode(", ", $err));
        } else {
            sukses(base_url('billiard'), 'Success.');
        }
    }
    public function pembayaran_kantin_barcode()
    {
        $db = db('notif');
        $no_nota = clear($this->request->getVar('no_nota'));
        $diskon = (int)clear($this->request->getVar('diskon'));
        $uang = (int)clear($this->request->getVar('uang'));
        $biaya = (int)clear($this->request->getVar('biaya'));
        $total = (int)clear($this->request->getVar('total'));
        $q = $db->orderBy('no_nota', $no_nota)->where('dibaca', 'PROCESS')->get()->getResultArray();

        if (!$q) {
            gagal_js('No. nota not found!.');
        }

        if ($uang < $biaya) {
            gagal_js('Uang pembayaran kurang!.');
        }

        if ($diskon > $biaya) {
            gagal_js('Diskon kebesaran!.');
        }
        $db_barang = db('barang');
        $err = [];
        $diskon_item = floor($diskon / count($q));
        $total_diskon = $diskon - ($diskon_item * count($q));

        foreach ($q as $k => $i) {
            $harga = $i['total'] - $diskon_item;
            $data = [
                'barang_id' => 0,
                'barang' => $i['menu'],
                'harga_satuan' => $i['harga'],
                'diskon' => $diskon_item,
                'qty' => $i['qty'],
                'total_harga' => $harga,
                'petugas' => user()['nama'],
                'tgl' => time(),
                'no_nota' => $no_nota
            ];
            $b = $db_barang->where('barang', $i['menu'])->get()->getRowArray();

            if ($b) {
                $data['barang_id'] = $b['id'];
            }

            if (($k + 1) == count($q)) {
                if ($total_diskon > 0) {
                    $data['diskon'] = $data['diskon'] + $total_diskon;
                    $data['total_harga'] = ($data['total_harga'] + $diskon_item) - $data['diskon'];
                }
            }
            $dbk = db('kantin');
            if ($dbk->insert($data)) {
                $i['dibaca'] = 'DONE';
                $db->where('id', $i['id']);
                $db->update($i);
            } else {
                $err[] = $i['menu'];
            }
        }
        if (count($err) > 0) {
            gagal_js('Data ' . implode(", ", $err) . 'gagal diinput!.');
        } else {
            sukses_js('Proses sukses.', $uang - $biaya, $no_nota);
        }
    }


    public function pindah_ke_hutang()
    {
        $db = db('notif');
        $no_nota = clear($this->request->getVar('no_nota'));

        $q = $db->orderBy('no_nota', $no_nota)->where('dibaca', 'PROCESS')->get()->getResultArray();

        if (!$q) {
            gagal_js('No. nota not found!.');
        }


        $db_barang = db('barang');
        $dbh = db('hutang');
        $err = [];

        foreach ($q as $k => $i) {
            $b = $db_barang->where('barang', $i['menu'])->get()->getRowArray();
            $data = [
                'kategori' => 'Kantin',
                'no_nota' => $i['no_nota'],
                'user_id' => $i['id_pemesan'],
                'nama' => $i['pemesan'],
                'tgl' => $i['tgl'],
                'teller' => user()['nama'],
                'status' => 0,
                'barang_id' => ($b ? $b['id'] : 0),
                'barang' => $i['menu'],
                'harga_satuan' => $i['harga'],
                'qty' => $i['qty'],
                'total_harga' => $i['total'],
                'tgl_lunas' => 0,
            ];

            if ($dbh->insert($data)) {
                $i['dibaca'] = 'MOVE';
                $db->where('id', $i['id']);
                $db->update($i);
            } else {
                $err[] = $i['menu'];
            }
        }
        if (count($err) > 0) {
            gagal_js('Data ' . implode(", ", $err) . 'gagal diinput!.');
        } else {
            sukses_js('Data sukses dipindah.');
        }
    }
    public function hapus_pesanan()
    {
        $db = db('notif');
        $no_nota = clear($this->request->getVar('no_nota'));

        $q = $db->orderBy('no_nota', $no_nota)->where('dibaca', 'WAITING')->get()->getResultArray();

        if (!$q) {
            gagal_js('No. nota not found!.');
        }

        $err = [];

        foreach ($q as $i) {

            $db->where('id', $i['id']);
            if (!$db->delete()) {
                $err[] = $i['menu'];
            }
        }
        if (count($err) > 0) {
            gagal_js('Data ' . implode(", ", $err) . 'gagal diinput!.');
        } else {
            sukses_js('Data sukses dipindah.');
        }
    }
}
