<?php

helper('qr_code_helper');
$data = ['ip' => getenv('IP')];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Absen Qrcode</title>
</head>

<body>
    <div style="text-align: center;">
        <img width="500px;" src="<?= set_qr_code(base_url('presentation/') . encode_jwt($data), 'logo', 'Absen'); ?>" alt="Absen Playground">

    </div>
</body>

</html>