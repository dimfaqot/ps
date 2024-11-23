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
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Sofia">
    <link href='https://fonts.googleapis.com/css?family=Damion' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Audiowide">
    <link href='https://fonts.googleapis.com/css?family=Doppio One' rel='stylesheet'>

    <script src="https://code.jquery.com/jquery-3.7.1.js"></script><?= view('functions_js'); ?><style>
        body {
            background-color: #0d0d0d;
        }

        .menu {
            font-family: 'Asap Condensed';
            color: #fffefd;
        }

        .list_menu {
            background-color: #3f484e;
            font-size: medium;
            border-bottom: 1px solid #fbe793;
        }
    </style>
</head>

<body>

    <div class="d-flex justify-content-center mt-3">
        <div class="p-1 text-center text-white" style="background-color: #f1355c;border-radius:50%;width:150px;height:150px;font-size:90px">
            <i class="fa-solid fa-utensils"></i>
        </div>
    </div>
    <h1 class="text-center" style="font-family: 'Damion';color:#f1355c;font-size:60px">
        Hayu Food Court
    </h1>

    <div class="container-fluid">
        <div class="row">
            <div class="col-4" style="border-right: 3px solid #f1355c;">
                <div class="d-flex justify-content-center mb-2">
                    <div style="width: 150px;height:150px;background-color:#f1355c;border-radius:50%;padding:27px;">
                        <div class="text-center fw-bold" style="color: #fbe793;margin-bottom:-27px;font-size:medium">MEJA</div>
                        <div class="fw-bold text-center text-light" style="font-size: 75px;"><?= $no_meja; ?></div>
                    </div>
                </div>

                <div style="background-color: #3e474d;" class="p-3 d-flex justify-content-center">
                    <div class="text-center">
                        <div style="color: #fbe793;font-family:Sofia">Nama Pemesan</div>
                        <h4 style="color: #f7d02f;font-family:Doppio One"><?= $nama_pemesan; ?></h4>
                        <div class="text-white">Invoice: <?= $data[0]['no_nota']; ?></div>

                    </div>
                </div>
            </div>
            <div class="col-8">
                <div class="d-flex">
                    <div class="col-1 p-2 list_menu text-white fw-bold">#</div>
                    <div class="col-6 p-2 list_menu text-white fw-bold">Menu</div>
                    <div class="col-2 p-2 list_menu text-white fw-bold">Qty</div>
                    <div class="col-3 p-2 list_menu text-white fw-bold">Harga</div>

                </div>
                <?php $total = 0; ?>
                <?php foreach ($data as $k => $i): ?>
                    <?php $total += $i['total']; ?>
                    <div class="d-flex">
                        <div class="col-1 text-white p-2 list_menu"><?= ($k + 1); ?>.</div>
                        <div class="col-6 text-white p-2 list_menu"><?= $i['menu']; ?></div>
                        <div class="col-2 text-white p-2 list_menu"><?= $i['qty']; ?></div>
                        <div style="text-align: right;padding-right:150px" class="col-3 text-white py-2 ps-2 list_menu"><?= rupiah($i['total']); ?></div>

                    </div>


                <?php endforeach; ?>

                <div style="text-align: right;padding-right:150px;background-color:#f1355c" class="text-white fw-bold py-2 ps-2 list_menu">Rp<?= rupiah($total); ?></div>

            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center mt-5 gap-5 body_progress">

    </div>

    <div class="d-flex justify-content-center mt-5">
        <button style="background-color: #f1355c;font-family:Asap Condensed;font-size:x-large;border:1px solid white" class="fw-bold px-3 py-2 text-white rounded"><i class="fa-regular fa-file-lines"></i> INVOICE</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        const process = () => {
            post('ext/invoice', {
                kategori: 'Pesanan',
                jenis: 'Kantin',
                no_nota: "<?= $data[0]['no_nota']; ?>"
            }).then(res => {
                if (res.status = "200") {
                    let html = '';
                    html += '<div class="py-2" style="background-color: #f7d02f;width:100px;height:100px;text-align:center;font-size:xx-large;border-radius:50%;color:' + (res.data == 'WAITING' ? '#f1355c' : '#fbe793') + '">';
                    html += '<i class="fa-solid fa-stopwatch"></i>';
                    html += '<div style="font-size: medium;">WAITING</div>';
                    html += '</div>';
                    html += '<div class="py-2" style="background-color: #f7d02f;width:100px;height:100px;text-align:center;font-size:xx-large;border-radius:50%;color:' + (res.data == 'PROCESS' ? '#f1355c' : '#fbe793') + '">';
                    html += '<i class="fa-solid fa-fire-burner"></i>';
                    html += '<div style="font-size: medium;">PROCESS</div>';
                    html += '</div>';
                    html += '<div class="py-2" style="background-color: #f7d02f;width:100px;height:100px;text-align:center;font-size:xx-large;border-radius:50%;color:' + (res.data == 'DONE' ? '#f1355c' : '#fbe793') + '">';
                    html += '<i class="fa-solid fa-clock-rotate-left"></i>';
                    html += '<div style="font-size: medium;">DONE</div>';
                    html += '</div>';

                    $('.body_progress').html(html);

                    if (res.data == 'DONE') {

                    }
                } else {
                    gagal(res.message);
                }
            })
        }

        process();
        // setInterval(() => {
        // }, 2000);
    </script>
</body>

</html>