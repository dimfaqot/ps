<?php

$nama_server = "Billiards";
$status_esp = "[21]";
// $status_esp = "";

$jml_perangkat = 0;
$jml_meja = 0;

$db = db('perangkat');
$qp = $db->where('grup', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->orderBy('no_urut', 'ASC')->get()->getResultArray();
if (!$qp) {
    gagal_js("Nama server tidak ditemukan!.");
}

$data = [];

if ($nama_server == "Billiards") {
    $dbj = db('jadwal_2');
    if ($status_esp == "") {
        foreach ($qp as $i) {
            $hasil = $i['no_urut'] . $i['status'];
            $data[] = (int)$hasil;
        }
        $jml_perangkat = count($qp);
        $qb = $dbj->orderBy('meja', 'ASC')->get()->getResultArray();
        if (!$qb) {
            gagal_js("Nama server tidak ditemukan!.");
        }

        foreach ($qb as $i) {
            $meja = ($i['meja'] == 1 ? 10 : ($i['meja'] == 2 ? 11 : $i['meja']));
            $hasil = $meja . $i['is_active'];
            $data[] =  (int)$hasil;
        }

        $jml_meja = count($qb);
    } else {
        $statusArr = stringArr_to_arr($status_esp);
        $jml_mej = [];

        $dbb = db('jadwal_2');
        foreach ($statusArr as $i) {
            $mej = ($i['perangkat'] == 10 ? 1 : ($i['perangkat'] == 11 ? 2 : $i['perangkat']));
            $q = $dbb->where('meja', $mej)->get()->getRowArray();
            if ($i['perangkat'] >= 10) {
                if ($q && $mej == $q['meja'] && $q['is_active'] != $i['status']) {
                    $meja = ($q['meja'] == 1 ? 10 : ($q['meja'] == 2 ? 11 : $q['meja']));
                    $hasil = $meja . $q['is_active'];
                    $jml_mej[] = $hasil;
                    $data[] =  (int)$hasil;
                }
            }
        }
        $jml_meja = count($jml_mej);

        $jml_per = [];
        foreach ($statusArr as $i) {
            $q = $db->where('grup', ($nama_server == "Billiards" ? "Billiard" : $nama_server))->where("no_urut", $i['perangkat'])->get()->getRowArray();

            if ($q && $q['status'] != $i['status']) {
                $hasil = $q['no_urut'] . $q['status'];
                $jml_per[] = $hasil;
                $data[] =  (int)$hasil;
            }
        }
        $jml_perangkat = count($jml_per);
    }
    dd((count($data) == 0 || $data == null ? "" : $data));
    sukses_js("sukses", (count($data) == 0 || $data == null ? "" : $data), $jml_perangkat, $jml_meja, $jml_perangkat + $jml_meja);
}


$db = db('unit');
$q = $db->whereNotIn('status', ['Maintenance'])->orderBy('id', 'ASC')->get()->getResultArray();
$dbr = db('rental');
$ps = [];
foreach ($q as $i) {
    $qr = $dbr->where('unit_id', $i['id'])->where('is_active', 1)->get()->getRowArray();
    if ($qr) {
        $i['is_active'] = 1;
    } else {
        $i['is_active'] = 0;
    }
    $exp = explode(' ', $i['unit']);
    $i['meja'] = (int)end($exp);
    $ps[] = $i;
}

$db = db('jadwal_2');
$billiard = $db->orderBy('meja', 'ASC')->get()->getResultArray();
$db = db('perangkat');
$others = $db->orderBy('no_urut', 'ASC')->get()->getResultArray();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul; ?></title>
    <link rel="icon" type="image/png" href="<?= base_url(); ?>logo.png" sizes="16x16">
    <script src="https://kit.fontawesome.com/a193ca89ae.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <?= view('functions_js'); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <style>
        .con {
            width: 400px;
            margin: auto;
            border-radius: 10px;
            box-shadow: -1px -1px 2px 0 rgba(92, 92, 92, 0.7);
            padding: 10px;
        }

        .embos {
            box-shadow: -1px -1px 2px 0 rgba(92, 92, 92, 0.7);
        }

        .embos2 {
            box-shadow: -1px -1px 2px 0 rgba(243, 236, 185, 0.7);
        }

        .default {
            background-color: transparent;
        }

        .active {
            background-color: #9b9a8f;
        }

        .select {
            background-color: #0dcaf0;
            color: white;
        }

        .btn_grey {
            border-radius: 5px;
            background-color: #292a32;
            padding: 5px 10px;
            text-decoration: none;
            border: 1px solid #2e3039;
            color: white;
            font-size: small;

        }

        .btn_grey:hover {
            border-radius: 5px;
            background-color: #40424a;
            padding: 5px 10px;
            text-decoration: none;
            font-size: small;
            color: #ffffff;
        }


        .input_light div {
            color: #bcc1f1;
            margin-bottom: 5px;
        }

        .input_light input {
            background-color: #292550;
            border: 5px;
            padding: 5px 10px;
            border-radius: 4px;
            color: #bcc1f1
        }

        .link_3 {
            text-decoration: none;
            font-size: 10px;
            color: #010517;
            padding: 5px 10px;
            background-color: #6173fc;
        }

        .link_3:hover {
            text-decoration: none;
            font-size: 10px;
            background-color: #010517;
            padding: 5px 10px;
            border: none;
            color: #6173fc
        }
    </style>
</head>

<body style="background-color: #23252d;">
    <!-- warning alert message -->
    <div class="box_warning" style="position:fixed;z-index:999999;display:none;"></div>

    <!-- <div style="margin-top: 30px;" class="data_hutang text-center px-3"></div> -->
    <div class="content mt-5">
        <div class="container">
            <div class="d-flex justify-content-center div_menu pt-3"></div>
            <div class="body_meja mt-4"></div>
            <div class="body_durasi"></div>
            <div class="body_btn_ok mt-3 d-grid"></div>
            <?= view('booking/date_time'); ?>
        </div>
    </div>

    <!-- modal search_db-->
    <div class="modal fade" id="search_db" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content modal_body_search_db">

            </div>
        </div>
    </div>
    <!-- modal menunggu-->
    <div class="modal fade bg-dark" id="menunggu" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: transparent;border:none">
                <div class="d-flex justify-content-center">
                    <div class="div_judul_menunggu px-3 border-bottom border-warning text-warning" style="margin-top:-200px;margin-bottom:200px"></div>
                </div>
                <div class="modal-body text-center modal_body_menunggu" style="margin-top: -150px;">

                </div>
            </div>
        </div>
    </div>
    <!-- modal open-->
    <div class="modal fade bg-dark" id="open" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">

        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="background-color: transparent;border:none">
                <div class="text-danger text-center" data-bs-dismiss="modal" style="cursor:pointer;margin-top:-200px;margin-bottom:50px;font-size:x-large"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="d-flex justify-content-center">
                    <div class="div_judul_menunggu px-3 border-bottom border-warning text-warning"></div>
                </div>
                <h6 class="text-center div_pesan_konfirmasi mt-3 text-secondary fst-italic">Selesaikan dan lakukan pembayaran?</h6>
                <div class="d-flex justify-content-center mt-2">
                    <h6 class="text-center harga_cara_bayar px-3 pb-2 text-light border-light border-bottom"></h6>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-3 body_open">

                </div>
            </div>
        </div>
    </div>

    <!-- modal hutang-->
    <div class="modal fade bg-dark" id="data_hutang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: transparent;border:none">
                <div class="modal-body text-center">
                    <div class="d-flex justify-content-center gap-2 py-2">
                        <div class="embos countdown text-center mt-1 pt-1" style="color:#cbf4f0;width:30px;height:30px;font-size:16px;border-radius:50%;;border:1px solid #3c3e46"></div>
                        <div class="text-light div_message_hutang" style="font-size: x-large;">
                            TAP UNTUK MELUNASI
                        </div>
                    </div>
                    <div class="body_message my-2 p-1" style="border:1px dashed white">
                        <div class="d-flex gap-2 justify-content-center text-light">
                            <div class="spinner-border spinner-border-sm mt-1 text-light" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div class="div_body_processing" style="font-style: italic;">processing...</div>
                        </div>
                    </div>
                    <div class="d-grid div_bayar_hutang_cash">
                        <button type="button" class="bayar_hutang_cash btn btn-outline-info"><i class="fa-solid fa-hand-holding-dollar"></i> Cash</button>
                    </div>

                    <div style="text-align: left;" class="total_hutang fw-bold text-warning"></div>
                    <div class="modal_body_data_hutang mt-2">

                    </div>

                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bg-dark" id="panel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content text-light" style="background-color: transparent;border:none">
                <div class="text-danger text-center close_modal_panel mt-4 mb-3" style="cursor:pointer;font-size:x-large"><i class="fa-solid fa-circle-xmark"></i></div>
                <div class="d-flex justify-content-center gap-3">
                    <button class="rounded-pill default btn_panel text-light px-4 py-1" data-menu="Ps" style="font-size: small;">PS</button>
                    <button class="rounded-pill default btn_panel text-light px-4 py-1" data-menu="Billiard" style="font-size: small;">BILLIARD</button>
                    <button class="rounded-pill default btn_panel text-light px-4 py-1" data-menu="Others" style="font-size: small;">OTHERS</button>
                </div>

                <div class="body_panel mt-3"></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script>
        // let myModal = document.getElementById('open');
        // let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        // modal.show();
        let data = {};
        let kode_bayar = <?= json_encode(kode_bayar()); ?>;
        let data_ps = <?= json_encode($ps); ?>;
        let html_ps = "";
        let data_billiard = <?= json_encode($billiard); ?>;
        let html_billiard = "";
        let data_others = <?= json_encode($others); ?>;
        let html_others = "";
        data_ps.forEach((e, i) => {
            if (i % 4 == 0) {
                html_ps += '<div class="d-flex justify-content-center gap-2 my-2">';
            }
            html_ps += '<div class="rounded-circle p-2 embos2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'active' : 'default') + '" data-meja="' + e.meja + '" data-menu="Ps" data-is_active="' + e.is_active + '" style="cursor:pointer;font-size:35px;width: 75px;height:75px;color:#7c6f3e;border:1px solid #fce882">';
            html_ps += '<div class="text-center" style="font-size:9px;margin-bottom:-2px">MEJA</div>' + e.meja;
            html_ps += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px">Available</div>';
            html_ps += '</div>';
            if (i % 4 == 3) {
                html_ps += '</div>';
            }
        })
        data_billiard.forEach((e, i) => {
            if (i % 4 == 0) {
                html_billiard += '<div class="d-flex justify-content-center gap-2 my-2">';
            }
            html_billiard += '<div class="rounded-circle p-2 embos2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'active' : 'default') + '" data-meja="' + e.meja + '" data-menu="Billiard" data-is_active="' + e.is_active + '" style="cursor:pointer;font-size:35px;width: 75px;height:75px;color:#7c6f3e;border:1px solid #fce882">';
            html_billiard += '<div class="text-center" style="font-size:9px;margin-bottom:-2px">MEJA</div>' + e.meja;
            html_billiard += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px">Available</div>';
            html_billiard += '</div>';
            if (i % 4 == 3) {
                html_billiard += '</div>';
            }
        })

        html_others += '<div class="container text-center">';
        html_others += '<div class="row g-2">';
        data_others.forEach((e, i) => {
            html_others += '<div class="col-4">';
            html_others += '<div class="rounded border ' + (e.status == 1 ? "border-light text-light" : "border-secondary text-secondary") + ' py-1 btn_meja btn_meja_' + e.id + '" data-meja="' + e.id + '" data-menu="Others" style="font-size: 16px;">' + e.nama + '</div>';
            html_others += '</div>';
        })

        html_others += '</div>';
        html_others += '</div>';

        const menus = {
            member: `<span class="pe-2 tangan" data-menu="admin" style="font-size: 27px;margin-top:-12px"><i class="fa-regular fa-hand-point-right"></i></span>
                    <div class="text-center text-info ms-2">
                        <span style="cursor: pointer;" data-menu="Barber" class="btn_menu py-2 px-4 border rounded border-info">BARBER</span>
                        <span style="cursor: pointer;" data-menu="Ps" class="btn_menu py-2 px-4 rounded border border-info">PS</span>
                        <span style="cursor: pointer;" data-menu="Billiard" class="btn_menu py-2 px-4 rounded border border-info">BILLIARD</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Hutang" class="btn_menu py-2 px-4 rounded border border-info">HUTANG</span>
                        <span style="cursor: pointer;" data-menu="Saldo" class="btn_menu py-2 px-4 rounded border border-info">SALDO</span>
                        </div>`,
            admin: `<span class="pe-2 tangan" data-menu="member" style="font-size: 27px;margin-top:-12px"><i class="fa-regular fa-hand-point-right"></i></span>
                        <div class="text-center text-info ms-2">
                        <span style="cursor: pointer;" data-menu="Absen" class="btn_menu py-2 px-4 border rounded border-info">ABSEN</span>
                        <span style="cursor: pointer;" data-menu="Poin" class="btn_menu py-2 px-4 rounded border border-info">POIN</span>
                        <span style="cursor: pointer;" data-menu="Panel" class="btn_menu py-2 px-4 rounded border border-info">PANEL</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Topup" class="btn_menu py-2 px-4 rounded border border-info">TOPUP</span>
                        <span style="cursor: pointer;" data-menu="Daftar" class="btn_menu py-2 px-4 border rounded border-info">DAFTAR</span>
                        <span style="cursor: pointer;" data-menu="Remove" class="btn_menu py-2 px-4 rounded border border-info">REMOVE</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Reload" class="btn_menu py-2 px-4 rounded border border-danger text-danger"><i class="fa-solid fa-arrows-rotate"></i> RELOAD</span>
                        <span style="cursor: pointer;" data-menu="Add" class="btn_menu py-2 px-4 rounded border border-info">ADD</span>
                        <span style="cursor: pointer;" data-menu="Delete" class="btn_menu py-2 px-4 rounded border border-info">DELETE</span>
                    </div>`,
            topup: `<div class="rounded px-4 pt-4">
                    <h6 class="text-center text-light mb-4">TOPUP</h6>
                    <div class="d-flex justify-content-center gap-5 mb-3">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="1" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">1</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="2" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">2</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="3" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">3</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-3">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="4" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">4</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="5" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">5</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="6" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">6</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-3">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="10" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">10</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="20" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">20</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="30" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">30</div>
                    </div>
                    </div>`,
            durasi: `<div class="rounded px-4 pt-4 mt-2">
                    <h6 class="text-center text-light mb-4">DURASI (JAM)</h6>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="1" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">1</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="2" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">2</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="3" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">3</div>
                    </div>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="4" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">4</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="5" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">5</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="6" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">6</div>
                    </div>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="7" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">7</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="8" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">8</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="9" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">9</div>
                    </div>
                    <div class="d-flex justify-content-center">
                    <div class="embos text-center border-warning rounded-pill text-warning px-3 py-2 fw-bold btn_durasi" data-durasi="0" style="cursor:pointer;font-size:x-large;">Open</div>
                    </div>
                    </div>`,
            meja_ps: html_ps,
            meja_billiard: html_billiard,
            meja_others: html_others,
            btn_ok: '<button class="btn_grey embos mt-2 py-3 btn_ok" style="font-size:xx-large">Ok</button>'
        }
        const modal_show = (id) => {
            let res = 0;
            document.addEventListener('DOMContentLoaded', function() {
                const exampleModal = document.getElementById(id);

                exampleModal.addEventListener('shown.bs.modal', function() {
                    console.log('The modal is now shown.');
                    res = 1;
                });

                exampleModal.addEventListener('hidden.bs.modal', function() {
                    console.log('The modal is now hidden.');
                    res = 0;
                });
            });
            return res;
        }

        const show_search_db = (kategori) => {
            let html = '';
            html += '<div class="input_light">';
            html += '<input data-kategori="' + kategori + '" class="search_db_input" placeholder="Ketik sesuatu..." value="" style="width: 100%;" type="text">';
            html += '<section class="bg_3 sticky-top bg_3" style="z-index:10">';
            html += '<section style="position:absolute;width:100%;text-align:left" class="bg_3 px-2 body_list_search_db">';

            html += '</section>';
            html += '<div class="btn_insert_value d-grid mt-2">';
            html += '</div>';
            html += '</section>';
            html += '</div>';
            if (modal_show("menunggu") == 0) {
                menunggu(html);
            } else {
                $('.body_message').html(html);
            }
        }


        const show_data_hutang = (list_data_hutang, uid) => {
            $(".modal_body_menunggu").html("");
            let html = "";

            html += '<table style="font-size: 13px;" data-uid="' + uid + '" class="table tabel_hutang table-sm text-light table-bordered border-info">';
            html += '<thead>';
            html += '<tr>';
            html += '<td style="text-align: center;">#</td>';
            html += '<td style="text-align: center;">Tgl</td>';
            html += '<td style="text-align: center;">Kat</td>';
            html += '<td style="text-align: center;">Barang</td>';
            html += '<td style="text-align: center;">Harga</td>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            let total = 0;
            list_data_hutang.forEach((e, i) => {
                total += parseInt(e.total_harga);
                html += '<tr>';
                html += '<td style="text-align: center;">' + (i + 1) + '</td>';
                html += '<td style="text-align: center;">' + time_php_to_js(e.tgl) + '</td>';
                html += '<td style="text-align:left">' + e.kategori + '</td>';
                html += '<td style="text-align:left">' + e.barang + '</td>';
                html += '<td style="text-align: right;">' + angka(e.total_harga) + '</td>';
                html += '</tr>';

            })
            html += '</tbody>';
            html += '</table>';

            $(".total_hutang").text("TOTAL: " + angka(total));
            $(".total_hutang").attr("data-total_hutang", total);
            $(".modal_body_data_hutang").html(html);

            let menunggu = document.getElementById('menunggu');
            let modalM = bootstrap.Modal.getOrCreateInstance(menunggu)
            modalM.hide();

            let myModal = document.getElementById('data_hutang');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
            modal.show();

        }
        const show_data_poin = (list_data_poin, uid) => {
            $(".modal_body_menunggu").html("");
            let html = "";

            html += '<table style="font-size: 13px;" data-uid="' + uid + '" class="table tabel_poin table-sm text-light table-bordered border-info">';
            html += '<thead>';
            html += '<tr>';
            html += '<td style="text-align: center;">#</td>';
            html += '<td style="text-align: center;">Tgl</td>';
            html += '<td style="text-align: center;">Jenis</td>';
            html += '<td style="text-align: center;">Ket</td>';
            html += '<td style="text-align: center;">Poin</td>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';
            let total = 0;
            list_data_poin.forEach((e, i) => {
                total += parseInt(e.poin);
                html += '<tr>';
                html += '<td style="text-align: center;">' + (i + 1) + '</td>';
                html += '<td style="text-align: center;">' + e.tgl + '</td>';
                html += '<td style="text-align:left">' + (e.ket == "Ontime" || e.ket == "Terlambat" || e.ket == "Ghoib" ? "Absen" : "Aturan") + '</td>';
                html += '<td style="text-align:left">' + e.ket + '</td>';
                html += '<td style="text-align: right;">' + e.poin + '</td>';
                html += '</tr>';

            })
            html += '</tbody>';
            html += '</table>';

            $(".div_message_hutang").text(list_data_poin[0].nama.toUpperCase());
            $(".div_body_processing").text("Data ditemukan.");
            $(".total_hutang").text("TOTAL: " + total);
            $(".div_bayar_hutang_cash").text("");
            $(".modal_body_data_hutang").html(html);

            let menunggu = document.getElementById('menunggu');
            let modalM = bootstrap.Modal.getOrCreateInstance(menunggu)
            modalM.hide();

            let myModal = document.getElementById('data_hutang');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
            modal.show();

        }
        const show_modal_konfirmasi = (msg, order, data1 = undefined, data2 = undefined, data3 = undefined, data4 = undefined) => {
            let html = "";
            html += '<div class="d-flex justify-content-center gap-3">';
            html += '<div class="spinner-border text-danger" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div>' + msg + '</div>';
            html += '</div>';
            $('.div_pesan_konfirmasi').html(html);

            let html2 = "";
            if (order == "cara_absen") {
                html2 += '<button data-cara_absen="1" type="button" data-order="' + order + '" class="btn_konfirmasi btn px-5 btn-outline-light"><i class="fa-solid fa-fingerprint"></i> FINGER</button>';
                html2 += '<button data-cara_absen="2" type="button" data-order="' + order + '" class="btn_konfirmasi btn ms-3 px-5 btn-outline-light"><i class="fa-solid fa-credit-card"></i> TAP</button>';
            } else {
                html2 += '<button type="button" ' + (data1 !== undefined ? 'data-data1="' + data1 + '"' : '') + ' ' + (data2 !== undefined ? 'data-data2="' + data2 + '"' : '') + ' ' + (data3 !== undefined ? 'data-data3="' + data3 + '"' : '') + ' ' + (data4 !== undefined ? 'data-data4="' + data4 + '"' : '') + ' data-order="' + order + '" class="btn_konfirmasi btn px-5 btn-outline-light"><i class="fa-solid fa-chevron-right"></i> LANJUT</button>';
                html2 += '<button type="button" data-bs-dismiss="modal" class="btn ms-3 px-5 btn-outline-secondary"><i class="fa-solid fa-ban"></i> BATAL</button>';
            }
            $('.body_open').html(html2);
            let modal = document.getElementById('open');
            let myModal = bootstrap.Modal.getOrCreateInstance(modal)
            myModal.show();
        }
        const menunggu = (message = "Menunggu") => {
            let html = "";
            html += '<div class="btn_close_menunggu" style="cursor:pointer">';
            html += '</div>';
            html += '<div class="spinner-grow text-primary" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="spinner-grow text-secondary" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="spinner-grow text-success" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="spinner-grow text-danger" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="spinner-grow text-warning" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="spinner-grow text-info" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="mt-4 body_message">';
            html += message;
            html += '</div>';
            html += '<div class="d-flex justify-content-center mt-5">';
            html += '<div class="embos d-none px-1 pt-4 countdown" style="color:#cbf4f0;width:200px;height:200px;font-size:118px;border-radius:50%;;border:1px solid #3c3e46"></div>';
            html += '</div>';
            $(".modal_body_menunggu").html(html);
            let myModal = document.getElementById('menunggu');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
            modal.show();
        }

        const spinner = (text) => {
            let html = "";
            html += '<div class="d-flex justify-content-center">';
            html += '<div class="spinner-border text-light" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
            html += '<div class="text-light">';
            html += text;
            html += '</div>';
            html += '</div>';

            return html;
        }

        const hide_show_modal = (id, order = "show") => {
            let myModal = document.getElementById(id);
            let modal = bootstrap.Modal.getOrCreateInstance(myModal);
            if (order == "hide") {
                modal.hide();
            } else {
                modal.show();
            }
        }

        let interval_blink_message = "";
        const blink_message = () => {
            if ($(".div_message_hutang").hasClass("text-light")) {
                $(".div_message_hutang").removeClass("text-light");
                $(".div_message_hutang").addClass("text-info");
            } else {
                $(".div_message_hutang").removeClass("text-info");
                $(".div_message_hutang").addClass("text-light");
            }
        }

        let interval_countdown = "";
        let x = 0;
        let limit = 21;
        let countdown = () => {
            x++;
            if (data.kategori == "Hutang" || data.kategori == "Add" || data.kategori == "Delete") {
                limit = 61;
            }

            if (x < limit) {
                $(".countdown").text(x);
                $(".countdown").removeClass("d-none");
            } else {
                clearInterval(interval_countdown);
                clearInterval(interval_booking);
                clearInterval(interval_hutang);
                clearInterval(interval_durasi);
                post("del_message", {
                    id: 0
                }).then(rest => {
                    if (rest.status == "200") {
                        $(".body_message").html('<h5 class="text-danger">Waktu habis!.</h5>');
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    }
                })

            }
        }
        let interval_message_server = "";
        const message_server = () => {
            post('message_server', {
                data
            }).then(res => {
                if (res.status == "200") {
                    if (res.data != null) {
                        let html = '';
                        let status = res.data.status;

                        html += '<h6 class="text-center ' + (status == "400" ? "text-danger" : "text-light") + '">' + res.data.message + '</h6>';
                        if (res.data.uang !== "") {
                            html += '<h5 class="text-center ' + (status == "400" ? "text-danger" : "text-light") + '">' + res.data.uang + '</h5>';
                        }

                        $('.body_message').html(html);

                        if (status == "end" || status == "400") {
                            clearInterval(interval_hutang);
                            clearInterval(interval_booking);
                            clearInterval(interval_durasi);
                            setTimeout(() => {
                                post("del_message", {
                                    id: 0
                                }).then(rest => {
                                    if (rest.status == "200") {
                                        location.reload();
                                    }
                                })
                            }, 2000);

                        }
                    }


                }
            })
        }


        let interval_booking = "";
        let sudah_masuk = 0; //data sudah masuk db
        const add_booking = () => {

            post('add_booking', {
                data
            }).then(res => {
                if (res.status == "200") {
                    clearInterval(interval_booking); //antri booking
                    interval_message_server = setInterval(message_server, 1000); //memanggil data hutang
                    interval_countdown = setInterval(countdown, 1000); //memanggil data hutang
                    if (modal_show("menunggu") == 0) {
                        menunggu('<h5 class="text-light">Menunggu tap/finger...</h5>');
                    } else {
                        $(".body_message").html('<h5 class="text-light">Menunggu respon user...</h5>');
                    }

                    // jika hutang maka memanggil data hutang
                    if (data.kategori == "Hutang") {
                        interval_hutang = setInterval(get_data_hutang, 1000); //memanggil data hutang
                    } else {
                        clearInterval(interval_hutang);
                    }
                    // jika poin maka memanggil data poin
                    if (data.kategori == "Poin") {
                        interval_poin = setInterval(get_data_poin, 1000); //memanggil data poin
                    } else {
                        clearInterval(interval_poin);
                    }
                } else {
                    if (res.data == 1) { //ada transaksi lain
                        if (sudah_masuk == 0) {
                            sudah_masuk = 1;
                            if (modal_show("menunggu") == 0) {
                                menunggu('<h5 class="text-danger">' + res.message + '</h5>');
                            }

                        }
                    } else {
                        gagal(res.message);
                    }
                }
            })
        }

        let interval_hutang = "";
        const get_data_hutang = () => {
            post("ext/data_hutang", {
                id: 0
            }).then(res => {

                if (res.data != null) {
                    show_data_hutang(res.data, res.data2);
                    interval_blink_message = setInterval(blink_message, 1000);

                    clearInterval(interval_hutang);
                } else {
                    $(".div_message_hutang").addClass("text-info");
                    clearInterval(interval_blink_message);
                }
            })

        }
        let interval_poin = "";
        const get_data_poin = () => {
            post("ext/data_poin", {
                id: 0
            }).then(res => {

                if (res.data != null) {
                    show_data_poin(res.data, res.data2);
                    interval_blink_message = setInterval(blink_message, 1000);

                    clearInterval(interval_poin);
                } else {
                    $(".div_message_hutang").addClass("text-info");
                    clearInterval(interval_blink_message);
                }
            })

        }

        let interval_durasi = "";
        const get_durasi = () => {
            if (data.kategori == "Ps" || data.kategori == "Billiard") {
                // let btn_meja = document.querySelectorAll(".btn_meja");
                // btn_meja.forEach(e => {
                //     $(".btn_meja_" + e.dataset.meja).addClass("default");
                //     $(".btn_meja_" + e.dataset.meja).removeClass("active");
                //     $(".btn_meja_" + e.dataset.meja).removeClass("open");
                //     $(".div_durasi_" + e.dataset.meja).text("Available");
                // })
                post('get_durasi', {
                    kategori: data.kategori.toLowerCase()
                }).then(res => {
                    if (res.status == "200") {
                        let durasi_active = [];
                        let index_active = [];
                        let harga_active = [];
                        let meja_id_active = [];
                        res.data.forEach(e => {
                            // if (!$(".btn_meja_" + e.meja).hasClass("active")) {
                            let durasi = e.durasi;
                            $(".btn_meja_" + e.meja).removeClass("default");
                            $(".btn_meja_" + e.meja).addClass("active");
                            $(".div_durasi_" + e.meja).text(durasi);
                            if (durasi == "Open") {
                                $(".btn_meja_" + e.meja).addClass("open");
                                $(".btn_meja_" + e.meja).attr("data-harga", e.harga);
                                $(".btn_meja_" + e.meja).attr("data-meja_id", e.id);
                            }
                            // }
                            // else {
                            //     if ($(".btn_meja_" + e.meja).hasClass("active")) {
                            //         $(".btn_meja_" + e.meja).addClass("default");
                            //         $(".btn_meja_" + e.meja).removeClass("active");
                            //         $(".btn_meja_" + e.meja).removeClass("open");
                            //         $(".div_durasi_" + e.meja).text("Available");
                            //     }
                            // }

                        })


                    } else {
                        gagal(res.message);
                    }
                })

            } else {
                clearInterval(interval_durasi);
            }
        }

        $(document).on('keyup', '.search_db_input', function(e) {
            e.preventDefault();
            $(".btn_insert_value").html('');
            let value = $(this).val();
            let kategori = $(this).data("kategori");
            post('daftar/search_db', {
                kategori,
                value
            }).then(res => {
                if (res.status == '200') {
                    let html = '';
                    res.data.forEach((e, i) => {
                        html += '<a data-kategori="' + kategori + '" data-id="' + e.id + '" style="font-size:14px" href="" class="link_3 d-block rounded border-bottom insert_value">' + e.nama + '</a>';
                    })

                    $('.body_list_search_db').html(html);
                } else {
                    gagal_with_button(res.message);
                }
            })

        })

        $(document).on('click', '.insert_value', function(e) {
            e.preventDefault();
            let id = $(this).data('id');
            let nama = $(this).text();
            data['durasi'] = id;
            data['kategori'] = $(this).data("kategori");
            data['meja'] = 0;

            $(".search_db_input").val(nama);
            $(".body_list_search_db").html("");
            $(".btn_insert_value").html('<button class="btn_grey embos mb-4 btn_ok">Ok</button>');
        })

        const tangan = () => {
            setInterval(() => {
                if ($(".tangan").hasClass("text-danger")) {
                    $(".tangan").removeClass("text-danger");
                    $(".tangan").addClass("text-primary");

                } else {
                    $(".tangan").addClass("text-danger");
                    $(".tangan").removeClass("text-primary");

                }
            }, 600);
        }



        const remove_cls = (cls, rmv, add) => {
            let elem = document.querySelectorAll('.' + cls);
            elem.forEach(e => {
                if (rmv !== undefined) {
                    e.classList.remove(rmv);
                }
                if (add !== undefined) {
                    e.classList.add(add);

                }
            });
        }

        const call_main_menu = (menu = undefined) => {
            data = {};
            $(".body_meja").html("");
            $(".body_durasi").html("");
            let html = "";
            if (menu == undefined) {
                html = menus.member;
            } else {
                html = menus[menu];
            }
            $(".div_menu").html(html);
        }
        call_main_menu();

        const call_meja = (menu) => {

            let html = menus["meja_" + menu];
            if (data.kategori == "Panel") {
                $(".body_panel").html(html);
            } else {
                $(".body_meja").html(html);
            }
        }
        const call_durasi = (menu) => {
            let html = menus[menu];
            $(".body_durasi").html(html);
        }

        const call_btn_ok = () => {
            $(".body_btn_ok").html("");
            if (data.kategori !== undefined && data.meja !== undefined && data.durasi !== undefined) {
                $(".body_btn_ok").html(menus.btn_ok);
            }

        }



        $(document).on("click", ".tangan", function(e) {
            e.preventDefault();
            let menu = $(this).data("menu");
            if ($(".date_time").hasClass("d-none")) {
                $(".date_time").removeClass("d-none");
            }
            call_main_menu(menu);

            call_btn_ok();
        })
        // URUTAN PROSES
        // memanggil interval_booking = setInterval(add_booking, 4000); untuk antrian memasukkan ke table booking
        // di fungsi add booking memanggil pesan dari server(message_server), hitung mundur durasi (countdown)
        // fungsi add booking juga menghentikan setinterval clearInterval(interval_booking) jika status return 200
        // di .btn_men menu yang eksekusinya dengan tombol ok maka interval_booking = setInterval(add_booking, 4000); dipanggil pada saat btn_ok diklik
        // menu Saldo dan Hutang tanpa melalui btn_ok maka interval_booking = setInterval(add_booking, 1000) langsung dipanggil
        $(document).on("click", ".btn_menu", function(e) {
            e.preventDefault();
            $(".date_time").addClass("d-none");
            // semua clas select dihapus
            remove_cls("btn_menu", "select");
            let menu = $(this).data("menu");
            // mengisi jusul untuk modal menunggu
            $(".div_judul_menunggu").text(menu.toUpperCase());
            if (menu == "Saldo" || menu == "Hutang" || menu == "Reload" || menu == "Barber" || menu == "Poin") {
                $(this).addClass("select");
                data = {
                    kategori: menu,
                    durasi: 0,
                    meja: 0
                }
                let html = spinner("Proses...");
                $(".content").html(html);
                interval_booking = setInterval(add_booking, 4000);
                return;
            }
            if (menu == "Absen") {
                $(this).addClass("select");
                data["kategori"] = menu;
                show_modal_konfirmasi("Pilih cara absen!.", "cara_absen");
                return;
            }

            if (menu == "Daftar" || menu == "Add" || menu == "Remove" || menu == "Delete") {
                $(this).addClass("select");
                show_search_db(menu);
                $(".btn_close_menunggu").html('<div class="mb-5 text-danger" style="font-size:x-large;margin-top:-200px"><i class="fa-solid fa-circle-xmark"></i></div>');
                return;
            }

            // hilangkan html durasi dan meja
            $(".body_meja").html("");
            $(".body_durasi").html("");

            // yang ditampilkan setelah klik btn_menu
            if (menu == "Ps" || menu == "Billiard") {
                call_meja(menu.toLowerCase());
                interval_durasi = setInterval(get_durasi, 1000); //update status meja dan durasi main
            }
            if (menu == "Panel") {
                $(this).addClass("select");
                data["kategori"] = "Panel";
                let myModal = document.getElementById('panel');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
                return;
            }

            if (menu == "Topup") {
                data["meja"] = 0;
                call_durasi(menu.toLowerCase());
            }

            if (menu == "Tap" || menu == "Cash") {
                let kat = $(this).data("kategori");
                let mej = $(this).data("meja");
                let harga = $(this).data("harga");
                show_modal_konfirmasi("YAKIN BAYAR DENGAN <span class='fst-italic fw-bold text-warning'>" + menu.toUpperCase() + "</span>?", "cara_bayar", menu, kat, mej, harga);
                return;
            }


            // isi data dan add kelas select
            if (data.kategori == undefined) {
                $(this).addClass("select");
                data["kategori"] = menu;
            } else if (data.kategori == menu) {
                $(".body_durasi").html("");
                $(this).removeClass("select");
                $(".body_meja").html("");
                data = {};
            } else {
                $(this).addClass("select");
                data = {};
                data["kategori"] = menu;
            }
            if (data.kategori == undefined && data.meja == undefined && data.durasi == undefined) {
                if ($(".date_time").hasClass("d-none")) {
                    $(".date_time").removeClass("d-none");
                }
            }
            call_btn_ok();
        })

        $(document).on("click", ".btn_meja", function(e) {
            e.preventDefault();
            // semua clas select dihapus
            let meja = $(this).data("meja");
            let menu = $(this).data("menu");
            if (data.kategori == "Panel") {
                if (menu == "Others") {
                    if ($(this).hasClass("border-secondary")) {
                        $(this).removeClass("border-secondary");
                        $(this).removeClass("text-secondary");
                        $(this).addClass("border-light");
                        $(this).addClass("text-light");
                    } else {
                        $(this).addClass("border-secondary");
                        $(this).addClass("text-secondary");
                        $(this).removeClass("border-light");
                        $(this).removeClass("text-light");
                    }
                } else {
                    if ($(this).hasClass("active")) {
                        $(this).removeClass("active");
                        $(this).addClass("default");
                    } else {
                        $(this).addClass("active");
                        $(this).removeClass("default");
                    }
                }

                if (kode_bayar[menu] == undefined) {
                    gagal("Kosong!.");
                    return;
                }
                data["durasi"] = kode_bayar[menu];
                data["meja"] = meja;

                $(".modal_body_menunggu").html("");
                let menunggu = document.getElementById('panel');
                let modalM = bootstrap.Modal.getOrCreateInstance(menunggu)
                modalM.hide();
                let html = spinner("Proses...");
                $(".content").html(html);
                interval_booking = setInterval(add_booking, 4000);

                return;
            }
            let kategori = data.kategori;

            if (kategori == "Ps" || kategori == "Billiard") {
                if ($(this).hasClass("open")) {
                    let harga = $(this).data("harga");
                    let meja_id = $(this).data("meja_id");
                    let html = "";
                    html += '<button type="button" data-harga="' + harga + '" data-kategori="' + kategori + '" data-meja="' + meja_id + '" data-menu="Tap" class="btn_menu btn px-5 btn-outline-success"><i class="fa-solid fa-money-check"></i> TAP</button>';
                    html += '<button type="button" data-harga="' + harga + '" data-kategori="' + kategori + '" data-meja="' + meja_id + '" data-menu="Cash" class="btn_menu ms-3 btn px-5 btn-outline-info"><i class="fa-solid fa-hand-holding-dollar"></i> Cash</button>';
                    $('.body_open').html(html);
                    let modal = document.getElementById('open');
                    let myModal = bootstrap.Modal.getOrCreateInstance(modal)
                    myModal.show();

                    $(".harga_cara_bayar").text(angka(harga, 'Rp. '));
                    return;
                } else {
                    if ($(this).hasClass("active")) {
                        gagal("Meja sedang digunakan!.");
                        return;
                    }
                }
            }

            remove_cls("btn_meja", "select", "default");
            remove_cls("btn_durasi", "select", "default");
            // isi data dan add kelas select
            if (data.meja == undefined) {
                $(this).addClass("select");
                data["meja"] = meja;
                call_durasi("durasi");
            } else if (data.meja == meja) {
                $(this).removeClass("select");
                data = {};
                data["kategori"] = kategori;
                $('.body_durasi').html("");
            } else {
                $(this).addClass("select");
                data = {};
                data["kategori"] = kategori;
                data["meja"] = meja;
                call_durasi("durasi");
            }

            call_btn_ok();
        })

        $(document).on("click", ".btn_durasi", function(e) {
            e.preventDefault();
            // semua clas select dihapus
            let durasi = $(this).data("durasi");
            let kategori = data.kategori;
            let meja = data.meja;

            remove_cls("btn_durasi", "select", "default");
            // isi data dan add kelas select
            if (data.durasi == undefined) {
                $(this).addClass("select");
                data["durasi"] = durasi;
            } else if (data.durasi == durasi) {
                $(this).removeClass("select");
                data = {};
                data["kategori"] = kategori;
                data["meja"] = meja;
            } else {
                $(this).addClass("select");
                data = {};
                data["kategori"] = kategori;
                data["meja"] = meja;
                data["durasi"] = durasi;
            }
            call_btn_ok();
        })
        $(document).on("click", ".btn_panel", function(e) {
            e.preventDefault();
            let menu = $(this).data("menu");
            call_meja(menu.toLowerCase());
        })
        $(document).on("click", ".close_modal_panel", function(e) {
            e.preventDefault();
            data = {}
            let myModal = document.getElementById('panel');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
            modal.hide();
        })

        $(document).on('click', '.btn_ok', function(e) {
            e.preventDefault();
            if (data.kategori == undefined || data.meja == undefined || data.durasi == undefined) {
                gagal("Kategori/meja/durasi belum dipilih!.");
            }

            let html = spinner("Proses...");
            $(".content").html(html);
            interval_booking = setInterval(add_booking, 4000);
        })
        $(document).on('click', '.btn_close_menunggu', function(e) {
            e.preventDefault();
            $('.body_message').html("");
            let menunggu = document.getElementById('menunggu');
            let modalM = bootstrap.Modal.getOrCreateInstance(menunggu)
            modalM.hide();
            $(this).html("");
        })

        $(document).on('click', '.btn_konfirmasi', function(e) {
            e.preventDefault();
            let order = $(this).data("order");

            if (order == "cara_bayar") {
                let order = $(this).data("data2"); //yang dibayar ps atau billiard?
                data = {
                    kategori: $(this).data("data1"),
                    harga: $(this).data("data4"),
                    durasi: kode_bayar[order],
                    meja: $(this).data("data3")
                }
                let html = spinner("Proses...");
                $(".content").html(html);
                interval_booking = setInterval(add_booking, 4000);
                let myModal = document.getElementById('open');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.hide();
                return;
            }

            if (order == "bayar_hutang_cash") {
                let uid = $(this).data("data1");
                let total = $(this).data("data2");

                let open = document.getElementById('open');
                let modalO = bootstrap.Modal.getOrCreateInstance(open)
                modalO.hide();
                $(".div_bayar_hutang_cash").html("");
                $(".div_message").html("");
                $(".div_message_hutang").html("");
                let myModal = document.getElementById('data_hutang');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();

                post('ext/bayar_hutang_cash', {
                    uid,
                    total
                }).then(res => {
                    if (res.status == "200") {
                        $(".div_message_hutang").html('<h5 class="text-success">' + res.message + '</h5>');

                    } else {
                        $(".div_message_hutang").html('<h5 class="text-danger">' + res.message + '</h5>');
                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    }
                })
            }

            if (order == "cara_absen") {
                let cara_absen = $(this).data("cara_absen");
                data["durasi"] = cara_absen;
                let html = spinner("Proses...");
                $(".content").html(html);
                hide_show_modal("open", "hide");
                interval_booking = setInterval(add_booking, 4000);
            }
        })
        $(document).on('click', '.bayar_hutang_cash', function(e) {
            e.preventDefault();
            let uid = $(".tabel_hutang").data("uid");
            let total = $(".total_hutang").data("total_hutang");
            $(".harga_cara_bayar").text(angka(total, 'Rp. '));

            let myModal = document.getElementById('data_hutang');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
            modal.hide();
            show_modal_konfirmasi("YAKIN BAYAR DENGAN <span class='fst-italic fw-bold text-warning'>Cash</span>?", "bayar_hutang_cash", uid, total);



        })

        tangan();
    </script>
</body>

</html>