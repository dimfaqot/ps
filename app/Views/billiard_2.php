<?= $this->extend('logged') ?>

<?= $this->section('content') ?>


<div style="margin-top: 280px;"></div>
<div class="mobile" style="border-radius: 4px;">


    <div class="mb-3 fixed-top bg-light container p-2 border" style="top:47px;max-width: 400px;">
        <?php foreach ($meja as $i): ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div style="width: 70px;" class="detail" data-meja="<?= $i['meja']; ?>" data-id="<?= $i['id']; ?>">Meja <?= $i['meja']; ?></div>
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
                        <div class="bg_danger px-2 fw-bold" style="border-radius: 5px;"><a href="" data-billiard_id="<?= get_detail_billiard($i['id'])['id']; ?>" data-order="end" data-id="<?= $i['id']; ?>" class="text_light btn_start_stop" style="font-size: medium;"><i class="fa-regular fa-circle-stop"></i></a></div>

                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
        <div class="input-group input-group-sm mt-3">
            <span class="input-group-text">Search</span>
            <input type="text" class="form-control cari" placeholder="Ketik sesuatu...">
        </div>
    </div>
    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>

        <div>
            <table class="table table-sm table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="text-align: center;" scope="col">#</th>
                        <th style="text-align: center;" scope="col">Tgl</th>
                        <th style="text-align: center;" scope="col">Meja</th>
                        <th style="text-align: center;" class="d-none d-md-table-cell">Tarif</th>
                        <th style="text-align: center;" scope="col">Durasi</th>
                        <th style="text-align: center;" class="d-none d-md-table-cell">Diskon</th>
                        <th style="text-align: center;" scope="col">Harga</th>
                        <th style="text-align: center;" class="d-none d-md-table-cell">Admin</th>
                    </tr>
                </thead>
                <tbody class="tabel_search">
                    <?php $total = 0; ?>
                    <?php foreach ($data as $k => $i) : ?>
                        <?php $total += $i['biaya']; ?>
                        <tr>
                            <td style="text-align: center;"><?= ($k + 1); ?></td>
                            <td style="text-align: center;"><?= date('d/m/y', $i['tgl']); ?></td>
                            <td class="detail" style="cursor: pointer;" data-id="<?= $i['id']; ?>"><?= $i['meja']; ?></td>
                            <td style="text-align: right;" class="d-none d-md-table-cell"><?= $i['harga']; ?></td>
                            <td style="text-align: right;"><?= angka($i['durasi']); ?> Mnt</td>
                            <td style="text-align: right;" class="d-none d-md-table-cell"><?= $i['diskon']; ?></td>
                            <td style="text-align: right;"><?= rupiah($i['biaya']); ?></td>
                            <td class="d-none d-md-table-cell"><?= $i['petugas']; ?></td>
                        </tr>

                    <?php endforeach; ?>
                    <tr>
                        <td colspan="4" style="text-align: right;font-weight:bold">TOTAL</td>
                        <td style="font-weight: bold;text-align: right;"><?= rupiah($total); ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
    <?php endif; ?>
</div>



<!-- modal total harga -->
<div class="modal fade" style="margin-top: 150px;" id="total_harga" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="d-flex justify-content-between p-2 bg_danger">
                <div class="fw-bold text_light"><i class="fa-solid fa-cash-register"></i> PEMBAYARAN</div>
                <div class="bg_light" style="border-radius: 50%;padding:1.2px 4px"><a href="" data-bs-dismiss="modal"><i class="fa-solid fa-circle-xmark text_danger"></i></a></div>
            </div>
            <div class="modal-body body_total_harga">

            </div>
        </div>
    </div>
</div>

<!-- <div class="modal fade show" id="user" tabindex="-1" aria-modal="true" role="dialog" style="display: block;">
    <div class="modal-dialog">
        <div class="modal-content modal_body_user">
            <div class="bg_light">
                <input class="user_input" placeholder="Ketik sesuatu..." value="" style="width: 100%;" type="text">
                <div class="bg_3 sticky-top bg_3" style="z-index:10">
                    <div style="position:absolute;width:100%" class="bg_3 px-2 body_list_user">
                        <a data-col="sub" style="font-size:14px" href="" class="d-block rounded border-bottom insert_value">Smp</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> -->

<script>
    // let myModal = document.getElementById('exampleModal');
    // let modal = bootstrap.Modal.getOrCreateInstance(myModal);
    // modal.show();
    $(document).on('change', '.check_hutang', function(e) {
        e.preventDefault();
        if ($(this).is(':checked')) {
            $('.harga_jml_uang').val(0);
            $('.harga_jml_uang').attr('readonly', true);
            let html = '';
            html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">Nama</span>';
            html += '<input type="text" placeholder="Klik untuk mencari nama..." class="form-control nama_user_hutang" value="">';
            html += '<div class="body_search_user" style="z-index:10;position:absolute;left:120px;top:42px;">';

            html += '</div>';
            $('.body_user_hutang').html(html);
            $('.nama_user_hutang').focus();
        } else {
            $('.harga_jml_uang').val("");
            $('.harga_jml_uang').removeAttr('readonly');
            $('.body_user_hutang').html("");
        }

    })
    $(document).on('keyup', '.nama_user_hutang', function(e) {
        e.preventDefault();
        let user = $(this).val();
        post('billiard/get_user', {
            user
        }).then(res => {
            let html = '';
            res.data.forEach(e => {
                html += '<div class="div_search insert_value_user" data-user_id="' + e.id + '">' + e.nama + '</div>';
            });
            $('.body_search_user').html(html);
        })

    })
    $(document).on('click', '.insert_value_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let user_id = $(this).data('user_id');

        $('.nama_user_hutang').val(nama);
        $('.nama_user_hutang').attr('data-user_id', user_id);
        $('.body_search_user').html("");
    })


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

        // html += '<div class="input-group mb-2">';
        // html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
        // html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" class="form-control uang harga_jml_uang" value="">';
        // html += '</div>';
        html += '<div class="input-group mb-3">';
        html += '<div class="input-group-text d-flex justify-content-center bg_warning text_warning_dark" style="width:120px">';
        html += '<input class="form-check-input mt-0 check_hutang" style="text-align: right;" type="checkbox" value="" aria-label="Checkbox for following text input">';
        html += '</div>';
        html += '<input type="text" class="form-control uang harga_jml_uang" aria-label="Text input with checkbox" placeholder="Uang yang dibayarkan">';
        html += '</div>';

        html += '<div class="input-group mb-3 body_user_hutang">';

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
        let billiard_id = $(this).data('billiard_id');
        let order = $(this).data('order');
        let durasi = $('.durasi_billiard_' + id).val();

        post('billiard/start_stop', {
            id,
            order,
            billiard_id,
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
        let hutang = $('.check_hutang').is(':checked');
        let nama_user = $('.nama_user_hutang').val();
        let user_id = $('.nama_user_hutang').data('user_id');

        if (uang == "") {
            gagal("Uang harus diisi!.");
            return false;
        }
        let diskon_int = str_replace(".", "", $('.harga_diskon').val());
        if (diskon_int == "") {
            diskon_int = 0;
        }

        if (!hutang) {
            if (parseInt(str_replace(".", "", uang)) < (parseInt(str_replace(".", "", total_biaya)) - parseInt(diskon_int))) {
                gagal('Jumlah uang pembayaran kurang!.');
                return false;
            }

        } else {
            if (nama_user == "") {
                gagal('Nama user kosong!.');
                return;
            }
            if (user_id == "") {
                gagal('User id kosong!.');
                return;
            }
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
            hutang,
            nama_user,
            user_id,
            diskon
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                if (hutang) {
                    location.reload();
                } else {
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

                    $('#total_harga').on('hidden.bs.modal', function() {
                        location.reload();
                    });
                }
            } else {
                gagal_with_button(res.message);
            }
        })

    })

    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.tabel_search tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });
</script>
<?= $this->endSection() ?>