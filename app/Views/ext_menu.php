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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
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


        .input_light div {
            color: #bcc1f1;
            margin-bottom: 5px;
        }

        .input_light input {
            background-color: #292550;
            border: 5px;
            padding: 5px 10px;
            border-radius: 4px;
            color: #bcc1f1
        }
    </style>
</head>

<body>
    <!-- warning alert message -->
    <div class="box_warning" style="position:fixed;z-index:999999;display:none;">

    </div>
    <!-- warning alert message with button -->
    <div class="box_warning_with_button" style="position:fixed;z-index:999999;display:none;">

    </div>
    <!-- warning confirm -->
    <div class="box_confirm" style="position:fixed;z-index:999999;display:none;">

    </div>
    <div class="container" style="margin-bottom: 100px;">

        <div>
            <h1 class=" text-center mt-5" style="font-family: 'Tangerine', serif;color:#d78926;font-size:60px">
                Hayu Food Court
            </h1>
            <h1 class="text-center menu" style="font-size:60px">
                MENU
            </h1>

        </div>
        <div class="d-none d-md-block">
            <?= view('ext_menu_md'); ?>
        </div>

        <div class="d-block d-md-none d-sm-block">
            <?= view('ext_menu_sm'); ?>
        </div>

    </div>



</body>

</html>