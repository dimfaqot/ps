<?php

namespace App\Controllers;

class Absen extends BaseController
{
    function __construct()
    {
        helper('functions');
        check_role('id');
    }
    public function index(): string
    {
        return view('absen', ['judul' => 'Home - ABSEN']);
    }
    public function encode()
    {;

        $data = encode_jwt(json_decode(json_encode($this->request->getVar('data')), true));
        sukses_js('Jwt Sukses.', $data);
    }
    public function presentation($jwt)
    {

        $data = decode_jwt($jwt);

        $val = get_absen();

        $value = [
            'tgl' => (int)date('d', $val['time_server']),
            'username' => user()['username'],
            'ket' => $val['ket'],
            'poin' => $val['poin'],
            'nama' => user()['nama'],
            'role' => session('role'),
            'user_id' => session('id'),
            'shift' => $val['shift'],
            'jam' => $val['jam'],
            'absen' => $val['time_server'],
            'terlambat' => $val['menit']
        ];

        $db = db('absen');
        if ($db->insert($value)) {
            $dbn = db('notif');
            $datan = [
                'kategori' => 'Absen',
                'pemesan' => $value['nama'],
                'tgl' => $value['absen'],
                'menu' => ($value['ket'] == 'Ontime' ? 'Absen pada ' . date('H:i', $val['time_server']) : $val['diff']),
                'meja' => $value['ket'],
                'qty' => $value['poin']
            ];

            $dbn->insert($datan);

            if ($val['msg'] == 'Kamu tepat waktu.') {
                sukses(base_url('home'), $val['msg']);
            } else {
                gagal_with_button(base_url('home'), $val['msg']);
            }
        } else {
            gagal_with_button(base_url('home'), 'Absen gagal!.');
        }
    }

    public function poin_absen()
    {
        $id = clear($this->request->getVar('id'));

        $data = poin_absen($id);

        sukses_js('Sukses', $data);
    }
    public function add_aturan()
    {
        $id = clear($this->request->getVar('id'));
        $ket = clear($this->request->getVar('ket'));
        $poin = clear($this->request->getVar('poin'));
        $username = clear($this->request->getVar('username'));
        $nama = clear($this->request->getVar('nama'));
        $role = clear($this->request->getVar('role'));

        $data = [
            'ket' => $ket,
            'tgl' => date('d/m/Y'),
            'username' => $username,
            'nama' => $nama,
            'role' => $role,
            'shift' => 0,
            'jam' => 0,
            'absen' => time(),
            'terlambat' => 0,
            'poin' => $poin,
            'user_id' => $id
        ];

        $db = db('absen');
        if ($db->insert($data)) {
            $dbn = db('notif');
            $datan = [
                'kategori' => 'Aturan',
                'pemesan' => $data['nama'],
                'tgl' => $data['absen'],
                'menu' => $data['ket'],
                'meja' => ($data['poin'] < 0 ? 'melanggar' : 'layak dipuji'),
                'qty' => $data['poin']
            ];

            $dbn->insert($datan);
            sukses_js('Data berhasil diinput.');
        } else {
            gagal_js('Data gagal diinput!.');
        }
    }
    public function reset_absen()
    {

        if ((int)date('d') > 2) {
            gagal(base_url('home'), 'Tanggal reset berlalu!.');
        }
        $db = db('absen');

        $q = $db->get()->getResultArray();

        foreach ($q as $i) {
            $db->where('id', $i['id']);
            $db->delete();
        }

        sukses(base_url('home'), 'Absen reset.');
    }

    public function update_poin()
    {

        $id = clear($this->request->getVar('id'));
        $val = (int)clear($this->request->getVar('val'));

        $db = db('absen');
        $q = $db->where('id', $id)->get()->getRowArray();

        if (!$q) {
            gagal_js('Id not found!.');
        } else {

            $q['poin'] = $val;
            $db->where('id', $id);
            if ($db->update($q)) {
                sukses_js('Update sukses.');
            } else {
                sukses_js('Update gagal!.');
            }
        }
    }
    public function perizinan()
    {

        $ket = clear($this->request->getVar('ket'));
        $tanggal = clear($this->request->getVar('tgl'));
        $username = clear($this->request->getVar('username'));
        $id = clear($this->request->getVar('id'));
        $nama = clear($this->request->getVar('nama'));
        $role = clear($this->request->getVar('role'));
        $poin = (int)clear($this->request->getVar('poin'));
        $jam = clear($this->request->getVar('jam') . ':00');
        $tgl = strtotime($tanggal . ' ' . $jam);

        $db = db('absen');
        $data = [
            'ket' => $ket,
            'tgl' => (int)date('d', $tgl),
            'username' => $username,
            'nama' => $nama,
            'role' => $role,
            'poin' => $poin,
            'user_id' => $id
        ];

        if ($db->insert($data)) {
            $dbn = db('notif');
            $datan = [
                'kategori' => 'Aturan',
                'pemesan' => $data['nama'],
                'tgl' => $tgl,
                'menu' => $data['ket'],
                'meja' => ($data['poin'] < 0 ? 'layak dikampleng karena' : 'layak dipuji karena'),
                'qty' => $data['poin']
            ];

            $dbn->insert($datan);
            sukses_js('Data sukses dimasukkan.');
        } else {
            gagal_js('Data gagal dimasukkan.');
        }
    }
    public function qrcode()
    {
        return view('qrcode_absen', ['judul' => 'Qrcode Absen']);
    }
    public function cetak_absen_qrcode()
    {
        $set = [
            'mode' => 'utf-8',
            'format' => [215, 330],
            'orientation' => 'P',
            'margin-left' => 20,
            'margin-right' => 20,
            'margin-top' => -0,
            'margin-bottom' => 0
        ];

        $mpdf = new \Mpdf\Mpdf($set);

        $html = view('cetak_absen_qrcode', ['judul' => 'Cetak Absen Qrcode']);
        $mpdf->AddPage();
        $mpdf->WriteHTML($html);

        $this->response->setHeader('Content-Type', 'application/pdf');

        $mpdf->Output('Absen Qrcode' . '.pdf', 'I');
    }
}
