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
        $data = [
            'latitude' => '-7.5716',
            'longitude' => '110.8226',
            'ip' => '125.163.46.150'
        ];
        dd(encode_jwt($data));
        return view('absen', ['judul' => 'Home - ABSEN']);
    }
    public function presentation($jwt)
    {

        $data = decode_jwt($jwt);

        if ($data['ip'] == session('ip') || $data['latitude'] == session('latitude') || $data['longitude'] == session('longitude')) {
            $val = get_absen();
            if ($val == null) {
                gagal_with_button(base_url('home'), 'Kamu sudah absen!.');
            }
            $value = [
                'tgl' => date('d/m/Y', $val['time_server']),
                'username' => user()['username'],
                'nama' => user()['nama'],
                'role' => session('role'),
                'shift' => $val['shift'],
                'jam' => $val['jam'],
                'absen' => $val['time_server'],
                'terlambat' => $val['menit']
            ];

            $db = db('absen');
            if ($db->insert($value)) {
                if ($val['msg'] == 'Kamu tepat waktu.') {
                    sukses(base_url('home'), $val['msg']);
                } else {
                    gagal_with_button(base_url('home'), $val['msg']);
                }
            } else {
                gagal_with_button(base_url('home'), 'Absen gagal!.');
            }
        } else {
            gagal_with_button(base_url('home'), 'Lokasi terlalu jauh atau wifi belum digunakan!.');
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
