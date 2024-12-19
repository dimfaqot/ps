<?php
$db = db('jadwal_2');
$meja = $db->orderBy('meja', 'ASC')->get()->getResultArray();
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul; ?></title>
    <link rel="icon" type="image/png" href="<?= base_url(); ?>logo.png" sizes="16x16">
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
            background-color: #edd000;
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

    <div class="content">
        <div class="d-flex justify-content-center gap-2 my-5">
            <?php foreach ($meja as $k => $m): ?>
                <div class="rounded-circle embos2 text-center fw-bold meja <?= ($m['is_active'] == 1 ? 'active' : 'default'); ?>" data-meja="<?= $m['meja']; ?>" data-is_active="<?= $m['is_active']; ?>" style="cursor:pointer;padding:13px 5px 5px 6px;font-size:35px;width: 85px;height:85px;color:#7c6f3e;border:1px solid #fce882">
                    <div class="text-center" style="font-size:9px;margin-bottom:-13px">MEJA</div><?= $m['meja']; ?>
                    <div class="text-center div_durasi_<?= $m['meja']; ?>" style="font-size:9px;margin-top:-5px"><?= ($m['is_active'] == 1 ? "1h 58m" : "0h 0m"); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="rounded con px-5 pt-5" style="border: 1px solid #242b32;">
            <h6 class="text-center text-light mb-5">DURASI (JAM)</h6>
            <?php for ($i = 1; $i < 10; $i++) : ?>
                <?php if ($i == 1 || $i == 4 || $i == 7): ?>
                    <div class="d-flex justify-content-center gap-5 mb-5">
                    <?php endif; ?>
                    <div class="rounded-circle embos text-center p-2 fw-bold durasi" data-durasi="<?= $i; ?>" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32"><?= $i; ?></div>
                    <?php if ($i == 3 || $i == 6 || $i == 9): ?>
                    </div>
                <?php endif; ?>
            <?php endfor; ?>
            <div class="sticky-bottom d-grid div_btn_ok">

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>

    <script>
        let data = {};
        $(document).on('click', '.meja', function(e) {
            e.preventDefault();

            let meja = $(this).data('meja');
            let is_active = $(this).data('is_active');

            if (is_active == 1) {
                gagal('Meja sedang digunakan!.');
                return;
            }
            let elem = document.querySelectorAll('.meja');
            elem.forEach(e => {

                if (e.dataset.meja == meja) {
                    if (e.classList.contains('select')) {
                        e.classList.add("default");
                        e.classList.remove("select");
                        let elm = document.querySelectorAll('.durasi');
                        elm.forEach(el => {
                            el.classList.remove("select");
                        });
                        data = {};
                    } else {
                        e.classList.add("select");
                        e.classList.remove("default");
                        data['meja'] = meja;
                    }
                } else {
                    e.classList.remove("select");
                    e.classList.add("default");
                }
            });
            show_btn_ok();
        })
        $(document).on('click', '.durasi', function(e) {
            e.preventDefault();

            let durasi = $(this).data('durasi');
            if (!data.meja) {
                gagal("Meja belum dipilih!.");
                return;
            }
            let meja = data.meja;
            let elem = document.querySelectorAll('.durasi');
            elem.forEach(e => {

                if (e.dataset.durasi == durasi) {
                    if (e.classList.contains('select')) {
                        e.classList.remove("select");
                        data = {};
                        data['meja'] = meja;
                    } else {
                        e.classList.add("select");
                        data['durasi'] = durasi;
                    }
                } else {
                    e.classList.remove("select");
                }
            });
            show_btn_ok();
        })

        const show_btn_ok = () => {
            if (data.meja && data.durasi) {
                $('.div_btn_ok').html('<button class="btn_grey embos mb-4 btn_ok">Ok</button>');
            } else {
                $('.div_btn_ok').html('');

            }
        }

        $(document).on('click', '.btn_ok', function(e) {
            e.preventDefault();
            if (!data.meja) {
                gagal('Meja belum dipilih!.');
                return;
            }
            if (!data.durasi) {
                gagal('Durasi belum dipilih!.');
                return;
            }
            data['kategori'] = "Billiard";
            post('add_booking', {
                data
            }).then(res => {
                if (res.status == "200") {
                    sukses(res.message);
                    hasil_tap(data);
                    let x = 0;
                    // setInterval(() => {
                    //     x++;
                    //     let html = '';
                    //     html += '<div class="d-flex justify-content-center" style="margin-top: 200px;">';
                    //     html += '<div class="rounded-circle embos text-center p-2 fw-bold" style="cursor:pointer;font-size:111px;width:200px;height:200px;color:#cbf4f0;border:1px solid #3c3e46">' + x + '</div>';
                    //     html += '</div>';
                    //     if (x < 621) {
                    //         $('.content').html(html);
                    //     } else {
                    //         gagal("Waktu tap habis!.");

                    //         setTimeout(() => {
                    //             location.reload();
                    //         }, 1200);
                    //     }
                    // }, 1000);
                    // hasil tap
                } else {
                    gagal(res.message);
                }
            })
        })

        const get_durasi = () => {
            post('get_durasi', {}).then(res => {
                if (res.status == "200") {
                    res.data.forEach(e => {
                        $('.div_durasi_' + e.meja).text(e.durasi);
                    })
                } else {
                    gagal(res.message);
                }
            })
        }

        setInterval(() => {
            get_durasi();
        }, 5000);

        const hasil_tap = (data) => {
            setInterval(() => {
                post('hasil_tap', {
                    data
                }).then(res => {
                    if (res.status == "200") {
                        sukses(res.message);
                        setTimeout(() => {
                            let ht = '';
                            ht += '<div style="margin-top: 250px;">';
                            ht += '<h6 class="text-center" style="color: aliceblue;">Saldo</h6>';
                            ht += '<h5 class="text-center" style="color: aliceblue;">' + res.data + '</h5>';
                            ht += '</div>';
                            $('.content').html(ht);
                        }, 1000);

                        setTimeout(() => {
                            location.reload();
                        }, 3000);
                    } else {
                        let ht = '';
                        ht += '<div style="margin-top: 250px;">';
                        ht += '<h6 class="text-center" style="color: aliceblue;">' + res.message + '</h6>';
                        ht += '<h5 class="text-center" style="color: aliceblue;">.......</h5>';
                        ht += '</div>';
                        $('.content').html(ht);
                    }
                })
            }, 3200);
        }
    </script>
</body>

</html>