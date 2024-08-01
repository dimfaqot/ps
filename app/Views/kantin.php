<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <button type="button" class="btn_success mb-3 cari_barang">
        Cari Barang
    </button>
    <div class="d-flex justify-content-center body_select_barang d-none">

    </div>
    <div class="d-flex gap-1 mb-2">
        <div class="body_barang">
            <label>Barang</label>
            <input class="form-control form-control-sm btn_cari_barang add_barang input_pesanan" data-order="add_barang" data-target="add_barang" type="text" placeholder="Barang" readonly required>
        </div>
        <div>
            <label>Jml.</label>
            <input class="form-control form-control-sm add_qty input_pesanan" type="number" data-order="add_barang" value="1" placeholder="Jumlah" required>
        </div>

    </div>
    <div class="div_card_failed mb-2 add_harga fw-bold" style="text-align: center;font-size:x-large;border-radius:4px">
        Rp. 0
    </div>
    <div class="mb-3 d-grid">
        <a class="btn_success btn_body_bayar" style="text-align: center;" href=""><i class="fa-solid fa-square-check"></i> Ok</a>
    </div>

    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text">Search</span>
            <input type="text" class="form-control cari" placeholder="Ketik sesuatu...">
        </div>
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th style="text-align: center;" scope="col">#</th>
                    <th style="text-align: center;" scope="col">Tgl</th>
                    <th style="text-align: center;" scope="col">Barang</th>
                    <th style="text-align: center;" class="d-none d-md-table-cell">Harga Satuan</th>
                    <th style="text-align: center;" scope="col">Qty</th>
                    <th style="text-align: center;" scope="col">Harga</th>
                    <th style="text-align: center;" class="d-none d-md-table-cell">Admin</th>
                </tr>
            </thead>
            <tbody class="tabel_search">
                <?php foreach ($data as $k => $i) : ?>
                    <tr>
                        <td style="text-align: center;"><?= ($k + 1); ?></td>
                        <td style="text-align: center;"><?= date('d/m/y', $i['tgl']); ?></td>
                        <td class="detail" style="cursor: pointer;" data-id="<?= $i['id']; ?>"><?= $i['barang']; ?></td>
                        <td style="text-align: right;" class="d-none d-md-table-cell"><?= $i['harga_satuan']; ?></td>
                        <td style="text-align: center;"><?= $i['qty']; ?></td>
                        <td style="text-align: right;"><?= rupiah($i['total_harga']); ?></td>
                        <td class="d-none d-md-table-cell"><?= $i['petugas']; ?></td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<div class="offcanvas offcanvas-end" aria-controls="offcanvasWithBothOptions" style="max-width:400px;" id="cari_barang">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Cari Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text">Cari Barang</span>
            <input type="text" class="form-control input_cari_barang" placeholder="Ketik sesuatu...">

        </div>
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th style="text-align: center;" scope="col">#</th>
                    <th style="text-align: center;" scope="col">Barang</th>
                    <th style="text-align: center;" scope="col">Qty</th>
                    <th style="text-align: center;" scope="col">Harga</th>
                </tr>
            </thead>
            <tbody class="tabel_cari_barang">

            </tbody>
        </table>
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

<!-- Modal detail-->
<div class="modal fade" id="detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_detail">

            </div>

        </div>
    </div>
</div>
<script>
    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        let datas = <?= json_encode($data); ?>;

        let data;
        datas.forEach(e => {
            if (e.id == id) {
                data = e;
            }
        });

        let html = '';
        html += '<h6 class="text_main2 fw-bold"> <i class="<?= menu()['icon']; ?>"></i> Detail ' + data.barang + '</h6>';
        html += '<hr>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Tgl.</div>';
        html += '<input class="input" type="text" value="' + time_php_to_js(data.tgl) + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Barang Id</div>';
        html += '<input class="input" type="text" value="' + data.barang_id + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Barang</div>';
        html += '<input class="input" type="text" value="' + data.barang + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Harga Satuan</div>';
        html += '<input class="input" type="text" value="' + angka(data.harga_satuan) + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Jml</div>';
        html += '<input class="input" type="text" value="' + data.qty + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Diskon</div>';
        html += '<input class="input" type="text" value="' + angka(data.diskon) + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Total Harga</div>';
        html += '<input class="input" type="text" value="' + angka(data.total_harga) + '" readonly>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Admin</div>';
        html += '<input class="input" type="text" value="' + data.petugas + '" readonly>';
        html += '</div>';

        $('.body_detail').html(html);

        let myModal = document.getElementById('detail');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();
    })
    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.tabel_search tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });
    $(document).on('keyup', '.input_cari_barang', function(e) {
        e.preventDefault();
        let value = $(this).val();
        post('barang/cari_barang', {
            value
        }).then(res => {
            if (res.status == '200') {
                let html = '';
                res.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td style="text-align: center;">' + (i + 1) + '</td>';
                    html += '<td>' + e.barang + '</td>';
                    html += '<td style="text-align: center;">' + e.qty + '</td>';
                    html += '<td style="text-align: right;">' + angka(e.harga) + '</td>';
                    html += '</tr>';

                })

                $('.tabel_cari_barang').html(html);
            } else {
                gagal(res.message);
            }
        })

    });
    $(document).on('click', '.cari_barang', function(e) {
        e.preventDefault();
        const bsOffcanvas = new bootstrap.Offcanvas('#cari_barang');
        bsOffcanvas.show();

    });

    $(document).on('click', '.btn_cari_barang', function(e) {
        e.preventDefault();
        let target = $(this).data('target');

        html = '';
        html += '<div class="flex-fill mobile bg_light" style="position:fixed;z-index:9999;">';
        html += '<a href="" style="position:fixed;margin-top:-25px;margin-left:-15px;font-size:medium;" class="close_select text_danger"><i class="fa-solid fa-circle-xmark"></i></a>'
        html += '<input type="text" class="form-control cari_select_barang mb-2 form-control-sm" placeholder="Cari...">';
        html += '<div class="value_list_select_barang_' + target + '"></div>';
        html += '</div>';
        $('.body_select_barang').html(html);
        $('.body_select_barang').removeClass('d-none');

        $('.cari_select_barang').focus();

        $(document).on('keyup', '.cari_select_barang', function(e) {
            e.preventDefault();
            let value = $(this).val();

            post('js/select_barang', {
                value
            }).then(res => {

                // console.log(args_values);
                if (res.status == '200') {
                    html = '';
                    if (res.data.length == 0) {
                        html += '<div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>';
                    } else {
                        res.data.forEach(e => {
                            html += '<div ' + get_key_value_obj(e, 'autofill').join(" ") + ' class="div_list select_list_barang">' + e.barang + '/' + e.stok + '</div>';
                        })

                    }
                    $('.value_list_select_barang_' + target).html(html);

                } else {
                    gagal_with_button(res.message);
                }
            })

        });

        $(document).on('click', '.select_list_barang', function(e) {
            e.preventDefault();
            $('.' + target).remove();
            let args = $(this).data();

            let arr_args = object_to_array(args);

            let val = $(this).text();
            if (val.split("/").length > 1) {
                val = val.split("/")[0];
            }
            let html = '<input ';
            arr_args.forEach(e => {
                html += 'data-' + e.key + "=" + '"' + e.value + '" ';
            })
            html += 'class="form-control form-control-sm btn_cari_barang add_barang input_pesanan" data-order="add_barang" value="' + val + '" data-target="add_barang" type="text" placeholder="Barang" readonly required>';

            $('.body_barang').append(html);

            $('.body_select_barang').html('');
            $('.body_select_barang').addClass('d-none');
            harga();
        });

        $(document).on('click', '.close_select', function(e) {
            e.preventDefault();
            $('.body_select_barang').html('');
            $('.body_select_barang').addClass('d-none');
        });

        const harga = () => {
            let order = $(this).data('order');

            let qty = $('.add_qty').val();
            let brg = $('.add_barang').val();
            if (brg == "") {
                gagal('Barang harus dipilih terlebih dahulu!.');
                return false;
            }

            if (qty == 0 || qty == "") {
                gagal('Jml. tidak boleh 0 atau kosong!.');
                return false;
            }

            let harga = $('.add_barang').data('autofill_harga_satuan');
            let id = $('.add_barang').data('autofill_id');
            let barang = $('.add_barang').data('autofill_barang');
            let stok = $('.add_barang').data('autofill_stok');

            if (qty > stok) {
                gagal('Jml. tidak boleh melebihi stok!.');
                $('.add_qty').val(stok);
                return false;
            }
            if (barang !== undefined && barang !== "" && brg !== "" && qty !== "" && qty !== 0) {
                let jml_harga = harga * qty;
                $('.add_harga').text(angka(jml_harga.toString()));
            }
        }
        $(document).on('keyup', '.input_pesanan', function(e) {
            e.preventDefault();
            harga();

        });



    });

    $(document).on("click", '.btn_body_bayar', function(e) {
        e.preventDefault();

        // let order = $(this).data('order');
        let qty = $('.add_qty').val();
        let brg = $('.add_barang').val();
        let harga = $('.add_barang').data('autofill_harga_satuan');
        let id = $('.add_barang').data('autofill_id');
        let barang = $('.add_barang').data('autofill_barang');
        let stok = $('.add_barang').data('autofill_stok');

        if (barang == undefined) {
            gagal('Barang harus diisi!.');
            return false;
        }
        if (qty == undefined || qty == "" || qty == 0) {
            gagal('Jml. harus diisi!.');
            return false;
        }
        let total_biaya = harga * qty;
        let html = '';
        html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';


        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">TOTAL BIAYA</span>';
        html += '<input style="text-align: right;" type="text" class="form-control harga_biaya" value="' + angka(total_biaya.toString()) + '" readonly>';
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
        html += '<button data-id="' + id + '" data-stok="' + stok + '" data-qty="' + qty + '" data-harga_satuan="' + harga + '" data-barang="' + barang + '" data-total_biaya="' + total_biaya + '" class="btn_primary btn_bayar"><i class="fa-solid fa-cash-register"></i> Bayar</button>';
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
        let qty = $(this).attr('data-qty');
        let harga_satuan = $(this).attr('data-harga_satuan');
        let barang = $(this).attr('data-barang');
        let stok = $(this).attr('data-stok');
        let total_biaya = $(this).attr('data-total_biaya');

        if (qty > stok) {
            gagal('Jml. tidak boleh melebihi stok!.');
        }
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
            qty,
            harga_satuan,
            barang,
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