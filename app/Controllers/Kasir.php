<?php

namespace App\Controllers;

use Mpdf\Tag\Tr;

class Kasir extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role();
    }

    public function index()
    {

        return view(menu()['controller'], ['judul' => menu()['menu'] . ' - PS']);
    }

    public function nota($nota)
    {
        $nota = str_replace("-", "/", $nota);

        // // dd($data);
        // $set = [
        //     'mode' => 'utf-8',
        //     'format' => [210, 330],
        //     'orientation' => 'P',
        //     'margin_left' => 5,
        //     'margin_right' => 5,
        //     'margin_top' => 5,
        //     'margin_bottom' => 5
        // ];

        $db = db('nota');
        $q = $db->where('no_nota', $nota)->get()->getResultArray();

        // $mpdf = new \Mpdf\Mpdf($set);

        // $judul = "NOTA " . $nota;
        // // Dapatkan konten HTML
        // // $logo = '<img width="90" src="logo.png" alt="KOP"/>';
        // $html = view('nota', ['judul' => $judul, 'data' => $data]); // view('pdf_template') mengacu pada file view yang akan dirender menjadi PDF
        // dd($html);
        // // Setel konten HTML ke mPDF
        // $mpdf->WriteHTML($html);

        // // Output PDF ke browser
        // $this->response->setHeader('Content-Type', 'application/pdf');
        // $mpdf->Output($judul . '.pdf', 'I');


        $total = 0;
        $diskon = 0;

        $html = '<!DOCTYPE html>
                <html lang="id">
                <head>
                  <meta charset="UTF-8">
                  <title>Nota Pembelian</title>
                  <style>
                    body {
                      font-family: Arial, sans-serif;
                      max-width: 400px;
                      margin: auto;
                      border: 1px solid #ccc;
                      padding: 20px;
                    }
                    hr {
                      border: none;
                      border-top: 1px dashed grey;
                    }
                    table {
                      width: 100%;
                      border-collapse: collapse;
                      margin-top: 10px;
                    }
                    th, td {
                      padding: 4px;
                    }
                    th {
                      text-align: right;
                    }
                    td {
                      vertical-align: top;
                    }
                      @media print {
            @page {
              margin: 0;
              width:300px;
              height:100%;
            }

            body {
              margin: 0; /* Menghilangkan margin default body */
            }

            /* Jika perlu, tambahkan styling lainnya untuk elemen nota */
            .nota {
              font-size: 10px; /* Contoh penyesuaian tampilan */
            }
          }

                }
                  </style>
                </head>
                <body>
                  <h2 style="text-align: center; margin-bottom: 10px;">SONGO PLAYGROUND</h2>
                  <p style="text-align: center; margin: 0;">Karangmalang Sragen Jawa Tengah</p>
                  <p style="text-align: center; margin: 0 0 10px;">0857-4661-6165</p>

                  <hr>

                  <p style="margin: 4px 0;"><strong>Nota:</strong> ' . esc($nota) . '</p>
                  <p style="margin: 4px 0;"><strong>Kasir:</strong> ' . esc($q[0]["petugas"]) . '</p>
                  <p style="margin: 4px 0;"><strong>Tgl:</strong> ' . esc(date("d-m-Y H:i", $q[0]["tgl"])) . '</p>

                  <hr>

                  <table>
                    <thead>
                      <tr>
                        <td style="width: 50%; text-align: center;"><strong>Barang</strong></td>
                        <td style="width: 15%; text-align: center;"><strong>Harga</strong></td>
                        <td style="width: 20%; text-align: center;"><strong>Qty</strong></td>
                        <td style="width: 15%; text-align: right;"><strong>Total</strong></td>
                      </tr>
                    </thead>
                    <tbody>';

        foreach ($q as $item) {
            $total += $item['jml'];
            $diskon += $item['diskon'];

            $html .= '
                      <tr>
                        <td>' . esc($item['barang']) . '</td>
                        <td style="text-align: right;">' . esc($item['harga']) . '</td>
                        <td style="text-align: center;">' . esc($item['qty']) . '</td>
                        <td style="text-align: right;">' . esc($item['jml']) . '</td>
                      </tr>';
        }

        $html .= '
                    </tbody>
                  </table>

                  <hr>

                  <table>
                    <tr><th colspan="3">Sub Total</th><td style="text-align: right;">' . esc(angka($total)) . '</td></tr>
                    <tr><th colspan="3">Diskon</th><td style="text-align: right;">' . esc(angka($diskon)) . '</td></tr>
                    <tr><th colspan="3">Total</th><td style="text-align: right;">' . esc(angka($total - $diskon)) . '</td></tr>
                    <tr><th colspan="3">Uang</th><td style="text-align: right;">' . esc(angka($q[0]["uang"])) . '</td></tr>
                    <tr><th colspan="3">Kembalian</th><td style="text-align: right;">' . esc(angka(($q[0]["uang"]) - ($total - $diskon))) . '</td></tr>
                  </table>

                  <hr>

                  <p style="text-align: center; font-style: italic; margin-top: 10px;">* Terima kasih atas kunjungan anda *</p>


                </body>
                </html>';

        echo $html;
    }

    public function search_user()
    {

        $val = clear($this->request->getVar('val'));

        $db = db('users');
        $q = $db->like('nama', $val, "both")->orderBy("nama", "ASC")->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }
    public function cari_barang()
    {

        $val = clear($this->request->getVar('val'));

        $db = db('barang');
        $q = $db->like('barang', $val, "both")->orderBy("barang", "ASC")->limit(10)->get()->getResultArray();

        sukses_js("Ok", $q);
    }
    public function add_user()
    {

        $nama = upper_first(clear($this->request->getVar('nama')));
        $hp = clear($this->request->getVar('hp'));

        $db = db('users');
        $q = $db->where('hp', $hp)->get()->getRowArray();

        if ($q) {
            gagal_js("No. hp sudah ada [" . $q['nama'] . "]");
        }

        $data = [
            'nama' => $nama,
            'hp' => $hp,
            'img' => 'img_not_found.jpg',
            'role' => 'Member',
            'bidang' => '',
            'username' => generateRandomString(6),
            'password' => password_hash(getenv('default_password'), PASSWORD_DEFAULT),
            'uid' => generateRandomString(5),
            'finger' => 0,
            'no_id' => 0,
            'fulus' => 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJmdWx1cyI6MH0.xMR3V0PQ703flTaEkOgrnUiKC76BHAeptjAtj323ohk'
        ];


        if ($db->insert($data)) {
            sukses_js("Sukses");
        }
    }

    public function get_data()
    {
        $db_meja = db('jadwal_2');
        $meja = $db_meja->orderBy('meja', 'ASC')->get()->getResultArray();
        $db_billiard = db('billiard_2');

        $billiard = [];
        foreach ($meja as $i) {
            $q = $db_billiard->where('meja', "Meja " . $i['meja'])->where('is_active', 1)->get()->getRowArray();

            $temp = [
                'id' => $i['id'],
                'meja_id' => 0,
                'durasi' => 0,
                'petugas' => user()['nama'],
                'tgl' => time(),
                'meja' => "Meja " . $i['meja'],
                'harga' => $i['harga'],
                'biaya' => 0,
                'diskon' => 0,
                'start' => $i['start'],
                'end' => 0,
                'is_active' => 0,
                'metode' => 'Cash',
                'ket' => 'Available',
                'jam' => '',
                'waktu' => '',
                'text' => 'text-success'
            ];

            if ($q) {
                $temp = $q;
                $temp['jam'] = $q['durasi'] / 60;
                $temp['meja_id'] = $i['id'];
                if ($q['durasi'] == 0) {
                    $temp['ket'] = "Open";
                    $temp['text'] = "text-secondary";
                    $temp['waktu'] = durasi_waktu($q['start']);
                } else {
                    $temp['ket'] = (time() > $q['end'] ? "Over" : "In Game");
                    $temp['text'] = ($temp['ket'] == "Over" ? "text-danger" : "text-secondary");
                    $temp['waktu'] = (time() > $q['end'] ? 0 : "-" . durasi(time(), $q['end']));
                }
            }

            $billiard[] = $temp;
        }

        $db_unit = db('unit');
        $unit = $db_unit->whereNotIn('status', ['Maintenance'])->orderBy('no_urut', 'ASC')->get()->getResultArray();
        $db_ps = db('rental');
        $db_kode_bayar = db('settings');
        $ps = [];
        foreach ($unit as $i) {
            $q = $db_ps->where('meja', $i['unit'])->where('is_active', 1)->get()->getRowArray();
            $kode_bayar = $db_kode_bayar->where('nama_setting', $i['kode_harga'])->get()->getRowArray();

            $temp = [
                'id' => $i['id'],
                'meja_id' => 0,
                'durasi' => 0,
                'petugas' => user()['nama'],
                'tgl' => time(),
                'meja' => $i['unit'],
                'harga' => $kode_bayar['value_int'],
                'biaya' => 0,
                'diskon' => 0,
                'start' => 0,
                'end' => 0,
                'is_active' => 0,
                'metode' => 'Cash',
                'ket' => 'Available',
                'jam' => '',
                'waktu' => '',
                'text' => 'text-success'
            ];

            if ($q) {
                $temp = $q;
                $temp['jam'] = $q['durasi'] / 60;
                $temp['meja_id'] = $i['id'];
                if ($q['durasi'] == -1) {
                    $temp['ket'] = "Open";
                    $temp['text'] = "text-secondary";
                    $temp['waktu'] = durasi_waktu($q['dari']);
                } else {
                    $temp['ket'] = (time() > $q['ke'] ? "Over" : "In Game");
                    $temp['text'] = ($temp['ket'] == "Over" ? "text-danger" : "text-secondary");
                    $temp['waktu'] = (time() > $q['ke'] ? -1 : "-" . durasi(time(), $q['ke']));
                }
            }

            $ps[] = $temp;
        }

        $db_barang = db('barang');
        $barang = $db_barang->orderBy('barang', 'ASC')->get()->getResultArray();

        $db_layanan = db('layanan');
        $layanan = $db_layanan->orderBy('layanan', 'ASC')->get()->getResultArray();

        $res = [
            'billiard' => $billiard,
            'ps' => $ps,
            'barang' => $barang,
            'layanan' => $layanan
        ];

        sukses_js("Ok", $res);
    }

    public function bayar_langsung()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $nota = no_invoice();

        $tgl = time();

        $ps        = json_decode(json_encode($this->request->getVar('ps')), true);
        $billiard  = json_decode(json_encode($this->request->getVar('billiard')), true);
        $customer  = json_decode(json_encode($this->request->getVar('customer')), true);
        $barber    = json_decode(json_encode($this->request->getVar('barber')), true);
        $kantin    = json_decode(json_encode($this->request->getVar('kantin')), true);
        $total     = (int)clear($this->request->getVar('total'));
        $diskon    = (int)clear($this->request->getVar('diskon'));
        $uang      = (int)clear($this->request->getVar('uang'));
        $order      = clear($this->request->getVar('order'));
        $no_nota      = clear($this->request->getVar('no_nota'));


        if ($uang < ($total - $diskon)) gagal_js("Uang kurang");
        if ($diskon > $total) gagal_js("Diskon terlalu besar");

        // Reusable nota builder
        function insertNota($kategori, $uang, $db, $nota, $barang, $harga, $qty, $diskon, $tgl, $user, $jml = null)
        {
            $sub_total  = ($kategori == "billiard" || $kategori == "ps" ? $harga * ($qty / 60) : $harga * $qty);

            if ($jml !== null) {
                $sub_total = $jml;
            }
            $total = $sub_total - $diskon;

            $bar = $barang;
            if ($kategori == "ps") {
                $bar = "PS " . $barang;
            }
            if ($kategori == "billiard") {
                $bar = "BL " . $barang;
            }


            $db->table('nota')->insert([
                "no_nota" => $nota,
                "barang"  => $bar,
                "harga"   => $harga,
                "qty"     => $qty,
                "jml"     => $sub_total,
                "diskon"  => $diskon,
                "total"   => $total,
                "tgl"     => $tgl,
                "user"    => $user,
                "uang"    => $uang,
                "petugas" => user()['nama']
            ]);
        }

        if (!empty($kantin)) {
            foreach ($kantin as $i) {
                $db->table('kantin')->insert([
                    "no_nota" => $nota,
                    "barang_id" => $i['id'],
                    "barang" => $i['barang'],
                    "harga_satuan" => $i['harga_satuan'],
                    "total_harga" => $i['harga_satuan'] * $i['qty'],
                    "tgl" => $tgl,
                    "qty" => $i['qty'],
                    "petugas" => user()['nama'],
                    "diskon" => $i['diskon'],
                    "metode" => "Cash"
                ]);
                insertNota("kantin", $uang, $db, $nota, $i['barang'], $i['harga_satuan'], $i['qty'], $i['diskon'], $tgl, $customer['nama']);
            }
        }

        if (!empty($barber)) {
            foreach ($barber as $i) {
                $db->table('barber')->insert([
                    "layanan_id" => $i['id'],
                    "layanan" => $i['layanan'],
                    "harga" => $i['harga'],
                    "qty" => 1,
                    "total_harga" => $i['harga'],
                    "diskon" => $i['diskon'],
                    "status" => 1,
                    "tgl" => $tgl,
                    "user_id" => $customer['id'],
                    "lokasi" => "Kasir",
                    "petugas" => user()['nama'],
                    "metode" => "Cash"
                ]);
                insertNota("barber", $uang, $db, $nota, $i['layanan'], $i['harga'], 1, $i['diskon'], $tgl, $customer['nama']);
            }
        }

        if (!empty($billiard)) {
            $id = $billiard['id'];
            $biaya = ($order == "hutang" ? $billiard['total_harga'] : $billiard['harga'] * ($billiard['durasi'] / 60));
            if ($order == "hutang") {
                $meja = db('billiard_2')->where('id', $billiard['id'])->get()->getRowArray();
                if (!$meja) gagal_js("Id billiard tidak ditemukan");

                $id = $meja['meja_id'];

                $meja['is_active'] = 0;
                $meja['end'] = ($meja['end'] == 0 ? time() : $meja['end']);
                $meja['petugas'] = user()['nama'];
                $meja['durasi'] = ($meja['durasi'] == 0 ? $billiard['durasi'] : $meja['durasi']);
                $meja['diskon'] = $billiard['diskon'];
                $meja['biaya'] = $biaya - $billiard['diskon'];

                db('billiard_2')->where("id", $meja['id'])->update($meja);
            } else {
                db('billiard_2')->insert([
                    "meja_id" => $id,
                    "meja" => $billiard['meja'],
                    "tgl" => $tgl,
                    "durasi" => $billiard['durasi'],
                    "biaya" => $biaya - $billiard['diskon'],
                    "diskon" => $billiard['diskon'],
                    "start" => $tgl,
                    "end" => $tgl + ($billiard['durasi'] * 60),
                    "is_active" => 1,
                    "harga" => $billiard['harga'],
                    "petugas" => user()['nama'],
                    "metode" => "Cash"
                ]);
            }

            $jadwal = db('jadwal_2')->where('id', $id)->get()->getRowArray();
            if (!$jadwal) gagal_js("Id jadwal tidak ditemukan");

            $jadwal['is_active'] = ($order == "hutang" ? 0 : 1);
            $jadwal['start'] = ($order == "hutang" ? 0 : $tgl);

            db('jadwal_2')->where("id", $jadwal['id'])->update($jadwal);


            insertNota("billiard", $uang, $db, $nota, $billiard['meja'], $billiard['harga'], $billiard['durasi'], $billiard['diskon'], $tgl, $customer['nama'], $billiard['total_harga']);
        }

        if (!empty($ps)) {
            $id = $ps['id'];
            $biaya = ($order == "hutang" ? $ps['total_harga'] : $ps['harga'] * ($ps['durasi'] / 60));
            if ($order == "hutang") {
                $meja = db('rental')->where('id', $ps['id'])->get()->getRowArray();
                if (!$meja) gagal_js("Id ps tidak ditemukan");

                $id = $meja['unit_id'];

                $meja['is_active'] = 0;
                $meja['ke'] = ($meja['durasi'] == -1 ? time() : $meja['ke']);
                $meja['petugas'] = user()['nama'];
                $meja['durasi'] = ($meja['durasi'] == -1 ? $ps['durasi'] : $meja['durasi']);
                $meja['diskon'] = $ps['diskon'];
                $meja['biaya'] = $biaya;

                db('rental')->where("id", $meja['id'])->update($meja);
            } else {
                db('rental')->insert([
                    "unit_id" => $ps['id'],
                    "meja" => $ps['meja'],
                    "tgl" => $tgl,
                    "durasi" => $ps['durasi'],
                    "biaya" => $biaya,
                    "diskon" => $ps['diskon'],
                    "dari" => $tgl,
                    "ke" => $tgl + ($ps['durasi'] * 60),
                    "is_active" => 1,
                    "harga" => $ps['harga'],
                    "petugas" => user()['nama'],
                    "metode" => "Cash"
                ]);
            }

            $unit = db('unit')->where('id', $id)->get()->getRowArray();
            if (!$unit) gagal_js("Id unit tidak ditemukan");

            $unit['status'] = ($order == "hutang" ? "Available" : "In Game");

            db('unit')->where("id", $unit['id'])->update($unit);


            insertNota("ps", $uang, $db, $nota, $ps['meja'], $ps['harga'], $ps['durasi'], $ps['diskon'], $tgl, $customer['nama'], $ps['total_harga']);

            // $unit = db('unit')->where('id', $ps['id'])->get()->getRowArray();
            // if (!$unit) gagal_js("Cancel semua");

            // $unit['status'] = 'In Game';
            // db('unit')->where("id", $unit['id'])->update($unit);

            // $biaya = $ps['harga'] * ($ps['durasi'] / 60);
            // db('rental')->insert([
            //     "unit_id" => $ps['id'],
            //     "meja" => $ps['meja'],
            //     "tgl" => $tgl,
            //     "durasi" => $ps['durasi'],
            //     "biaya" => $biaya,
            //     "diskon" => $ps['diskon'],
            //     "dari" => $tgl,
            //     "ke" => $tgl + ($ps['durasi'] * 60),
            //     "is_active" => 1,
            //     "harga" => $ps['harga'],
            //     "petugas" => user()['nama'],
            //     "metode" => "Cash"
            // ]);
            // insertNota("ps", $uang, $db, $nota, $ps['meja'], $ps['harga'], $ps['durasi'], $ps['diskon'], $tgl, $customer['nama']);
        }

        $db->transComplete();

        if (!$db->transStatus()) {
            gagal_js("Transaksi dibatalkan.");
        } else {
            if ($order == "hutang") {
                $dbh = db('hutang');
                $dbh->where('no_nota', $no_nota);
                $dbh->delete();
            }
            $db = db('nota');
            $q = $db->where('no_nota', $nota)->orderBy('barang', 'ASC')->get()->getResultArray();
            sukses_js("Sukses", $uang - ($total - $diskon), $q, str_replace("/", "-", $nota));
        }
    }

    public function bayar_nanti()
    {
        $db       = \Config\Database::connect();
        $nota     = no_invoice("nanti");
        $tgl      = time();
        $customer = json_decode(json_encode($this->request->getVar('customer')), true);
        $billiard = json_decode(json_encode($this->request->getVar('billiard')), true);
        $ps = json_decode(json_encode($this->request->getVar('ps')), true);
        $total    = (int) clear($this->request->getVar('total'));
        $diskon   = (int) clear($this->request->getVar('diskon'));
        $uang     = (int) clear($this->request->getVar('uang'));


        if ($uang < ($total - $diskon)) gagal_js("Uang kurang");
        if ($diskon > $total) gagal_js("Diskon terlalu besar");

        $db->transStart();

        // 👇 Helper untuk simpan hutang
        function simpan($kategori, $item, $nota, $tgl, $customer)
        {

            if ($kategori == "Kantin") {
                $total_harga = $item['harga_satuan'] * $item['qty'];
                $qty = $item['qty'];
            } elseif ($kategori == "Barber") {
                $qty = 1;
                $total_harga = $item['harga'];
            } elseif ($kategori == "Billiard" || $kategori == "Ps") {
                $total_harga = ($item['durasi'] == 0 ? 0 : $item['harga'] * ($item['durasi'] / 60));
                $qty = $item['durasi'];
            }

            db('hutang')->insert([
                'kategori'     => $kategori,
                'no_nota'      => $nota,
                'user_id'      => $customer['id'],
                'nama'         => $customer['nama'],
                'teller'       => user()['nama'],
                'dibayar_kpd'  => '',
                'tgl'          => $tgl,
                'status'       => 0,
                'barang_id'    => $item['id'],
                'barang'       => $item['layanan'] ?? $item['barang'] ?? $item['meja'],
                'harga_satuan' => $item['harga_satuan'] ?? $item['harga'],
                'qty'          => $qty,
                'total_harga'  => $total_harga
            ]);
        }

        // 👇 Data kategori umum
        foreach (['kantin' => 'Kantin', 'barber' => 'Barber'] as $key => $kategori) {
            $data = json_decode(json_encode($this->request->getVar($key)), true) ?? [];
            foreach ($data as $item) simpan($kategori, $item, $nota, $tgl, $customer);
        }

        // 👇 Billiard logic
        if (!empty($billiard)) {
            $jadwal = db('jadwal_2')->where('id', $billiard['id'])->get()->getRowArray();
            if (!$jadwal) gagal_js("Id jadwal tidak ditemukan");

            $jadwal['is_active'] = 1;
            $jadwal['start'] = $tgl;
            db('jadwal_2')->where("id", $jadwal['id'])->update($jadwal);

            $dbb = $db->table('billiard_2');

            $dbb->insert([
                "meja_id"  => $billiard['id'],
                "meja"     => $billiard['meja'],
                "tgl"      => $tgl,
                "durasi"   => $billiard['durasi'],
                "biaya"    => ($billiard['harga'] * $billiard['durasi'] / 60) - $billiard['diskon'],
                "diskon"   => $billiard['diskon'],
                "start"    => $tgl,
                "end"      => ($billiard['durasi'] == 0 ?  0 : $tgl + ($billiard['durasi'] * 60)),
                "is_active" => 1,
                "harga"    => $billiard['harga'],
                "petugas"  => user()['nama'],
                "metode"   => "Cash"
            ]);

            $billiard['id'] = $db->insertID();

            simpan('Billiard', $billiard, $nota, $tgl, $customer);
        }

        // 👇 PS logic
        if (!empty($ps)) {
            $unit = db('unit')->where('id', $ps['id'])->get()->getRowArray();
            if (!$unit) gagal_js("Id unit tidak ditemukan");

            $unit['status'] = 'In Game';
            db('unit')->where("id", $unit['id'])->update($unit);

            $dbp = $db->table('rental');
            $dbp->insert([
                "unit_id"  => $ps['id'],
                "meja"     => $ps['meja'],
                "tgl"      => $tgl,
                "durasi"   => ($ps['durasi'] == 0 ? -1 : $ps['durasi']),
                "biaya"    => $ps['harga'] * ($ps['durasi'] / 60),
                "diskon"   => $ps['diskon'],
                "dari"     => $tgl,
                "ke"       => ($ps['durasi'] == 0 ? -1 : $tgl + ($ps['durasi'] * 60)),
                "is_active" => 1,
                "harga"    => $ps['harga'],
                "petugas"  => user()['nama'],
                "metode"   => "Cash"
            ]);

            $ps['id'] = $db->insertID();
            simpan('Ps', $ps, $nota, $tgl, $customer);
        }

        $db->transComplete();
        $db->transStatus() ? sukses_js("Sukses") : gagal_js("Transaksi dibatalkan.");
    }
    public function tambah_pesanan()
    {
        $db       = \Config\Database::connect();
        $tgl      = time();
        $billiard = json_decode(json_encode($this->request->getVar('billiard')), true);
        $kantin = json_decode(json_encode($this->request->getVar('kantin')), true);
        $barber = json_decode(json_encode($this->request->getVar('barber')), true);
        $ps = json_decode(json_encode($this->request->getVar('ps')), true);
        $no_nota     = clear($this->request->getVar('no_nota'));


        $db->transStart();

        $data_hutang = db('hutang')->where('no_nota', $no_nota)->get()->getResultArray();
        if (!$data_hutang) return gagal_js("No. nota tidak ditemukan");

        $customer = db('users')->where('id', $data_hutang[0]['user_id'])->get()->getRowArray();



        // 👇 Helper untuk simpan hutang
        function simpan_hutang($kategori, $item, $nota, $tgl, $customer)
        {

            if ($kategori == "Kantin") {
                $total_harga = $item['harga_satuan'] * $item['qty'];
                $qty = $item['qty'];
            } elseif ($kategori == "Barber") {
                $qty = $item['qty'];
                $total_harga = $item['harga'];
            } elseif ($kategori == "Billiard" || $kategori == "Ps") {
                $total_harga = ($item['durasi'] == 0 ? 0 : $item['harga'] * ($item['durasi'] / 60));
                $qty = $item['durasi'];
            }

            db('hutang')->insert([
                'kategori'     => $kategori,
                'no_nota'      => $nota,
                'user_id'      => $customer['id'],
                'nama'         => $customer['nama'],
                'teller'       => user()['nama'],
                'dibayar_kpd'  => '',
                'tgl'          => $tgl,
                'status'       => 0,
                'barang_id'    => $item['id'],
                'barang'       => $item['layanan'] ?? $item['barang'] ?? $item['meja'],
                'harga_satuan' => $item['harga_satuan'] ?? $item['harga'],
                'qty'          => $qty,
                'total_harga'  => $total_harga
            ]);
        }

        if (!empty($kantin)) {
            foreach ($kantin as $i) {
                if (array_key_exists('kode', $i)) {
                    // Data dengan key "kode" dianggap data baru → insert
                    simpan_hutang("Kantin", $i, $no_nota, $tgl, $customer);
                } else {
                    foreach ($data_hutang as $h) {
                        if ($h['kategori'] === "Kantin" && $h['barang_id'] === $i['id']) {
                            // Bandingkan qty, jika berbeda → update
                            if ((int)$h['qty'] !== (int)$i['qty']) {
                                $h['qty'] = $i['qty'];
                                $h['total_harga'] = (int)$i['qty'] * $h['harga_satuan'];
                                db('hutang')->where('id', $h['id'])->update($h);
                            }
                            break;
                        }
                    }
                }
            }
        }
        if (!empty($barber)) {
            foreach ($barber as $i) {
                if (array_key_exists('kode', $i)) {
                    // Data dengan key "kode" dianggap data baru → insert
                    simpan_hutang("Barber", $i, $no_nota, $tgl, $customer);
                } else {
                    foreach ($data_hutang as $h) {
                        if ($h['kategori'] === "Barber" && $h['barang_id'] === $i['id']) {
                            // Bandingkan qty, jika berbeda → update
                            if ((int)$h['qty'] !== (int)$i['qty']) {
                                $h['qty'] = $i['qty'];
                                $h['total_harga'] = (int)$i['qty'] * $h['harga_satuan'];
                                db('hutang')->where('id', $h['id'])->update($h);
                            }
                            break;
                        }
                    }
                }
            }
        }

        // 👇 Billiard logic
        if (!empty($billiard)) {

            if (array_key_exists("kode", $billiard)) {
                $jadwal = db('jadwal_2')->where('id', $billiard['id'])->get()->getRowArray();
                if (!$jadwal) gagal_js("Id jadwal billiard tidak ditemukan");

                $jadwal['is_active'] = 1;
                $jadwal['start'] = $tgl;
                db('jadwal_2')->where("id", $jadwal['id'])->update($jadwal);

                $dbb = $db->table('billiard_2');

                $dbb->insert([
                    "meja_id"  => $billiard['id'],
                    "meja"     => $billiard['meja'],
                    "tgl"      => $tgl,
                    "durasi"   => $billiard['durasi'],
                    "biaya"    => ($billiard['harga'] * $billiard['durasi'] / 60) - $billiard['diskon'],
                    "diskon"   => $billiard['diskon'],
                    "start"    => $tgl,
                    "end"      => ($billiard['durasi'] == 0 ?  0 : $tgl + ($billiard['durasi'] * 60)),
                    "is_active" => 1,
                    "harga"    => $billiard['harga'],
                    "petugas"  => user()['nama'],
                    "metode"   => "Cash"
                ]);

                $billiard['id'] = $db->insertID();

                simpan_hutang('Billiard', $billiard, $no_nota, $tgl, $customer);
            }
        }

        // 👇 PS logic
        if (!empty($ps)) {
            if (array_key_exists("kode", $ps)) {

                $unit = db('unit')->where('id', $ps['id'])->get()->getRowArray();
                if (!$unit) gagal_js("Id unit tidak ditemukan");

                $unit['status'] = 'In Game';
                db('unit')->where("id", $unit['id'])->update($unit);

                $dbp = $db->table('rental');
                $dbp->insert([
                    "unit_id"  => $ps['id'],
                    "meja"     => $ps['meja'],
                    "tgl"      => $tgl,
                    "durasi"   => ($ps['durasi'] == 0 ? -1 : $ps['durasi']),
                    "biaya"    => $ps['harga'] * ($ps['durasi'] / 60),
                    "diskon"   => $ps['diskon'],
                    "dari"     => $tgl,
                    "ke"       => ($ps['durasi'] == 0 ? -1 : $tgl + ($ps['durasi'] * 60)),
                    "is_active" => 1,
                    "harga"    => $ps['harga'],
                    "petugas"  => user()['nama'],
                    "metode"   => "Cash"
                ]);

                $ps['id'] = $db->insertID();
                simpan_hutang('Ps', $ps, $no_nota, $tgl, $customer);
            }
        }

        $db->transComplete();
        $db->transStatus() ? sukses_js("Sukses") : gagal_js("Transaksi dibatalkan.");
    }

    public function menu_utama()
    {
        if (clear($this->request->getVar('menu')) === 'hutang') {
            $end   = strtotime(date('Y-m-d', strtotime('-1 day')) . ' 12:00:00');
            $start   = strtotime(date('Y-m-d') . ' 06:00:00');

            if ((int)date("H") > 11 && date("H") < 24) {
                $start = strtotime(date('Y-m-d') . ' 12:00:00');
                $end   = strtotime(date('Y-m-d', strtotime('+1 day')) . ' 06:00:00');
            }
            sukses_js($start . " " . date("d/m/Y H:i", $start), $end . " " . date("d/m/Y H:i", $end));
            $data = db('hutang')
                ->select('*')
                ->where('tgl >=', $start)
                ->where('tgl <=', $end)
                ->where('status', 0)
                ->groupBy('no_nota')
                ->orderBy('tgl', "ASC")
                ->get()
                ->getResultArray();

            sukses_js('Ok', $data);
        }
    }
    public function data_hutang()
    {
        $no_nota = clear($this->request->getVar('no_nota'));

        $hasil = hutang($no_nota);

        sukses_js("Ok", $hasil);
    }
    public function options()
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $kategori        = clear($this->request->getVar('kategori'));
        $order           = clear($this->request->getVar('order'));
        $id              = clear($this->request->getVar('id'));
        $no_nota         = clear($this->request->getVar('no_nota'));
        $meja_tujuan_id  = clear($this->request->getVar('meja_tujuan_id'));
        $durasi          = (int)clear($this->request->getVar('durasi'));

        if ($kategori === 'billiard') {
            $q = db('billiard_2')->where('id', $id)->get()->getRowArray();
            if (!$q) return gagal_js("Data billiard tidak ditemukan");

            if ($order === 'tambah') {
                $q['durasi'] += 60 * $durasi;
                $q['end']    += (60 * $durasi) * 60;

                if (!db('billiard_2')->where('id', $q['id'])->update($q)) return gagal_js("Update durasi gagal");

                $hutang = db('hutang')
                    ->where('no_nota', $no_nota)
                    ->where('kategori', 'Billiard')
                    ->get()->getRowArray();
                if (!$hutang) return gagal_js("Data hutang tidak ditemukan");

                $hutang['qty']          += 60 * $durasi;
                $hutang['total_harga']  += $hutang['harga_satuan'] * $durasi;

                if (!db('hutang')->where('id', $hutang['id'])->update($hutang)) return gagal_js("Update hutang gagal");
            }

            if ($order === 'pindah') {
                $jadwal_old = db('jadwal_2')
                    ->where('id', $q['meja_id'])
                    ->where('is_active', 1)
                    ->get()->getRowArray();
                if (!$jadwal_old) return gagal_js("Jadwal awal tidak aktif");

                $jadwal = db('jadwal_2')
                    ->where('id', $meja_tujuan_id)
                    ->where('is_active', 0)
                    ->get()->getRowArray();
                if (!$jadwal) return gagal_js("Meja tujuan tidak tersedia");

                $jadwal['is_active'] = 1;
                $jadwal['start']     = $jadwal_old['start'];

                if (!db('jadwal_2')->where("id", $jadwal['id'])->update($jadwal)) return gagal_js("Gagal aktifkan meja tujuan");

                $q['meja_id'] = $jadwal['id'];
                $q['meja']    = "Meja " . $jadwal['meja'];

                if (!db('billiard_2')->where('id', $q['id'])->update($q)) return gagal_js("Gagal update billiard_2");

                $hutang = db('hutang')
                    ->where('no_nota', $no_nota)
                    ->where('kategori', 'Billiard')
                    ->get()->getRowArray();
                if (!$hutang) return gagal_js("Data hutang tidak ditemukan");

                $hutang['barang'] = "Meja " . $jadwal['meja'];

                if (!db('hutang')->where('id', $hutang['id'])->update($hutang)) return gagal_js("Gagal update hutang");

                $jadwal_old['is_active'] = 0;
                $jadwal_old['start']     = 0;

                if (!db('jadwal_2')->where('id', $jadwal_old['id'])->update($jadwal_old)) return gagal_js("Gagal nonaktifkan meja lama");
            }
        }
        if ($kategori == 'ps') {
            $q = db('rental')->where('id', $id)->get()->getRowArray();
            if (!$q) return gagal_js("Data ps tidak ditemukan");

            if ($order === 'tambah') {
                $q['durasi'] += 60 * $durasi;
                $q['ke']    += (60 * $durasi) * 60;

                if (!db('rental')->where('id', $q['id'])->update($q)) return gagal_js("Update durasi gagal");

                $hutang = db('hutang')
                    ->where('no_nota', $no_nota)
                    ->where('kategori', 'Ps')
                    ->get()->getRowArray();
                if (!$hutang) return gagal_js("Data hutang tidak ditemukan");

                $hutang['qty']          += 60 * $durasi;
                $hutang['total_harga']  += $hutang['harga_satuan'] * $durasi;

                if (!db('hutang')->where('id', $hutang['id'])->update($hutang)) return gagal_js("Update hutang gagal");
            }

            if ($order === 'pindah') {
                $unit_old = db('unit')
                    ->where('id', $q['unit_id'])
                    ->where('status', "In Game")
                    ->get()->getRowArray();
                if (!$unit_old) return gagal_js("Jadwal awal tidak aktif");

                $unit = db('unit')
                    ->where('id', $meja_tujuan_id)
                    ->where('status', "Available")
                    ->get()->getRowArray();
                if (!$unit) return gagal_js("Meja tujuan tidak tersedia");

                $unit['status'] = "In Game";

                if (!db('unit')->where("id", $unit['id'])->update($unit)) return gagal_js("Gagal aktifkan meja tujuan");

                $q['unit_id'] = $unit['id'];
                $q['meja']    = $unit['unit'];

                if (!db('rental')->where('id', $q['id'])->update($q)) return gagal_js("Gagal update ps");

                $hutang = db('hutang')
                    ->where('no_nota', $no_nota)
                    ->where('kategori', 'Ps')
                    ->get()->getRowArray();
                if (!$hutang) return gagal_js("Data hutang tidak ditemukan");

                $hutang['barang'] = $unit['unit'];

                if (!db('hutang')->where('id', $hutang['id'])->update($hutang)) return gagal_js("Gagal update hutang");

                $unit_old['status'] = "Available";

                if (!db('unit')->where('id', $unit_old['id'])->update($unit_old)) return gagal_js("Gagal nonaktifkan meja lama");
            }
        }

        $db->transComplete();

        if ($db->transStatus()) {
            sukses_js("Berhasil");
        } else {
            gagal_js("Gagal");
        }
    }

    public function status_now()
    {
        $db_meja = db('jadwal_2');
        $meja = $db_meja->orderBy('meja', 'ASC')->get()->getResultArray();
        $db_billiard = db('billiard_2');

        $billiard = [];
        foreach ($meja as $i) {
            $q = $db_billiard->where('meja', "Meja " . $i['meja'])->where('is_active', 1)->get()->getRowArray();

            $temp = [
                'meja' => "Meja " . $i['meja'],
                'status' => "Kosong",
                'harga' => $i['harga'],
                'text' => 'text-success',
                'durasi' => ''
            ];

            if ($q) {
                if ($q['durasi'] == 0) {
                    $temp['status'] = "Open";
                    $temp['text'] = "text-secondary";
                    $temp['durasi'] = durasi($q['start'], time());
                } else {
                    $temp['status'] = "Regular";
                    $temp['durasi'] = (time() > $q['end'] ? "Habis" : "-" . durasi(time(), $q['end']));
                    $temp['text'] = ($temp['durasi'] == "Habis" ? "text-danger" : "text-secondary");
                }
            }

            $billiard[] = $temp;
        }

        $db_unit = db('unit');
        $unit = $db_unit->whereNotIn('status', ['Maintenance'])->orderBy('no_urut', 'ASC')->get()->getResultArray();
        $db_ps = db('rental');
        $db_kode_bayar = db('settings');
        $ps = [];
        foreach ($unit as $i) {
            $q = $db_ps->where('meja', $i['unit'])->where('is_active', 1)->get()->getRowArray();
            $kode_bayar = $db_kode_bayar->where('nama_setting', $i['kode_harga'])->get()->getRowArray();

            $temp = [
                'meja' => $i['unit'],
                'status' => "Kosong",
                'harga' =>  $kode_bayar['value_int'],
                'text' => 'text-success',
                'durasi' => ''
            ];


            if ($q) {
                if ($q['durasi'] == -1) {
                    $temp['status'] = "Open";
                    $temp['text'] = "text-secondary";
                    $temp['durasi'] = durasi($q['dari'], time());
                } else {
                    $temp['status'] = "Regular";
                    $temp['durasi'] = (time() > $q['ke'] ? "Habis" : "-" . durasi(time(), $q['ke']));
                    $temp['text'] = ($temp['durasi'] == "Habis" ? "text-danger" : "text-secondary");
                }
            }
            $ps[] = $temp;
        }


        $res = [
            'billiard' => $billiard,
            'ps' => $ps
        ];

        sukses_js("Ok", $res);
    }
}
