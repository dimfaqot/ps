<?= $this->extend('logged') ?>

<?= $this->section('content') ?>

<div class="mobile">
    <a href="<?= base_url('home/replace'); ?>">Ok</a>
    <?php foreach ($meja as $i): ?>
        <div class="div_list">
            <div class="d-flex justify-content-between">
                <div class="detail" data-meja="<?= $i['meja']; ?>" data-id="<?= $i['id']; ?>">Meja <?= $i['meja']; ?></div>
                <?php if ($i['is_active'] == 0): ?>
                    <div><input style="font-size:smaller" type="number" class="form-control form-control-sm durasi_billiard_<?= $i['id']; ?>" value="60"></div>
                    <div><?= date('H:i'); ?></div>
                    <div class="bg_success px-2 fw-bold" style="border-radius: 5px;"><a href="" data-order="start" data-id="<?= $i['id']; ?>" class="text_light btn_start_stop" style="font-size: medium;"><i class="fa-regular fa-circle-play"></i></a></div>

                <?php else: ?>
                    <div><input style="font-size:smaller" type="number" class="form-control form-control-sm durasi_billiard_<?= $i['id']; ?>" value="<?= get_detail_billiard($i['id'])['durasi']; ?>"></div>
                    <?php if (get_detail_billiard($i['id'])['end'] == 0): ?>
                        <div><?= durasi(get_detail_billiard($i['id'])['start'], time()); ?></div>
                    <?php else: ?>
                        <div><?= date('H:i', get_detail_billiard($i['id'])['end']); ?></div>
                    <?php endif; ?>
                    <div class="bg_danger px-2 fw-bold" style="border-radius: 5px;"><a href="" data-order="end" data-id="<?= $i['id']; ?>" class="text_light btn_start_stop" style="font-size: medium;"><i class="fa-regular fa-circle-stop"></i></a></div>

                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
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


<script>
    let body_pembayaran = (data) => {
        let html = '';
        html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';


        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">TOTAL BIAYA</span>';
        html += '<input style="text-align: right;" type="text" class="form-control harga_biaya" value="' + angka(data.biaya) + '" readonly>';
        html += '</div>';

        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">DISKON</span>';
        html += '<input style="text-align: right;" type="text" placeholder="Potongan harga" class="form-control uang harga_diskon" value="' + angka(0) + '">';
        html += '</div>';

        html += '<div class="input-group mb-2">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
        html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" class="form-control uang harga_jml_uang" value="">';
        html += '</div>';

        html += '<div class="bg_light p-3 fw-bold" style="text-align:center;font-size:xx-large">';
        html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
        html += '<div class="uang_yang_harus_dibayar">' + angka(data.biaya, 'Rp') + '</div>';
        html += '<div class="total_durasi" data-durasi="' + data.durasi_waktu + '" style="font-size:12px">' + data.durasi_waktu + ' Minutes</div>';
        html += '</div>';

        html += '<div class="d-grid mt-3">';
        html += '<button data-id="' + data.id + '" data-meja_id="' + data.meja_id + '" data-total_biaya="' + data.biaya + '" class="btn_primary btn_bayar"><i class="fa-solid fa-cash-register"></i> Bayar</button>';
        html += '</div>';

        html += '</div>';
        $('.body_total_harga').html(html);
        let myModal = document.getElementById('total_harga');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        modal.show();
        setTimeout(() => {
            $('.harga_jml_uang').focus();

        }, 500);

    }


    $(document).on('click', '.btn_start_stop', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let order = $(this).data('order');
        let durasi = $('.durasi_billiard_' + id).val();

        post('billiard/start_stop', {
            id,
            order,
            durasi
        }).then(res => {
            if (res.status = "200") {
                if (order == 'start') {
                    sukses(res.message);
                    setTimeout(() => {
                        location.reload();
                    }, 800);
                }
                if (order == 'end') {
                    body_pembayaran(res.data);
                }
            } else {
                gagal_with_button(res.message);
            }
        })

    })

    $(document).on('change', '.biaya', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let elems = document.getElementsByClassName('biaya');


        let checked = [];
        // loop over them all
        for (let i = 0; i < elems.length; i++) {
            // And stick the checked ones onto an array...
            if (elems[i].checked) {
                checked.push(elems[i]);
            }
        }

        if (checked.length <= 0) {
            $('.body_bayar').fadeOut();
        } else {
            $('.body_bayar').fadeIn();
        }

    });


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

    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();
        let diskon = $('.harga_diskon').val();
        let durasi = $('.total_durasi').data('durasi');
        let uang = $('.harga_jml_uang').val();
        let id = $(this).attr('data-id');
        let meja_id = $(this).attr('data-meja_id');
        let total_biaya = $(this).attr('data-total_biaya');
        if (uang == "") {
            gagal("Uang harus diisi!.");
            return false;
        }
        let diskon_int = str_replace(".", "", $('.harga_diskon').val());
        if (diskon_int == "") {
            diskon_int = 0;
        }

        if (parseInt(str_replace(".", "", uang)) < (parseInt(str_replace(".", "", total_biaya)) - parseInt(diskon_int))) {
            gagal('Jumlah uang pembayaran kurang!.');
            return false;
        }
        if (parseInt(str_replace(".", "", diskon)) > (parseInt(str_replace(".", "", total_biaya)))) {
            gagal('Diskon tidak boleh lebih besar dari harga!.');
            return false;
        }

        post("<?= menu()['controller']; ?>" + '/pembayaran', {
            id,
            biaya: total_biaya,
            durasi,
            uang,
            meja_id,
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