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
                    <a href="" class="text_dark lonceng_notif" style="background-color: #f2f2f2; border:1px solid #cccccc;font-size:small;border-radius:10px;padding:4px 10px;text-decoration:none;"><i class="fa-solid fa-bell"></i> <span class="jml_notif">0</span></a>
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
                    <a href="" class="text_dark lonceng_notif" style="background-color: #f2f2f2; border:1px solid #cccccc;font-size:small;border-radius:10px;padding:4px 10px;text-decoration:none;"><i class="fa-solid fa-bell"></i> <span class="jml_notif">0</span></a>
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
                <a href="" class="text_dark lonceng_notif" style="font-size:small;border-radius:10px;padding:2px;text-decoration:none;"><i class="fa-solid fa-bell"></i> <span class="jml_notif">0</span></a>
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
<script>
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
            html += '<button style="font-size: small;" class="accordion-button collapsed ' + (e.read == 0 ? 'bg_success_light' : '') + ' read_notif_pesanan" data-id="' + e.id + '" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' + e.id + '" aria-expanded="false" aria-controls="flush-collapse' + e.id + '">';
            html += '<div class="d-flex gap-2">';
            html += '<div class="bg_success_bright rounded px-1" style="font-size:x-small;padding-top:2px">' + tgl_notif.getDate() + '/' + (tgl_notif.getMonth() + 1) + '/' + tgl_notif.getFullYear() + ' ' + tgl_notif.getHours() + ':' + (tgl_notif.getMinutes() + 1) + '</div>';
            html += '<div class="px-2 rounded bg_warning_light">' + e.kategori + '</div>';
            if (e.kategori == 'Absen') {
                html += '<div>' + e.pemesan + '  ' + (e.meja == 'Ontime' ? 'Ontime <i class="fa-solid fa-thumbs-up text_success"></i>' : 'Terlambat <i class="fa-solid fa-thumbs-down text_danger"></i>') + '</div>';
            }
            if (e.kategori == 'Kantin') {
                html += '<div>Meja ' + e.meja + ' ' + e.menu + '</div>';
            }
            if (e.kategori == 'Aturan') {
                html += '<div>' + e.pemesan + ' ' + (e.qty < 0 ? 'Kurang Ajar <i class="fa-solid fa-thumbs-down text_danger"></i>' : 'Menyala <i class="fa-solid fa-thumbs-up text_success"></i>') + '</div>';
            }
            html += '</div>';
            html += '</button>';
            html += '</div>';
            html += '<div id="flush-collapse' + e.id + '" class="accordion-collapse collapse shadow" aria-labelledby="flush-heading' + e.id + '" data-bs-parent="#accordionFlushExample">';
            html += '<div class="accordion-body px-0 bg_main_bright">';

            if (e.kategori == 'Kantin') {
                html += '<div class="row g-2">';

                html += '<div class="col-6">';
                html += '<div class="input-group input-group-sm">';
                html += '<span class="input-group-text" style="font-size:10px;">Meja</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.meja + '">';
                html += '</div>';
                html += '</div>';

                html += '<div class="col-6">';
                html += '<div class="input-group input-group-sm">';
                html += '<span class="input-group-text" style="font-size:10px;">Pemesan</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.pemesan + '">';
                html += '</div>';
                html += '</div>';

                html += '<div class="col-6">';
                html += '<div class="input-group input-group-sm">';
                html += '<span class="input-group-text" style="font-size:10px;">Menu</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.menu + '">';
                html += '</div>';
                html += '</div>';

                html += '<div class="col-6">';
                html += '<div class="input-group input-group-sm">';
                html += '<span class="input-group-text" style="font-size:10px;">Qty</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.qty + '">';
                html += '</div>';
                html += '</div>';

                html += '<div class="col-6">';
                html += '<div class="input-group input-group-sm">';
                html += '<span class="input-group-text" style="font-size:10px;">Biaya</span>';
                html += '<input style="font-size:10px;" type="text" class="form-control" value="' + e.total + '">';
                html += '</div>';
                html += '</div>';

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

    // setInterval(() => {
    //     notif_pesanan();

    // }, 1000);

    // let myOffcanvas = document.getElementById('offcanvasRight')
    // let bsOffcanvas = new bootstrap.Offcanvas(myOffcanvas)
    // bsOffcanvas.show()
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
</script>