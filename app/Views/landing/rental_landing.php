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
                            <div>
                                <p class="fw-bold" style="font-size: small;"><i class="fa-brands fa-playstation"></i> <?= strtoupper($i['kode_harga']); ?></p>
                            </div>
                            <div>
                                <p><?= $i['desc']; ?></p>
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


<script>
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
                    } else if (durasi_now > 0 && durasi_now <= 10) {

                        // clearInterval(x);
                        if (data.status == 'In Game') {
                            $('.card_' + data.id).removeClass('soal_belum_dijawab');
                            $('.card_' + data.id).removeClass((detik / 2 == 0 ? 'soal_error' : 'soal_ragu'));
                            $('.card_' + data.id).addClass((detik / 2 == 0 ? 'soal_ragu' : 'soal_error'));

                        }
                    }

                }

            } else {
                $(cls).text("0");
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
    });
</script>