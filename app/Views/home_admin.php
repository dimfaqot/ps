<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<?php
$db = db('aturan');
$q = $db->orderBy('poin', 'DESC')->get()->getResultArray();
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<div class="container">
    <div class="d-flex gap-2">
        <?php if (session('role') == 'Root'): ?>
            <button data-bs-toggle="modal" class="btn_info mb-2" data-bs-target="#modal_user">Users</button>
            <a href="<?= base_url('absen/reset_absen'); ?>" class="btn_danger mb-2">Reset Absen</a>
        <?php endif; ?>
        <a href="" class="btn_purple btn_saldo_tap mb-2">Tap</a>
        <button data-id="<?= session('id'); ?>" data-nama="<?= user()['nama']; ?>" class="btn_primary mb-2 fw-bold poin_absen">POIN: <?= poin_absen(session('id'))['poin']; ?></button>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="div_card bg_primary border border-primary text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-brands fa-playstation mb-1"></i> PS
                        <div style="font-weight: normal;font-size:x-small" class="total_rental"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <a href="" class="btn btn-sm btn-light koperasi" data-usaha="Ps" style="font-size: medium;"><i class="fa-solid fa-piggy-bank"></i></a>
                        <select class="form-select get_pendapatan" data-tabel="rental">
                            <?php foreach (get_tahuns('rental') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div style="font-weight: normal;font-size:x-small; margin-top:-5px" class="mb-1 div_data_tap_rental"></div>
                <div class="card p-2">
                    <canvas id="chart_rental" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="div_card bg_success border border-success text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-solid fa-bowling-ball mb-1"></i> BILLIARD
                        <div style="font-weight: normal;font-size:x-small" class="total_billiard"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <a href="" class="btn btn-sm btn-light koperasi" data-usaha="Billiard" style="font-size: medium;"><i class="fa-solid fa-piggy-bank"></i></a>
                        <select class="form-select get_pendapatan" data-tabel="billiard">
                            <?php foreach (get_tahuns('billiard_2') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div style="font-weight: normal;font-size:x-small; margin-top:-5px" class="mb-1 div_data_tap_billiard"></div>
                <div class="card p-2">
                    <canvas id="chart_billiard" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="div_card bg_purple border border-success text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-solid fa-shop mb-1"></i> KANTIN
                        <div style="font-weight: normal;font-size:x-small" class="total_kantin"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <a href="" class="btn btn-sm btn-light koperasi" data-usaha="Kantin" style="font-size: medium;"><i class="fa-solid fa-piggy-bank"></i></a>
                        <select class="form-select get_pendapatan" data-tabel="kantin">
                            <?php foreach (get_tahuns('kantin') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div style="font-weight: normal;font-size:x-small; margin-top:-5px" class="mb-1 div_data_tap_kantin"></div>
                <div class="card p-2">
                    <canvas id="chart_kantin" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="div_card bg_main border border-success text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-solid fa-scissors"></i> BARBER
                        <div style="font-weight: normal;font-size:x-small" class="total_barber"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <a href="" class="btn btn-sm btn-light koperasi" data-usaha="barber" style="font-size: medium;"><i class="fa-solid fa-piggy-bank"></i></a>
                        <select class="form-select get_pendapatan" data-tabel="barber">
                            <?php foreach (get_tahuns('barber') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div style="font-weight: normal;font-size:x-small;margin-top:-5px" class="div_data_tap_barber mb-1"></div>
                <div class="card p-2">
                    <canvas id="chart_barber" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Modal detail pendapatan-->
<div class="modal fade" id="detail_pendapatan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_detail_pendapatan">

            </div>
        </div>
    </div>
</div>
<!-- Modal detail koperasi-->
<div class="modal fade" id="koperasi" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_koperasi">

            </div>
        </div>
    </div>
</div>
<!-- Modal detail poin_absen-->
<div class="modal fade" id="poin_absen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_poin_absen">

            </div>
        </div>
    </div>
</div>
<!-- Modal data tap-->
<div class="modal fade" id="data_tap" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_data_tap">
            </div>
        </div>
    </div>
</div>

<!-- Modal saldo tap-->
<div class="modal fade" id="saldo_tap" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex gap-2 mb-3">
                    <select class="form-select form-select-sm tap_tahun filter_saldo_tap">
                        <?php foreach (get_tahuns('topup') as $i) : ?>
                            <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                        <?php endforeach; ?>
                        <option value="All">All</option>
                    </select>
                    <select class="form-select form-select-sm tap_bulan filter_saldo_tap">
                        <?php foreach (bulan() as $i): ?>
                            <option <?= ($i['angka'] == date('m' ? 'selected' : '')); ?> value="<?= $i['angka']; ?>"><?= $i['angka']; ?></option>
                        <?php endforeach; ?>
                        <option value="All">All</option>
                    </select>
                </div>
                <div class="body_saldo_tap"></div>

            </div>
        </div>
    </div>
</div>


<!-- Modal user-->
<div class="modal fade" id="modal_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_user">
                <table class="table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama</th>
                            <th scope="col">Role</th>
                            <th scope="col">Poin</th>
                            <th scope="col">Act</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $k => $i): ?>
                            <?php if ($i['role'] !== 'Member'): ?>
                                <tr>
                                    <td><?= ($k + 1); ?></td>
                                    <td><a href="" type="button" class="canvas_perizinan" data-role="<?= $i['role']; ?>" data-id="<?= $i['id']; ?>" data-username="<?= $i['username']; ?>" data-nama="<?= $i['nama']; ?>" style="text-decoration: none;"><?= $i['nama']; ?></a></td>
                                    <td><?= $i['role']; ?></td>
                                    <td><a data-nama="<?= $i['nama']; ?>" class="poin_absen" data-id="<?= $i['id']; ?>" href="">Poin</a></td>
                                    <td><a href="" class="copy_link_jwt" data-link="<?= base_url('login/a/') . $i['jwt']; ?>"><i class="fa-solid fa-link"></i></a></td>
                                </tr>

                            <?php endif; ?>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- offcanvas perizinan -->
<div class="offcanvas offcanvas-start" style="z-index:9999" tabindex="-1" id="canvas_perizinan" aria-labelledby="offcanvasLeftLabel">

    <div class="offcanvas-body p-0 mb-5 body_canvas_perizinan">

    </div>
</div>
<script>
    // let myModal = document.getElementById('tap');
    // let modal = bootstrap.Modal.getOrCreateInstance(myModal)
    // modal.show();
    $(document).on('click', '.koperasi', function(e) {
        e.preventDefault();

        let usaha = $(this).data('usaha');

        post('home/koperasi', {
            usaha
        }).then(res => {
            if (res.status == "200") {
                let html = '';
                <?php if (session('role') == 'Root'): ?>
                    html += '<div class="input-group input-group-sm mb-3">';
                    html += '<input type="text" class="form-control uang input_tabungan_' + usaha + '" placeholder="Jumlah Tabungan">';
                    html += '<button class="btn btn-outline-secondary save_tabungan" data-usaha="' + usaha + '" type="button"><i class="fa-solid fa-floppy-disk"></i> Save</button>';
                    html += '</div>';
                <?php endif; ?>

                html += '<table class="table table-striped table-bordered table-sm">';
                html += '<thead>';
                html += '<tr>';
                html += '<th style="text-align: center;" scope="row">#</th>';
                html += '<th style="text-align: center;" scope="row">Tgl</th>';
                html += '<th style="text-align: center;" scope="row">Usaha</th>';
                html += '<th style="text-align: center;" scope="row">Tabungan</th>';
                html += '</thead>';

                html += '<tbody>';
                let total_t = 0;
                res.data.forEach((e, idx) => {
                    total_t += parseInt(e.tabungan);
                    html += '<tr>';
                    html += '<td>' + (idx + 1) + '</td>';
                    html += '<td style="text-align:center">' + time_php_to_js(e.tgl) + '</td>';
                    html += '<td style="text-align:center">' + e.usaha + '</td>';
                    html += '<td style="text-align:right">' + angka(e.tabungan) + '</td>';
                    html += '</tr>';
                })

                html += '<tr>';
                html += '<th style="text-align:right" colspan="3">TOTAL</th>';
                html += '<th style="text-align:right">' + angka(total_t) + '</th>';
                html += '</td>';

                html += '</tbody>';
                html += '</table>';

                $('.body_koperasi').html(html);

                let myModal = document.getElementById('koperasi');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
            } else {
                gagal_with_button(res.message);
            }
        })

    })

    const get_tap = (tabel, bulan, tahun) => {
        post("home/saldo_tap_by_katagori", {
            tahun,
            tabel,
            bulan
        }).then(res => {
            let html = "";

            if (res.data.length == 0) {
                html += '<span class="text_danger">Data tidak ditemukan!.</span>';
            } else {
                html += '<h6>TOTAL: ' + angka(res.data2) + '</h6>';
                html += '<table class="table table-striped table-bordered table-sm">';
                html += '<thead>';
                html += '<tr>';
                html += '<th style="text-align: center;" scope="row">#</th>';
                html += '<th style="text-align: center;" scope="row">Tgl</th>';
                html += '<th style="text-align: center;" scope="row">Pet.</th>';
                html += '<th style="text-align: center;" scope="row">Kat.</th>';
                html += '<th style="text-align: center;" scope="row">Barang</th>';
                html += '<th style="text-align: center;" scope="row">Nama</th>';
                html += '<th style="text-align: center;" scope="row">Jml</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody class="tabel_search">';
                res.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td class="text-center">' + (i + 1) + '</td>';
                    html += '<td class="text-center">' + time_php_to_js(e.tgl) + '</td>';
                    html += '<td>' + e.petugas + '</td>';
                    html += '<td>' + e.kategori + '</td>';
                    html += '<td>' + e.barang + '</td>';
                    html += '<td>' + e.user + '</td>';
                    html += '<td class="text-end">' + angka(e.jml) + '</td>';
                    html += '</tr>';
                })
                html += '</tbody>';
                html += '</table>';
            }
            $('.tabel_pendapatan').html(html);
        })
    }

    $(document).on('click', '.save_tabungan', function(e) {
        e.preventDefault();

        let usaha = $(this).data('usaha');
        let tabungan = $('.input_tabungan_' + usaha).val();

        post('home/add_tabungan', {
            usaha,
            tabungan
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                gagal_with_button(res.message);
            }
        })

    })
    $(document).on('change', '.get_pendapatan', function(e) {
        e.preventDefault();

        let tabel = $(this).data('tabel');
        let tahun = $(this).val();

        chart_html(tabel, tahun);

    })

    $(document).on('click', '.canvas_perizinan', function(e) {
        e.preventDefault();

        let nama = $(this).data('nama');
        let username = $(this).data('username');
        let id = $(this).data('id');
        let role = $(this).data('role');
        let poin = $(this).data('poin');
        let ket = $(this).data('ket');
        let tgl = $('.tgl_izin').val();
        let jam = $('.jam_izin').val();

        let html = '';
        html += '<div class="sticky-top bg-light px-3">';
        html += '<div class="d-flex justify-content-between">';
        html += '<h6 class="offcanvas-title" id="offcanvasLeftLabel">Aturan untuk perizinan</h6>';
        html += '<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>';
        html += '</div>';
        html += '<div class="mt-2">' + nama + '</div>';
        html += '<div class="d-flex gap-2 mt-2">';
        html += '<input type="date" class="tgl_izin form-control form-control-sm" value="<?= date('Y-m-d'); ?>">';
        html += '<input type="text" class="jam_izin form-control form-control-sm" value="<?= date('H:i'); ?>">';
        html += '</div>';

        html += '<hr>';
        html += '</div>';

        html += '<div class="list-group px-3">';
        let atrn = <?= json_encode($q); ?>;
        for (let i = 0; i < atrn.length; i++) {
            html += '<a href="#" data-role="' + role + '" data-id="' + id + '" data-username="' + username + '" data-nama="' + nama + '" data-poin="' + atrn[i].poin + '" data-ket="' + atrn[i].aturan + '" class="perizinan list-group-item list-group-item-action">' + atrn[i].aturan + '/' + atrn[i].poin + '</a>';

        }
        html += '</div>';
        $('.body_canvas_perizinan').html(html);
        const bsOffcanvas = new bootstrap.Offcanvas('#canvas_perizinan');
        bsOffcanvas.show();
    })

    $(document).on('click', '.perizinan', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let nama = $(this).data('nama');
        let role = $(this).data('role');
        let username = $(this).data('username');
        let poin = $(this).data('poin');
        let ket = $(this).data('ket');
        let tgl = $('.tgl_izin').val();
        let jam = $('.jam_izin').val();

        if (tgl == "") {
            gagal('Tanggal harus diisi!.');
            return false;
        }
        if (jam == "") {
            gagal('Jam harus diisi!.');
            return false;
        }

        post('absen/perizinan', {
            ket,
            id,
            tgl,
            username,
            nama,
            role,
            poin,
            jam
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1500);

            } else {
                gagal_with_button(res.message);
            }
        })

    })

    const content_table = (order, tabel, data, index) => {
        let html = '';

        html += '<div class="tabel_pendapatan">';
        html += '<table class="table table-striped table-bordered table-sm">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align: center;" scope="row">#</th>';
        html += '<th style="text-align: center;" scope="row">Tgl</th>';
        if (order == 'pengeluaran') {
            html += '<th style="text-align: center;" scope="row">Pj</th>';
            html += '<th style="text-align: center;" scope="row">Barang</th>';
        }
        if (tabel == 'kantin') {
            html += '<th style="text-align: center;" scope="row">Barang</th>';
            html += '<th style="text-align: center;" scope="row">Harga' + (order == 'pemasukan' ? ' (Qty)' : '') + '</th>';
        } else {
            if (order == 'pemasukan') {
                if (tabel == 'barber') {
                    html += '<th style="text-align: center;" scope="row">Layanan</th>';

                } else {
                    html += '<th style="text-align: center;" scope="row">Meja</th>';
                    html += '<th style="text-align: center;" scope="row">Durasi</th>';

                }

            } else {
                html += '<th style="text-align: center;" scope="row">Qty</th>';
                html += '<th style="text-align: center;" scope="row">Harga</th>';

            }

        }
        if (order == 'pemasukan') {
            if (tabel == 'barber') {
                html += '<th style="text-align: center;" scope="row">Qty</th>';
            }
            html += '<th style="text-align: center;" scope="row">Diskon</th>';
            html += '<th style="text-align: center;" scope="row">' + (tabel == 'kantin' ? 'Harga' : 'Biaya') + '</th>';

        }
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        let total_m = 0;
        let bulan = '';
        data.forEach((val, idx) => {
            if (val.bulan == index) {
                bulan = val.bln;
                total_m = val.total;
                val.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td>' + (i + 1) + '</td>';
                    html += '<td style="text-align:center">' + e.tanggal + '</td>';
                    if (order == 'pengeluaran') {
                        html += '<td>' + (tabel == 'rental' && order == 'pemasukan' ? e.petugas : (tabel == 'rental' && order == 'pengeluaran' ? e.pembeli : e.pj)) + '</td>';
                        html += '<td>' + e.barang + '</td>';

                    }
                    if (tabel == 'kantin') {
                        html += '<td>' + e.barang + '</td>';
                        html += '<td style="text-align:right">' + (order == 'pemasukan' ? angka((e.harga_satuan * e.qty)) + ' (' + e.qty + ')' : angka(e.harga)) + '</td>';
                    } else {
                        if (order == 'pemasukan') {
                            if (tabel == 'barber') {
                                html += '<td>' + e.layanan + '</td>';
                                html += '<td>' + e.qty + '</td>';
                            } else {
                                html += '<td>' + e.meja + '</td>';
                                html += '<td>' + e.durasi + '</td>';

                            }

                        } else {
                            html += '<td style="text-align:center">' + e.qty + '</td>';
                            html += '<td style="text-align:right">' + angka(e.harga) + '</td>';

                        }

                    }
                    if (order == 'pemasukan') {

                        html += '<td style="text-align:right">' + angka(e.diskon) + '</td>';
                        html += '<td style="text-align:right">' + angka((tabel == 'barber' ? e.total_harga : e.biaya)) + '</td>';
                    }
                    html += '</tr>';
                })
            }
        })

        html += '<tr>';
        html += '<th style="text-align:right" colspan="5">TOTAL</th>';
        html += '<th style="text-align:right">' + angka(total_m) + '</th>';
        html += '</td>';

        html += '</tbody>';
        html += '</table>';
        html += '</div>';

        let res = {
            total_m,
            bulan,
            html
        }
        return res;

    }

    const chart_html = (tabel, tahun) => {

        const bulans = <?= json_encode(bulan()); ?>;
        let valueY = [];
        bulans.forEach(e => {
            valueY.push(e.satuan);
        });

        post('home/get_pendapatan', {
            tabel,
            tahun
        }).then(res => {
            if (res.status == '200') {
                // total pemasukan

                let total_m = 0;
                let total_tap = 0;

                let jml = "biaya";
                if (tabel == 'kantin' || tabel == "barber") {
                    jml = "total_harga";
                }
                res.data.forEach((val, idx) => {
                    total_m += val.total;

                    val.data.forEach(t => {
                        if (t.metode == "Tap") {
                            total_tap += parseInt(t[jml]);
                        }
                    });
                })

                // total pengeluaran
                let total_p = 0;
                res.data2.forEach(e => {
                    total_p += e.total;
                })
                $('.total_' + tabel).text(angka(total_m) + ' - ' + angka(total_p) + ' = ' + ((total_m - total_p) < 0 ? '-' : '') + angka((total_m - total_p).toString()));

                // total tap
                let jml_tap = (total_m - total_p) - total_tap;
                $(".div_data_tap_" + tabel).text("- " + angka(total_tap) + " = " + angka(jml_tap));

                valueX = [];

                // res.data.forEach(e => {
                //     valueX.push(e.total);
                // })

                for (let i = 0; i < res.data.length; i++) {
                    valueX.push(res.data[i].total - res.data2[i].total);
                }

                // const xPs = [100000, 6500000, 130000, 4590200, 452682, 987698, 4263528, 9876262, 665656, 879766, 879999, 0];
                // const yPs = bulan;

                new Chart("chart_" + tabel, {
                    type: "line",
                    data: {
                        labels: valueY,
                        datasets: [{
                            fill: false,
                            lineTension: 0,
                            backgroundColor: "rgba(0,0,255,1.0)",
                            borderColor: "rgba(0,0,255,0.1)",
                            data: valueX
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        onClick: (e, values) => {
                            let index = values[0]['_index'] + 1;
                            let body_table = content_table('pemasukan', tabel, res.data, index);

                            let html = '<div class="d-flex justify-content-between judul">';
                            html += '<div>';
                            html += 'KEUANGAN ' + tabel.toUpperCase() + ' ' + body_table.bulan.toUpperCase() + ' ' + tahun;
                            html += '</div>';
                            html += '<div>';
                            html += '<a type="button" href="" class="text_danger" data-bs-dismiss="modal"><i class="fa-solid fa-circle-xmark"></i></a>';
                            html += '</div>';
                            html += '</div>';
                            html += '<div style="border-radius:3px;" class="mb-2 judul_modal judul_' + tabel + '"></div>';
                            html += '<ul class="nav nav-tabs">';
                            html += '<li class="nav-item">';
                            html += '<a class="nav-link active detail_data" data-order="pemasukan" aria-current="page" href="#">Pemasukan</a>';
                            html += '</li>';
                            html += '<li class="nav-item">';
                            html += '<a class="nav-link detail_data" data-order="pengeluaran" aria-current="page" href="#">Pengeluaran</a>';
                            html += '</li>';
                            html += '<li class="nav-item">';
                            html += '<a class="nav-link detail_data" data-order="tap" data-bulan="' + index + '" data-tahun="' + tahun + '" data-tabel="' + tabel + '" aria-current="page" href="#">Tap</a>';
                            html += '</li>';
                            html += '</ul>';
                            html += '<div class="content_table">';
                            html += body_table.html;
                            html += '</div>';
                            $('.body_detail_pendapatan').html(html);
                            let myModal = document.getElementById('detail_pendapatan');
                            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                            modal.show();

                            let total_p = 0;
                            res.data2.forEach((val, idx) => {
                                if (val.bulan == index) {
                                    total_p = val.total;
                                }
                            })

                            $('.judul_' + tabel).text(angka(body_table.total_m) + ' - ' + angka(total_p) + ' = ' + ((body_table.total_m - total_p) < 0 ? '-' : '') + angka(body_table.total_m - total_p));

                            $(document).on('click', '.detail_data', function(e) {
                                e.preventDefault();
                                let order = $(this).data('order');
                                let elem = document.querySelectorAll('.detail_data');

                                elem.forEach(e => {
                                    e.classList.remove('active');
                                })
                                $(this).addClass('active');
                                if (order == "tap") {
                                    get_tap($(this).data("tabel"), $(this).data("bulan"), $(this).data("tahun"));
                                    return;
                                }
                                let content = content_table(order, tabel, (order == 'pemasukan' ? res.data : res.data2), index);

                                $('.content_table').html(content.html);
                            })

                            // let datasetIndex = activeEls[0].datasetIndex;
                            // let dataIndex = activeEls[0].index;
                            // let datasetLabel = e.chart.data.datasets[datasetIndex].label;
                            // let value = e.chart.data.datasets[datasetIndex].data[dataIndex];
                            // let label = e.chart.data.labels[dataIndex];
                            // console.log("In click", datasetLabel, label, value);
                        }
                    }
                });
            } else {
                gagal_with_button(res.message);
            }
        })
    }

    $(document).on('click', '.copy_link_jwt', function(e) {
        e.preventDefault();
        let jwt = $(this).data('link');
        navigator.clipboard.writeText(jwt);
        sukses('Copied.');
    })

    $(document).on('click', '.poin_absen', function(e) {
        e.preventDefault();

        let id = $(this).data('id');
        let nama = $(this).data('nama');

        post('absen/poin_absen', {
            id
        }).then(res => {
            if (res.status == '200') {
                let data = res.data.data;
                let html = '<h6>' + nama + '</h6>';
                html += '<table class="table table-sm table-bordered">';
                html += '<thead>';
                html += '<tr>';
                html += '<th scope="col">#</th>';
                html += '<th scope="col">Tgl</th>';
                html += '<th scope="col">Jenis</th>';
                html += '<th scope="col">Ket</th>';
                html += '<th scope="col">Poin</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                let total = 0;

                data.forEach((e, i) => {
                    total += parseInt(e.poin);
                    html += '<tr>';
                    html += '<td scope="row">' + (i + 1) + '</td>';
                    html += '<td>' + e.tgl + '</td>';
                    html += '<td>' + (e.ket == 'Terlambat' || e.ket == 'Ontime' ? 'Absen' : 'Aturan') + '</td>';
                    <?php if (session('role') == 'Root'): ?>
                        html += '<td><a style="text-decoration:none" href="" data-alert="Are you sure to delete this data?" data-url="general/delete" data-id="' + e.id + '" data-tabel="absen" data-col="id" class="text_danger btn_confirm">' + e.ket + '</a></td>';
                        html += '<td style="text-align:right" data-id="' + e.id + '" class="update_poin" contenteditable>' + e.poin + '</a></td>';
                    <?php else: ?>
                        html += '<td>' + e.ket + '</td>';
                        html += '<td style="text-align:right">' + e.poin + '</td>';
                    <?php endif; ?>
                    html += '</tr>';

                })
                html += '<tr>';
                html += '<th colspan="5" style="text-align:right">' + total + '</th>';
                html += '</tr>';
                html += '</tbody>';
                html += '</table>';
                html += '<div class="d-grid text-center py-1 bg_danger fw-bold text-light">' + (100 + total) + '</div>';


                let myModalUser = document.getElementById('modal_user');
                let modalUser = bootstrap.Modal.getOrCreateInstance(myModalUser)
                modalUser.hide();

                $('.body_poin_absen').html(html);
                let myModal = document.getElementById('poin_absen');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();

            } else {
                gagal(res.message);
            }
        })

    })


    $(document).on('blur', '.update_poin', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let val = $(this).text();

        post('absen/update_poin', {
            id,
            val
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
            } else {
                gagal(res.message);
            }
        })

    })



    chart_html('rental', '<?= date('Y'); ?>');
    chart_html('billiard', '<?= date('Y'); ?>');
    chart_html('kantin', '<?= date('Y'); ?>');
    chart_html('barber', '<?= date('Y'); ?>');



    let data_saldo_tap = [];
    $(document).on('change', '.filter_saldo_tap', function(e) {
        e.preventDefault();
        let tahun = $(".tap_tahun").val();
        let bulan = $(".tap_bulan").val();

        if (tahun == "" || bulan == "") {
            gagal("Tahun/bulan belum dipilih!.");
            return;
        }

        post("home/saldo_tap", {
            tahun,
            bulan
        }).then(res => {
            let html = "";
            let total_masuk = 0;
            let total_keluar = 0;
            let total_hapus = 0;
            if (res.data.length == 0) {
                html += '<span class="text_danger">Data tidak ditemukan!.</span>';
            } else {
                data_saldo_tap = res.data;

                html += '<h6>TOTAL: ' + angka(res.data2) + ' - ' + angka(res.data3) + ' = ' + angka(res.data2 - res.data3) + '</h6>';
                html += '<div class="div_total_filter_tap"></div>';
                if (res.data4 == "Root") {
                    html += '<div class="div_total_filter_tap_hapus"></div>';
                }
                html += '<div class="form-check form-check-inline form-switch my-2">';
                html += '<input class="form-check-input jenis" name="jenis" type="radio" role="switch" value="in">';
                html += '<label class="form-check-label">In</label>';
                html += '</div>';
                html += '<div class="form-check form-check-inline form-switch">';
                html += '<input class="form-check-input jenis" name="jenis" type="radio" role="switch" value="out">';
                html += '<label class="form-check-label">Out</label>';
                html += '</div>';
                if (res.data4 == "Root") {
                    html += '<div class="form-check form-check-inline form-switch">';
                    html += '<input class="form-check-input jenis" name="jenis" type="radio" role="switch" value="hapus">';
                    html += '<label class="form-check-label">Remove</label>';
                    html += '</div>';
                }
                html += '<input class="form-control mt-1 form-control-sm cari" type="text" placeholder="Cari nama...">'
                html += '<table class="table table-sm table-bordered">';
                html += '<thead>';
                html += '<tr>';
                html += '<th class="text-center">#</th>';
                html += '<th class="text-center">Tgl</th>';
                html += '<th class="text-center">Pet</th>';
                html += '<th class="text-center">Kat</th>';
                html += '<th class="text-center">Nama</th>';
                html += '<th class="text-center">Uang</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody class="tabel_search">';

                res.data.forEach((e, i) => {
                    if (e.jenis == "in") {
                        total_masuk += parseInt(e.jml);
                    }
                    if (e.jenis == "out") {
                        total_keluar += parseInt(e.jml);
                    }
                    if (res.data4 == "Root" && e.jenis == "hapus") {
                        total_hapus += parseInt(e.jml);
                    }
                    html += '<tr>';
                    html += '<td class="text-center">' + (i + 1) + '</td>';
                    html += '<td class="text-center">' + time_php_to_js(e.tgl) + '</td>';
                    html += '<td>' + e.petugas + '</td>';
                    html += '<td>' + e.kategori + '</td>';
                    html += '<td>' + e.user + '</td>';
                    html += '<td class="text-end">' + angka(e.jml) + '</td>';
                    html += '</tr>';
                })
                html += '</tbody>';
                html += '</table>';
            }
            $(".body_saldo_tap").html(html);
            $(".div_total_filter_tap").html('FILTER: ' + angka(total_masuk) + ' - ' + angka(total_keluar) + ' = ' + angka(total_masuk - total_keluar) + '');
            $(".div_total_filter_tap_hapus").html('HAPUS: ' + angka(total_hapus));
        })
    })

    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.tabel_search tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });

    $(document).on('change', '.jenis', function(e) {
        e.preventDefault();
        let jenis = $(this).val();
        let total = 0;
        let html = "";
        if (data_saldo_tap.length == 0) {
            html += '<span class="text_danger">Data tidak ditemukan!.</span>';
        } else {
            data_saldo_tap.forEach((e, i) => {
                if (e.jenis == jenis) {
                    total += parseInt(e.jml);
                    html += '<tr>';
                    html += '<td class="text-center">' + (i + 1) + '</td>';
                    html += '<td class="text-center">' + time_php_to_js(e.tgl) + '</td>';
                    html += '<td>' + e.petugas + '</td>';
                    html += '<td>' + e.kategori + '</td>';
                    html += '<td>' + e.user + '</td>';
                    html += '<td class="text-end">' + angka(e.jml) + '</td>';
                    html += '</tr>';
                }
            })
            html += '<tr>';
            html += '<th class="text-end" colspan="5">';
            html += 'TOTAL: ' + angka(total);
            html += '</th>';
            html += '</tr>';
        }
        $(".tabel_search").html(html);

    });
</script>

<?= $this->endSection() ?>