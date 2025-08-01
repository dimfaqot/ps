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
            <a href="" style="text-decoration: none;" class="menu_utama" data-menu="hutang">
                <div>
                    <h5><i class="fa-solid fa-cash-register"></i></h5>
                    <div>BAYAR</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_utama" data-menu="billiard">
                <div>
                    <h5><i class="fa-solid fa-bowling-ball"></i></h5>
                    <div>BILLIARD</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_utama" data-menu="ps">
                <div>
                    <h5><i class="fa-brands fa-playstation"></i></h5>
                    <div>PS</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_utama" data-menu="kantin">
                <div>
                    <h5><i class="fa-solid fa-utensils"></i></h5>
                    <div>KANTIN</div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-2">
            <a href="" style="text-decoration: none;" class="menu_utama" data-menu="barber">
                <div>
                    <h5><i class="fa-solid fa-scissors"></i></h5>
                    <div>BARBER</div>
                </div>
            </a>
        </div>
    </div>


    <div class="my-3">
        <div class="mb-3" style="position: relative;">
            <div class="input-group my-3">
                <input type="text" class="form-control users" placeholder="Cari nama pembeli">
                <button class="btn btn-outline-secondary btn_tambah_user" type="button">Tambah</button>
                <div class="data_list"></div>
            </div>
        </div>
    </div>
</div>


<div class="pesanan">
</div>




<!-- Modal -->
<div class="modal fade" id="kasir" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true" style="z-index: 9999;">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-body modal_kasir">
                <iframe src="<?= view('nota') ?>" style="border: none; width: 100%; height: 100%;"></iframe>
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

            <div class="modal-body body_fullscreen">
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
    // modal.show();


    let myOffcanvas = document.getElementById('canvas_kasir')
    let canvas = new bootstrap.Offcanvas(myOffcanvas);


    let data_from_server = [];
    let user_selected = {};

    let billiard = {};
    let ps = {};
    let barber = [];

    let list_barang = [];

    $(document).on('click', '.menu_utama', function(e) {
        e.preventDefault();
        let menu = $(this).data("menu");


        post("kasir/menu_utama", {
            menu
        }).then(res => {
            if (res.status == "200") {
                if (menu == "hutang") {

                    let html = `<div class="container mt-3">
                    <div class="text-center mb-3"><button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Kembali</button></div>
                                    <input class="form-control cari_nama_hutang" type="text" placeholder="Cari nama...">`;

                    res.data.forEach(e => {
                        html += `<a class="btn btn-light text-start w-100 data_cari_nama_hutang" data-no_nota="${e.no_nota}" href="#" role="button">${e.nama}</a>`;
                    })
                    $("#canvas_kasir").html(html);
                    canvas.show();
                }
            } else {
                gagal(res.message);
            }
        })

    });

    $(document).on('keyup', '.cari_nama_hutang', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.data_cari_nama_hutang').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });

    const tampilkan_data_hutang = (no_nota, status) => {

        post("kasir/data_hutang", {
            no_nota
        }).then(res => {
            if (res.status == "200") {
                let temp_kantin = [];
                let temp_barber = [];
                let temp_billiard = {};
                let temp_ps = {};


                let user_selected_temp = {};
                user_selected_temp["id"] = res.data[0].user_id;
                user_selected_temp["nama"] = res.data[0].nama;
                user_selected = user_selected_temp;

                // <th class="text-center">Kategori</th>
                //         <th class="text-center">Barang/Meja</th>
                //         <th class="text-center">Qty/Jam</th>
                //         <th class="text-center">Diskon</th>
                //         <th class="text-center">Harga</th>
                //         </tr>

                res.data.forEach(e => {
                    if (e.kategori == "Kantin") {
                        temp_kantin.push({
                            id: e.barang_id,
                            barang: e.barang,
                            qty: e.qty,
                            harga_satuan: e.harga_satuan
                        });
                    } else if (e.kategori == "Barber") {
                        temp_barber.push({
                            id: e.barang_id,
                            layanan: e.barang,
                            qty: e.qty,
                            harga: e.harga_satuan
                        });
                    } else if (e.kategori == "Billiard") {
                        temp_billiard = {
                            id: e.barang_id,
                            meja: e.barang,
                            durasi: e.qty,
                            harga: e.harga_satuan,
                            total_harga: e.total_harga,
                            ket: e.ket
                        }
                    } else if (e.kategori == "Ps") {
                        temp_ps = {
                            id: e.barang_id,
                            meja: e.barang,
                            durasi: e.qty,
                            harga: e.harga_satuan,
                            total_harga: e.total_harga,
                            ket: e.ket
                        }
                    }

                })

                list_barang = temp_kantin;
                barber = temp_barber;
                billiard = temp_billiard;
                ps = temp_ps;
                $(".body_fullscreen").html(buat_tambah_pesanan(no_nota, status));

                mdl_fc.show();

            } else {
                gagal(res.message);
            }
        })

    }

    $(document).on('click', '.data_cari_nama_hutang', function(e) {
        e.preventDefault();
        let no_nota = $(this).data("no_nota");

        tampilkan_data_hutang(no_nota);

    });

    $(document).on('keyup', '.users', function(e) {
        e.preventDefault();
        let val = $(this).val();

        if (val == "") {
            $(".data_list").html("");
            return;
        }

        post("kasir/search_user", {
            val
        }).then(res => {
            if (res.status == "200") {

                let html = "";
                if (res.data.length == 0) {
                    html += '<div>Data tidak ditemukan!.</div>';
                }
                res.data.forEach(e => {
                    html += '<div data-user_id="' + e.id + '" class="select_user">' + e.nama + '</div>';
                })

                $(".data_list").html(html);
            } else {
                gagal(res.message);
            }
        })
    });

    document.addEventListener("DOMContentLoaded", function() {
        const btnPrintNota = document.getElementById("btn_print_nota");

        if (btnPrintNota) {
            btnPrintNota.addEventListener("click", function(e) {
                e.preventDefault();

                const iframe = document.querySelector("iframe");
                if (iframe && iframe.contentWindow) {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                    console.log('Ok');
                } else {
                    console.log("gagal");
                }
            });
        }
    });

    $(document).on('click', '.select_user', function(e) {
        e.preventDefault();
        let nama = $(this).text();
        let id = $(this).data("user_id");
        user_selected['nama'] = nama;
        user_selected['id'] = id;

        $(".users").val("");
        $(".data_list").html("");

        $(".pesanan").html(pesanan());
    });

    $(document).on('click', '.btn_tambah_user', function(e) {
        e.preventDefault();
        let html = `<div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" class="form-control nama_user" placeholder="Nama...">
                </div>
                <div class="mb-4">
                    <label class="form-label">No. Hp</label>
                    <input type="text" class="form-control hp_user" placeholder="No. hp...">
                </div>

                <div class="d-grid">
                    <button class="btn btn-primary btn_add_user">Save</button>
                </div>`;
        $(".modal_kasir").html(html);
        modal.show();
    });

    $(document).on('click', '.btn_add_user', function(e) {
        e.preventDefault();
        let nama = $(".nama_user").val();
        let hp = $(".hp_user").val();

        post("kasir/add_user", {
            nama,
            hp
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
            } else {
                gagal(res.message);
            }
        })
    });

    const get_data = () => {
        post("kasir/get_data", {
            id: 0
        }).then(res => {
            data_from_server = res.data;

        });
    }

    get_data();

    const pesanan = () => {

        let html = `<div class="container">
        <div class="d-flex justify-content-center gap-2 mb-3">
            <h5 class="border rounded border-3 px-3 py-1">${user_selected.nama.toUpperCase()}</h5>
            <div><button class="btn btn-secondary cancel_kasir">CANCEL</button></div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="row g-2">
                    <div class="col-6">
                        <div style="text-decoration: none;" class="menu_kasir card" data-menu="billiard">
                            <div class="card-body">
                                <h5>BILLIARD</h5>
                                <label>Meja</label>
                                <select class="form-select mb-2 billiard">
                                    <option selected value="">Pilih Meja</option>`;

        data_from_server.billiard.forEach(e => {
            if (e.ket == "Available") {
                html += `<option value="${e.id}">${e.meja}</option>`;
            }
        })
        html += `</select>

                                <label>Jam</label>
                                <select class="form-select jam_billiard">
                                    <option selected value="">Pilih Jam</option>
                                    <option value="0">Open</option>`;
        for (let i = 1; i < 11; i++) {
            html += `<option value="${i}">${i} Jam</option>`;
        }
        html += `</select>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="text-decoration: none;" class="menu_kasir card" data-menu="ps">
                            <div class="card-body">
                                <h5>PS</h5>
                                <label>Meja</label>
                                <select class="form-select mb-2 ps">
                                    <option selected value="">Pilih Meja</option>`;
        data_from_server.ps.forEach(e => {
            if (e.ket == "Available") {
                html += `<option value="${e.id}">${e.meja}</option>`;
            }
        })
        html += `</select>

                                <label>Jam</label>
                                <select class="form-select jam_ps">
                                    <option selected value="">Pilih Jam</option>
                                     <option value="0">Open</option>`;
        for (let i = 1; i < 11; i++) {
            html += `<option value="${i}">${i} Jam</option>`;
        }
        html += `</select>

                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="text-decoration: none;" class="menu_kasir card" data-menu="ps">
                            <div class="card-body">
                                <h5>BARBER</h5>`;
        data_from_server.layanan.forEach(e => {
            html += `<div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="barber" value="${e.id}">
                                <label class="form-check-label">${e.layanan}</label>
                                </div>`;
        })
        html += `</div>
                        </div>
                    </div>
                </div>
                <h1 class="total border rounded text-center mt-3"></h1>
                <div class="d-grid mt-3"><button class="btn btn-primary buat_pesanan">BUAT PESANAN</button></div>
            </div>
            <div class="col-md-6">
                <h5>KANTIN</h5>
                 <div class="input-group my-3">
                    <input type="text" class="form-control cari_barang" placeholder="Cari barang">
                    <button class="btn btn-outline-secondary btn_tambah_barang" type="button">Tambah</button>
                    <div class="data_list_barang"></div>
                </div>
              <div class="list_barang"></div>
            </div>
        </div>
        
    </div>`;

        return html;
    }

    const clear = () => {
        billiard = {};
        ps = {};
        list_barang = [];
        barber = [];
    }

    $(document).on('click', '.cancel_kasir', function(e) {
        e.preventDefault();
        $(".pesanan").html("");
        clear();
    });

    $(document).on('keyup', '.cari_barang', function(e) {
        e.preventDefault();
        let val = $(this).val();
        let order = $(this).data("order");
        let no_nota = $(this).data("no_nota");

        if (val == "") {
            $(".data_list").html("");
            return;
        }

        post("kasir/cari_barang", {
            val
        }).then(res => {
            let html = "";
            if (res.data.length == 0) {
                html += '<div>Data tidak ditemukan!.</div>';
            }
            res.data.forEach(e => {
                html += '<div data-order="' + order + '" data-no_nota="' + no_nota + '" data-id="' + e.id + '" class="select_barang">' + e.barang + '</div>';
            })
            if (order == "tambah_pesanan") {
                $(".data_list_barang_tambah_pesanan").html(html);

            } else {
                $(".data_list_barang").html(html);
            }
        })
    });


    const html_list_barang = () => {

        let html = `<ul class="list-group">`;
        list_barang.forEach((e, i) => {
            html += `<li class="list-group-item">
            <div class="d-flex justify-content-between">
                <div><button class="btn btn-sm btn-light qty" data-order="min" data-id="${e.id}">-</button></div>
                <div style="font-size:19px">${e.barang}</div>
                <div>
                <div class="d-flex justify-content-between gap-3">
                <button class="btn btn-sm btn-light qty" data-order="plus" data-id="${e.id}">+</button>
                <button class="btn btn-sm btn-light">${e.qty}</button>
                <button class="btn btn-sm btn-danger cancel_barang" data-id="${e.id}"><i class="fa-solid fa-trash-can"></i></button>
                </div>
                </div>
            </div>
        </li>`;
        })
        html += `</ul>`;


        return html;
    }
    $(document).on('click', '.btn_pesanan_tambahan', function(e) {
        e.preventDefault();
        let kategori = $(this).data("kategori");
        let no_nota = $(this).data("no_nota");

        if (kategori == "barber") {
            const checkedValues = $('input[name="tambah_pesanan_barber"]:checked').map(function() {
                return this.value;
            }).get();

            let exist = false;
            barber.forEach(e => {
                if (checkedValues.includes(e.id)) {
                    exist = true;
                }
            })

            if (exist) {
                gagal("Barang sudah ada");
                return;
            }

            data_from_server.layanan.forEach(e => {
                if (checkedValues.includes(e.id)) {
                    e.kode = "tambah";
                    barber.push(e);
                }
            })


        }

        if (kategori == "billiard") {
            let id = $(".tambah_pesanan_billiard").val();
            let jam = $(".tambah_pesanan_jam_billiard").val();

            if (jam == "") {
                gagal("Jam belum dipilih");
                return;
            }

            data_from_server.billiard.forEach(e => {
                if (e.id == id) {
                    e.durasi = parseInt(jam) * 60;
                    e.kode = "tambah";
                    e.ket = (jam == 0 ? "Open" : "Reguler");
                    billiard = e;
                }
            })
        }
        console.log(kategori);
        if (kategori == "ps") {
            let id = $(".tambah_pesanan_ps").val();
            let jam = $(".tambah_pesanan_jam_ps").val();

            if (jam == "") {
                gagal("Jam belum dipilih");
                return;
            }

            data_from_server.ps.forEach(e => {
                if (e.id == id) {
                    e.durasi = parseInt(jam) * 60;
                    e.kode = "tambah";
                    e.ket = (jam == 0 ? "Open" : "Reguler");
                    ps = e;
                }
            })
        }

        let html = tabel_tambah_pesanan(no_nota, total_harga("tambah"));
        $(".body_tabel_tambah_pesanan").html(html);

    })
    $(document).on('click', '.cancel_tambah', function(e) {
        e.preventDefault();
        let kategori = $(this).data("kategori");
        let no_nota = $(this).data("no_nota");
        let index = $(this).data("i");

        if (kategori == "barber") {
            let temp_barber = [];
            barber.forEach((e, i) => {
                if (i !== index) {
                    temp_barber.push(e);
                }
            })
            barber = temp_barber;
        }
        if (kategori == "kantin") {
            let temp_kantin = [];
            list_barang.forEach((e, i) => {
                if (i !== index) {
                    temp_kantin.push(e);
                }
            })
            list_barang = temp_kantin;
        }
        if (kategori == "billiard") {
            billiard = {};
        }
        if (kategori == "ps") {
            ps = {};
        }
        let html = tabel_tambah_pesanan(no_nota, total_harga("tambah"));
        $(".body_tabel_tambah_pesanan").html(html);

    })

    $(document).on('click', '.select_barang', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let order = $(this).data("order");
        let no_nota = $(this).data("no_nota");
        if (order == "tambah_pesanan") {
            let exist = false;
            list_barang.forEach(e => {
                if (e.id == id) {
                    exist = true;
                    return;
                }
            })
            if (exist) {
                gagal("Barang sudah ada");
                return;
            }
        }

        data_from_server.barang.forEach(e => {
            if (e.id == id) {
                e.qty = 1;
                if (order == "tambah_pesanan") {
                    e.kode = "tambah";
                }
                list_barang.push(e);
            }
        })


        if (order == "tambah_pesanan") {
            $(".cari_barang_tambah_pesanan").val("");
            $(".data_list_barang_tambah_pesanan").html("");

            let html = tabel_tambah_pesanan(no_nota, total_harga("tambah"));

            $(".body_tabel_tambah_pesanan").html(html);
        } else {
            $(".cari_barang").val("");
            $(".data_list_barang").html("");

            $(".list_barang").html(html_list_barang());

            total_harga();
        }
    });
    $(document).on('keyup', '.tambah_qty', function(e) {
        e.preventDefault();
        let index = $(this).data("i");
        let order = $(this).data("order");
        let qty = $(this).text();

        if (order == "kantin") {
            let temp_data = [];
            list_barang.forEach((e, i) => {
                if (index == i) {
                    e.qty = parseInt(qty);
                }
                temp_data.push(e);
            })
            list_barang = temp_data;
            return;
        }
        if (order == "barber") {
            let temp_data = [];
            barber.forEach((e, i) => {
                if (index == i) {
                    e.qty = parseInt(qty);
                }
                temp_data.push(e);
            })
            barber = temp_data;
            return;
        }
    });
    $(document).on('click', '.cancel_barang', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let temp = [];
        list_barang.forEach(e => {
            if (e.id != id) {
                temp.push(e);
            }
        })

        list_barang = temp;
        $(".list_barang").html(html_list_barang());
        total_harga();
    });
    $(document).on('click', '.qty', function(e) {
        e.preventDefault();
        let id = $(this).data("id");
        let order = $(this).data("order");
        let temp = [];
        list_barang.forEach(e => {
            if (e.id == id) {
                if (order == "min") {
                    e.qty -= 1;
                } else {
                    e.qty += parseInt(1);
                }
            }
            if (e.qty > 0) {
                temp.push(e);
            }
        })

        list_barang = temp;
        $(".list_barang").html(html_list_barang());
        total_harga();
    });

    const total_harga = (order = undefined) => {
        let total = 0;

        if (billiard.id) {
            if (billiard.durasi > 0) {
                total += parseInt(billiard.durasi / 60) * parseInt(billiard.harga);
            }
        }
        if (ps.id) {
            if (ps.durasi > 0) {
                total += parseInt(ps.durasi / 60) * parseInt(ps.harga);
            }
        }

        list_barang.forEach(e => {
            total += parseInt(e.harga_satuan) * parseInt(e.qty);
        })
        barber.forEach(e => {
            total += parseInt(e.harga) * parseInt(e.qty);
        })

        if (order == "tambah") {
            return total;
        } else {
            $(".total").text(angka(total));
        }
    }

    const total_harga_int = (order = undefined) => {
        let total = 0;
        let biaya = 0;
        let diskon = 0;

        if (billiard.id) {
            if (billiard.durasi > 0) {
                if (order == "hutang") {
                    total += parseInt(billiard.total_harga);
                } else {
                    total += parseInt(billiard.durasi / 60) * parseInt(billiard.harga);
                }
                biaya += parseInt(billiard.biaya);
                diskon += parseInt(billiard.diskon);
            }
        }
        if (ps.id) {
            if (ps.durasi > 0) {
                if (order == "hutang") {
                    total += parseInt(ps.total_harga);

                } else {
                    total += parseInt(ps.durasi / 60) * parseInt(ps.harga);

                }
                biaya += parseInt(ps.biaya);
                diskon += parseInt(ps.diskon);
            }
        }

        list_barang.forEach(e => {
            total += parseInt(e.harga_satuan) * parseInt(e.qty);
            biaya += parseInt(e.biaya);
            diskon += parseInt(e.diskon);
        })
        barber.forEach(e => {
            total += parseInt(e.harga) * parseInt(e.qty);
            biaya += parseInt(e.biaya);
            diskon += parseInt(e.diskon);
        })

        let res = {
            biaya,
            total,
            diskon
        }
        return res;
    }

    $(document).on('change', '.billiard', function(e) {
        e.preventDefault();
        let id = $(this).val();

        if (id == "") {
            billiard = {};
        } else {
            data_from_server.billiard.forEach(e => {
                if (e.id == id) {
                    let jam = $(".jam_billiard").val();
                    e.durasi = parseInt(jam) * 60;
                    billiard = e;
                }
            })
        }

        total_harga();
    });

    $(document).on('change', '.jam_billiard', function(e) {
        e.preventDefault();
        let id = $(".billiard").val();
        let jam = $(this).val();

        if (jam == "") {
            billiard = {};
        } else {
            data_from_server.billiard.forEach(e => {
                if (e.id == id) {
                    let jam = $(this).val();
                    e.durasi = parseInt(jam) * 60;
                    billiard = e;
                }
            })
        }
        total_harga();
    });
    $(document).on('change', '.ps', function(e) {
        e.preventDefault();
        let id = $(this).val();

        if (id == "") {
            ps = {};
        } else {
            data_from_server.ps.forEach(e => {
                if (e.id == id) {
                    let jam = $(".jam_ps").val();
                    e.durasi = parseInt(jam) * 60;
                    ps = e;
                }
            })
        }

        total_harga();
    });
    $(document).on('change', '.jam_ps', function(e) {
        e.preventDefault();
        let id = $(".ps").val();
        let jam = $(this).val();

        if (jam == "") {
            ps = {};
        } else {
            data_from_server.ps.forEach(e => {
                if (e.id == id) {
                    let jam = $(this).val();
                    e.durasi = parseInt(jam) * 60;
                    ps = e;
                }
            })
        }
        total_harga();
    });

    $(document).on('change', '.form-check-input[name="barber"]', function() {
        const checkedValues = $('input[name="barber"]:checked').map(function() {
            return this.value;
        }).get();
        let temp = [];
        data_from_server.layanan.forEach(e => {
            if (checkedValues.includes(e.id)) {
                temp.push(e);
            }
        })
        barber = temp;
        total_harga();
    });




    $(document).on('click', '.tambah_pesanan', function(e) {
        e.preventDefault();
        let kategori = $(this).data('kategori');
        let no_nota = $(this).data('nota');

        let html = ``;
        if (kategori == "billiard" || kategori == "ps") {

            post("kasir/get_data", {
                id: 0
            }).then(res => {
                html += `<div style="text-decoration: none;" class="card">
                            <div class="card-body">
                                <h5>${kategori.toUpperCase()}</h5>
                                <label>Meja</label>
                                <select class="form-select mb-2 tambah_pesanan_${kategori}">
                                    <option selected value="">Pilih Meja</option>`;

                res.data[kategori].forEach(e => {
                    if (e.ket == "Available") {
                        html += `<option value="${e.id}">${e.meja}</option>`;
                    }
                })
                html += `</select>

                                <label>Jam</label>
                                <select class="form-select tambah_pesanan_jam_${kategori}">
                                    <option selected value="">Pilih Jam</option>
                                    <option value="0">Open</option>`;
                for (let i = 1; i < 11; i++) {
                    html += `<option value="${i}">${i} Jam</option>`;
                }
                html += `</select>
                            </div>
                        </div>
                        <div class="d-grid mt-3"><button class="btn btn-primary btn_pesanan_tambahan" data-kategori="${kategori}" data-no_nota="${no_nota}">TAMBAH</button></div>
                        `;
                $(".modal_kasir").html(html);
                modal.show();
                return;
            });

        }
        if (kategori == "kantin") {
            html += `<h5>KANTIN</h5>
                 <div class="input-group my-3">
                    <input type="text" class="form-control cari_barang cari_barang_tambah_pesanan" data-no_nota="${no_nota}" data-order="tambah_pesanan" placeholder="Cari barang">
                    <button class="btn btn-outline-secondary btn_tambah_barang" type="button">Tambah</button>
                    <div class="data_list_barang data_list_barang_tambah_pesanan"></div>
                </div>
              <div class="list_barang_tambah_pesanan"></div>`;
            $(".modal_kasir").html(html);
            modal.show();
            return;
        }
        if (kategori == "barber") {
            html += `<div class="card">
                            <div class="card-body">
                                <h5>BARBER</h5>`;
            data_from_server.layanan.forEach(e => {
                html += `<div class="form-check form-check-inline">
                                    <input class="form-check-input" type="checkbox" name="tambah_pesanan_barber" value="${e.id}">
                                <label class="form-check-label">${e.layanan}</label>
                                </div>`;
            })
            html += `</div>
                        </div>
                        
                         <div class="d-grid mt-3"><button class="btn btn-primary btn_pesanan_tambahan" data-kategori="barber" data-no_nota="${no_nota}">TAMBAH</button></div>`;
            $(".modal_kasir").html(html);
            modal.show();
            return;
        }

    });

    const tabel_tambah_pesanan = (no_nota, total, status) => {

        let html = '';

        if (list_barang.length > 0) {
            list_barang.forEach((e, i) => {
                html += `<tr>
                            <td class="text-center">${i+1}</td>
                            <td>Kantin</td>
                            <td>${(e.kode=="tambah"?'<a style="text-decoration:none;color:red" data-kategori="kantin" class="cancel_tambah" data-no_nota="'+no_nota+'" data-i="'+i+'" href="">'+e.barang+'</a>':e.barang)}</td>
                            <td class="text-center tambah_qty" data-order="kantin" data-i="${i}" contenteditable="${(status=="selesai"?"false":"true")}">${angka(e.qty)}</td>
                            <td class="text-end diskon" data-kategori="kantin" data-index="${i}" contenteditable="${(status=="selesai"?"true":"false")}">0</td>
                            <td class="text-end">${angka(e.harga_satuan*e.qty)}</td>
                            </tr>`;
            })

        }

        if (barber.length > 0) {
            barber.forEach((e, i) => {
                html += `<tr>
                            <td class="text-center">${i+list_barang.length+1}</td>
                            <td>Barber</td>
                             <td>${(e.kode=="tambah"?'<a style="text-decoration:none;color:red" data-kategori="barber" class="cancel_tambah" data-no_nota="'+no_nota+'" data-i="'+i+'" href="">'+e.layanan+'</a>':e.layanan)}</td>
                            <td class="text-center tambah_qty" data-order="barber" data-i="${i}" contenteditable="${(status=="selesai"?"false":"true")}">${(e.qty==undefined?1:e.qty)}</td>
                            <td class="text-end diskon" data-kategori="barber" data-index="${i}" contenteditable="${(status=="selesai"?"true":"false")}">0</td>
                            <td class="text-end">${angka(e.qty==undefined?e.harga:e.harga*e.qty)}</td>
                            </tr>`;
            })

        }
        if (billiard.id) {
            let no = list_barang.length + barber.length + 1;
            html += `<tr>`;
            // html += `<td class="text-center">${(billiard.kode=="tambah"?'<a style="text-decoration:none;color:red" data-kategori="billiard" class="cancel_tambah" data-no_nota="'+no_nota+'" href="">'+no+'</a>':no)}</td>
            //             <td><a href="" style="text-decoration:none" class="options" data-order="tambah" data-ket="${billiard.ket}" data-kategori="billiard" data-barang="${billiard.meja}" data-qty="${billiard.durasi}" data-no_nota="${no_nota}" data-id="${billiard.id}">Billiard</a></td>
            //             <td>${billiard.meja}</td>`;

            if (status == "selesai") {
                html += `<td class="text-center">${no}</td>
                                        <td>Billiard</a></td>
                                        <td>${billiard.meja}</td>`;
                html += `<td class="text-center">${(billiard.ket=="Open"?menit_ke_jam(billiard.durasi):menit_ke_jam(billiard.durasi,"reg"))}</td>`;
            } else {
                html += `<td class="text-center">${(billiard.kode=="tambah"?'<a style="text-decoration:none;color:red" data-kategori="billiard" class="cancel_tambah" data-no_nota="'+no_nota+'" href="">'+no+'</a>':no)}</td>
                            <td><a href="" style="text-decoration:none" class="options" data-order="tambah" data-ket="${billiard.ket}" data-kategori="billiard" data-barang="${billiard.meja}" data-qty="${billiard.durasi}" data-no_nota="${no_nota}" data-id="${billiard.id}">Billiard</a></td>
                            <td>${billiard.meja}</td>`;
                html += `<td class="text-center">${(billiard.ket=="Open"?"Open":angka(billiard.durasi/60))}</td>`;

            }


            html += `<td class="text-end diskon" data-kategori="billiard" data-index="${billiard.id}" contenteditable="${(status=="selesai"?"true":"false")}">0</td>`;
            if (status == "selesai") {
                html += `<td class="text-end">${angka((billiard.ket=="Open"?billiard.total_harga:(billiard.durasi/60)*billiard.harga))}</td>`;
            } else {
                html += `<td class="text-end">${angka((billiard.ket=="Open"?0:(billiard.durasi/60)*billiard.harga))}</td>`;

            }

            html += `</tr>`;

        }

        if (ps.id) {
            let no = list_barang.length + barber.length + 1;
            if (ps.id) {
                no = no + 1;
            }
            html += `<tr>`;

            if (status == "selesai") {
                html += `<td class="text-center">${no}</td>
                             <td>Ps</td>
                                <td>${ps.meja}</td>`;
                html += `<td class="text-center">${(ps.ket=="Open"?menit_ke_jam(ps.durasi):menit_ke_jam(ps.durasi,"reg"))}</td>`;
            } else {
                html += `<td class="text-center">${(ps.kode=="tambah"?'<a style="text-decoration:none;color:red" data-kategori="ps" class="cancel_tambah" data-no_nota="'+no_nota+'" href="">'+no+'</a>':no)}</td>
                             <td><a href="" style="text-decoration:none" class="options" data-ket="${ps.ket}" data-order="tambah" data-kategori="ps" data-ket="'+ps.ket+'" data-barang="${ps.meja}" data-qty="${ps.durasi}" data-no_nota="${no_nota}" data-id="${ps.id}">Ps</a></td>
                                <td>${ps.meja}</td>`;
                html += `<td class="text-center">${(ps.ket=="Open"?"Open":angka(ps.durasi/60))}</td>`;

            }

            html += `<td class="text-end diskon" data-kategori="ps" data-index="${ps.id}" contenteditable="${(status=="selesai"?"true":"false")}">0</td>`;


            if (status == "selesai") {
                html += `<td class="text-end">${angka((ps.ket=="Open"?ps.total_harga:(ps.durasi/60)*ps.harga))}</td>`;
            } else {
                html += `<td class="text-end">${angka((ps.ket=="Open"?0:(ps.durasi/60)*ps.harga))}</td>`;

            }


            html += `</tr>`;

        }

        html += ` <tr>
                                    <th class="text-end" colspan="5">TOTAL</th>
                                    <th class="text-end total_tambah_pesanan">${angka(total)}</th>
                                </tr>`;
        return html;
    }

    const buat_tambah_pesanan = (no_nota, status = undefined) => {


        let html = `<div class="container ${(status=="selesai"?"border border-danger rounded p-3":"")}" ${(status=="selesai"?'style="background-color:#F5F5F5"':"")}>`;
        html += `<h5 class="text-center mt-4">DETAIL PESANAN</h5>`;
        html += `<div class="row">`;
        if (status == undefined) {
            if (billiard.id == undefined) {
                html += `<div class="col-1">
            <a href="" style="text-decoration: none;" class="tambah_pesanan" data-nota="${no_nota}" data-kategori="billiard">
                    <h2><i class="fa-solid fa-bowling-ball"></i></h2>
                    <div>Billiard</div>
            </a>
        </div>`
            }
            if (ps.id == undefined) {
                html += `<div class="col-1">
            <a href="" style="text-decoration: none;" class="tambah_pesanan" data-nota="${no_nota}" data-kategori="ps">
                    <h2><i class="fa-brands fa-playstation"></i></h2>
                    <div>Ps</div>
            </a>
        </div>`;
            }


            html += `<div class="col-1">
                <a href="" style="text-decoration: none;" class="tambah_pesanan" data-nota="${no_nota}" data-kategori="kantin">
                        <h2><i class="fa-solid fa-utensils"></i></h2>
                        <div>Kantin</div>
                </a>
            </div>
            <div class="col-1">
                <a href="" style="text-decoration: none;" class="tambah_pesanan" data-nota="${no_nota}" data-kategori="barber">
                        <h2><i class="fa-solid fa-scissors"></i></h2>
                        <div>Barber</div>
                </a>
            </div>
        </div>`;
        }

        html += `<table class="table table-bordered">
                    <thead>
                        <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Barang/Meja</th>
                        <th class="text-center">Qty/Jam</th>
                        <th class="text-center">Diskon</th>
                        <th class="text-center">Harga</th>
                        </tr>
                    </thead>
                    <tbody class="body_tabel_tambah_pesanan">`;


        html += tabel_tambah_pesanan(no_nota, total_harga_int().total, status);

        html += `</tbody>
                </table>
                <div class="text-center">`;

        html += `<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>`;
        if (status == undefined) {
            html += `<button type="button" class="btn btn-success btn_tambah_pesanan mx-2" data-order="hutang" data-no_nota="${no_nota}">Ubah Pesanan</button>`;
        }

        if (status == "selesai") {

            html += `<hr><div class="text-center"><button class="btn btn-lg btn-primary bayar_langsung" data-order="hutang" data-no_nota="${no_nota}">BAYAR</button></div>`;
        } else {
            html += `<hr><div class="text-center"><button class="btn btn-lg btn-primary bayar_hutang" data-order="selesai" data-no_nota="${no_nota}">PROSES</button></div>`;

        }

        html += `</div>`;
        html += `</div>`;

        return html;
    }
    const buat_pesanan = () => {

        let html = `<div class="container">`;
        html += `<h5 class="text-center mt-4">DETAIL PESANAN</h5>`;

        html += `<table class="table table-bordered">
                    <thead>
                        <tr>
                        <th class="text-center">#</th>
                        <th class="text-center">Kategori</th>
                        <th class="text-center">Barang/Meja</th>
                        <th class="text-center">Qty/Jam</th>
                        <th class="text-center">Diskon</th>
                        <th class="text-center">Harga</th>
                        </tr>
                    </thead>
                    <tbody>`;
        if (list_barang.length > 0) {
            list_barang.forEach((e, i) => {
                html += `<tr>
                            <td class="text-center">${i+1}</td>
                            <td>Kantin</td>
                            <td>${e.barang}</td>
                            <td class="text-center">${angka(e.qty)}</td>
                            <td class="text-end diskon" data-kategori="kantin" data-index="${i}" contenteditable="true">0</td>
                            <td class="text-end">${angka(e.harga_satuan*e.qty)}</td>
                            </tr>`;
            })

        }

        if (barber.length > 0) {
            barber.forEach((e, i) => {
                html += `<tr>
                            <td class="text-center">${i+list_barang.length+1}</td>
                            <td>Barber</td>
                            <td>${e.layanan}</td>
                            <td class="text-center">1</td>
                            <td class="text-end diskon" data-kategori="barber" data-index="${i}" contenteditable="true">0</td>
                            <td class="text-end">${angka(e.harga)}</td>
                            </tr>`;
            })

        }
        if (billiard.id) {
            let no = list_barang.length + barber.length + 1;
            html += `<tr>
                        <td class="text-center">${no}</td>
                        <td>${billiard}</td>
                        <td>${billiard.meja}</td>`;


            html += `<td class="text-center">${(billiard.durasi==0?"Open":angka(billiard.durasi/60))}</td>`;


            html += `<td class="text-end diskon" data-kategori="billiard" data-index="${billiard.id}" contenteditable="true">0</td>`;

            html += `<td class="text-end">${angka(billiard.harga*(billiard.durasi/60))}</td>`;

            html += `</tr>`;

        }
        if (ps.id) {
            let no = list_barang.length + barber.length + 1;
            if (ps.id) {
                no = no + 1;
            }
            html += `<tr>
                        <td class="text-center">${no}</td>
                         <td>${ps}</td>
                            <td>${ps.meja}</td>`;


            html += `<td class="text-center">${(ps.durasi==0?"Open":angka(ps.durasi/60))}</td>`;
            html += `<td class="text-end diskon" data-kategori="ps" data-index="${ps.id}" contenteditable="true">0</td>`;


            html += `<td class="text-end">${angka(ps.harga*(ps.durasi/60))}</td>`;

            html += `</tr>`;

        }
        html += `<tr>
                                    <th class="text-end" colspan="5">TOTAL</th>
                                    <th class="text-end">${angka(total_harga_int().total)}</th>
                                </tr>`;
        html += `</tbody>
                </table>
                <div class="text-center">`;

        html += `<button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">Cancel</button>`;


        html += `<button type="button" class="btn btn-success bayar_langsung mx-2" data-order="langsung">Bayar Langsung</button>`;

        html += `<button type="button" class="btn btn-primary bayar_nanti">Bayar Nanti</button>`;

        html += `</div>`;
        html += `</div>`;

        return html;
    }

    const options = {

        pindah: function(kategori, barang, id, qty, no_nota) {
            post("kasir/get_data", {
                id: 0
            }).then(res => {
                let html = `<ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link pindah_tambah" data-order="tambah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}" href="#">Tambah</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active pindah_tambah" data-order="pindah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}" href="#">Pindah</a>
                                </li>
                                </ul>`;
                html += `<h6 class="my-2">${kategori.toUpperCase()} ${barang.toUpperCase()}</h6>
                                    <label>Meja</label>
                                    <select class="form-select mb-2 option_meja">
                                        <option selected value="">Pilih Meja</option>`;

                res.data[kategori].forEach(e => {
                    if (e.ket == "Available") {
                        html += `<option value="${e.id}">${e.meja}</option>`;
                    }
                })
                html += `</select>`;

                html += `<div class="d-grid mt-4"><button class="btn btn-primary btn_options" data-order="pindah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}">PINDAH</button></div>`;


                $(".modal_kasir").html(html);

            });
        },
        tambah: function(kategori, barang, id, qty, no_nota) {
            let html = `<ul class="nav nav-tabs">
                            <li class="nav-item">
                                <a class="nav-link active pindah_tambah" data-order="tambah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}" href="#">Tambah</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link pindah_tambah" data-order="pindah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}" href="#">Pindah</a>
                            </li>
                            </ul>`;
            html += `<h6 class="my-2">${kategori.toUpperCase()} ${barang.toUpperCase()}</h6>
                                <select class="form-select mb-2 option_billiard">
                                    <option selected value="">Pilih Durasi</option>`;

            for (let i = 1; i < 11; i++) {
                html += `<option value="${i}">${i} Jam</option>`;
            }

            html += `</select>`;

            html += `<div class="d-grid mt-4"><button class="btn btn-primary btn_options" data-order="tambah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}">TAMBAH</button></div>`;


            return html;
        },
        open: function(kategori, barang, id, qty, no_nota) {
            post("kasir/get_data", {
                id: 0
            }).then(res => {
                let html = `<ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#">Pindah</a>
                                </li>
                                </ul>`;
                html += `<h6 class="my-2">${kategori.toUpperCase()} ${barang.toUpperCase()}</h6>
                                    <label>Meja</label>
                                    <select class="form-select mb-2 option_meja">
                                        <option selected value="">Pilih Meja</option>`;

                res.data[kategori].forEach(e => {
                    if (e.ket == "Available") {
                        html += `<option value="${e.id}">${e.meja}</option>`;
                    }
                })
                html += `</select>`;

                html += `<div class="d-grid mt-4"><button class="btn btn-primary btn_options" data-order="pindah" data-kategori="${kategori}" data-barang="${barang}" data-no_nota="${no_nota}" data-qty="${qty}" data-id="${id}">PINDAH</button></div>`;


                $(".modal_kasir").html(html);
                modal.show();
            });
        }
    }


    $(document).on('click', '.options', function(e) {
        e.preventDefault();
        let order = $(this).data("order");
        let kategori = $(this).data("kategori");
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        let qty = $(this).data("qty");
        let ket = $(this).data("ket");
        let no_nota = $(this).data("no_nota");


        if (kategori == "billiard" || kategori == "ps") {
            if (ket == "Open") {
                options.open(kategori, barang, id, qty, no_nota);
            } else {
                let html = options.tambah(kategori, barang, id, qty, no_nota);
                $(".modal_kasir").html(html);
                modal.show();

            }
        }

    })

    $(document).on('click', '.pindah_tambah', function(e) {
        e.preventDefault();
        let kategori = $(this).data("kategori");
        let order = $(this).data("order");
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        let qty = $(this).data("qty");
        let no_nota = $(this).data("no_nota");

        if (kategori == "billiard" || kategori == "ps") {
            if (order == "pindah") {
                options[order](kategori, barang, id, qty, no_nota);
            } else {
                let html = options.tambah(kategori, barang, id, qty, no_nota);
                $(".modal_kasir").html(html);
            }
        }



    });
    $(document).on('click', '.btn_options', function(e) {
        e.preventDefault();
        let kategori = $(this).data("kategori");
        let order = $(this).data("order");
        let barang = $(this).data("barang");
        let id = $(this).data("id");
        let no_nota = $(this).data("no_nota");
        let qty = $(this).data("qty");
        let durasi = $(".option_billiard").val();
        let meja_tujuan_id = $(".option_meja").val();

        if (durasi == "") {
            gagal("Durasi belum dipilih");
            return;
        }

        post("kasir/options", {
            kategori,
            id,
            order,
            durasi,
            no_nota,
            meja_tujuan_id
        }).then(res => {
            data_from_server = res.data;

        });

    });
    $(document).on('click', '.buat_pesanan', function(e) {
        e.preventDefault();

        $("#canvas_kasir").html(buat_pesanan());

        canvas.show();
    });

    let body_pembayaran = (order = undefined, total, biaya, diskon, no_nota) => {

        let html = '';
        html += '<div class="soal_yakin p-3 bg_warning_light" style="border-radius: 5px;">';


        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">TOTAL BIAYA</span>';
        html += '<input style="text-align: right;" type="text" class="form-control harga_biaya" value="' + angka(total) + '" readonly>';
        html += '</div>';

        html += '<div class="input-group mb-3">';
        html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">DISKON</span>';
        html += '<input style="text-align: right;" type="text" placeholder="Potongan" class="form-control" value="' + angka(diskon) + '" readonly>';
        html += '</div>';

        html += '<div class="bg_light p-3 fw-bold" style="text-align:center;font-size:xx-large">';
        html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
        html += '<div>' + angka(total - diskon, 'Rp') + '</div>';
        html += '</div>';
        html += `
                    <div class="text-center mt-3">UANG PEMBAYARAN</div>
                    <input class="form-control form-control-lg harga_jml_uang text-center uang" type="text" value="0">
                `;
        html += '<div>';
        html += '</div>';

        html += '<div class="d-grid mt-3">';
        html += '<button class="btn_primary btn_bayar" data-no_nota="' + no_nota + '" data-order="' + order + '" data-total="' + total + '" data-biaya="' + biaya + '" data-diskon="' + diskon + '"><i class="fa-solid fa-cash-register"></i> Bayar</button>';
        html += '</div>';

        html += '</div>';

        return html;

    }

    $(document).on('click', '.bayar_langsung', function(e) {
        e.preventDefault();
        let order = $(this).data("order");
        let no_nota = $(this).data("no_nota");
        if (billiard.id) {
            if (billiard.durasi == 0) {
                gagal("Billiard open tidak bisa bayar langsung.");
                return;
            }
        }
        if (ps.id) {
            if (ps.durasi == 0) {
                gagal("Ps open tidak bisa bayar langsung.");
                return;
            }
        }

        let diskonData = [];
        $('td.diskon').each(function() {
            let index = $(this).data('index');
            let kategori = $(this).data('kategori');
            let diskon = $(this).text();
            diskonData.push({
                index,
                kategori,
                diskon
            });
        });

        let temp_list_barang = [];
        list_barang.forEach((e, i) => {
            diskonData.forEach((elem, idx) => {
                if (elem.kategori == "kantin" && elem.index == i) {
                    e['diskon'] = elem.diskon;
                    e['biaya'] = (e.harga_satuan * e.qty) - elem.diskon;
                    temp_list_barang.push(e);
                }
            })
        })

        list_barang = temp_list_barang;

        let temp_barber = [];
        barber.forEach((e, i) => {
            diskonData.forEach((elem, idx) => {
                if (elem.kategori == "barber" && elem.index == i) {
                    e['diskon'] = elem.diskon;
                    e['biaya'] = (e.harga * e.qty) - elem.diskon;
                    temp_barber.push(e);
                }
            })
        })

        barber = temp_barber;

        if (billiard.id) {
            diskonData.forEach((elem, idx) => {
                if (elem.kategori == "billiard") {
                    billiard['diskon'] = elem.diskon;
                    billiard['biaya'] = (billiard.harga * (billiard.durasi / 60)) - elem.diskon;
                }
            })
        }

        if (ps.id) {
            diskonData.forEach((elem, idx) => {
                if (elem.kategori == "ps") {
                    ps['diskon'] = elem.diskon;
                    ps['biaya'] = (ps.harga * (ps.durasi / 60)) - elem.diskon;
                }
            })
        }
        let pembayaran = total_harga_int(order);

        let html = body_pembayaran(order, pembayaran.total, pembayaran.biaya, pembayaran.diskon, no_nota);
        $(".modal_kasir").html(html);
        modal.show();
    });

    function bluetooth_print(nota = "111") {
        bluetoothSerial.isEnabled(function() {
            bluetoothSerial.list(function(devices) {
                const printer = devices.find(d => d.name.includes("RPP02N"));
                if (!printer) {
                    gagal("Printer RPP02N tidak ditemukan.");
                    return;
                }

                bluetoothSerial.connect(printer.id, function() {
                    const ESC = '\x1B';
                    const reset = ESC + '@';
                    const alignCenter = ESC + 'a' + '\x01';
                    const feed = '\n\n\n';

                    const payload = reset + alignCenter + nota + feed;

                    bluetoothSerial.write(payload, function() {
                        sukses("Nota berhasil dicetak.");
                    }, function(err) {
                        gagal("Gagal kirim ke printer: " + err);
                    });
                }, function(err) {
                    gagal("Gagal koneksi ke printer: " + err);
                });
            });
        }, function() {
            gagal("Bluetooth tidak aktif.");
        });
    }

    $(document).on('click', '.btn_bayar', function(e) {
        e.preventDefault();
        let order = $(this).data("order");
        let total = $(this).data("total");
        let diskon = $(this).data("diskon");
        let no_nota = $(this).data("no_nota");
        let uang = angka_to_int($(".harga_jml_uang").val());

        if (uang < (total - diskon)) {
            gagal("Uang kurang.");
            return;
        }

        if (diskon > total) {
            gagal("Diskon terlalu besar.");
            return;
        }
        let pembayaran = total_harga_int();
        post("kasir/bayar_langsung", {
            kantin: list_barang,
            customer: user_selected,
            order,
            billiard,
            barber,
            ps,
            total,
            diskon,
            uang,
            no_nota
        }).then(res => {
            if (res.status == "200") {
                let html = `<iframe src="<?= base_url('kasir/nota/') ?>${res.data3}" style="border: none; width: 100%; height: 600px;"></iframe>`;
                html += '<div class="text-center mt-5"><button class="btn btn-secondary selesai me-2i">Selesai</button> <button id="btn_print_nota" class="btn btn-primary">Print Nota</button></div>';
                $(".modal_kasir").html(html);
                modal.show();
            } else {
                gagal(res.message);
            }
        })
    });

    $(document).on('click', '.bayar_nanti', function(e) {
        e.preventDefault();


        post("kasir/bayar_nanti", {
            kantin: list_barang,
            customer: user_selected,
            billiard,
            barber,
            ps
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                gagal(res.message);
            }
        })
    });
    $(document).on('click', '.selesai', function(e) {
        e.preventDefault();
        location.reload();
    });
    $(document).on('click', '.bayar_hutang', function(e) {
        e.preventDefault();
        let no_nota = $(this).data("no_nota");
        let order = $(this).data("order");

        mdl_fc.hide();
        $(".body_fullscreen").html("");


        tampilkan_data_hutang(no_nota, order);
    });
    $(document).on('click', '.btn_tambah_pesanan ', function(e) {
        e.preventDefault();
        let no_nota = $(this).data("no_nota");
        post("kasir/tambah_pesanan", {
            kantin: list_barang,
            billiard,
            barber,
            no_nota,
            ps
        }).then(res => {
            if (res.status == "200") {

            } else {
                gagal(res.message);
            }
        })
    });
</script>
<?= $this->endSection() ?>