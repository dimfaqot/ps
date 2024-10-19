<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div style="margin-top: 160px;"></div>
<div class="mobile" style="border-radius: 4px;">
    <div class="mb-3 fixed-top bg-light container p-2 border" style="top:47px;max-width: 400px;">
        <button type="button" class="btn_success mb-3 cari_barang">
            <i class="fa-solid fa-magnifying-glass"></i> Cari Barang
        </button>
        <button type="button" class="btn_purple mb-3 pembayaran">
            <i class="fa-solid fa-cash-register"></i> Pembayaran
        </button>

        <div class="input-group input-group-sm mb-2">
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
                        <th style="text-align: center;" scope="col">Barang</th>
                        <th style="text-align: center;" class="d-none d-md-table-cell">Harga Satuan</th>
                        <th style="text-align: center;" scope="col">Qty</th>
                        <th style="text-align: center;" class="d-none d-md-table-cell">Diskon</th>
                        <th style="text-align: center;" scope="col">Harga</th>
                        <th style="text-align: center;" class="d-none d-md-table-cell">Admin</th>
                    </tr>
                </thead>
                <tbody class="tabel_search">
                    <?php $total = 0; ?>
                    <?php foreach ($data as $k => $i) : ?>
                        <?php $total += $i['total_harga']; ?>
                        <tr>
                            <td style="text-align: center;"><?= ($k + 1); ?></td>
                            <td style="text-align: center;"><?= date('d/m/y', $i['tgl']); ?></td>
                            <td class="detail" style="cursor: pointer;" data-id="<?= $i['id']; ?>"><?= $i['barang']; ?></td>
                            <td style="text-align: right;" class="d-none d-md-table-cell"><?= $i['harga_satuan']; ?></td>
                            <td style="text-align: center;"><?= $i['qty']; ?></td>
                            <td style="text-align: right;" class="d-none d-md-table-cell"><?= $i['diskon']; ?></td>
                            <td style="text-align: right;"><?= rupiah($i['total_harga']); ?></td>
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
<!-- Modal pembayaran-->
<div class="modal fade" id="pembayaran" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center body_select_barang d-none">

                </div>

                <div class="body_pembayaran">

                </div>

            </div>

        </div>
    </div>
</div>

<!-- modal total harga -->
<div class="modal fade" style="margin-top: 150px;z-index:9999" id="total_harga" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
    $(document).on('click', '.pembayaran', function(e) {
        e.preventDefault();

        let html = '<h6 class="judul"><i class="fa-solid fa-cash-register"></i> Pembayaran</h6>';

        html += '<form class="row g-2 mb-2">';
        html += '<div class="col-4 body_barang">';
        html += '<input class="form-control form-control-sm btn_cari_barang add_barang input_pesanan" data-order="add_barang" data-target="add_barang" type="text" placeholder="Barang" readonly required>';
        html += '</div>';
        html += '<div class="col-2">';
        html += '<input class="form-control form-control-sm add_qty input_pesanan" type="number" data-order="add_barang" value="1" placeholder="Jumlah" required>';
        html += '</div>';
        html += '<div class="col-4">';
        html += '<input class="form-control form-control-sm add_diskon input_pesanan uang" data-order="diskon" type="text" value="' + angka(0) + '" placeholder="Diskon" required>';
        html += '</div>';
        html += '<div class="col-2">';
        html += '<button type="button" class="btn_primary btn_insert_pembayaran"><i class="fa-solid fa-square-check"></i> Ok</button>';
        html += '</div>';
        html += '</form>';
        html += '<div class="div_card text_success mb-2 add_harga fw-bold" style="text-align: center;font-size:large;border-radius:4px;padding:5px">';
        html += angka(0);
        html += '</div>';

        html += '<table class="table table-sm table-bordered table-striped">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align:center;" scope="col">#</th>';
        html += '<th style="text-align:center;" scope="col">Barang</th>';
        html += '<th style="text-align:center;" scope="col">Qty</th>';
        html += '<th style="text-align:center;" scope="col">Diskon</th>';
        html += '<th style="text-align:center;" scope="col">Harga</th>';
        html += '<th style="text-align:center;" scope="col">Del</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody class="body_pembelanjaan">';

        html += '</tbody>';
        html += '</table>';
        html += '<div class="soal_ragu p-3 mb-3 bg_warning_light" style="border-radius: 5px;">';

        html += '<div class="bg_light p-3 mb-2 fw-bold" style="text-align:center;font-size:xx-large">';
        html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
        html += '<div class="uang_yang_harus_dibayar">' + angka(0, 'Rp') + '</div>';
        html += '</div>';

        html += '<div class="input-group">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
        html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" class="form-control uang harga_jml_uang" value="">';
        html += '</div>';

        html += '</div>'

        html += '<div class="mb-3 d-grid">';
        html += '<a class="btn_success btn_bayar" style="text-align: center;" href=""><i class="fa-solid fa-cash-register"></i> Bayar</a>';
        html += '</div>';

        $('.body_pembayaran').html(html);

        let myModal = document.getElementById('pembayaran');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    })
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

            let total_harga = parseInt(str_replace(".", "", $('.add_harga').text()));
            let diskon = parseInt(str_replace("Rp. ", "", str_replace(".", "", $('.add_diskon').val())));
            if (diskon > 0) {
                if (diskon > total_harga) {
                    gagal('Diskon tidak boleh lebih besar dari harga!.');
                    $('.add_diskon').val(angka(total_harga.toString()));
                    return false;
                } else {
                    $('.add_harga').text(angka((harga * qty) - diskon).toString());
                }

            }

        }
        $(document).on('keyup', '.input_pesanan', function(e) {
            e.preventDefault();

            if ($(this).data('order') == 'diskon') {
                let harga = $('.add_barang').data('autofill_harga_satuan') * $('.add_qty').val();
                if (harga == undefined || harga == 0 || harga == "") {
                    gagal('Barang belum dimasukkan!.');
                    return false;
                }
                let diskon = parseInt(str_replace("Rp. ", "", str_replace(".", "", $(this).val())));
                if (diskon > harga) {
                    gagal('Diskon tidak boleh lebih besar dari harga!.');
                    $(this).val(angka(harga));
                    $('.add_harga').text(angka(harga));
                    return false;
                }

            }


            harga();

        });



    });

    $(document).on('click', '.btn_insert_pembayaran', function(e) {
        e.preventDefault();
        let harga_satuan = $('.add_barang').data('autofill_harga_satuan');
        let barang_id = $('.add_barang').data('autofill_id');
        let barang = $('.add_barang').data('autofill_barang');
        let stok = $('.add_barang').data('autofill_stok');
        let qty = $('.add_qty').val();
        let diskon = $('.add_diskon').val();
        let html = "";

        if (barang == undefined) {
            gagal('Barang belum dipilih!.');

            return false;
        }
        let list_belanja = document.querySelectorAll('.list_belanja').length + 1;

        html += '<tr data-barang_id="' + barang_id + '" data-index="' + list_belanja + '" class="list_belanja list_belanja_' + list_belanja + '">';
        html += '<th style="text-align:center" scope="row">' + list_belanja + '</th>';
        html += '<td class="list_barang_' + list_belanja + '">' + barang + '</td>';
        html += '<td style="text-align:center" class="list_qty_' + list_belanja + '">' + qty + '</td>';
        html += '<td style="text-align:right" class="list_diskon_' + list_belanja + '">' + angka(diskon) + '</td>';
        html += '<td style="text-align:right" class="list_harga_' + list_belanja + '">' + angka(((harga_satuan * qty) - parseInt(str_replace(".", "", diskon))).toString()) + '</td>';
        html += '<td style="text-align:center"><a href="" class="text_danger del_list_belanja" data-id_list="' + list_belanja + '"><i class="fa-solid fa-circle-xmark"></i></a></td>';
        html += '</tr>';


        $('.add_barang').remove();
        $('.body_barang').html('<input class="form-control form-control-sm btn_cari_barang add_barang input_pesanan" data-order="add_barang" data-target="add_barang" type="text" placeholder="Barang" readonly required>');
        $('.add_diskon').val(angka(0));
        $('.add_qty').val(1);
        $('.add_harga').val(angka(0));
        $('.body_pembelanjaan').append(html);

        let elem_list_belanja = document.querySelectorAll('.list_belanja');

        let total_harga = 0;
        elem_list_belanja.forEach((e, i) => {
            let barang_id = e.getAttribute('data-barang_id');
            let index = e.getAttribute('data-index');

            let qty = parseInt($('.list_qty_' + index).text());
            let diskon = parseInt(str_replace(".", "", $('.list_diskon_' + index).text()));
            let harga = parseInt(str_replace(".", "", $('.list_harga_' + index).text()));

            total_harga += harga;
        })

        $('.uang_yang_harus_dibayar').text(angka(total_harga, 'Rp'));
    })

    $(document).on('click', '.del_list_belanja', function(e) {
        e.preventDefault();
        let index = $(this).data('id_list');
        $('.list_belanja_' + index).remove();

        let elem_list_belanja = document.querySelectorAll('.list_belanja');

        let html = '';

        let total_harga = 0;
        elem_list_belanja.forEach((e, i) => {
            let barang_id = e.getAttribute('data-barang_id');
            let index = e.getAttribute('data-index');

            let qty = parseInt($('.list_qty_' + index).text());
            let diskon = parseInt(str_replace(".", "", $('.list_diskon_' + index).text()));
            let harga = parseInt(str_replace(".", "", $('.list_harga_' + index).text()));
            let barang = $('.list_barang_' + index).text();

            total_harga += harga;

            html += '<tr data-barang_id="' + barang_id + '" data-index="' + (i + 1) + '" class="list_belanja list_belanja_' + (i + 1) + '">';
            html += '<th style="text-align:center" scope="row">' + (i + 1) + '</th>';
            html += '<td>' + barang + '</td>';
            html += '<td style="text-align:center" class="list_qty_' + (i + 1) + '">' + qty + '</td>';
            html += '<td style="text-align:right" class="list_diskon_' + (i + 1) + '">' + angka(diskon) + '</td>';
            html += '<td style="text-align:right" class="list_harga_' + (i + 1) + '">' + angka(harga) + '</td>';
            html += '<td style="text-align:center"><a href="" class="text_danger del_list_belanja" data-id_list="' + (i + 1) + '"><i class="fa-solid fa-circle-xmark"></i></a></td>';
            html += '</tr>';
        })
        $('.body_pembelanjaan').html(html);
        $('.uang_yang_harus_dibayar').text(angka(total_harga, 'Rp'));

    })

    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();

        let data = [];
        let elem_list_belanja = document.querySelectorAll('.list_belanja');

        let total_harga = 0;
        elem_list_belanja.forEach((e, i) => {
            let barang_id = e.getAttribute('data-barang_id');
            let index = e.getAttribute('data-index');

            let qty = parseInt($('.list_qty_' + index).text());
            let diskon = parseInt(str_replace(".", "", $('.list_diskon_' + index).text()));
            let harga = parseInt(str_replace(".", "", $('.list_harga_' + index).text()));
            let barang = $('.list_barang_' + index).text();

            data.push({
                barang_id,
                qty,
                diskon
            });

            total_harga += harga;
        })

        let uang = $('.harga_jml_uang').val();

        if (uang == "") {
            gagal('Uang harus diisi!.');
            return false;
        }

        uang = parseInt(str_replace("Rp. ", "", str_replace(".", "", uang)));

        if (uang < total_harga) {
            gagal("Uang pembayaran kurang!.");
            return false;
        }

        post('kantin/pembayaran', {
            data,
            uang,
            total_harga
        }).then(res => {
            if (res.status == '200') {
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

                let myModal_p = document.getElementById('pembayaran');
                let modal_p = bootstrap.Modal.getOrCreateInstance(myModal_p)
                modal_p.hide();

                $(document).on('click', '.btn_close_modal_pembayaran', function() {
                    modal.hide();
                    $('.add_harga').text(angka(0));
                    $('.uang_yang_harus_dibayar').text(angka(0, 'Rp.'));
                    $('.body_pembelanjaan').html("");
                    $('.harga_jml_uang').val("");
                    modal_p.show();

                    $('#pembayaran').on('hidden.bs.modal', function() {
                        location.reload();
                    });
                })
            } else {
                gagal_with_button(res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>