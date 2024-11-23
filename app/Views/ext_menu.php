<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Menu Food Court</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/a193ca89ae.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" type="text/css" href="<?= base_url(); ?>style.css" />
    <link href='https://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Barrio' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Asap Condensed' rel='stylesheet'>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <?= view('functions_js'); ?>

    <style>
        body {
            background-image: repeating-linear-gradient(0deg, #0a0908, #0a0908 2px, transparent 2px, transparent);
            background-size: 85px 85px;
            background-color: #1b120c;
        }

        .menu {
            font-family: 'Asap Condensed';
            color: #fffefd;
        }
    </style>
</head>

<body>
    <div class="container" style="margin-bottom: 100px;">
        <div class="body_order">


        </div>
        <div>
            <h1 class=" text-center mt-5" style="font-family: 'Tangerine', serif;color:#d78926;font-size:60px">
                Hayu Food Court
            </h1>
            <h1 class="text-center menu" style="font-size:60px">
                MENU
            </h1>

        </div>

        <div class="row g-5">
            <div class="col-6">
                <h1 class="text-center" style="font-family: 'Barrio';color:#fffefd;font-size:30px">
                    Makanan
                </h1>
                <div style="border-left: 4px solid #db8600;">
                    <div class="row">
                        <?php foreach (barang('Makanan') as $i): ?>
                            <div class="col-9">
                                <h1 class="menu px-2 menu_list" data-order="Makanan" data-id="<?= $i['id']; ?>" data-barang="<?= $i['barang']; ?>" data-harga="<?= $i['harga_satuan']; ?>" style="cursor: pointer;"><?= $i['barang']; ?></h1>
                            </div>
                            <div class="col-3 menu py-2 fw-bold" style="padding-right:35px; background-color: #db8600;text-align:right;font-size:x-large"><?= rupiah($i['harga_satuan']); ?></div>

                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-5">
                    <img class="img-fluid rounded-circle" width="50%" src="<?= base_url('berkas'); ?>/drink.jpg" alt="Drink">

                </div>
                <h1 class="text-center" style="font-family: 'Barrio';color:#fffefd;font-size:30px">
                    Cemilan
                </h1>
                <div style="border-left: 4px solid #db8600;">
                    <div class="row">
                        <?php foreach (barang('Cemilan') as $i): ?>
                            <div class="col-9">
                                <h1 class="menu px-2 menu_list" data-order="Cemilan" data-id="<?= $i['id']; ?>" data-barang="<?= $i['barang']; ?>" data-harga="<?= $i['harga_satuan']; ?>" style="cursor: pointer;"><?= $i['barang']; ?></h1>
                            </div>
                            <div class="col-3 menu py-2 fw-bold" style="padding-right:35px; background-color: #db8600;text-align:right;font-size:x-large"><?= rupiah($i['harga_satuan']); ?></div>

                        <?php endforeach; ?>
                    </div>
                </div>
                <img class="img-fluid rounded-circle" width="50%" src="<?= base_url('berkas'); ?>/snack.jpg" alt="Snack">
            </div>
            <div class="col-6">
                <img class="img-fluid rounded-circle" width="50%" src="<?= base_url('berkas'); ?>/food.jpg" alt="Food">
                <h1 class="text-center" style="font-family: 'Barrio';color:#fffefd;font-size:30px">
                    Minuman
                </h1>
                <div style="border-left: 4px solid #db8600;">
                    <div class="row">
                        <?php foreach (barang('Minuman') as $i): ?>
                            <div class="col-9">
                                <h1 class="menu px-2 menu_list" data-order="Minuman" data-id="<?= $i['id']; ?>" data-barang="<?= $i['barang']; ?>" data-harga="<?= $i['harga_satuan']; ?>" style="cursor: pointer;"><?= $i['barang']; ?></h1>
                            </div>
                            <div class="col-3 menu py-2 fw-bold" style="padding-right:35px; background-color: #db8600;text-align:right;font-size:x-large"><?= rupiah($i['harga_satuan']); ?></div>

                        <?php endforeach; ?>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <div class="fixed-bottom" style="background-color: #201914;">
        <!-- <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#ffae00" fill-opacity="1" d="M0,0L80,42.7C160,85,320,171,480,218.7C640,267,800,277,960,266.7C1120,256,1280,224,1360,208L1440,192L1440,320L1360,320C1280,320,1120,320,960,320C800,320,640,320,480,320C320,320,160,320,80,320L0,320Z"></path>
        </svg> -->

        <h1 class="text-center" style="font-family: 'Tangerine', serif;color:#df8600;font-size:60px">
            Hayu Food Court
        </h1>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        let order_list = [];
        const daftar_order = (data = []) => {

            let html = '';
            let total = 0;
            data.forEach((e, i) => {

                html += '<div class="row pe-3">';
                html += '<div class="col-6">' + (i + 1) + '. ' + e.barang + '</div>';
                html += '<div class="col-3">';
                html += '<div class="d-flex">';
                html += '<a class="change_qty" data-order="minus" data-id="' + e.id + '" data-harga="' + e.harga + '" style="font-size:medium;text-decoration: none;color:#1b120c" href=""><i class="fa-solid fa-circle-minus"></i></a>';
                html += '<div class="mx-2 qty_order_' + e.id + '">' + e.qty_item + '</div>';
                html += '<a class="change_qty" data-order="plus" data-id="' + e.id + '" data-harga="' + e.harga + '" style="font-size:medium;text-decoration: none;color:#1b120c" href=""><i class="fa-solid fa-circle-plus"></i></a>';
                html += '</div>';
                html += '</div>';
                html += '<div class="col-3">';
                html += '<div style="text-align:right">' + angka(e.total_item) + '</div>';
                html += '</div>';

                html += '</div>';


                total += e.total_item;

            });
            let res = {
                html,
                total
            };
            return res;
        }


        const body_order = (html_item) => {
            let html = '';
            html += '<div class="position-fixed top-0 end-0 daftar_order_body" style="width: 500px;">';
            html += '<div class="d-flex justify-content-between px-3 py-2" style="background-color: #d78926;">';
            html += '<div style="font-size:medium;font-weight:bold">ORDER MENU</div>';
            html += '<div style="font-size: medium;"><a class="close_order" href="" style="text-decoration: none;color:#1b120c"><i class="fa-solid fa-circle-xmark"></i></a></div>';
            html += '</div>';
            html += '<div class="p-3" style="background-color: #fffefd;">';
            html += '<div class="input-group input-group-sm mb-2">';
            html += '<span style="width:120px" class="input-group-text">Nomor Meja</span>';
            html += '<input type="text" class="form-control nomor_meja" placeholder="Nomor meja...">';
            html += '</div>';
            html += '<div class="input-group input-group-sm mb-3">';
            html += '<span style="width:120px" class="input-group-text">Nama Pemesan</span>';
            html += '<input type="text" class="form-control nomor_meja" placeholder="Nama pemesan...">';
            html += '</div>';
            html += '<hr>';
            html += '<h6>RINCIAN PESANAN:</h6>';
            html += html_item.html;
            html += '<div class="d-flex justify-content-end gap-1">';
            html += '<hr style="border:1px solid #d78926;width:100%">';
            html += '<div style="color: #d78926;font-weight:bold">+</div>';
            html += '</div>';
            html += '<div class="pe-3" style="color: #d78926;text-align:right;font-weight:bold;font-size:medium;margin-top:-12px">' + angka(html_item.total) + '</div>';

            html += '<div class="d-flex justify-content-center gap-2 mt-2">';
            html += '<button class="btn_grey cancel_order"><i class="fa-solid fa-thumbs-down"></i> Batal</button>';
            html += '<button class="btn_warning"><i class="fa-solid fa-thumbs-up"></i> Pesan</button>';
            html += '</div>';

            html += '</div>';
            html += '</div>';
            $('.body_order').html(html);
        }



        $(document).on('click', '.close_order', function(e) {
            e.preventDefault();
            $('.daftar_order_body').hide();
        })
        $(document).on('click', '.cancel_order', function(e) {
            e.preventDefault();
            order_list = [];
            $('.body_order').html("");
        })

        $(document).on('click', '.menu_list', function(e) {
            e.preventDefault();
            let order = $(this).data('order');
            let barang = $(this).data('barang');
            let harga = $(this).data('harga');
            let id = $(this).data('id');

            if (order_list.length == 0) {
                let item = {
                    id,
                    barang,
                    harga,
                    qty_item: 1,
                    total_item: parseInt(harga)
                };

                order_list.push(item);
            } else {
                let index;
                order_list.forEach((e, i) => {
                    if (e.id == id) {
                        index = i;
                    }
                })

                if (index == undefined) {
                    let item = {
                        id,
                        barang,
                        harga,
                        qty_item: 1,
                        total_item: parseInt(harga)
                    };
                    order_list.push(item);
                } else {
                    order_list[index].qty_item += 1;
                    order_list[index].total_item += parseInt(harga);

                }

            }


            let res = daftar_order(order_list);

            body_order(res);

        })


        $(document).on('click', '.change_qty', function(e) {
            e.preventDefault();

            let id = $(this).data('id');
            let order = $(this).data('order');
            let harga = $(this).data('harga');

            let data_temp = [];
            order_list.forEach((e, i) => {
                if (e.id == id) {
                    e.qty_item = (order == 'minus' ? e.qty_item - 1 : e.qty_item + 1);
                    if (e.qty_item <= 0) {
                        return;
                    } else {
                        e.total_item = e.qty_item * harga;
                        data_temp.push(e);
                    }
                } else {
                    data_temp.push(e);
                }
            })
            order_list = data_temp;
            let res = daftar_order(order_list);

            body_order(res);
        })
    </script>
</body>

</html>