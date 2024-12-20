<?= $this->extend('logged') ?>

<?= $this->section('content') ?>


<div class="d-block d-md-none d-sm-block mb-1" style="margin-top: -10px;">
    <a type="button" class="text_primary" data-bs-toggle="offcanvas" data-bs-target="#navigation_sm" style="font-size: x-small;" aria-controls="offcanvasWithBothOptions"><i style="font-size: large;" class="fa-solid fa-compass"></i></a>
</div>
<div class="container">
    <div class="row g-2">
        <?php foreach ($data as $i) : ?>
            <?php
            $rental = get_rental($i);
            $border = ($i['status'] == 'Available' ? 'soal_yakin' : ($i['status'] == 'Booked' ? 'soal_ragu' : ($i['status'] == 'Maintenance' ? 'soal_error' : 'soal_belum_dijawab')));
            $bg = ($i['status'] == 'Available' ? 'btn_success' : ($i['status'] == 'Booked' ? 'btn_warning' : ($i['status'] == 'Maintenance' ? 'btn_danger' : 'btn_grey')));
            ?>
            <div class="col-md-4 col_<?= $i['id']; ?>">
                <div id="card_<?= $i['id']; ?>" class="<?= $border; ?> card_<?= $i['id']; ?> modal_card_<?= $i['id']; ?>" style="border-radius: 5px;">
                    <div class="d-flex gap-2">
                        <div class="bg_warning_light p-3 flex-fill" style="width: 60%;">
                            <div class="d-flex justify-content-between gap-3 bg_main_bright p-2" style="border-radius: 5px;">
                                <h6><?= $i['unit']; ?></h6>
                                <div class="<?= $bg; ?>"><?= $i['status']; ?></div>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <div>
                                    <a class="description" data-value="<?= $i['desc']; ?>" data-kode_harga="<?= $i['kode_harga']; ?>" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-circle-exclamation"></i></a>
                                </div>
                                <div>
                                    <p class="fw-bold" style="font-size: small;"><i class="fa-brands fa-playstation"></i> <?= strtoupper($i['kode_harga']); ?></p>
                                </div>
                            </div>

                            <div class="d-flex gap-2">
                                <select id="select_durasi_<?= $i['id']; ?>" class="form-select form-select-sm durasi_<?= $i['id']; ?>" <?= ($i['status'] == 'Maintenance' ? 'disabled' : 'data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Duration in minutes"'); ?>>
                                    <option <?= ($rental ? 'selected' : ''); ?> value="-">-</option>
                                    <?php for ($d = 1; $d < 20; $d++) : ?>
                                        <?php $durasi = 30 * $d; ?>
                                        <option <?= ($rental ? ($rental['durasi'] == $durasi ? 'selected' : '') : ''); ?> value="<?= $durasi; ?>"><?= $durasi; ?></option>
                                    <?php endfor; ?>
                                    <option <?= ($rental ? ($rental['durasi'] == -1 ? 'selected' : '') : ''); ?> value="-1">Loss Dol</option>
                                </select>
                                <button <?= ($i['status'] == 'Maintenance' ? 'disabled' : 'data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Reset"'); ?> data-id="<?= $i['id']; ?>" data-url="reset_play" class="btn_grey btn_rental" data-metode="<?= ($rental && $rental['metode'] == 'Tap' ? 'Tap' : 'Cash'); ?>"><i style="font-size:medium" class="fa-solid fa-rotate"></i></button>
                                <button <?= ($i['status'] == 'Maintenance' ? 'disabled' : ($rental ? ($rental['durasi'] == -1 ? 'disabled' : 'data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Start/update/add"') : 'data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Start/update/add"')); ?> data-id="<?= $i['id']; ?>" data-url="start_play" class="btn_primary btn_rental" data-metode="<?= ($rental && $rental['metode'] == 'Tap' ? 'Tap' : 'Cash'); ?>"><i style="font-size:medium" class="fa-solid fa-circle-play"></i></button>
                                <button <?= ($i['status'] == 'Maintenance' ? 'disabled' : 'data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="End/payment"'); ?> data-id="<?= $i['id']; ?>" data-url="end_play" class="btn_purple btn_rental" data-metode="<?= ($rental && $rental['metode'] == 'Tap' ? 'Tap' : 'Cash'); ?>"><i style="font-size:medium" class="fa-solid fa-circle-stop"></i></button>
                            </div>

                        </div>

                        <div class="flex-fill my-auto" style="width: 40%;">
                            <div style="font-size:50px;text-align:center">
                                <div class="audio_<?= $i['id']; ?>" style="font-size: small;"></div>
                                <div data-dari="<?= ($rental ? $rental['dari'] * 1000 : 0); ?>" data-ke="<?= ($rental ? ($rental['ke'] == -1 ? -1 : $rental['ke'] * 1000) : 0); ?>" class="body_countdown_<?= $i['id']; ?>"></div>
                                <div class="minutes_<?= $i['id']; ?>" style="font-size: small;">Minutes left</div>
                                <div style="font-size: small;" class="harga_sewa_<?= $i['id']; ?>"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- canvas left md-->
<div class="d-none d-md-block">
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" style="z-index:9999999;max-width:50px;" tabindex="-1" id="navigation" aria-labelledby="offcanvasRightLabel">

        <div class="offcanvas-body canvas_navigation_md p-1">
            <!-- <div type="button" data-on_off="1" class="bg_light border_dark mb-2 mute_audio" style="border-radius: 5px;text-align:center;cursor:pointer"><i class="text_success fa-solid fa-volume-low"></i></div> -->
            <?php foreach ($data as $k => $i) : ?>
                <?php
                $rental = get_rental($i);
                $border = ($i['status'] == 'Available' ? 'soal_yakin' : ($i['status'] == 'Booked' ? 'soal_ragu' : ($i['status'] == 'Maintenance' ? 'soal_error' : 'soal_belum_dijawab')));
                $bg = ($i['status'] == 'Available' ? 'btn_success' : ($i['status'] == 'Booked' ? 'btn_warning' : ($i['status'] == 'Maintenance' ? 'btn_danger' : 'btn_grey')));
                ?>
                <div data-id="<?= $i['id']; ?>" type="button" class="<?= $border; ?> detail_rental card_<?= $i['id']; ?> mb-2" style="border-radius: 5px;text-align:center;cursor:pointer"><?= $k + 1; ?></div>
            <?php endforeach; ?>

        </div>
    </div>
</div>

<!-- canvas left sm-->
<div class="d-block d-md-none d-sm-block mb-1">
    <div class="offcanvas offcanvas-start" data-bs-scroll="true" data-bs-backdrop="false" style="z-index:9999999;max-width:50px;" tabindex="-1" id="navigation_sm" aria-labelledby="offcanvasRightLabel">

        <div class="offcanvas-body p-1">
            <div type="button" class="mb-2 text_danger" style="border-radius: 5px;text-align:center;cursor:pointer;font-size:medium" data-bs-dismiss="offcanvas"><i class="fa-solid fa-circle-xmark"></i></div>
            <?php foreach ($data as $k => $i) : ?>
                <?php
                $rental = get_rental($i);
                $border = ($i['status'] == 'Available' ? 'soal_yakin' : ($i['status'] == 'Booked' ? 'soal_ragu' : ($i['status'] == 'Maintenance' ? 'soal_error' : 'soal_belum_dijawab')));
                $bg = ($i['status'] == 'Available' ? 'btn_success' : ($i['status'] == 'Booked' ? 'btn_warning' : ($i['status'] == 'Maintenance' ? 'btn_danger' : 'btn_grey')));
                ?>
                <div data-id="<?= $i['id']; ?>" type="button" class="<?= $border; ?> detail_rental card_<?= $i['id']; ?> mb-2" style="border-radius: 5px;text-align:center;cursor:pointer"><?= $k + 1; ?></div>
            <?php endforeach; ?>

        </div>
    </div>
</div>


<!-- modal detail rental -->
<div class="modal fade" style="margin-top: 150px;" id="detail_rental" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_detail_rental">

            </div>
        </div>
    </div>
</div>

<!-- modal detail description -->
<div class="modal fade" style="margin-top: 150px;" id="description" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_description">

            </div>
        </div>
    </div>
</div>
<!-- modal total harga -->
<div class="modal fade" style="margin-top: 150px;" id="total_harga" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="d-flex justify-content-between p-2 bg_danger">
                <div class="fw-bold text_light"><i class="fa-solid fa-cash-register"></i> PEMBAYARAN</div>
                <div class="bg_light" style="border-radius: 50%;padding:1.2px 4px"><a href="" class="btn_close_modal_pembayaran" data-bs-dismiss="modal"><i class="fa-solid fa-circle-xmark text_danger"></i></a></div>
            </div>
            <div class="modal-body body_total_harga">

            </div>
        </div>
    </div>
</div>
<?= view('speech_js'); ?>
<script>
    const bsOffcanvas = new bootstrap.Offcanvas('#navigation');
    bsOffcanvas.show();
    let harga_sewa = <?= json_encode(get_harga_rental()); ?>;
    const menghitung_harga = (menit, harga) => {
        let h = Math.floor(menit / 60);
        let m = menit % 60;
        let q = Math.floor(m / 15);
        let sisa = m % 15;;

        let hrg = (h * harga) + ((harga / 4) * q) + ((Math.floor(harga / 60) * sisa));
        let data = {
            harga: hrg,
            durasi: h + ' jam ' + m + ' menit'
        }
        return data;
    }

    const countdown = (data, cls) => {

        // Update the count down every 1 second
        let detik = 0;
        let x = setInterval(function() {

            let now = new Date().getTime();
            // Find the distance between now and the count down date
            let distance = (data.ke == -1 ? now - data.dari : data.ke - now);
            // Time calculations for days, hours, minutes and seconds
            // let days = Math.floor(distance / (1000 * 60 * 60 * 24));
            // let hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            // let minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            // let seconds = Math.floor((distance % (1000 * 60)) / 1000);

            let durasi_now = Math.floor(distance / (1000 * 60));

            let sisa = Math.floor((now - data.dari) / (1000 * 60))
            // If the count down is over, write some text 




            if (data.status == 'In Game') {
                if (data.ke == -1) {
                    $(cls).text(durasi_now);
                    if (data.status == 'In Game') {
                        $('.card_' + data.id).removeClass('soal_yakin');
                        $('.card_' + data.id).removeClass('soal_belum_dijawab');
                        $('.card_' + data.id).removeClass('soal_error');
                        $('.card_' + data.id).removeClass('soal_ragu');
                        $('.card_' + data.id).addClass('border_primary');
                    }
                } else if (data.ke != -1) {
                    $(cls).text(durasi_now);
                    if (durasi_now <= 0) {
                        // clearInterval(x);
                        $(cls).text("0");
                        if (localStorage.getItem(data.id) == 0 && data.status == "In Game") {
                            speak('Perhatian!. Waktu ' + data.unit + ' sudah habis!.');
                        }
                    } else if (durasi_now > 0 && durasi_now <= 10) {

                        // clearInterval(x);
                        if (data.status == 'In Game') {
                            $('.card_' + data.id).removeClass('soal_belum_dijawab');
                            $('.card_' + data.id).removeClass((detik / 2 == 0 ? 'soal_error' : 'soal_ragu'));
                            $('.card_' + data.id).addClass((detik / 2 == 0 ? 'soal_ragu' : 'soal_error'));

                        }
                        if (localStorage.getItem(data.id) == 0 && data.status == "In Game") {
                            speak('Perhatian!. Waktu ' + data.unit + ' tinggal ' + durasi_now + ' menit!.');
                        }
                    }

                }

                let sewa;
                let biaya;
                harga_sewa.forEach(e => {
                    if (e.nama_setting == data.kode_harga) {
                        biaya = e.biaya;
                        if (data.ke == -1) {
                            sewa = menghitung_harga(durasi_now, e.value_int);
                            $('.minutes_' + data.id).text('Minutes Playing');
                        } else {
                            if (data.ke < now) {
                                sewa = menghitung_harga(data.durasi, e.value_int);
                            } else {
                                sewa = menghitung_harga(sisa, e.value_int);
                            }
                        }
                    }
                })
                $('.harga_sewa_' + data.id).text(angka(sewa.harga, 'Rp'));
            } else {
                $(cls).text("0");
                $('.harga_sewa_' + data.id).text(angka(0, 'Rp'));
            }

            if (durasi_now <= 10) {
                // clearInterval(x);
                if (data.status == 'In Game') {
                    detik++;
                    $('.card_' + data.id).removeClass('soal_belum_dijawab');

                    if (detik % 2 == 0) {
                        $('.card_' + data.id).removeClass('soal_error');
                        $('.card_' + data.id).addClass('soal_ragu');
                    } else {
                        $('.card_' + data.id).removeClass('soal_ragu');
                        $('.card_' + data.id).addClass('soal_error');

                    }

                }
            }



        }, 1000);
    }


    // nol hidup 1 mati
    let datas = <?= json_encode($data); ?>;

    datas.forEach(e => {
        let dari = $('.body_countdown_' + e.id).attr('data-dari');
        let ke = $('.body_countdown_' + e.id).attr('data-ke');
        e['dari'] = dari;
        e['ke'] = ke;
        countdown(e, '.body_countdown_' + e.id);
        if (localStorage.getItem(e.id) === null) {
            localStorage.setItem(e.id, 0);
        }
    });

    if (localStorage.getItem('all_audio') === null) {
        localStorage.setItem('all_audio', 0);
    } else {
        if (localStorage.getItem('all_audio') == 0) {
            $('.canvas_navigation_md').prepend('<div type="button" class="bg_light border_dark mb-2 play_audio" style="border-radius: 5px;text-align:center;cursor:pointer"><i class="text_success fa-solid fa-volume-low"></i></div>');
        } else {
            $('.canvas_navigation_md').prepend('<div type="button" class="bg_light border_dark mb-2 play_audio" style="border-radius: 5px;text-align:center;cursor:pointer"><i class="fa-solid fa-volume-xmark text_danger"></i></div>');
        }
    }

    datas.forEach(e => {
        if (localStorage.getItem(e.id) == 0) {
            $('.audio_' + e.id).html('<a class="play_audio" data-id="' + e.id + '" href=""><i class="text_success fa-solid fa-volume-low"></i>');
        } else {
            $('.audio_' + e.id).html('<a class="play_audio" data-id="' + e.id + '" href=""><i class="fa-solid fa-volume-xmark text_danger"></i></a>');
        }
    });

    $(document).on('click', '.play_audio', function(e) {
        e.preventDefault();
        let id = $(this).attr('data-id');
        if (id == undefined) {
            if (localStorage.getItem('all_audio') == 0) {

                $(this).html('<i class="fa-solid fa-volume-xmark text_danger"></i>');
                localStorage.setItem('all_audio', 1);
                datas.forEach(e => {
                    if (e.status == "In Game") {
                        localStorage.setItem(e.id, 1);
                        $('.audio_' + e.id).html('<a class="play_audio" data-id="' + e.id + '" href=""><i class="fa-solid fa-volume-xmark text_danger"></i></a>');
                    }
                });
                stop();
            } else {
                $(this).html('<i class="text_success fa-solid fa-volume-low">');
                localStorage.setItem('all_audio', 0);
                datas.forEach(e => {
                    if (e.status == "In Game") {
                        localStorage.setItem(e.id, 0);
                        $('.audio_' + e.id).html('<a class="play_audio" data-id="' + e.id + '" href=""><i class="text_success fa-solid fa-volume-low"></i>');
                    }
                });
            }

        } else {
            if (localStorage.getItem(id) == 0) {
                $('.audio_' + id).html('<a class="play_audio" data-id="' + id + '" href=""><i class="fa-solid fa-volume-xmark text_danger"></i></a>');
                localStorage.setItem(id, 1);
                stop();
            } else {
                $('.audio_' + id).html('<a class="play_audio" data-id="' + id + '" href=""><i class="text_success fa-solid fa-volume-low"></i>');
                localStorage.setItem(id, 0);
            }

        }


    })

    $(document).on('click', '.btn_rental', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let metode = $(this).data('metode');
        let url = $(this).data('url');
        let durasi = $('.durasi_' + id).val();

        if (metode == 'Tap') {
            gagal("Otomatis!.");
            return;
        }

        if (durasi == '-' && url !== 'reset_play') {
            if (url !== 'end_play') {
                gagal_with_button('Durasi belum dipilih!.');
                return false;
            }
        }

        post("<?= menu()['controller']; ?>/" + url, {
            id,
            durasi
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1400);
            } else {
                if (res.data !== null) {
                    let html = '';
                    html += '<div class="d-flex flex-column min-vh-100 min-vw-100">';
                    html += '<div class="d-flex flex-grow-1 justify-content-center align-items-center">';
                    html += '<div class="d-flex gap-3" style="border:2px solid #FF9FA1;border-radius:8px;padding:5px;background-color:#FFC9C9;color:#A90020">';
                    html += '<div class="d-flex">';
                    html += '<div class="px-2"><i class="fa-solid fa-triangle-exclamation" style="color: #cc0000;"></i> ' + res.message + '</div>';
                    html += '<a class="btn_close_warning me-2" style="text-decoration: none;color:#A90020" href=""><i class="fa-solid fa-circle-xmark"></i></a>';
                    if (res.data3 == 'ubah/tambah') {
                        html += '<a class="btn_warning_confirm text_primary me-2" data-id="' + res.data + '" data-durasi="' + res.data2 + '" data-url="ubah" style="text-decoration: none;border-radius:5px" href=""><i class="fa-regular fa-pen-to-square"></i> Ubah</a>';
                        html += '<a class="btn_warning_confirm text_success me-2" data-id="' + res.data + '" data-durasi="' + res.data2 + '" data-url="tambah" style="text-decoration: none;" href=""><i class="fa-solid fa-circle-plus"></i> Tambah</a>';
                    } else {
                        if (res.data4 == null) {
                            html += '<a class="btn_warning_confirm" data-id="' + res.data + '" data-durasi="' + res.data2 + '" data-url="' + res.data3 + '" style="text-decoration: none;color:#109a63" href=""><i class="fa-solid fa-circle-check"></i></a>';
                        } else {
                            html += '<a class="btn_warning_confirm" data-durasi_realtime="' + res.data4.durasi_realtime + '" data-biaya="' + res.data4.biaya + '" data-durasi_db="' + res.data4.durasi + '" data-id="' + res.data + '" data-durasi="' + res.data2 + '" data-url="' + res.data3 + '" style="text-decoration: none;color:#109a63" href=""><i class="fa-solid fa-circle-check"></i></a>';
                        }
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';

                    $('.box_warning_with_button').html(html);

                    $('.box_warning_with_button').show();

                    $(document).on('click', '.btn_close_warning', function(e) {
                        e.preventDefault();
                        $('.box_warning_with_button').fadeOut();
                    });

                } else {
                    gagal_with_button(res.message);
                }
            }
        })
    })

    $(document).on('click', '.btn_warning_confirm', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let url = $(this).data('url');
        let durasi = $(this).data('durasi');
        if (durasi == undefined) {
            let old_durasi = $(this).data('old_durasi');
            slc = document.querySelectorAll('.select_ubah_tambah_durasi')[1];
            if (slc !== undefined) {
                durasi = slc.value;

                if (old_durasi == durasi && url == 'rental/confirm_ubah') {
                    gagal('Durasi yang baru harus berbeda!.');
                    return false;
                }

            }
        }

        if (url == 'rental/confirm_end_play') {

            $('.box_warning_with_button').fadeOut();
            let harga;
            if ($(this).attr('data-durasi_db') == -1) {
                harga = menghitung_harga($(this).attr('data-durasi_realtime'), $(this).attr('data-biaya'));

                let nol_length = 0;
                let arr_harga = angka(harga.harga).split('.');
                arr_harga.forEach((elem, idx) => {
                    if (idx > 0) {
                        nol_length += elem.length;
                    }
                })
                harga['harga'] = parseInt((parseInt(arr_harga[0]) + 1) + '0'.repeat(nol_length));
            } else {
                harga = menghitung_harga($(this).attr('data-durasi_db'), $(this).attr('data-biaya'));
            }

            let html = '';
            html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';


            html += '<div class="input-group mb-3">';
            html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">TOTAL BIAYA</span>';
            html += '<input style="text-align: right;" type="text" class="form-control harga_biaya" value="' + angka(harga.harga) + '" readonly>';
            html += '</div>';

            html += '<div class="input-group mb-3">';
            html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">DISKON</span>';
            html += '<input style="text-align: right;" type="text" placeholder="Potongan harga" class="form-control uang harga_diskon" value="' + angka(0) + '">';
            html += '</div>';

            html += '<div class="input-group mb-2">';
            html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
            html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" class="form-control uang harga_jml_uang" value="">';
            html += '</div>';
            if ($(this).attr('data-durasi_db') == -1) {
                html += '<div class="btn_warning mb-2 fw-bold text-center" style="font-size:small;color:brown;">' + menghitung_harga($(this).attr('data-durasi_realtime'), 4000).durasi + '</div>';
            } else {
                html += '<div class="btn_warning mb-2 fw-bold text-center" style="font-size:small;color:brown;">' + menghitung_harga($(this).attr('data-durasi_realtime'), 4000).durasi + ' | ' + menghitung_harga(durasi, 4000).durasi + '</div>';

            }

            html += '<div class="bg_light p-3 fw-bold" style="text-align:center;font-size:xx-large">';
            html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
            html += '<div class="uang_yang_harus_dibayar">' + angka(harga.harga, 'Rp') + '</div>';
            html += '</div>';

            html += '<div class="d-grid mt-3">';
            html += '<button data-id="' + id + '" data-url="' + url + '" class="btn_primary btn_bayar"><i class="fa-solid fa-cash-register"></i> Bayar</button>';
            html += '</div>';

            html += '</div>';
            $('.body_total_harga').html(html);
            let myModal = document.getElementById('total_harga');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal);
            modal.show();
            setTimeout(() => {
                $('.harga_jml_uang').focus();

            }, 500);

            $(document).on('keyup', '.harga_diskon', function(e) {
                e.preventDefault();
                let harga = parseInt(str_replace(".", "", $('.harga_biaya').val()));
                let diskon = str_replace(".", "", $(this).val());
                if (diskon == "") {
                    diskon = 0;
                }
                if (parseInt(diskon) > harga) {
                    gagal('Diskon melebihi harga!.');
                    return false;
                }
                $('.uang_yang_harus_dibayar').text(angka(harga - parseInt(diskon), 'Rp'));

            })
            $(document).on('keyup', '.harga_jml_uang', function(e) {
                e.preventDefault();
                let harga = parseInt(str_replace(".", "", $('.harga_biaya').val()));
                let uang = str_replace(".", "", $(this).val());
                let diskon = str_replace(".", "", $('.harga_diskon').val());
                if (diskon == "") {
                    diskon = 0;
                }
                if (uang == "") {
                    uang = 0;
                }

                if (parseInt(uang) < (harga - parseInt(diskon))) {
                    gagal('Jumlah uang pembayaran kurang!.');
                    return false;
                }

            })
            return false;
        }
        if (url == 'ubah' || url == 'tambah') {
            $('.body_description').prepend('<h6 class="judul judul_ubah_tambah mb-2">' + upper_first(url) + ' Durasi</h6>');

            let select = $('#select_durasi_' + id);
            select.addClass('select_ubah_tambah_durasi');
            $('.judul_ubah_tambah').after(select.clone());

            let html = '';
            html += '<div class="mt-2 d-grid">';
            html += '<button data-old_durasi="' + durasi + '" data-id="' + id + '" data-url="rental/confirm_' + url + '" class="btn_success btn_warning_confirm">' + $(this).html() + ' Durasi</button>';
            html += '</div>';
            $('.body_description').append(html);
            let myModal = document.getElementById('description');
            let modal = bootstrap.Modal.getOrCreateInstance(myModal);
            modal.show();
            $('.box_warning_with_button').fadeOut();
            return false;
        }



        post(url, {
            id,
            durasi
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1400);
            } else {
                let myModal = document.getElementById('description');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();
                gagal_with_button(res.message);
            }
        })
    })

    $(document).on('click', '.detail_rental', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let html = document.getElementById('card_' + id);
        $('.body_detail_rental').html(html);
        let myModal = document.getElementById('detail_rental');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    })

    $(document).on('click', '.description', function(e) {
        e.preventDefault();
        let value = $(this).data('value');
        let kode_harga = $(this).data('kode_harga');
        let data;
        harga_sewa.forEach(e => {
            if (e.nama_setting == kode_harga) {
                data = e;
            }
        })
        $('.body_description').html('<h5 class="judul">Harga per jam: ' + angka(data.value_int, 'Rp') + '</h5>');
        let myModal = document.getElementById('description');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    })

    $('#detail_rental').on('hidden.bs.modal', function() {
        let elem = document.querySelector('.body_detail_rental div');
        let card = elem.getAttribute('id');
        let id = parseInt(card.split("_")[1]);
        $(".col_" + id).html(elem);
    });
    $('#description').on('hidden.bs.modal', function() {
        $('.body_description').html('');
    });

    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();

        let biaya = $('.harga_biaya').val();
        let diskon = $('.harga_diskon').val();
        let uang = $('.harga_jml_uang').val();
        let url = $(this).attr('data-url');
        let id = $(this).attr('data-id');
        let durasi = $(this).attr('data-durasi');
        if (uang == "") {
            gagal("Uang harus diisi!.");
            return false;
        }
        let diskon_int = str_replace(".", "", $('.harga_diskon').val());
        if (diskon_int == "") {
            diskon_int = 0;
        }

        if (parseInt(str_replace(".", "", uang)) < (parseInt(str_replace(".", "", biaya)) - parseInt(diskon_int))) {
            gagal('Jumlah uang pembayaran kurang!.');
            return false;
        }

        if (parseInt(str_replace(".", "", diskon)) > (parseInt(str_replace(".", "", biaya)))) {
            gagal('Diskon tidak boleh lebih besar dari harga!.');
            return false;
        }

        post(url, {
            id,
            biaya,
            uang,
            diskon
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                let html = '';
                html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';
                html += '<div style="font-size: xxx-large;font-weight:bold;text-align:center" class="text_success"><i class="fa-solid fa-circle-check"></i></div>';
                html += '<div style="text-align: center;">' + res.message + '</div>';
                html += '<div class="bg_warning mt-4 p-3">';
                html += '<div class="text_warning_dark" style="text-align: center;font-weight:bold;">Jumlah Kembalian</div>';
                html += '<div style="font-size: xxx-large;font-weight:bold;text-align:center" class="text_warning_dark">';
                html += angka(res.data, 'Rp');
                html += '</div>';

                html += '</div>';
                html += '</div>';
                $('.body_total_harga').html(html);
                let myModal = document.getElementById('total_harga');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.show();

                $(document).on('click', '.btn_close_modal_pembayaran', function() {
                    location.reload();
                })
            } else {
                gagal_with_button(res.message);
            }
        })

    })
</script>
<?= $this->endSection() ?>