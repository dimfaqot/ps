<!-- md -->
<?php if (user()['role'] == 'Root') : ?>
    <div class="d-none d-md-block">
        <div class="box_navbar fixed-top shadow shadow-sm">
            <div class="container d-flex justify-content-between">
                <div class="d-flex gap-1">
                    <?php
                    $db = db('menus');
                    $q1[] = ['id' => 0, 'no_urut' => 0, 'role' => user()['role'], 'menu' => 'Home', 'tabel' => 'users', 'controller' => 'home', 'icon' => "fa-solid fa-earth-asia", 'url' => 'home', 'logo' => 'file_not_found.jpg', 'grup' => ''];
                    $q2 = $db->where('role', 'Root')->groupBy('grup')->orderBy('urutan', 'ASC')->get()->getResultArray();

                    $menus = array_merge($q1, $q2);
                    ?>
                    <?php foreach ($menus as $m) : ?>
                        <?php if ($m['menu'] == 'Home') : ?>
                            <a href="<?= base_url($m['controller']); ?>" class="navbar_link <?= (url() == $m['controller'] ? 'navbar_active' : ''); ?> type=" button">
                                <i class="<?= $m['icon']; ?>"></i> <?= $m['menu']; ?>
                            </a>
                        <?php else : ?>
                            <div class="dropdown">
                                <a href="" class="navbar_link <?= (is_menu_active($m['grup']) ? 'navbar_active' : ''); ?> dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="<?= $m['icon']; ?>"></i> <?= $m['grup']; ?>
                                </a>
                                <ul class="dropdown-menu">
                                    <?php foreach (menus() as $i) : ?>
                                        <?php if ($i['grup'] == $m['grup']) : ?>
                                            <li><a style="border: none;" class="dropdown-item navbar_link <?= (url() == $i['controller'] ? 'navbar_active' : ''); ?>" href="<?= base_url($i['controller']); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a></li>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </ul>

                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                </div>
                <div class="pt-1">
                    <?php if (session('role') !== 'Member'): ?>
                        <a href="" class="text_dark lonceng_notif" style="background-color: #f2f2f2; border:1px solid #cccccc;font-size:small;border-radius:10px;padding:4px 10px;text-decoration:none;"><i class="fa-solid fa-bell"></i> <span class="jml_notif">0</span></a>
                    <?php endif; ?>
                    <span class="px-3 py-1" style="background-color: #f2f2f2; border:1px solid #cccccc; color:#666666;font-size:small;border-radius:10px;"><?= user()['nama']; ?>/<?= user()['role']; ?></span>
                    <a class="btn_danger" style="border-radius: 10px;" href="<?= base_url('logout'); ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> Logout</a>
                </div>
            </div>
        </div>

    </div>

<?php else : ?>
    <div class="d-none d-md-block">
        <div class="box_navbar fixed-top shadow shadow-sm">
            <div class="container d-flex justify-content-between">
                <div class="d-flex gap-1">
                    <?php foreach (menus() as $i) : ?>

                        <a href="<?= base_url($i['controller']); ?>" class="navbar_link <?= (url() == $i['controller'] ? 'navbar_active' : ''); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a>
                    <?php endforeach; ?>

                </div>
                <div class="pt-1">
                    <?php if (session('role') !== 'Member'): ?>
                        <a href="" class="text_dark lonceng_notif" style="background-color: #f2f2f2; border:1px solid #cccccc;font-size:small;border-radius:10px;padding:4px 10px;text-decoration:none;"><i class="fa-solid fa-bell"></i> <span class="jml_notif">0</span></a>
                    <?php endif; ?>
                    <span class="px-3 py-1" style="background-color: #f2f2f2; border:1px solid #cccccc; color:#666666;font-size:small;border-radius:10px;"><?= user()['nama']; ?>/<?= user()['role']; ?></span>
                    <a class="btn_danger" style="border-radius: 10px;" href="<?= base_url('logout'); ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> Logout</a>
                </div>
            </div>
        </div>

    </div>

<?php endif; ?>



<!-- navbar sm -->
<div class="d-block d-md-none d-sm-block fixed-top" style="top:-5px">
    <div class="container bg-light py-2 shadow shadow-sm">
        <div class="d-flex justify-content-between">
            <div>
                <a class="navbar-brand" href="<?= base_url(); ?>"><img src="<?= base_url(); ?>logo.png" alt="LOGO" width="30"></a>
            </div>
            <div class="d-flex justify-content-center gap-1">
                <div class="pt-1">
                    <span class="px-3 py-1" style="background-color: #f2f2f2; border:1px solid #cccccc; color:#666666;font-size:x-small;border-radius:10px;"><?= user()['nama']; ?>/<?= user()['role']; ?></span>
                </div>
                <div class="pt-1">
                    <span class="px-3 py-1 bg_main text-white" style="border:1px solid #cccccc; color:#666666;font-size:x-small;border-radius:10px;"><i class="<?= menu()['icon']; ?>"></i> <?= menu()['menu']; ?></span>

                </div>

            </div>

            <div class="pt-1">
                <?php if (session('role') !== 'Member'): ?>
                    <a href="" class="text_dark lonceng_notif" style="font-size:small;border-radius:10px;padding:2px;text-decoration:none;"><i class="fa-solid fa-bell"></i> <span class="jml_notif">0</span></a>
                <?php endif; ?>
                <a href="" class="btn_act_purple" data-bs-toggle="offcanvas" data-bs-target="#leftMenu" aria-controls="leftMenu"><i class="fa-solid fa-bars text_purple"></i></a>
            </div>
        </div>

    </div>
</div>

<!-- camvas -->
<div class="offcanvas offcanvas-start" style="width:90%" data-bs-scroll="true" tabindex="-1" id="leftMenu" aria-labelledby="leftMenuLabel">
    <div class="offcanvas-header shadow shadow-bottom shadow-sm">
        <h6 class="offcanvas-title" id="leftMenuLabel">Menu</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <?php foreach (menus() as $i) : ?>
            <div class="mb-1 d-grid">
                <a href="<?= base_url($i['controller']); ?>" style="font-size: small;" class="px-3 py-1 <?= (url() == $i['controller'] ? 'btn_add' : 'btn_light no_underline'); ?>"><i class="<?= $i['icon']; ?>"></i> <?= $i['menu']; ?></a>
            </div>
        <?php endforeach; ?>
        <div class="d-grid">
            <a class="btn_danger" href="<?= base_url('logout'); ?>"><i class="fa-solid fa-arrow-right-to-bracket"></i> Logout</a>
        </div>

    </div>
</div>


<!-- notif canvas -->
<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-body p-0">
        <div class="sticky-top bg-light">
            <div class="shadow shadow-sm d-flex justify-content-between px-3 py-2">
                <div>NOTIFIKASI</div>
                <div>
                    <a data-bs-dismiss="offcanvas" aria-label="Close" href="" class="text_danger" style="text-decoration: none;"><i class="fa-solid fa-circle-xmark"></i></a>
                </div>
            </div>

            <div class="d-flex gap-2 px-3 py-2">
                <a class="btn_act_grey px-4 rounded filter_notif" data-order="Absen" href="">Absen</a>
                <a class="btn_act_grey px-4 rounded filter_notif" data-order="Aturan" href="">Aturan</a>
                <a class="btn_act_grey px-4 rounded filter_notif" data-order="Pesanan" href="">Pesanan</a>
                <a class="btn_act_success px-4 rounded filter_notif" data-order="All" href="">All</a>
            </div>
        </div>

        <div class="accordion accordion-flush body_notif_pesanan" id="accordionFlushExample">

        </div>

    </div>
</div>

<!-- modal notif_detail_pesanan -->
<div class="modal fade" id="notif_detail_pesanan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0 modal_body_notif_detail_pesanan">

            </div>
        </div>
    </div>
</div>

<!-- Modal pembayaran-->
<div class="modal fade" id="pembayaran_navbar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center d-none">

                </div>

                <div class="body_pembayaran_navbar">

                </div>

            </div>

        </div>
    </div>
</div>

<!-- modal total_harga_navbar -->
<div class="modal fade" style="margin-top: 150px;z-index:9999" id="total_harga_navbar" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="d-flex justify-content-between p-2 bg_danger">
                <div class="fw-bold text_light"><i class="fa-solid fa-cash-register"></i> PEMBAYARAN</div>
                <div class="bg_light" style="border-radius: 50%;padding:1.2px 4px"><a href="" data-bs-dismiss="modal"><i class="fa-solid fa-circle-xmark text_danger"></i></a></div>
            </div>
            <div class="modal-body body_total_harga_navbar">

            </div>
        </div>
    </div>
</div>
<script>
    // let myModal = document.getElementById('notif_detail_pesanan');
    // let modal = bootstrap.Modal.getOrCreateInstance(myModal)
    // modal.show();
    // let myOffcanvas = document.getElementById('offcanvasRight')
    // let bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
    // bsOffcanvas.show()
    let data_notif;

    const body_notif = (data, order = undefined) => {
        let html = '';
        data.forEach((e, i) => {
            if (order !== undefined) {
                if (e.kategori !== order) {
                    return;
                }
            }
            let tgl_kejadian = new Date(e.tgl * 1000);
            let tgl_notif = new Date(e.harga * 1000);
            html += '<div class="accordion-item">';
            html += '<div style="border-bottom:1px solid #f2eaca" class="accordion-header" id="flush-heading' + e.id + '">';
            html += '<button style="font-size: small;" class="accordion-button collapsed ' + (e.read == 0 ? 'bg_success_light' : '') + ' read_notif_pesanan" data-id="' + e.id + '" data-kategori="' + e.kategori + '" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' + e.id + '" aria-expanded="false" aria-controls="flush-collapse' + e.id + '">';
            html += '<div class="d-flex gap-2">';
            html += '<div class="bg_success_bright rounded px-1" style="font-size:x-small;padding-top:2px">' + tgl_notif.getDate() + '/' + (tgl_notif.getMonth() + 1) + '/' + tgl_notif.getFullYear() + ' ' + tgl_notif.getHours() + ':' + (tgl_notif.getMinutes() + 1) + '</div>';
            html += '<div class="px-2 rounded bg_warning_light">' + e.kategori + '</div>';
            if (e.kategori == 'Absen') {
                html += '<div>' + e.pemesan + '  ' + (e.meja == 'Ontime' ? 'Ontime <i class="fa-solid fa-thumbs-up text_success"></i>' : 'Terlambat <i class="fa-solid fa-thumbs-down text_danger"></i>') + '</div>';
            }
            if (e.kategori == 'Pesanan') {
                html += '<div>Meja ' + e.meja + '</div>';
            }
            if (e.kategori == 'Aturan') {
                html += '<div>' + e.pemesan + ' ' + (e.qty < 0 ? 'Kurang Ajar <i class="fa-solid fa-thumbs-down text_danger"></i>' : 'Menyala <i class="fa-solid fa-thumbs-up text_success"></i>') + '</div>';
            }
            html += '</div>';
            html += '</button>';
            html += '</div>';
            html += '<div id="flush-collapse' + e.id + '" class="accordion-collapse collapse shadow" aria-labelledby="flush-heading' + e.id + '" data-bs-parent="#accordionFlushExample">';
            html += '<div class="accordion-body px-0 bg_main_bright">';

            if (e.kategori == 'Pesanan') {
                html += '<div class="px-3">';
                html += '<div class="p-1 text-center fw-bold bg_primary text-white rounded mb-2">';
                html += e.dibaca;
                html += '</div>';
                html += '<div class="input-group input-group-sm mb-2">';
                html += '<span class="input-group-text" style="font-size:10px;width:90px">Meja</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.meja + '">';
                html += '</div>';

                html += '<div class="input-group input-group-sm mb-2">';
                html += '<span class="input-group-text" style="font-size:10px;width:90px">Pemesan</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.pemesan + '">';
                html += '</div>';

                html += '<div class="d-grid">';
                html += '<button class="btn_info notif_detail_pesanan" data-no_nota="' + e.no_nota + '">Detail Pesanan</button>';
                html += '</div>';

                html += '</div>';
            }
            if (e.kategori == 'Absen') {

                html += '<div class="px-4">';
                html += e.pemesan + ' ' + e.meja + ' <b>' + e.menu + '</b> dan ' + (e.qty < 0 ? 'dikurangi' : 'mendapatkan') + ' ' + e.qty + ' poin pada ' + tgl_kejadian.getDate() + '/' + (tgl_kejadian.getMonth() + 1) + '/' + tgl_kejadian.getFullYear() + ' ' + tgl_kejadian.getHours() + ':' + (tgl_kejadian.getMinutes() + 1) + ' WIB';
                html += '</div>';
            }
            if (e.kategori == 'Aturan') {

                html += '<div class="px-4">';
                html += e.pemesan + ' ' + e.meja + ' <b>' + e.menu + '</b> dan ' + (e.qty < 0 ? 'dikurangi' : 'mendapatkan') + ' ' + e.qty + ' poin pada ' + tgl_kejadian.getDate() + '/' + (tgl_kejadian.getMonth() + 1) + '/' + tgl_kejadian.getFullYear() + ' ' + tgl_kejadian.getHours() + ':' + (tgl_kejadian.getMinutes() + 1) + ' WIB';
                html += '</div>';
            }

            html += '</div>';
            html += '</div>';
        })

        $('.body_notif_pesanan').html(html);

    }
    const notif_pesanan = () => {
        post('notif/pesanan', {}).then(res => {
            if (res.status == '200') {
                let jml_notif = parseInt($('.jml_notif').text());

                if (jml_notif !== res.data2) {
                    $('.jml_notif').text(res.data2);
                }

                if (res.data2 == 0) {
                    $('.lonceng_notif').removeClass('text_danger');
                    $('.lonceng_notif').addClass('text_dark');
                    $('.jml_notif').text(res.data.length);
                } else {
                    $('.lonceng_notif').addClass('text_danger');
                    $('.lonceng_notif').removeClass('text_dark');

                }
            }
        })
    }

    notif_pesanan();


    $(document).on('click', '.lonceng_notif', function(e) {
        e.preventDefault();

        post('notif/detail_pesanan', {}).then(res => {

            if (res.status == '200') {
                data_notif = res.data;
                body_notif(data_notif);
                let myOffcanvas = document.getElementById('offcanvasRight')
                let bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
                bsOffcanvas.show()
            }
        })

    })
    $(document).on('click', '.filter_notif', function(e) {
        e.preventDefault();
        let notifs = document.querySelectorAll('.filter_notif');
        notifs.forEach(e => {
            e.classList.add('btn_act_grey');
            e.classList.remove('btn_act_success');
        })

        $(this).removeClass('btn_act_grey');
        $(this).addClass('btn_act_success');
        let order = $(this).data('order');
        body_notif(data_notif, (order == 'All' ? undefined : order));

    })

    $(document).on('click', '.read_notif_pesanan', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let kategori = $(this).data('kategori');

        if (kategori == 'Pesanan') {
            return false;
        }
        post('notif/read_notif_pesanan', {
            id
        }).then(res => {
            if (res.status == '200') {
                $(this).removeClass('bg_success_bright');
            } else {
                gagal(res.message);
            }
        })
    })
    $(document).on('click', '.notif_detail_pesanan', function(e) {
        e.preventDefault();
        let no_nota = $(this).data('no_nota');
        post('notif/notif_detail_pesanan', {
            no_nota
        }).then(res => {
            if (res.status == '200') {
                let html = '';
                html += '<div class="p-3 border-bottom mb-3 shadow shadow-sm">DETAIL PESANAN</div>';
                html += '<div class="p-3">';
                html += '<div class="input-group input-group-sm mb-2">';
                html += '<span style="width: 80px;" class="input-group-text">No. Meja</span>';
                html += '<input type="text" class="form-control" value="' + res.data[0].meja + '">';
                html += '</div>';
                html += '<div class="input-group input-group-sm mb-2">';
                html += '<span style="width: 80px;" class="input-group-text">Pemesan</span>';
                html += '<input type="text" class="form-control" value="' + res.data[0].pemesan + '">';
                html += '</div>';
                html += 'Pesanan ' + res.data[0].no_nota + ':';
                html += '<table class="table table-striped table-sm">';
                html += '<thead>';
                html += '<tr>';
                html += '<th>#</th>';
                html += '<th>Menu</th>';
                html += '<th>Qty</th>';
                html += '<th>Harga</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                res.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td>' + (i + 1) + '</td>';
                    html += '<td>' + e.menu + '</td>';
                    html += '<td>' + e.qty + '</td>';
                    html += '<td style="text-align:right">' + angka(e.total) + '</td>';
                    html += '</tr>';

                })
                html += '</tbody>';
                html += '</table>';

                html += '<div class="d-grid">';
                if (res.data[0].dibaca !== 'DONE') {
                    html += '<button class="' + (res.data[0].dibaca == 'WAITING' ? 'btn_info kerjakan_pesanan' : 'btn_danger selesaikan_pesanan') + '" data-no_nota="' + res.data[0].no_nota + '"><i class="fa-solid fa-fire-burner"></i> ' + (res.data[0].dibaca == 'WAITING' ? 'Kerjakan' : 'Selesai') + '</button>';
                }
                html += '</div>';
                html += '</div>';

                $('.modal_body_notif_detail_pesanan').html(html);
                let myModal = document.getElementById('notif_detail_pesanan');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
            } else {
                gagal(res.message);
            }
        })
    })

    $(document).on('click', '.kerjakan_pesanan', function(e) {
        e.preventDefault();
        let no_nota = $(this).data('no_nota');
        post('notif/kerjakan_pesanan', {
            no_nota
        }).then(res => {
            if (res.status == '200') {
                sukses(res.message);

                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                gagal(res.message);
            }
        })
    })
    $(document).on('click', '.selesaikan_pesanan', function(e) {
        e.preventDefault();
        let myModal = document.getElementById('notif_detail_pesanan');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.hide();

        let no_nota = $(this).data('no_nota');
        post('notif/kerjakan_pesanan', {
            no_nota
        }).then(res => {
            if (res.status == '200') {
                let html = '<h6 class="judul"><i class="fa-solid fa-cash-register"></i> Pembayaran</h6>';
                html += '<div class="mb-2">Meja: ' + res.data[0].meja + ' - Pemesan: ' + res.data[0].pemesan + ' - Invoice: ' + res.data[0].no_nota + '</div>';
                html += '<table class="table table-sm table-bordered table-striped">';
                html += '<thead>';
                html += '<tr>';
                html += '<th style="text-align:center;" scope="col">#</th>';
                html += '<th style="text-align:center;" scope="col">Barang</th>';
                html += '<th style="text-align:center;" scope="col">Qty</th>';
                html += '<th style="text-align:center;" scope="col">Harga</th>';
                html += '<th style="text-align:center;" scope="col">Total</th>';
                html += '</tr>';
                html += '</thead>';
                html += '<tbody>';
                let total = 0;
                res.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td>' + (i + 1) + '.</td>';
                    html += '<td>' + e.menu + '</td>';
                    html += '<td>' + e.qty + '</td>';
                    html += '<td style="text-align:right">' + angka(e.harga) + '</td>';
                    html += '<td style="text-align:right">' + angka(e.total) + '</td>';
                    html += '</tr>';
                    total += parseInt(e.total);

                })
                html += '</tbody>';
                html += '</table>';
                html += '<div class="soal_ragu p-3 mb-3 bg_warning_light" style="border-radius: 5px;">';


                html += '<div class="bg_light p-3 mb-2 fw-bold" style="text-align:center;font-size:xx-large">';
                html += '<div style="font-weight:normal;font-size:small">Uang yang harus dibayar</div>';
                html += '<div class="uang_yang_harus_dibayar">' + angka(total, 'Rp') + '</div>';
                html += '</div>';

                html += '<div class="input-group mb-2">';
                html += '<span style="width: 120px;" class="input-group-text bg_danger_light text_danger fw-bold">DISKON</span>';
                html += '<input style="text-align: right;" type="text" data-total="' + total + '" placeholder="Diskon" class="form-control uang diskon" value="0">';
                html += '</div>';

                html += '<div class="input-group">';
                html += '<span style="width: 120px;" class="input-group-text bg_warning text_warning_dark fw-bold">UANG</span>';
                html += '<input style="text-align: right;" type="text" placeholder="Uang yang dibayarkan" class="form-control uang harga_jml_uang" value="">';
                html += '</div>';

                html += '</div>'

                html += '<div class="mb-3 d-grid">';
                html += '<a class="btn_success btn_bayar_navbar" data-no_nota=' + res.data[0].no_nota + ' data-total="' + total + '" style="text-align: center;" href=""><i class="fa-solid fa-cash-register"></i> Bayar</a>';
                html += '</div>';

                $('.body_pembayaran_navbar').html(html);

                let myModalPembayaran = document.getElementById('pembayaran_navbar');
                let modalPembayaran = bootstrap.Modal.getOrCreateInstance(myModalPembayaran)
                modalPembayaran.show();
            } else {
                gagal(res.message);
            }
        })


    })
    $(document).on('keyup', '.diskon', function(e) {
        e.preventDefault();
        let total = parseInt($(this).data('total'));
        let dis = $(this).val();

        if (dis == 0 || dis == "") {
            $('.uang_yang_harus_dibayar').text(angka(total, 'Rp. ').toString());

        } else {
            diskon = parseInt(str_replace("Rp. ", "", str_replace(".", "", dis)));
            if (diskon > total) {
                $('.uang_yang_harus_dibayar').text(angka(total, 'Rp. ').toString());

                gagal('Diskon tidak boleh lebih besar dari harga!.');
                return false;
            } else {
                $('.uang_yang_harus_dibayar').text(angka(total - diskon, 'Rp. ').toString());
            }
        }
    })
    $(document).on('click', '.btn_bayar_navbar', function(e) {
        e.preventDefault();
        let diskon = parseInt(str_replace(".", "", $('.diskon').val()));
        let no_nota = $(this).data('no_nota');
        let total = parseInt($(this).data('total'));
        let biaya = parseInt(str_replace("Rp ", "", str_replace(".", "", $('.uang_yang_harus_dibayar').text())));
        let uang = parseInt(str_replace(".", "", $('.harga_jml_uang').val()));

        let myModalPembayaran = document.getElementById('pembayaran');
        let modalPembayaran = bootstrap.Modal.getOrCreateInstance(myModalPembayaran)
        modalPembayaran.hide();

        if (uang < biaya) {
            gagal('Uang pembayaran kurang!.');
            return false;
        }
        if (diskon > biaya) {
            gagal('Diskon kebesaran!.');
            $('.uang_yang_harus_dibayar').text(angka(total, 'Rp. '));
            return false;
        }
        post('home/pembayaran_kantin_barcode', {
            no_nota,
            diskon,
            biaya,
            total,
            uang
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
                $('.body_total_harga_navbar').html(html);

                let myModal2 = document.getElementById('total_harga_navbar');
                let modal2 = bootstrap.Modal.getOrCreateInstance(myModal2)
                modal2.show();
            } else {
                gagal(res.message);
            }
        })


        $('#total_harga_navbar').on('hidden.bs.modal', function() {
            location.reload();
        });
    })
</script>