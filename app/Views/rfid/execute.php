<?php
if (session('lokasi') == "Barber") {
    $dbu = db('users');
    $user = $dbu->where('uid', session('uid'))->get()->getRowArray();
    $barber = [];
    $dbb = db('barber');
    $qbarber = $dbb->where('user_id', $user['id'])->where('status', 0)->get()->getResultArray();
    if ($qbarber) {
        $barber = $qbarber;
    }
} else {
    $menu = [];
    $rfid = [];
    $finger = [];
    $perangkat = [];
    if ($role !== 'Member') {

        $db = db('menu_tap');
        $qMenu = $db->where('role', $role)->groupBy('grup')->orderBy('id', 'ASC')->get()->getResultArray();
        if ($qMenu) {
            $menu = $qMenu;
        }
        foreach ($menu as $i) {
            if ($i['grup'] !== "perangkat") {
                ${$i['grup']} = $db->where('grup', $i['grup'])->orderBy('urutan', 'ASC')->get()->getResultArray();
            }
        }
        $qmPerangkat = $db->where('role', $role)->where('grup', "perangkat")->orderBy('grup', 'ASC')->get()->getResultArray();
        $dbp = db("perangkat");
        if ($qmPerangkat) {
            foreach ($qmPerangkat as $i) {
                $q = $dbp->where('grup', $i['menu'])->orderBy('no_urut', 'ASC')->get()->getResultArray();
                if ($q) {
                    foreach ($q as $p) {
                        $perangkat[] = $p;
                    }
                }
            }
        }

        foreach ($qMenu as $i) {
            if ($i['grup'] !== "perangkat") {
                ${$i['grup']} = $db->where('grup', $i['grup'])->orderBy('urutan', 'ASC')->get()->getResultArray();
            }
        }

        if ($role == "Root") {
            $qr = $db->where('role', 'Root')->where('grup', "rfid")->orderBy('urutan', 'ASC')->get()->getResultArray();
            if ($qr) {
                $rfid = $qr;
            }
            $qf = $db->where('role', 'Root')->where('grup', "rfid")->orderBy('urutan', 'ASC')->get()->getResultArray();
            if ($qf) {
                $finger = $qf;
            }
        }
    }
    $dbb = db('jadwal_2');
    $billiard = $dbb->orderBy('meja', 'ASC')->get()->getResultArray();

    $ps = [];
    $dbp = db('unit');
    $qps = $dbp->whereNotIn('status', ["Maintenance"])->orderBy('id', 'ASC')->get()->getResultArray();

    foreach ($qps as $i) {
        $exp = explode(" ", $i['unit']);
        $i['meja'] = (int)end($exp);
        $i['is_active'] = ($i['status'] == "In Game" ? 1 : 0);
        $ps[] = $i;
    }
}

?>

<?= $this->extend('rfid/template') ?>

<?= $this->section('content') ?>
<?php if (session('lokasi') !== "Barber"): ?>
    <?= view('rfid/menu_js', ['menu' => $menu, 'perangkat' => $perangkat, 'ps' => $ps, 'billiard' => $billiard, 'role' => $role, 'rfid' => $rfid, 'finger' => $finger]); ?>
<?php endif; ?>

<div class="d-flex justify-content-center">
    <div style="padding-top: 20px;width:70%" class="rounded-bottom px-2 pb-2 text-light border border-top-0 border-light bg-secondary bg-opacity-50">
        <div class="d-flex justify-content-center mb-3">
            <div class="spinner-border text-dark" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        <div class="rounded-2 p-1 mb-2 bg-secondary"><label style="width: 50px;">Nama</label>: <?= $nama; ?></div>
        <div class="rounded-2 p-1 mb-2 bg-secondary"><label style="width: 50px;">Saldo</label>: <?= rupiah($saldo); ?></div>

    </div>
</div>

<?php if (session('lokasi') == "Barber"): ?>
    <?php if (count($barber) == 0): ?>
        <h6 class="text-center text-secondary mt-4">Thx, elu kagak punya utang di BARBER!.</h6>
    <?php else: ?>
        <?php $total = 0; ?>
        <div class=" mx-4 px-2 mt-4">
            <div class="d-flex justify-content-between">
                <h6 class="total_hutang text-warning"></h6>
                <div style="cursor: pointer;font-size:12px" class="btn_lunasi_barber px-4 text-light p-1 rounded bg-warning border border-warning bg-opacity-25">BAYAR</div>
            </div>
            <table class="table table-sm table-border table-dark" style="font-size: small">
                <thead>
                    <tr>
                        <td class="text-center">#</td>
                        <td class="text-center">Tgl</td>
                        <td class="text-center">Layanan</td>
                        <td class="text-center">Harga</td>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barber as $k => $i): ?>
                        <?php $total += (int)$i['total_harga']; ?>
                        <tr>
                            <td><?= $k + 1; ?></td>
                            <td class="text-center"><?= date('d/m/Y', $i['tgl']); ?></td>
                            <td><?= $i['layanan']; ?></td>
                            <td class="text-end"><?= angka($i['total_harga']); ?></td>
                        </tr>

                    <?php endforeach; ?>

                </tbody>;
            </table>
        </div>
        <script>
            let total = "<?= $total; ?>";
            $(".total_hutang").text("TOTAL: " + angka(total));

            $(document).on("click", ".btn_lunasi_barber", function(e) {
                e.preventDefault();
                $('.header_fullscreen').html(header_modal("loading", "BARBER"));
                $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');
                show_modal("fullscreen");
                clearInterval(interval_countdown);
                let user_id = $(this).data("user_id");

                post("rfid/lunasi_barber", {
                    user_id: 40
                }).then(res => {
                    if (res.status == "200") {
                        $(".body_fullscreen").html('<div class="text-light text-center">' + res.message + '</div>');
                    } else {
                        $(".body_fullscreen").html('<div class="text-danger text-center">' + res.message + '</div>');
                    }

                    setTimeout(() => {
                        logout("Waktu habis!.");
                    }, 2000);
                })
            })
        </script>
    <?php endif; ?>
<?php endif; ?>
<?php if (session('lokasi') !== "Barber"): ?>
    <div class="mx-4 px-2 text-center mt-2">
        <div class="row justify-content-center g-2">
            <?php foreach ($menu as $i): ?>
                <div class="col-4">
                    <div style="font-size: medium;" class="rounded border border-warning border-opacity-75 py-2 text-secondary btn_menu" data-menu="<?= $i["grup"]; ?>"><?= upper_first($i['grup']); ?></div>
                </div>
            <?php endforeach; ?>
            <?php if ($role == "Member"): ?>
                <div style="font-size: medium;" class="rounded border border-warning border-opacity-75 py-2 text-secondary btn_hutang" data-user_id="<?= $user_id; ?>" data-menu="hutang">HUTANG</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="sub_menu mx-3 mt-3"></div>
    <div class="durasi mx-4 px-2 mt-2"></div>
    <div class="body_btn_save d-grid mt-4 mx-4 px-2"></div>

    <div class="d-flex justify-content-center mt-4">
        <div style="cursor:pointer;font-size:35px;width: 75px;height:75px;" class="fw-bold text-center pt-3 rounded-circle border border-secondary text-secondary body_countdown">1</div>
    </div>


    <script>
        let data = {};
        countdown(10);
        const default_sub_menu = () => {
            if (data.menu == undefined) {
                let html = '<h6 class="text-center">' + lokasi.toUpperCase() + '</h6>';
                html += menus[lokasi];
                $('.sub_menu').html(html);
            }
        }

        default_sub_menu();
        const durasi = () => {
            if (data.menu == undefined || data.sub_menu == undefined || data.menu !== lokasi.toLowerCase()) {
                $('.durasi').html("");
            } else {
                let html = '<h6 class="text-center">' + lokasi.toUpperCase() + '</h6>';
                html += menus.durasi;
                $('.durasi').html(html);
            }
            console.log(data);
        }
        const btn_save = () => {
            if (data.menu == lokasi) {
                if (data.menu == lokasi && data.sub_menu !== undefined && data.durasi !== undefined) {
                    $('.body_btn_save').html(menus.btn_save);
                } else {
                    $('.body_btn_save').html("");
                }

            } else {
                if (data.menu !== undefined && data.sub_menu !== undefined) {
                    $('.body_btn_save').html(menus.btn_save);
                } else {
                    $('.body_btn_save').html("");
                }
            }
        }

        const remove_cls = (cls, rmv) => {
            let elem = document.querySelectorAll('.' + cls);
            elem.forEach(e => {
                e.classList.remove(rmv);
            });
        }

        $(document).on("click", ".btn_menu", function(e) {
            e.preventDefault();
            let menu = $(this).data("menu");
            if (menu == 'absen' || menu == 'poin' || menu == "finger" || menu == "shift") {
                if (menu == "finger") {
                    warning_message("Fitur belum tersedia!.");
                    return;
                }
                admin(menu);
                return;
            }
            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                remove_cls("btn_menu", "active");
                $(this).addClass("active");
            }

            if (data.menu == undefined || data.menu !== menu) {
                data = {};
                data['menu'] = menu;
            } else if (data.menu == menu) {
                data = {};
            }
            $(".sub_menu").html(menus[menu]);
            default_sub_menu();
            durasi();
            btn_save();
            console.log(data);
        })

        $(document).on("click", ".btn_sub_menu", function(e) {
            e.preventDefault();
            let sub_menu = $(this).data("sub_menu");
            let menu = data.menu;

            if (menu == "perangkat") {
                admin(menu, sub_menu);
                return;
            }

            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                remove_cls("btn_sub_menu", "active");
                $(this).addClass("active");
            }

            if (data.sub_menu == undefined || data.sub_menu !== sub_menu) {
                data['sub_menu'] = sub_menu;
            } else if (data.sub_menu == sub_menu) {
                data = {};
                data['menu'] = menu;
            }
            default_sub_menu();
            durasi();
            btn_save();
            console.log(data);
        })

        $(document).on("click", ".btn_meja", function(e) {
            e.preventDefault();
            let menu = lokasi;
            let sub_menu = $(this).data("meja");

            data['menu'] = menu;
            if ($(this).hasClass("disable")) {
                if (role == "Member") {
                    warning_message("Meja sedang digunakan");
                } else {
                    admin("meja", sub_menu);
                }
                return;
            }

            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                remove_cls("btn_meja", "active");
                $(this).addClass("active");
            }

            if (data.sub_menu == undefined || data.sub_menu !== sub_menu) {
                data = {};
                data['menu'] = menu;
                data['sub_menu'] = sub_menu;
            } else if (data.sub_menu == sub_menu) {
                data = {};
                data['menu'] = menu;
            }

            default_sub_menu();
            durasi();
            btn_save();
            console.log(data);
        })
        $(document).on("click", ".btn_durasi", function(e) {
            e.preventDefault();
            let menu = data.menu;
            let sub_menu = data.sub_menu;
            let durasi = $(this).data("durasi");

            if ($(this).hasClass("active")) {
                $(this).removeClass("active");
            } else {
                remove_cls("btn_durasi", "active");
                $(this).addClass("active");
            }

            if (data.durasi == undefined || data.durasi !== durasi) {
                data['durasi'] = durasi;
            } else if (data.durasi == durasi) {
                data = {};
                data['menu'] = menu;
                data['sub_menu'] = sub_menu;
            }
            console.log(data);
            btn_save();
        })
        $(document).on("click", ".btn_save", function(e) {
            e.preventDefault();
            $(".body_btn_save").html("");
            clearInterval(interval_countdown);

            if (data.menu == 'topup' || data.menu == 'rfid' || data.menu == 'finger') {
                if (data.durasi == undefined) {
                    $('.header_fullscreen').html(header_modal("loading", (data.menu == "topup" ? data.menu.toUpperCase() : data.sub_menu.toUpperCase() + " " + data.menu.toUpperCase())));
                    let html = "";
                    html += '<input data-biaya="' + data.sub_menu + '" data-meja="" class="form-control form-control-sm text-secondary bg-dark search_db_input" autofocus type="text" placeholder="Cari nama...">';
                    html += '<div class="mt-2 body_list_search_user">';
                    html += '</div>';
                    $(".body_fullscreen").html(html);
                    show_modal();
                    countdown(10);
                    return;

                }
            }
            if (data.menu == "tap") {
                transaksi_tap();
                return;
            }
            $('.header_fullscreen').html(header_modal("loading", "TRANSAKSI"));
            $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');
            show_modal();
            post("rfid/transaksi", {
                data
            }).then(res => {
                let messages = res.message.split("|");
                let html = "";
                messages.forEach(e => {
                    html += '<div class="' + (res.status == "200" ? "text-light" : "text-danger") + ' text-center">' + e + '</div>';
                })
                $(".body_fullscreen").html(html);


                setTimeout(() => {
                    logout("Waktu habis!.");
                }, 2000);
            })
        })
        $(document).on("click", ".btn_hutang", function(e) {
            e.preventDefault();
            let user_id = $(this).data("user_id");

            post("rfid/hutang", {
                user_id: 40
            }).then(res => {
                if (res.status == "200") {
                    let html = "";
                    $('.header_fullscreen').html(header_modal("button", "HUTANG"));
                    let total = 0;
                    if (res.data.length == 0) {
                        html += '<h6 class="text-center text-secondary">Thx, elu kagak punya utang!.</h6>';
                    } else {
                        html += '<div class="d-flex justify-content-between">';
                        html += '<h6 class="total_hutang text-warning"></h6>';
                        html += '<div style="cursor: pointer;font-size:12px" class="btn_lunasi_hutang px-4 text-light p-1 rounded bg-warning border border-warning bg-opacity-25">BAYAR</div>';
                        html += '</div>';
                        html += '<table class="table table-sm table-border table-dark" style="font-size: small">';
                        html += '<thead>';
                        html += '<tr>';
                        html += '<td class="text-center">#</td>';
                        html += '<td class="text-center">Tgl</td>';
                        html += '<td class="text-center">Barang</td>';
                        html += '<td class="text-center">Harga</td>';
                        html += '</tr>';
                        html += '</thead>';
                        html += '<tbody>';
                        res.data.forEach((e, i) => {
                            total += parseInt(e.total_harga);
                            html += '<tr>';
                            html += '<td>' + (i + 1) + '</td>';
                            html += '<td class="text-center">' + time_php_to_js(e.tgl) + '</td>';
                            html += '<td>' + e.barang + '</td>';
                            html += '<td class="text-end">' + angka(e.total_harga) + '</td>';
                            html += '</tr>';
                        })

                        html += '</tbody>';
                        html += '</table>';
                    }
                    $(".body_fullscreen").html(html);
                    show_modal("fullscreen", "show");
                    $(".total_hutang").text("TOTAL: " + angka(total));
                }
            })
        })
        $(document).on("click", ".btn_lunasi_hutang", function(e) {
            e.preventDefault();
            $('.header_fullscreen').html(header_modal("loading", "HUTANG"));
            $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');

            clearInterval(interval_countdown);
            let user_id = $(this).data("user_id");
            post("rfid/lunasi_hutang", {
                user_id: 40
            }).then(res => {
                if (res.status == "200") {
                    $(".body_fullscreen").html('<div class="text-light text-center">' + res.message + '</div>');
                } else {
                    $(".body_fullscreen").html('<div class="text-danger text-center">' + res.message + '</div>');
                }

                setTimeout(() => {
                    logout("Waktu habis!.");
                }, 2000);
            })
        })
        $(document).on("click", ".btn_konfirmasi", function(e) {
            e.preventDefault();
            show_modal("warning", "hide");
            $('.header_fullscreen').html(header_modal("loading", "AKHIRI PERMAINAN"));
            $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');
            show_modal();

            clearInterval(interval_countdown);

            let order = $(this).data("order");
            let id = $(this).data("id");
            post("rfid/akhiri_permainan", {
                order,
                id
            }).then(res => {
                if (res.status == "200") {
                    if (res.data2 == "open") {
                        let html = "";
                        data['menu'] = "tap";
                        data['id'] = res.data3;
                        data['biaya'] = res.data;
                        data['meja'] = id;
                        html += '<h6 class="text-center text-light">' + res.message + '</h6>';
                        html += '<h6 class="text-center text-light">' + angka(res.data) + '</h6>';
                        html += '<h6 class="text-center text-light text-opacity-50">PILIH CARA PEMBAYARAN:</h6>';
                        html += '<div class="d-flex justify-content-center gap-2 py-1">';
                        html += '<div data-order="cash" data-id="' + res.data3 + '" data-biaya="' + res.data + '" class="btn_cara_pembayaran text-center text-light px-5 border rounded-2 border-light border-opacity-25 py-1 bg-success bg-opacity-75" style="cursor: pointer;">Cash</div>';
                        html += '<div data-order="tap" data-id="' + res.data3 + '" data-biaya="' + res.data + '" class="btn_cara_pembayaran text-center text-light px-5 border rounded-2 border-light border-opacity-25 py-1 bg-dark bg-opacity-75" style="cursor: pointer;">Tap</div>';
                        html += '</div>';
                        $(".body_fullscreen").html(html);
                        countdown(10);
                        return;
                    } else {
                        $(".body_fullscreen").html('<div class="text-light text-center">' + res.message + '</div>');
                    }
                } else {
                    $(".body_fullscreen").html('<div class="text-danger text-center">' + res.message + '</div>');
                }

                setTimeout(() => {
                    logout("Waktu habis!.");
                }, 2000);
            })
        })

        $(document).on("click", ".btn_cara_pembayaran", function(e) {
            e.preventDefault();

            $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');
            let order = $(this).data("order");
            let biaya = $(this).data("biaya");
            let id = $(this).data("id");
            clearInterval(interval_countdown);

            if (order == "tap") {
                let html = "";
                data['menu'] = "tap";
                html += '<input data-biaya="' + biaya + '" data-meja="' + id + '" class="form-control form-control-sm text-secondary bg-dark search_db_input" autofocus type="text" placeholder="Cari nama...">';
                html += '<div class="mt-2 body_list_search_user">';
                html += '</div>';
                $(".body_fullscreen").html(html);
                countdown(10);
                return;
            }

            post("rfid/bayar_permainan", {
                order,
                biaya,
                id
            }).then(res => {
                if (res.status == "200") {
                    $(".body_fullscreen").html('<div class="text-light text-center">' + res.message + '</div>');
                } else {
                    $(".body_fullscreen").html('<div class="text-danger text-center">' + res.message + '</div>');
                }

                setTimeout(() => {
                    logout("Waktu habis!.");
                }, 2000);
            })
        })

        $(document).on('keyup', '.search_db_input', function(e) {
            e.preventDefault();

            let value = $(this).val();
            let meja = $(this).data('meja');
            let biaya = $(this).data('biaya');
            post('rfid/search_user', {
                value
            }).then(res => {
                if (res.status == '200') {
                    let html = '';
                    if (res.data.length == 0) {
                        html += '<div class="bg-dark border px-2 pt-1 pb-2 border-secondary border-opacity-50 text-secondary rounded-2" style="cursor: pointer;">Data tidak ditemukan!.</div>';
                    } else {
                        res.data.forEach((e, i) => {
                            html += '<div data-biaya="' + biaya + '" data-meja="' + meja + '" data-id="' + e.id + '" class="insert_value bg-dark border px-2 pt-1 pb-2 border-secondary border-opacity-50 ' + (e.role == "Member" ? "text-secondary" : "text-warning") + ' rounded-2" style="cursor: pointer;">' + e.nama + '</div>';
                        })

                    }

                    $('.body_list_search_user').html(html);
                } else {
                    gagal_with_button(res.message);
                }
            })

        })

        $(document).on('click', '.insert_value', function(e) {
            e.preventDefault();

            let id = $(this).data('id');

            $('.body_btn_save').html("");
            $(".search_db_input").val($(this).text());
            $('.body_btn_save').html(menus.btn_save);
            $(".body_list_search_user").html("");

            data["durasi"] = id;

        })

        const transaksi_tap = () => {
            let billiard_id = data.id;
            let biaya = data.biaya;
            let member_id = data.durasi;
            let meja = data.meja;
            $('.header_fullscreen').html(header_modal("loading", "TRANSAKSI TAP"));
            $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');
            post('rfid/transaksi_tap', {
                billiard_id,
                meja,
                biaya,
                member_id
            }).then(res => {
                if (res.status == "200") {
                    $(".body_fullscreen").html('<div class="text-light text-center">' + res.message + '</div>');
                } else {
                    $(".body_fullscreen").html('<div class="text-danger text-center">' + res.message + '</div>');
                }

                setTimeout(() => {
                    logout("Waktu habis!.");
                }, 2000);
            })
        }
    </script>
<?php endif; ?>

<?= $this->endSection() ?>