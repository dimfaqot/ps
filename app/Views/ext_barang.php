<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bootstrap demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link href='http://fonts.googleapis.com/css?family=Tangerine' rel='stylesheet' type='text/css'>
    <link href='https://fonts.googleapis.com/css?family=Barrio' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Asap Condensed' rel='stylesheet'>

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
        <div>
            <h1 class="text-center mt-5" style="font-family: 'Tangerine', serif;color:#d78926;font-size:60px">
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
                                <h1 class="menu px-2"><?= $i['barang']; ?></h1>
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
                                <h1 class="menu px-2"><?= $i['barang']; ?></h1>
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
                                <h1 class="menu px-2"><?= $i['barang']; ?></h1>
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
</body>

</html>