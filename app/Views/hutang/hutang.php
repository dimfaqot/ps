<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#pembeli">
        <i class="fa-solid fa-user"></i> Pembeli
    </button>

    <input class="form-control form-control-sm cari mb-1" type="text" placeholder="Cari...">
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>No. Hp</th>
                <th>Detail</th>
                <th>Beli</th>
            </tr>
        </thead>
        <tbody class="body_hutang tabel_search">
            <?php foreach (data_pembeli() as $k => $i): ?>
                <tr>
                    <td><?= ($k + 1); ?></td>
                    <td class="<?= ($i['status'] <= 0 ? 'text_success' : 'text_warning'); ?>"><?= $i['nama']; ?></td>
                    <td><?= $i['hp']; ?></td>
                    <td style="text-align: center;"><a class="detail" data-id="<?= $i['id']; ?>" href=""><i class="fa-solid fa-up-right-from-square"></i></a></td>
                    <td style="text-align: center;"><a href="" class="pembayaran" data-user_id="<?= $i['id']; ?>" data-kategori="Kantin"><i class="fa-solid fa-circle-plus"></i></a></td>
                </tr>

            <?php endforeach; ?>

        </tbody>
    </table>
</div>

<!-- <td class="d-none d-md-table-cell"></td> -->

<!-- Modal pembeli-->
<div class="modal fade" id="pembeli" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-grid text-center mb-2">
                    <a href="" class="btn_grey" data-bs-dismiss="modal" aria-label="Close"><i class="fa-solid fa-circle-xmark"></i> close</a>
                </div>
                <div class="input-group input-group-sm mb-2">
                    <input type="text" class="form-control add_pembeli" placeholder="Masukkan nama pembeli...">
                    <input type="text" class="form-control add_hp" placeholder="No. HP...">
                    <button class="btn btn-outline-secondary btn_add_pembeli" type="button"><i class="fa-solid fa-floppy-disk"></i> Save</button>
                </div>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Ket</th>
                            <th scope="col">Login</th>
                            <th scope="col">Act</th>
                        </tr>
                    </thead>
                    <tbody class="body_pembeli">

                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
<!-- Modal data_hutang-->
<div class="modal fade" id="data_hutang" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body modal_body_data_hutang">

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
<!-- Modal lunas-->
<div class="modal fade" id="lunas" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="body_lunas">

                </div>

            </div>

        </div>
    </div>
</div>
<!-- Modal kembalian-->
<div class="modal fade" id="kembalian" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="body_kembalian">

                </div>

            </div>

        </div>
    </div>
</div>
<script>
    // let myModal = document.getElementById('data_hutang');
    // let modal = bootstrap.Modal.getOrCreateInstance(myModal)
    // modal.show();
    const data_pembeli = () => {
        post('hutang/pembeli', {
            id: 1
        }).then(res => {
            let html = "";
            res.data.forEach((e, i) => {
                html += '<tr>';
                html += '<td>' + (i + 1) + '</td>';
                html += '<td class="update_pembeli" data-col="nama" data-id="' + e.id + '" contenteditable>' + e.nama + '</td>';
                html += '<td class="update_pembeli" data-col="hp" data-id="' + e.id + '" contenteditable>' + e.hp + '</td>';
                html += '<td style="text-align:center;"><a class="copy_link" data-link="' + e.jwt + '" href=""><i class="fa-solid fa-link"></i></a>';
                html += '<td style="text-align:center;"><a href=""><i class="fa-solid fa-circle-xmark text-danger"></i></a></td>';
                html += '</tr>';
            });

            $('.body_pembeli').html(html);
        })

        $('#pembeli').on('hidden.bs.modal', function() {
            location.reload();
        });
    }

    data_pembeli();

    const data_hutang_byid = (data, no_nota) => {
        let html = "";
        html += '<table class="table table-sm table-bordered">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align: center;">#</th>';
        html += '<th style="text-align: center;">Barang</th>';
        html += '<th style="text-align: center;">Harga</th>';
        html += '<th style="text-align: center;">Qty</th>';
        html += '<th style="text-align: center;">Total</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody class="body_hutang">';
        let total = 0;
        data.forEach((e, i) => {
            total += parseInt(e.total_harga);
            html += '<tr>';
            html += '<td style="text-align: center;">' + (i + 1) + '</td>';
            html += '<td>' + e.barang + '</td>';
            html += '<td style="text-align: right;">' + angka(e.harga_satuan) + '</td>';
            html += '<td style="text-align: center;">' + e.qty + '</td>';
            html += '<td style="text-align: right;">' + angka(e.total_harga) + '</td>';
            html += '</tr>';
        })
        html += '<tr>';
        html += '<th colspan="4">TOTAL</th>';
        html += '<th style="text-align: right;">' + angka(total) + '</th>';
        html += '</tr>';
        html += '</tbody>';
        html += '</table>';

        return html;

    }
    let list_hutang = [];
    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        post('hutang/data_hutang', {
            id
        }).then(res => {
            if (res.status == "200") {
                list_hutang = res.data;
                let html = '';
                html += '<div class="bg_purple text-light fw-bold py-2 px-3">';
                html += res.data[0].nama + ' [' + angka(res.data2, "Rp. ") + ']';
                html += '</div>';

                res.data.forEach((e, i) => {
                    html += '<div>';
                    html += '<div class="d-flex ' + (e.status == 1 ? 'bg_success' : 'bg_warning') + ' px-3 py-2">';
                    html += '<div style="width: 30px;">' + (i + 1) + '.</div>';
                    html += '<div style="cursor: pointer;" class="fw-bold text-light" style="text-align: right;"" data-bs-toggle="collapse" href="#hutang_' + str_replace("/", "_", e.no_nota) + '" role="button" aria-expanded="false" aria-controls="hutang_' + str_replace("/", "_", e.no_nota) + '">';
                    html += time_php_to_js(e.tgl);
                    html += '</div>';

                    html += '<div class="fw-bold text-light flex-fill" style="text-align: right;">';
                    html += (e.status == 1 ? 'LUNAS' : 'HUTANG');
                    html += '</div>';
                    html += '</div>';
                    html += '<div class="collapse" id="hutang_' + str_replace("/", "_", e.no_nota) + '">';
                    html += '<div style="border-radius:0px" class="card ' + (e.status == 1 ? 'bg_success_light' : 'bg_warning_light') + ' card-body body_hutang_' + str_replace("/", "_", e.no_nota) + '">';
                    html += data_hutang_byid(e.data, e.no_nota);
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                })
                if (res.data2 > 0) {
                    html += '<div class="d-grid mt-2">';
                    html += '<button class="btn_info btn_whatsapp py-2 mb-1" data-kategori="<?= (session('role') == 'Root' ? 'Root' : explode(" ", session('role'))[1]); ?>" data-jwt="' + res.data5 + '" data-nama="' + res.data[0].nama + '" data-no_hp="' + res.data4 + '" style="border-radius:0px" data-user_id="' + res.data3 + '" data-total="' + res.data2 + '"><i class="fa-brands fa-whatsapp"></i> Kirim Whatsapp</button>';
                    html += '<button class="btn_primary btn_lunas py-2" style="border-radius:0px" data-user_id="' + res.data3 + '" data-total="' + res.data2 + '" data-kategori="<?= (session('role') == 'Root' ? 'Root' : explode(" ", session('role'))[1]); ?>"><i class="fa-solid fa-hand-holding-dollar"></i> Lunasi</button>';
                    html += '</div>';

                }
                $('.modal_body_data_hutang').html(html);
                let myModal = document.getElementById('data_hutang');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
            } else {
                gagal(res.message);
            }
        })

    })

    $(document).on('click', '.btn_add_pembeli', function(e) {
        e.preventDefault();
        let nama = $('.add_pembeli').val();
        if (nama == "") {
            gagal("Nama harus diisi!.");
            return;
        }
        let hp = $('.add_hp').val();

        post('hutang/add_pembeli', {
            nama,
            hp
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                data_pembeli();
            } else {
                gagal(res.message);
            }
        })
    })
    $(document).on('click', '.copy_link', function(e) {
        e.preventDefault();
        let link = $(this).data('link');
        navigator.clipboard.writeText(link);
        sukses('Data copied.');
    })
    $(document).on('blur', '.update_pembeli', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let col = $(this).data('col');
        let val = $(this).text();

        post('hutang/update_pembeli', {
            id,
            col,
            val
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                data_pembeli();
            } else {
                gagal(res.message);
            }
        })
    })

    $(document).on('click', '.pembayaran', function(e) {
        e.preventDefault();
        let user_id = $(this).data('user_id');
        let kategori = $(this).data('kategori');
        let html = '<h6 class="judul"><i class="fa-solid fa-cash-register"></i> Pembayaran</h6>';

        html += '<form class="row g-2 mb-2">';
        html += '<div class="col-8 body_barang">';
        html += '<input class="form-control form-control-sm btn_cari_barang add_barang input_pesanan" data-order="add_barang" data-target="add_barang" type="text" placeholder="Barang" readonly required>';
        html += '</div>';
        html += '<div class="col-2">';
        html += '<input class="form-control form-control-sm add_qty input_pesanan" type="number" data-order="add_barang" value="1" placeholder="Jumlah" required>';
        html += '</div>';
        html += '<div class="col-2">';
        html += '<button type="button" class="btn_primary btn_insert_pembayaran" data-user_id="' + user_id + '" data-kategori="' + kategori + '"><i class="fa-solid fa-square-check"></i> Ok</button>';
        html += '</div>';
        html += '</form>';

        html += '<table class="table table-sm table-bordered table-striped">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align:center;" scope="col">#</th>';
        html += '<th style="text-align:center;" scope="col">Barang</th>';
        html += '<th style="text-align:center;" scope="col">Qty</th>';
        html += '<th style="text-align:center;" scope="col">Harga</th>';
        html += '<th style="text-align:center;" scope="col">Del</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody class="body_pembelanjaan">';

        html += '</tbody>';
        html += '</table>';
        html += '<div class="soal_ragu p-3 mb-3 bg_warning_light" style="border-radius: 5px;">';

        html += '<div class="bg_light p-3 mb-2 fw-bold" style="text-align:center;font-size:xx-large">';
        html += '<div style="font-weight:normal;font-size:small">Total Harga    </div>';
        html += '<div class="uang_yang_harus_dibayar">' + angka(0, 'Rp') + '</div>';
        html += '</div>';

        // html += '<div class="input-group">';
        // html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
        // html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" class="form-control uang harga_jml_uang" value="">';
        // html += '</div>';

        html += '</div>'

        html += '<div class="mb-3 d-grid">';
        html += '<a class="btn_success btn_bayar" data-user_id="' + user_id + '" data-kategori="<?= (session('role') == 'Root' ? 'Root' : explode(" ", session('role'))[1]); ?>" style="text-align: center;" href=""><i class="fa-solid fa-cash-register"></i> Simpan</a>';
        html += '</div>';

        $('.body_pembayaran').html(html);

        let myModal = document.getElementById('pembayaran');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    })


    $(document).on('click', '.btn_insert_pembayaran', function(e) {
        e.preventDefault();
        let user_id = $('this').data('user_id');
        let kategori = $('this').data('kategori');
        let harga_satuan = $('.add_barang').data('autofill_harga_satuan');
        let barang_id = $('.add_barang').data('autofill_id');
        let barang = $('.add_barang').data('autofill_barang');
        let stok = $('.add_barang').data('autofill_stok');
        let qty = $('.add_qty').val();
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
        html += '<td style="text-align:right" class="list_harga_' + list_belanja + '">' + angka((harga_satuan * qty).toString()) + '</td>';
        html += '<td style="text-align:center"><a href="" class="text_danger del_list_belanja" data-id_list="' + list_belanja + '"><i class="fa-solid fa-circle-xmark"></i></a></td>';
        html += '</tr>';


        $('.add_barang').remove();
        $('.body_barang').html('<input class="form-control form-control-sm btn_cari_barang add_barang input_pesanan" data-order="add_barang" data-target="add_barang" type="text" placeholder="Barang" readonly required>');
        $('.add_qty').val(1);
        $('.body_pembelanjaan').append(html);

        let elem_list_belanja = document.querySelectorAll('.list_belanja');

        let total_harga = 0;
        elem_list_belanja.forEach((e, i) => {
            let barang_id = e.getAttribute('data-barang_id');
            let index = e.getAttribute('data-index');

            let qty = parseInt($('.list_qty_' + index).text());
            let harga = parseInt(str_replace(".", "", $('.list_harga_' + index).text()));

            total_harga += harga;
        })

        $('.uang_yang_harus_dibayar').text(angka(total_harga, 'Rp'));
    })

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
            }

            // let total_harga = parseInt(str_replace(".", "", $('.add_harga').text()));
            // let diskon = parseInt(str_replace("Rp. ", "", str_replace(".", "", $('.add_diskon').val())));
            // if (diskon > 0) {
            //     if (diskon > total_harga) {
            //         gagal('Diskon tidak boleh lebih besar dari harga!.');
            //         $('.add_diskon').val(angka(total_harga.toString()));
            //         return false;
            //     } else {
            //         $('.add_harga').text(angka((harga * qty) - diskon).toString());
            //     }

            // }

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
                    return false;
                }

            }


            harga();

        });



    });

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
        let user_id = $(this).data('user_id');
        let kategori = $(this).data('kategori');
        if (kategori == 'Root') {
            gagal('Root tidak bisa input!.');
            return;
        }
        let total_harga = 0;
        if (elem_list_belanja.length <= 0) {
            gagal('Pesanan masih kosong!.');
            return;
        }
        elem_list_belanja.forEach((e, i) => {
            let barang_id = e.getAttribute('data-barang_id');
            let index = e.getAttribute('data-index');

            let qty = parseInt($('.list_qty_' + index).text());
            let harga = parseInt(str_replace(".", "", $('.list_harga_' + index).text()));
            let barang = $('.list_barang_' + index).text();

            data.push({
                barang_id,
                qty
            });

            total_harga += harga;
        })


        post('hutang/add', {
            data,
            total_harga,
            user_id,
            kategori

        }).then(res => {
            if (res.status == '200') {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                gagal_with_button(res.message);
            }
        })
    })


    $(document).on('click', '.btn_lunas', function(e) {
        e.preventDefault();

        let user_id = $(this).data('user_id');
        let total = $(this).data('total');
        let kategori = $(this).data('kategori');

        let html = '';
        html += '<table class="table table-sm table-bordered table-striped">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align:center;">#</th>';
        html += '<th style="text-align:center;">Tgl</th>';
        html += '<th style="text-align:center;">Barang</th>';
        html += '<th style="text-align:center;">Harga</th>';
        html += '<th style="text-align:center;">Qty</th>';
        html += '<th style="text-align:center;">Total</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        let x = 1;
        list_hutang.forEach((e, i) => {
            e.data.forEach((el, idx) => {
                if (el.status == 0) {
                    html += '<tr>';
                    html += '<td style="text-align:center;">' + x++ + '</td>';
                    html += '<td>' + time_php_to_js(el.tgl) + '</td>';
                    html += '<td>' + el.barang + '</td>';
                    html += '<td style="text-align:right;">' + angka(el.harga_satuan) + '</td>';
                    html += '<td style="text-align:center;">' + el.qty + '</td>';
                    html += '<td style="text-align:right;">' + angka(el.total_harga) + '</td>';
                    html += '</tr>';
                }
            })

        })
        html += '</tbody>';
        html += '<div class="soal_ragu p-3 mb-3 bg_warning_light" style="border-radius: 5px;">';

        html += '<div class="bg_light p-3 mb-2 fw-bold" style="text-align:center;font-size:xx-large">';
        html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
        html += '<div class="total_uang_lunas">' + angka(total, 'Rp') + '</div>';
        html += '</div>';

        html += '<div class="input-group mb-1 <?= (session('role') == 'Admin Billiard' ? 'd-none' : ''); ?>">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning_light text_warning_dark fw-bold">DISKON</span>';
        html += '<input style="text-align: right;" type="text" data-total="' + total + '" placeholder="Diskon..." class="form-control uang jml_diskon_lunas" value="0">';
        html += '</div>';
        html += '<div class="input-group">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
        html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" data-total="' + total + '" class="form-control uang jml_uang_lunas_dari_pembeli" value="">';
        html += '</div>';

        html += '</div>'

        html += '<div class="mb-3 d-grid">';
        html += '<a class="btn_success btn_bayar_lunas" data-kategori="<?= (session('role') == 'Root' ? 'Root' : explode(" ", session('role'))[1]); ?>" data-user_id="' + user_id + '" data-total="' + total + '" style="text-align: center;" href=""><i class="fa-solid fa-cash-register"></i> Bayar</a>';
        html += '</div>';

        $('.body_lunas').html(html);

        let lunasMdl = document.getElementById('lunas');
        let lns = bootstrap.Modal.getOrCreateInstance(lunasMdl)
        lns.show();

        $('.jml_uang_lunas_dari_pembeli').focus();
        // post('hutang/lunas', {
        //     no_nota

        // }).then(res => {
        //     if (res.status == '200') {
        //         sukses(res.message);
        //         setTimeout(() => {
        //             location.reload();
        //         }, 1200);
        //     } else {
        //         gagal_with_button(res.message);
        //     }
        // })
    })

    $(document).on('keyup', '.jml_diskon_lunas', function(e) {
        e.preventDefault();
        let total = $(this).data('total');
        let val = $(this).val();

        if (val == "") {
            $('.total_uang_lunas').text(angka(total));
            return false;
        } else {
            val = parseInt(str_replace(".", "", val));
        }
        if (val > total) {
            gagal('Diskon melebihi harga!.');
            $('.total_uang_lunas').text(angka(total));
            return false;
        } else {
            $('.total_uang_lunas').text(angka(total - val));
        }
    });
    $(document).on('keyup', '.jml_uang_lunas_dari_pembeli', function(e) {
        e.preventDefault();
        let val = parseInt(str_replace(".", "", $(this).val()));
        let total = parseInt(str_replace(".", "", str_replace("Rp. ", "", $('.total_uang_lunas').text())));

        if (val < total) {
            gagal('Uang kurang!.');
            return false;
        }
    });

    $(document).on('click', '.btn_bayar_lunas', function(e) {
        e.preventDefault();
        let total = $(this).data('total');
        let user_id = $(this).data('user_id');
        let kategori = $(this).data('kategori');
        let diskon = $('.jml_diskon_lunas').val();
        if (kategori == 'Root') {
            gagal('Root tidak diizinkan!.');
            return;
        }
        if (diskon == "" || diskon == 0) {
            diskon = 0;
        } else {
            diskon = parseInt(str_replace(".", "", diskon));
        }
        if ($('.jml_uang_lunas_dari_pembeli').val() == "") {
            gagal('Uang masih kosong!.');
            return;
        }
        let uang = parseInt(str_replace(".", "", $('.jml_uang_lunas_dari_pembeli').val()));

        if (diskon > total) {
            gagal('Diskon melebihi harga!.');
            return false;
        }
        if (uang < (total - diskon)) {
            gagal('Uang kurang!.');
            return false;
        }

        post('hutang/bayar_lunas', {
            uang,
            user_id,
            diskon,
            total_setelah_diskon: total - diskon,
            kategori

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
                $('.body_kembalian').html(html);


                let myModal = document.getElementById('lunas');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal);
                modal.hide();

                let myModalDftHtng = document.getElementById('data_hutang');
                let modalHtng = bootstrap.Modal.getOrCreateInstance(myModalDftHtng);
                modalHtng.hide();

                let mdLuns = document.getElementById('kembalian');
                let modalLns = bootstrap.Modal.getOrCreateInstance(mdLuns);
                modalLns.show();

                $('#kembalian').on('hidden.bs.modal', function() {
                    location.reload();
                });
            } else {
                gagal_with_button(res.message);
            }
        })
    });

    $(document).on('click', '.btn_whatsapp', function(e) {
        e.preventDefault();
        let nama = $(this).data('nama');
        let jwt = $(this).data('jwt');
        let kategori = $(this).data('kategori');
        let no_hp = "62";
        no_hp += $(this).data('no_hp').substring(1);
        console.log(kategori);
        if (kategori == 'Root') {
            gagal('Root tidak diizinkan!.');
            return;
        }

        let text = "_Assalamualaikum Wr. Wb._%0a";
        text += "Yth. *" + nama + '*%0a%0a';
        text += 'Tagihan Anda di Hayu Playground:%0a%0a';
        text += '*Tgl - Barang - Qty - Total*%0a'

        let x = 1;
        let total = 0;
        list_hutang.forEach((e, i) => {
            e.data.forEach((el, idx) => {
                if (el.status == 0) {
                    if (el.kategori == kategori) {
                        total += el.harga_satuan * el.qty;
                        text += (x++) + '. ' + time_php_to_js(el.tgl) + ' - ' + el.barang + ' - ' + el.qty + ' - ' + angka(el.harga_satuan * el.qty) + '%0a';
                    }
                }
            })

        })
        text += '%0a';
        text += "*TOTAL: " + angka(total) + "*%0a%0a";
        text += "*_Mohon segera dibayar njihhh..._*%0a";
        text += "_Wassalamualaikum Wr. Wb._%0a%0a";
        text += 'Petugas%0a%0a';
        text += '<?= user()['nama']; ?>';
        text += "%0a%0a";
        text += "_(*)Pesan ini dikirim oleh sistem, jadi mohon maklum dan ampun tersinggung njih._";
        text += "%0a%0a";
        text += "Info lebih lengkap klik: %0a%0a";
        text += jwt;


        // let url = "https://api.whatsapp.com/send/?phone=" + no_hp + "&text=" + text;
        let url = "whatsapp://send/?phone=" + no_hp + "&text=" + text;

        location.href = url;
        // window.open(url);
    });

    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.tabel_search tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });
</script>
<?= $this->endSection() ?>