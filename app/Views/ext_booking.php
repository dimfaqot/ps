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
            background-color: #00ed64;
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
    </style>
</head>

<body style="background-color: #23252d;">
    <!-- warning alert message -->
    <div class="box_warning" style="position:fixed;z-index:999999;display:none;"></div>



    <div class="container mt-5">
        <?= view('booking/top'); ?>
    </div>
    <div class="messages"></div>
    <div class="content">

        <div class="container">
            <?= view('booking/date_time'); ?>
            <?= view('booking/billiard'); ?>
            <?= view('booking/ps'); ?>
            <?= view('booking/kantin'); ?>
            <?= view('booking/durasi'); ?>
            <?= view('booking/topup'); ?>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script>
        let data = {};

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


        $(document).on('click', '.btn_topup', function(e) {
            e.preventDefault();
            let menu = $(this).data('menu');
            remove_cls('meja', 'select', 'default');
            remove_cls('durasi', 'select', 'default');
            remove_cls('btn_menu', 'select', 'default');
            if (data.kategori == menu) {
                $(this).removeClass("select rounded-circle");
                $('.date_time').removeClass('d-none');
                $('.target_topup').addClass('d-none');
                data = {};
            } else {
                $(this).addClass("select rounded-circle");
                $('.date_time').addClass('d-none');
                $('.target_topup').removeClass('d-none');
                data['kategori'] = menu;
            }

            $('.target_durasi').addClass('d-none');
            $('.target_menu').addClass('d-none');
        })


        $(document).on('click', '.btn_menu', function(e) {
            e.preventDefault();


            let menu = $(this).data('menu');

            $(".durasi").removeClass("d-none");
            $(".target_durasi").addClass("d-none");

            $(".date_time").addClass("d-none"); //hide date time


            if (data.kategori == undefined) {
                let elem = document.querySelectorAll('.target_menu');
                elem.forEach(e => {
                    if (e.dataset.target_menu == menu) {
                        e.classList.remove("d-none");
                    } else {
                        e.classList.add("d-none");
                    }
                });
                $(this).addClass("select");
                data['kategori'] = menu;
            } else {
                if (data.kategori == menu) {
                    remove_cls("meja", "select", "default");
                    remove_cls("durasi", "select", "default");
                    $(".target_menu").addClass("d-none");
                    $(".target_durasi").addClass("d-none");

                    $(".date_time").removeClass("d-none");
                    $(this).removeClass("select");

                    data = {};
                } else {
                    remove_cls('btn_menu', "select", "default");
                    $(this).addClass("select");

                    let elem = document.querySelectorAll('.target_menu');
                    elem.forEach(e => {
                        if (e.dataset.target_menu == menu) {
                            e.classList.remove("d-none");
                        } else {
                            e.classList.add("d-none");
                        }
                    });
                    remove_cls('meja', 'select', 'default');
                    remove_cls('durasi', 'select', 'default');
                    $(".target_durasi").addClass("d-none");
                    $(".target_topup").addClass("d-none");
                    data = {};
                    data['kategori'] = menu;
                }
            }

            call_durasi();

        })


        $(document).on('click', '.meja', function(e) {
            e.preventDefault();

            let meja = $(this).data('meja');
            let kategori = data.kategori;
            if (kategori == undefined) {
                gagal("Kategori belum dipilih!.");
                return;
            }
            if ($(this).hasClass("active")) {
                gagal('Meja sedang digunakan!.');
                return;
            }
            if (!data.kategori) {
                gagal('Kategori belum dipilih!.');
                return;
            }

            if (data.meja == undefined) {
                remove_cls("meja", "select", "default");
                $(this).addClass("select");
                $(".target_durasi").removeClass("d-none");
                data.meja = meja;
            } else {
                if (data.meja == meja) {
                    remove_cls("meja", "select", "default");
                    remove_cls("durasi", "select", "default");
                    $(".target_durasi").addClass("d-none");
                    data = {};
                    data['kategori'] = kategori;
                } else {
                    remove_cls("meja", "select", "default");
                    remove_cls("durasi", "select", "default");
                    $(this).addClass("select");
                    data = {};
                    data['kategori'] = kategori;
                    data.meja = meja;
                }
            }
            show_btn_ok();
        })
        $(document).on('click', '.durasi', function(e) {
            e.preventDefault();

            let durasi = $(this).data('durasi');
            let meja = data.meja;
            let kategori = data.kategori;
            if (meja == undefined && kategori !== "Topup") {
                gagal("Meja belum dipilih!.");
                return;
            }
            if (kategori == undefined) {
                gagal("Kategori belum dipilih!.");
                return;
            }

            if (data.durasi == undefined) {
                remove_cls("durasi", "select", "default");
                $(this).addClass("select");
                data.durasi = durasi;
            } else {
                if (data.durasi == durasi) {
                    remove_cls("durasi", "select", "default");
                    data = {};
                    data['kategori'] = kategori;
                    data['meja'] = meja;
                } else {
                    remove_cls("durasi", "select", "default");
                    $(this).addClass("select");
                    data.durasi = durasi;
                }
            }

            show_btn_ok();
        })


        const show_btn_ok = () => {
            if (data.kategori == "Topup") {
                if (data.durasi && data.kategori) {
                    $('.div_btn_ok').html('<button class="btn_grey embos mb-4 btn_ok">Ok</button>');
                } else {
                    $('.div_btn_ok').html('');
                }
            } else {
                if (data.meja && data.durasi && data.kategori) {
                    $('.div_btn_ok').html('<button class="btn_grey embos mb-4 btn_ok">Ok</button>');
                } else {
                    $('.div_btn_ok').html('');

                }
            }
        }

        $(document).on('click', '.btn_ok', function(e) {
            e.preventDefault();
            if (!data.kategori) {
                gagal('Kategori belum dipilih!.');
                return;
            }
            if (!data.meja && data.kategori !== 'Topup') {
                gagal('Meja belum dipilih!.');
                return;
            }
            if (!data.durasi) {
                gagal('Durasi belum dipilih!.');
                return;
            }

            post('add_booking', {
                data
            }).then(res => {
                if (res.status == "200") {
                    sukses(res.message);
                    hasil_tap(data);
                    let x = 0;
                    setInterval(() => {
                        x++;
                        let html = '';
                        html += '<div class="d-flex justify-content-center" style="margin-top: 200px;">';
                        html += '<div class="rounded-circle embos text-center p-2 fw-bold" style="cursor:pointer;font-size:111px;width:200px;height:200px;color:#cbf4f0;border:1px solid #3c3e46">' + x + '</div>';
                        html += '</div>';
                        if (x < 21) {
                            $('.content').html(html);
                        } else {
                            gagal("Waktu tap habis!.");

                            setTimeout(() => {
                                location.reload();
                            }, 1200);
                        }
                    }, 1000);
                } else {
                    gagal(res.message);
                }
            })
        })

        const get_durasi = (kategori) => {
            post('get_durasi', {
                kategori
            }).then(res => {
                if (res.status == "200") {
                    let elem = document.querySelectorAll('.body_content');
                    if (res.data.length <= 0) {
                        console.log(res.data.length);
                        elem.forEach(elm => {
                            if (elm.classList.contains("active")) {
                                elm.classList.remove("active");
                                elm.classList.add("default");
                            }
                            $(".div_durasi_" + elm.dataset.meja).text("0h 0m");

                        });
                    } else {
                        res.data.forEach(e => {
                            elem.forEach(el => {
                                let meja = el.dataset.meja;
                                if (meja == e.meja) {
                                    el.classList.remove("default");
                                    el.classList.add("active");
                                    $(".div_durasi_" + e.meja).text(e.durasi);
                                } else {
                                    el.classList.remove("active");
                                    el.classList.add("default");
                                    $(".div_durasi_" + el.dataset.meja).text("0h 0m");
                                }
                            });
                        })

                    }

                } else {
                    gagal(res.message);
                }
            })
        }



        const call_durasi = () => {
            let counter = 0;
            let intervalId = setInterval(() => {
                counter++;
                let kategori = data.kategori;
                if (kategori && kategori !== "Kantin") {
                    get_durasi(kategori);
                }
                if (!kategori) {
                    clearInterval(intervalId);
                    console.log('Interval stopped after 5 iterations.');
                }
            }, 1000);
        }


        const hasil_tap = (data) => {
            setInterval(() => {
                post('hasil_tap', {
                    data
                }).then(res => {
                    if (res.status == "200") {

                        if (res.data == null) {

                            let htmy = "";
                            htmy += '<div style="margin-top: 250px;">';
                            htmy += '<h6 class="text-center text-light">Segera tap kartu...!</h6>';
                            htmy += '</div>';
                            $('.messages').html(htmy);

                            setTimeout(() => {
                                let htmx = "";
                                htmx += '<div style="margin-top: 250px;">';
                                htmx += '<h6 class="text-center text-light">Segera tap kartu...!</h6>';
                                htmx += '</div>';
                                $('.messages').html(htmx);
                            }, 2000);
                        } else {
                            let html = '';
                            let status = res.data.status;
                            html += '<div style="margin-top: 250px;">';
                            html += '<h6 class="text-center ' + (status == "400" ? "text-danger" : "text-light") + '">' + res.data.message + '</h6>';
                            if (res.data.message_2 !== "") {
                                html += '<h5 class="text-center ' + (status == "400" ? "text-danger" : "text-light") + '">' + res.data.message_2 + '</h5>';
                            }
                            html += '</div>';
                            $('.messages').html(html);

                            setTimeout(() => {
                                location.reload();
                            }, 7000);
                        }


                    }
                })
            }, 1000);
        }

        hasil_tap();
    </script>
</body>

</html>