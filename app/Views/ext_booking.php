<?php
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
            <div class="d-flex justify-content-center div_menu"></div>
            <div class="body_meja mt-4"></div>
            <div class="body_durasi"></div>
            <div class="body_btn_ok d-grid"></div>
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
                    <div class="div_judul_menunggu px-3 border-bottom border-warning text-warning" style="margin-top:-300px;margin-bottom:300px"></div>
                </div>
                <div class="modal-body text-center modal_body_menunggu" style="margin-top: -150px;">

                </div>
            </div>
        </div>
    </div>

    <!-- modal hutang-->
    <div class="modal fade bg-dark" id="data_hutang" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" style="background-color: transparent;border:none">
                <div class="modal-body text-center">
                    <div class="d-flex justify-content-center gap-2 py-2 rounded border border-info">
                        <div class="embos countdown text-center mt-1" style="color:#cbf4f0;width:30px;height:30px;font-size:16px;border-radius:50%;;border:1px solid #3c3e46"></div>
                        <div class="text-light div_message_hutang" style="font-size: x-large;">
                            TAP UNTUK MELUNASI
                        </div>
                    </div>

                    <div class="body_message my-2 p-1" style="border:1px dashed white">
                        <div class="d-flex gap-2 justify-content-center text-light">
                            <div class="spinner-border spinner-border-sm mt-1 text-light" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <div style="font-style: italic;">processing...</div>
                        </div>
                    </div>
                    <div style="text-align: left;" class="total_hutang fw-bold text-warning"></div>
                    <div class="modal_body_data_hutang mt-2">

                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

        <script>
            // let myModal = document.getElementById('menunggu');
            // let modal = bootstrap.Modal.getOrCreateInstance(myModal)
            // modal.show();
            let data = {};
            let data_ps = <?= json_encode($ps); ?>;
            let html_ps = "";
            let data_billiard = <?= json_encode($billiard); ?>;
            let html_billiard = "";
            data_ps.forEach((e, i) => {
                if (i % 4 == 0) {
                    html_ps += '<div class="d-flex justify-content-center gap-2 my-2">';
                }
                html_ps += '<div class="rounded-circle embos2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'active' : 'default') + '" data-meja="' + e.meja + '" data-is_active="' + e.is_active + '" style="cursor:pointer;padding:13px 5px 5px 6px;font-size:35px;width: 85px;height:85px;color:#7c6f3e;border:1px solid #fce882">';
                html_ps += '<div class="text-center" style="font-size:9px;margin-bottom:-4px">MEJA</div>' + e.meja;
                html_ps += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px"></div>';
                html_ps += '</div>';
                if (i % 4 == 3) {
                    html_ps += '</div>';
                }
            })
            data_billiard.forEach((e, i) => {
                if (i % 4 == 0) {
                    html_billiard += '<div class="d-flex justify-content-center gap-2 my-2">';
                }
                html_billiard += '<div class="rounded-circle embos2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'active' : 'default') + '" data-meja="' + e.meja + '" data-is_active="' + e.is_active + '" style="cursor:pointer;padding:13px 5px 5px 6px;font-size:35px;width: 85px;height:85px;color:#7c6f3e;border:1px solid #fce882">';
                html_billiard += '<div class="text-center" style="font-size:9px;margin-bottom:-4px">MEJA</div>' + e.meja;
                html_billiard += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px"></div>';
                html_billiard += '</div>';
                if (i % 4 == 3) {
                    html_billiard += '</div>';
                }
            })

            const menus = {
                member: `<span class="pe-2 tangan" data-menu="admin" style="font-size: 27px;margin-top:-12px"><i class="fa-regular fa-hand-point-right"></i></span>
                    <div class="text-center text-info ms-2">
                        <span style="cursor: pointer;" data-menu="Absen" class="btn_menu py-2 px-4 border rounded border-info">ABSEN</span>
                        <span style="cursor: pointer;" data-menu="Ps" class="btn_menu py-2 px-4 rounded border border-info">PS</span>
                        <span style="cursor: pointer;" data-menu="Billiard" class="btn_menu py-2 px-4 rounded border border-info">BILLIARD</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Hutang" class="btn_menu py-2 px-4 rounded border border-info">HUTANG</span>
                        <span style="cursor: pointer;" data-menu="Saldo" class="btn_menu py-2 px-4 rounded border border-info">SALDO</span>
                    </div>`,
                admin: `<span class="pe-2 tangan" data-menu="member" style="font-size: 27px;margin-top:-12px"><i class="fa-regular fa-hand-point-right"></i></span>
                    <div class="text-center text-info ms-2">
                        <span style="cursor: pointer;" data-menu="Daftar" class="btn_menu py-2 px-4 border rounded border-info">DAFTAR</span>
                        <span style="cursor: pointer;" data-menu="Remove" class="btn_menu py-2 px-4 rounded border border-info">REMOVE</span>
                        <span style="cursor: pointer;" data-menu="Topup" class="btn_menu py-2 px-4 rounded border border-info">TOPUP</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Add" class="btn_menu py-2 px-4 rounded border border-info">ADD</span>
                        <span style="cursor: pointer;" data-menu="Delete" class="btn_menu py-2 px-4 rounded border border-info">DELETE</span>
                    </div>`,
                topup: `<div class="rounded px-4 pt-4">
                    <h6 class="text-center text-light mb-4">TOPUP</h6>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="1" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">1</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="2" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">2</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="3" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">3</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="4" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">4</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="5" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">5</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="6" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">6</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="10" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">10</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="20" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">20</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="30" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">30</div>
                    </div>
                    </div>`,
                durasi: `<div class="rounded px-4 pt-4 mt-2">
                    <h6 class="text-center text-light mb-4">DURASI (JAM)</h6>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="1" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">1</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="2" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">2</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="3" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">3</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="4" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">4</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="5" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">5</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="6" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">6</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="7" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">7</div>
                    <div class="rounded-circle embos text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="8" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">8</div>
                    <div class="rounded-circle embos text-center p-2 fw-bold btn_durasi" data-durasi="9" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">9</div>
                    </div>
                    </div>`,
                meja_ps: html_ps,
                meja_billiard: html_billiard,
                btn_ok: '<button class="btn_grey embos mb-4 btn_ok">Ok</button>'
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


            const show_data_hutang = (list_data_hutang) => {
                let html = "";

                html += '<table style="font-size: 13px;" class="table table-sm text-light table-bordered border-info">';
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

                let menunggu = document.getElementById('menunggu');
                let modalM = bootstrap.Modal.getOrCreateInstance(menunggu)
                modalM.hide();
                $(".modal_body_data_hutang").html(html);
                let myModal = document.getElementById('data_hutang');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();

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
                html += '<div class="embos d-none p-1 countdown" style="color:#cbf4f0;width:200px;height:200px;font-size:118px;border-radius:50%;;border:1px solid #3c3e46"></div>';
                html += '</div>';
                $(".modal_body_menunggu").html(html);
                let myModal = document.getElementById('menunggu');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
            }


            let interval_blink_message = "";
            const blink_message = () => {
                if ($(".div_message_hutang").hasClass("text-light")) {
                    console.log("ada");
                    $(".div_message_hutang").removeClass("text-light");
                    $(".div_message_hutang").addClass("text-info");
                } else {
                    console.log("tidak ada");
                    $(".div_message_hutang").removeClass("text-info");
                    $(".div_message_hutang").addClass("text-light");
                }
            }

            let interval_booking = "";
            const add_booking = () => {

                post('add_booking', {
                    data
                }).then(res => {
                    if (res.status == "200") {
                        clearInterval(interval_booking); //antri booking
                        message_server(data); //pesan dari server
                        countdown(res); //durasi waktu proses
                        if (modal_show("menunggu") == 0) {
                            menunggu('<h5 class="text-light">Menunggu tap/finger...</h5>');
                        } else {
                            $(".body_message").html('<h5 class="text-light">Menunggu respon user...</h5>');
                        }

                        // jika hutang maka memanggil data hutang
                        if (data.kategori == "Hutang") {
                            ingterval_hutang = setInterval(get_data_hutang, 1000); //memanggil data hutang
                        } else {
                            clearInterval(ingterval_hutang); //antri booking
                        }
                    } else {
                        if (res.data == 1) {
                            if (modal_show("menunggu") == 0) {
                                menunggu('<h5 class="text-danger">' + res.message + '</h5>');
                            }
                        } else {
                            gagal(res.message);
                        }
                    }
                })
            }

            let ingterval_hutang = "";
            const get_data_hutang = () => {
                post("ext/data_hutang", {
                    id: 0
                }).then(res => {
                    if (res.status == "200") {
                        if (res.data != null) {
                            show_data_hutang(res.data);
                            interval_blink_message = setInterval(blink_message, 1000);
                        }
                        clearInterval(ingterval_hutang);
                    } else {
                        $(".div_message_hutang").addClass("text-info");
                        clearInterval(interval_blink_message);
                    }
                })

            }

            let interval_durasi = "";
            const get_durasi = () => {
                if (data.kategori == "Ps" || data.kategori == "Billiard") {
                    post('get_durasi', {
                        kategori: data.kategori.toLowerCase()
                    }).then(res => {
                        if (res.status == "200") {
                            let btn_meja = document.querySelectorAll(".btn_meja");
                            let durasi_active = [];
                            let index_active = [];
                            res.data.forEach(e => {
                                btn_meja.forEach((elem, i) => {
                                    if (elem.dataset.meja == e.meja) {
                                        index_active.push(i);
                                        durasi_active.push(e.durasi);
                                    }
                                })
                            })

                            for (let i = 0; i < btn_meja.length; i++) {
                                let x = 0;
                                if (index_active.includes(i)) {
                                    $(".btn_meja_" + btn_meja[i].dataset.meja).removeClass("default");
                                    $(".btn_meja_" + btn_meja[i].dataset.meja).addClass("active");
                                    $(".div_durasi_" + btn_meja[i].dataset.meja).text(durasi_active[x++]);
                                } else {
                                    $(".btn_meja_" + btn_meja[i].dataset.meja).addClass("default");
                                    $(".btn_meja_" + btn_meja[i].dataset.meja).removeClass("active");
                                    $(".div_durasi_" + btn_meja[i].dataset.meja).text("Available");
                                }

                            }


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
                    $(".tangan").removeClass("text-danger");
                    $(".tangan").addClass("text-primary");
                }, 500);
                setInterval(() => {
                    $(".tangan").addClass("text-danger");
                    $(".tangan").removeClass("text-primary");
                }, 1000);
            }

            tangan();

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
                $(".body_meja").html(html);
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

            let countdown = (res) => {
                sukses(res.message);
                let x = 0;
                let limit = 21;
                if (data.kategori == "Hutang") {
                    limit = 61;
                }
                setInterval(() => {
                    x++;

                    if (x < limit) {
                        $(".countdown").text(x);
                        $(".countdown").removeClass("d-none");
                    } else {
                        post("del_message", {
                            id: 0
                        }).then(rest => {
                            if (rest.status == "200") {
                                $(".body_message").html('<h5 class="text-danger">Waktu habis!.</h5>');
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
                            }
                        })

                    }
                }, 1000);
            }

            const message_server = (data) => {
                setInterval(() => {
                    post('message_server', {
                        data
                    }).then(res => {
                        if (res.status == "200") {
                            if (res.data != null) {
                                let html = '';
                                let status = res.data.status;

                                html += '<h6 class="text-center ' + (status == "200" ? "text-light" : "text-danger") + '">' + res.data.message + '</h6>';
                                if (res.data.uang !== "") {
                                    html += '<h5 class="text-center ' + (status == "200" ? "text-light" : "text-danger") + '">' + res.data.uang + '</h5>';
                                }

                                $('.body_message').html(html);

                                if (status == "end" || status == "400") {
                                    setTimeout(() => {
                                        post("del_message", {
                                            id: 0
                                        }).then(rest => {
                                            if (rest.status == "200") {
                                                location.reload();
                                            }
                                        })
                                    }, 3000);

                                }
                            }


                        }
                    })
                }, 1000);
            }


            $(document).on("click", ".tangan", function(e) {
                e.preventDefault();
                let menu = $(this).data("menu");
                call_main_menu(menu);

                call_btn_ok();
            })
            // URUTAN PROSES
            // memanggil interval_booking = setInterval(add_booking, 1000); untuk antrian memasukkan ke table booking
            // di fungsi add booking memanggil pesan dari server(message_server), hitung mundur durasi (countdown)
            // fungsi add booking juga menghentikan setinterval clearInterval(interval_booking) jika status return 200
            // di .btn_men menu yang eksekusinya dengan tombol ok maka interval_booking = setInterval(add_booking, 1000); dipanggil pada saat btn_ok diklik
            // menu Saldo dan Hutang tanpa melalui btn_ok maka interval_booking = setInterval(add_booking, 1000) langsung dipanggil
            $(document).on("click", ".btn_menu", function(e) {
                e.preventDefault();
                $(".date_time").addClass("d-none");
                // semua clas select dihapus
                remove_cls("btn_menu", "select");
                let menu = $(this).data("menu");
                // mengisi jusul untuk modal menunggu
                $(".div_judul_menunggu").text(menu.toUpperCase());
                if (menu == "Saldo" || menu == "Absen" || menu == "Hutang") {
                    $(this).addClass("select");
                    data = {
                        kategori: menu,
                        durasi: 0,
                        meja: 0
                    }
                    interval_booking = setInterval(add_booking, 1000);
                    return;
                }
                // if (menu == "Absen") {
                //     $(this).addClass("select");
                //     data = {
                //         kategori: menu,
                //         durasi: 0,
                //         meja: 0
                //     }
                //     interval_booking = setInterval(add_booking, 1000);
                //     return;
                // }
                // if (menu == "Hutang") {
                //     $(this).addClass("select");
                //     data = {
                //         kategori: menu,
                //         durasi: 0,
                //         meja: 0
                //     }
                //     interval_booking = setInterval(add_booking, 1000);
                //     return;
                // }
                if (menu == "Daftar" || menu == "Add" || menu == "Remove" || menu == "Delete") {
                    $(this).addClass("select");
                    show_search_db(menu);
                    $(".btn_close_menunggu").html('<div class="mb-5 text-danger" style="font-size:x-large;margin-top:-100px"><i class="fa-solid fa-circle-xmark"></i></div>');
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

                if (menu == "Topup") {
                    call_durasi(menu.toLowerCase());
                    // data["meja"] = 0;
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

                call_btn_ok();
            })

            $(document).on("click", ".btn_meja", function(e) {
                e.preventDefault();
                // semua clas select dihapus
                let meja = $(this).data("meja");
                let kategori = data.kategori;

                if (kategori == "Ps" || kategori == "Billiard") {
                    if ($(this).hasClass("active")) {
                        gagal("Meja sedang digunakan!.");
                        return;
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

            $(document).on('click', '.btn_ok', function(e) {
                e.preventDefault();
                if (data.kategori == undefined || data.meja == undefined || data.durasi == undefined) {
                    gagal("Kategori/meja/durasi belum dipilih!.");
                }

                $('.content').html("");
                interval_booking = setInterval(add_booking, 1000);
            })
            $(document).on('click', '.btn_close_menunggu', function(e) {
                e.preventDefault();
                $('.body_message').html("");
                let menunggu = document.getElementById('menunggu');
                let modalM = bootstrap.Modal.getOrCreateInstance(menunggu)
                modalM.hide();
                $(this).html("");
            })
        </script>
</body>

</html>