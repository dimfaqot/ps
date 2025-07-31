<?= $this->extend('logged') ?>

<?= $this->section('content') ?>

<?php
// dd(date('d/m/Y H:i:s', 1740924391));
// dd(time());
// dd(date('d/m/Y H:i:s', time()));
?>
<div class="container text-center">
    <div class="row justify-content-center g-2">
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_kasir card" data-menu="billiard">
                <div class="card-body">
                    <h1><i class="fa-solid fa-bowling-ball"></i></h1>
                    <p>BILLIARD</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_kasir card" data-menu="ps">
                <div class="card-body">
                    <h1><i class="fa-brands fa-playstation"></i></h1>
                    <p>PS</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_kasir card" data-menu="kantin">
                <div class="card-body">
                    <h1><i class="fa-solid fa-utensils"></i></h1>
                    <p>KANTIN</p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_kasir card" data-menu="barber">
                <div class="card-body">
                    <h1><i class="fa-solid fa-scissors"></i></h1>
                    <p>BARBER</p>
                </div>
            </a>
        </div>
    </div>
</div>



<!-- Modal -->
<div class="modal fade" id="kasir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body modal_kasir">

            </div>

        </div>
    </div>
</div>

<div class="offcanvas offcanvas-bottom" style="--bs-offcanvas-height: 100vh;" tabindex="-1" id="canvas_kasir" aria-labelledby="offcanvasBottomLabel">

</div>

<!-- modal fullscreen -->
<div class="modal fade" id="modal_fullscreen" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">

            <div class="modal-body bg-dark body_fullscreen">
                <div class="text-center text-light mt-5 border border-raunded border-light p-5">
                    <h6>UANG KEMBALIAN</h6>
                    <h3>Rp. 10.000</h3>
                    <button class="btn btn-secondary">Selesai</button>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    let myModal = document.getElementById('kasir');
    let modal = bootstrap.Modal.getOrCreateInstance(myModal);
    let mdl_fullscreen = document.getElementById('modal_fullscreen');
    let mdl_fc = bootstrap.Modal.getOrCreateInstance(mdl_fullscreen);
    // mdl_fc.show();

    let myOffcanvas = document.getElementById('canvas_kasir')
    let bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)

    let body_pembayaran = (data, menu) => {
        let biaya = biaya_sewa(data.durasi, parseInt(data.end), data.harga);
        if (data.end == 0) {
            biaya = biaya_sewa(data.start, parseInt(data.end), data.harga);
        }

        let html = '';
        html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';


        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">TOTAL BIAYA</span>';
        html += '<input style="text-align: right;" type="text" class="form-control harga_biaya" value="' + angka(biaya.biaya) + '" readonly>';
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
        html += '<input style="text-align: right;" type="text" class="form-control uang harga_jml_uang" aria-label="Text input with checkbox" placeholder="Uang yang dibayarkan">';
        html += '</div>';

        html += '<div class="input-group mb-3 body_user_hutang">';

        html += '</div>';

        html += '<div class="bg_light p-3 fw-bold" style="text-align:center;font-size:xx-large">';
        html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
        html += '<div class="uang_yang_harus_dibayar">' + angka(biaya.biaya, 'Rp') + '</div>';
        html += '<div class="total_durasi" data-durasi="' + biaya.menit + '" style="font-size:12px">' + biaya.waktu + '</div>';
        html += '</div>';

        html += '<div class="d-grid mt-3">';
        html += '<button data-id="' + data.id + '" data-menu="' + data.menu + '" data-meja_id="' + data.meja_id + '" data-total_biaya="' + biaya.biaya + '" class="btn_primary btn_bayar"><i class="fa-solid fa-cash-register"></i> Bayar</button>';
        html += '</div>';

        html += '</div>';

        return html;

    }


    let data_from_server = [];
    $(document).on("click", ".menu_kasir", function(e) {
        e.preventDefault();
        let menu = $(this).data("menu");

        post("kasir2/get_data", {
            menu
        }).then(res => {
            data_from_server = res.data;
            let html = '';

            html += `<div class="offcanvas-header">
        <h5 class="offcanvas-title">${menu.toUpperCase()}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body small p-3">`;
            res.data.forEach((e, x) => {
                html += `<div class="input-group input-group-lg mb-3">
                <span class="input-group-text" style="width:100px;">${e.meja}</span>
            <select class="form-select durasi_${x}" aria-label="Example select with button addon">
            <option ${(e.ket=="Available"?"selected":"")} value="">Durasi</option>
            <option ${(e.ket=="Open"?"selected":"")} value="0">Open</option>`;

                for (let i = 1; i < 9; i++) {
                    html += `<option value="${i}" ${(e.jam==i?"selected":"")}>${i} Jam</option>`;
                }

                html += `</select>
            <span class="input-group-text ${e.text}" style="width:120px;display: block; text-align: center;">${(e.ket=="Available"|| e.ket=="Over"?e.ket:e.waktu)}</span>`;
                if (e.ket == "Available") {
                    html += `<button class="btn btn-outline-secondary" type="button"><i class="fa-solid fa-ban"></i></button>`;
                    html += `<button data-id="${e.id}" data-meja="${e.meja}" data-menu="${menu}" data-x="${x}" data-order="play" class="btn btn_exe btn-outline-success" type="button"><i class="fa-solid fa-circle-play"></i></button>`;
                } else if (e.ket == "Over") {
                    html += `<button data-id="${e.id}" data-meja="${e.meja}" data-menu="${menu}" data-x="${x}" data-order="add" class="btn btn_exe btn-outline-primary" type="button"><i class="fa-solid fa-clock"></i></button>`;
                    html += `<button data-id="${e.id}" data-meja="${e.meja}" data-menu="${menu}" data-x="${x}" data-order="stop" class="btn btn_exe btn-outline-danger" type="button"><i class="fa-solid fa-circle-stop"></i></button>`;
                } else {
                    html += `<button data-id="${e.id}" data-meja="${e.meja}" data-menu="${menu}" data-x="${x}" data-order="change" class="btn btn_exe btn-outline-warning" type="button"><i class="fa-solid fa-shuffle"></i></button>`;
                    html += `<button data-id="${e.id}" data-meja="${e.meja}" data-menu="${menu}" data-x="${x}" data-order="stop" class="btn btn_exe btn-outline-danger" type="button"><i class="fa-solid fa-circle-stop"></i></button>`;
                }
                html += `</div>`;
            });
            html += `</div>`;
            $("#canvas_kasir").html(html);
            bsOffcanvas.show()
        })
    })


    $(document).on("click", ".btn_exe", function(e) {
        e.preventDefault();
        let order = $(this).data("order");
        let x = $(this).data("x");
        let menu = $(this).data("menu");
        let meja = $(this).data("meja");
        let id = $(this).data("id");
        let durasi = $(".durasi_" + x).val();

        if (order == "change") {
            let avail = 0;
            data_from_server.forEach(e => {
                if (e.ket == "Available") {
                    avail++;
                }
            })
            if (avail == 0) {
                gagal("Tidak ada meja kosong");
                return;
            }
            let html = '';

            html += `<h6>PINDAH KE MEJA:</h6>
                    <select class="form-select form-select-lg pindah_meja" style="font-size:20px">`;
            data_from_server.forEach(e => {
                if (e.ket == "Available") {
                    html += `<option value="${e.meja}">${e.meja}</option>`;
                }
            })
            html += `</select>
            <div class="d-grid mt-3"><button class="btn btn-primary confirm" data-order="${order}" data-meja="${meja}" data-menu="${menu}" data-id="${id}">Ok</button></div>`;

            $(".modal_kasir").html(html);
            modal.show();
            return;
        }

        if (order == "add") {
            let html = "";
            html += `<h6>TAMBAH JAM:</h6>
            <select class="form-select durasi_${x} form-select-lg tambah_jam" style="font-size:20px">`;

            for (let i = 1; i < 9; i++) {
                html += `<option value="${i}" ${(i==1?"selected":"")}>${i} Jam</option>`;
            }

            html += `</select>
            <div class="d-grid mt-3"><button class="btn btn-primary confirm" data-order="${order}" data-meja="${meja}" data-menu="${menu}" data-id="${id}">Ok</button></div>`;

            $(".modal_kasir").html(html);
            modal.show();
            return;
        }

        if (order == "stop") {

            let html = body_pembayaran(data_from_server[x], menu);
            $(".modal_kasir").html(html);
            modal.show();
            return;
        }



        if (order == "play") {
            if (durasi == "") {
                gagal("Durasi belum dipilih");
                return;
            }
        }

        post("kasir2/execute", {
            menu,
            id,
            durasi,
            order
        }).then(res => {

        })
    })


    $(document).on("click", ".confirm", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let order = $(this).data("order");
        let menu = $(this).data("menu");
        let meja = $(this).data("meja");
        let tambah_jam = $(".tambah_jam").val();
        let pindah_meja = $(".pindah_meja").val();


        let html = `<div class="modal-body text-center text-light">`;
        if (order == "add") {
            html += `<h6 class="text-center" style="margin-top: 200px;">Yakin ${meja} Tambah ${tambah_jam} Jam?</h6>`;
        } else {
            html += `<h6 class="text-center" style="margin-top: 200px;">Yakin ${meja} Pindah ke ${pindah_meja}?</h6>`;
        }

        html += `<div class="d-flex justify-content-center gap-2 mt-4">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-primary btn_add_change" data-id="${id}" data-menu="${menu}" data-order="${order}" data-durasi="${tambah_jam}" data-ke_meja="${pindah_meja}">Ya</button>
                    </div>
                </div>`;


        $(".body_fullscreen").html(html);
        mdl_fc.show();

    })
    $(document).on("click", ".btn_add_change", function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let order = $(this).data("order");
        let menu = $(this).data("menu");
        let meja = $(this).data("meja");
        let val = $(this).data("durasi");

        if (order == "change") {
            val = $(this).data("ke_meja");
        }

        post("kasir2/add_change", {
            id,
            order,
            menu,
            meja,
            val
        }).then(res => {

        })


    })

    $(document).on('keyup', '.harga_diskon', function(e) {
        e.preventDefault();
        let harga = angka_to_int($('.harga_biaya').val());
        let diskon = angka_to_int($(this).val());
        if (diskon == "") {
            diskon = 0;
        }
        if (diskon > harga) {
            $(this).val(angka(harga));
            gagal('Diskon melebihi harga!.');
            $('.uang_yang_harus_dibayar').text(angka("0", 'Rp'));
            return false;
        }
        $('.uang_yang_harus_dibayar').text(angka(harga - parseInt(diskon), 'Rp'));

    })
    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();

        if ($('.harga_jml_uang').val() == "") {
            gagal("Uang belum dimasukkan.");
            return;
        }
        let id = $(this).data("id");
        let menu = $(this).data("menu");
        let uang = angka_to_int($('.harga_jml_uang').val());
        let diskon = angka_to_int($('.harga_diskon').val());
        let biaya = angka_to_int($('.uang_yang_harus_dibayar').text());

        if (diskon > biaya) {
            gagal("Diskon melebihi biaya.");
            gagal;
        }
        if (uang < biaya) {
            gagal("Uang kurang.");
            gagal;
        }

        post("kasir2/bayar", {
            id,
            uang,
            biaya,
            diskon,
            menu
        }).then(res => {

        })

    })
</script>
<?= $this->endSection() ?>