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
        $temp = ['ip' => getenv('IP')];
        dd(encode_jwt($temp));
        return view('absen', ['judul' => 'Home - ABSEN']);
    }
    public function presentation($jwt)
    {

        $data = decode_jwt($jwt);


        if ($data['ip'] !== getenv('IP')) {
            gagal(base_url('home'), 'Lokasi terlalu jauh atau wifi belum digunakan!.');
        }

        sukses(base_url('home'), 'Absen Sukses.');
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
