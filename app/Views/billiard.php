<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="fixed-top body_bayar" style="left: 45%;top:10%;width:20%;display:none">
        <a href="" class="btn_success btn_body_bayar"><i class="fa-solid fa-cash-register"></i> Bayar</a>
    </div>
    <?php if (count($meja) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <h6 class="bg_warning_light p-2 text-center"><?= hari(date('l'))['indo'] . ', ' . date('d') . ' ' . bulan(date('m'))['bulan'] . ' ' . date('Y'); ?></h6>
        <div class="row g-2">
            <?php foreach ($meja as $m) : ?>
                <div class="col-md-6">
                    <h6 class="judul"><?= $m['meja']; ?></h6>
                    <?php for ($i = 1; $i < 25; $i++) : ?>
                        <?php if ($i == 1) : ?>
                            <div class="div_card mb-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;" scope="col">Jam</th>
                                            <th style="text-align: center;" scope="col">Pemesan</th>
                                            <th style="text-align: center;" scope="col">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($y = 1; $y < 6; $y++) : ?>
                                            <?php $jam = (strlen($y) <= 1 ? '0' . $y : $y); ?>
                                            <tr>
                                                <th style="text-align: center;" scope="row"><?= $jam . ".00"; ?></th>
                                                <?php foreach ($data as $d) : ?>
                                                    <?php if ($d['hari'] == hari(date('l'))['indo'] && $y == $d['jam'] && $d['meja'] == $m['meja']) : ?>
                                                        <td><?= $d['pemesan']; ?></td>
                                                        <td style="text-align: center;"><input data-biaya="<?= get_harga_billiard(); ?>" data-id="<?= $d['id']; ?>" class="form-check-input biaya" type="checkbox" value="" <?= $d['pemesan'] == '' || billiard_paid($d['id']) ? 'disabled' : ''; ?>> <?= rupiah(get_harga_billiard()); ?></td>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        <?php if ($i % 6 == 0) : ?>

                            <div class="div_card mb-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;" scope="col">Jam</th>
                                            <th style="text-align: center;" scope="col">Pemesan</th>
                                            <th style="text-align: center;" scope="col">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php for ($x = $i; $x < ($i + 6); $x++) : ?>
                                            <?php if ($x < 25) : ?>

                                                <tr>
                                                    <th style="text-align: center;" scope="row"><?= $jam . ".00"; ?></th>
                                                    <?php foreach ($data as $d) : ?>

                                                        <?php if ($d['hari'] == hari(date('l'))['indo'] && $x == $d['jam'] && $d['meja'] == $m['meja']) : ?>
                                                            <td><?= $d['pemesan']; ?></td>
                                                            <td style="text-align: center;"><input data-biaya="<?= get_harga_billiard(); ?>" data-id="<?= $d['id']; ?>" class="form-check-input biaya" type="checkbox" value="" <?= $d['pemesan'] == '' || billiard_paid($d['id']) ? 'disabled' : ''; ?>> <?= rupiah(get_harga_billiard()); ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>

                </div>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>
</div>




<!-- Modal add-->
<div class="modal fade" id="add_<?= menu()['controller']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center body_select d-none">

                </div>
                <h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Add <?= menu()['menu']; ?></h6>
                <hr>
                <form action="<?= base_url(menu()['controller']); ?>/add" method="post">
                    <div class="mb-2">
                        <div class="text_main2">Meja</div>
                        <input class="input" type="text" name="meja" placeholder="Nama meja" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn_primary"><i class="fa-solid fa-cloud"></i> Save</button>
                    </div>

                </form>
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


<script>
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
            if (checked.length > 1) {
                gagal('Hanya boleh satu pembayaran!.');
                $('.body_bayar').fadeOut();
                return false;
            } else if (checked.length == 1) {
                $('.body_bayar').fadeIn();

            } else {
                gagal('Hanya boleh satu pembayaran!.');
                $('.body_bayar').fadeOut();
                return false;
            }
        }

    });

    $(document).on("click", '.btn_body_bayar', function(e) {
        e.preventDefault();

        let elems = document.getElementsByClassName('biaya');

        let data = [];
        let ids = [];
        let total_biaya = 0;
        // loop over them all
        for (let i = 0; i < elems.length; i++) {
            // And stick the checked ones onto an array...
            if (elems[i].checked) {
                ids.push(elems[i].getAttribute('data-id'));
                total_biaya += parseInt(elems[i].getAttribute('data-biaya'));
            }
        }

        if (ids.length <= 0) {
            gagal('Pembayaran belum dipilih!.');
        }


        let html = '';
        html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';


        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">TOTAL BIAYA</span>';
        html += '<input style="text-align: right;" type="text" class="form-control harga_biaya" value="' + angka(total_biaya) + '" readonly>';
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
        html += '<div class="uang_yang_harus_dibayar">' + angka(total_biaya, 'Rp') + '</div>';
        html += '</div>';

        html += '<div class="d-grid mt-3">';
        html += '<button data-id="' + ids.join(",") + '" data-total_biaya="' + total_biaya + '" class="btn_primary btn_bayar"><i class="fa-solid fa-cash-register"></i> Bayar</button>';
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
    })

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
        let uang = $('.harga_jml_uang').val();
        let id = $(this).attr('data-id');
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
            total_biaya,
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