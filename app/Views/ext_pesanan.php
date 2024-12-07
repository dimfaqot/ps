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
    <div class="d-flex justify-content-center mt-3">
        <div class="p-1 text-center text-white" style="background-color: #f1355c;border-radius:50%;width:150px;height:150px;font-size:90px">
            <i class="fa-solid fa-utensils"></i>
        </div>
    </div>
    <h1 class="text-center" style="font-family: 'Damion';color:#f1355c;font-size:60px">
        Hayu Food Court
    </h1>
    <div class="d-none d-md-block">


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
    </div>

    <div class="d-block d-md-none d-sm-block">
        <div class="container" style="padding-bottom: 110px;">
            <div class="p-3 mb-3 border-bottom rounded fw-bold text-light" style="background-color: #db8600;font-size: medium;">
                <i class="fa-solid fa-bowl-rice"></i> Daftar Pesanan
            </div>
            <div class="rounded p-3" style="background-color: #1b120c;">
                <div class="input_light mb-3">
                    <div>No. Meja</div>
                    <input type="number" value="<?= $no_meja; ?>" style="width: 100%;">
                </div>
                <div class="input_light mb-3">
                    <div>Nama Pemesan</div>
                    <input type="text" value="<?= $nama_pemesan; ?>" style="width: 100%;">
                </div>
                <div class="input_light mb-3">
                    <div>Invoice</div>
                    <input type="text" value="<?= $data[0]['no_nota']; ?>" style="width: 100%;">
                </div>
                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Menu</th>
                            <th>Qty</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $k => $i): ?>

                            <tr>
                                <td><?= ($k + 1); ?></td>
                                <td><?= $i['menu']; ?></td>
                                <td><?= $i['qty']; ?></td>
                                <td style="text-align: right;"><?= angka($i['total']); ?></td>
                            </tr>

                        <?php endforeach; ?>
                        <tr>
                            <th colspan="3">TOTAL</th>
                            <th style="text-align: right;"><?= angka($total); ?></th>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <div style="padding:5px;width: 60px;height:60px;border-radius:50%;border:1px solid white;font-size:xx-large;color:white;text-align:center;background-color:#db8600"><i class="fa-regular fa-clock"></i></div>
                </div>
                <div class="d-grid">
                    <button style="background-color:#1b120c" class="rounded text-light p-2 btn_save_pesanan_sm"><i class="fa-regular fa-file-lines"></i> INVOICE</button>
                </div>
            </div>
        </div>
    </div>
    <nav class="fixed-bottom border-top">
        <div class="m-auto text-center p-3" style="background-color: #1b120c;">
            <a target="_blank" href="https://www.instagram.com/djanasragen/">
                <svg width="98" height="17" viewBox="0 0 98 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M8.02734 2.80078V1.48828L9.90234 0.550781V2.80078V3.73828V7.39453C9.90234 8.23828 9.69531 9.01172 9.28125 9.71484C8.86719 10.4102 8.3125 10.9648 7.61719 11.3789C6.92188 11.793 6.14844 12 5.29688 12C4.45312 12 3.68359 11.793 2.98828 11.3789C2.29297 10.9648 1.73828 10.4102 1.32422 9.71484C0.910156 9.01953 0.703125 8.25 0.703125 7.40625C0.703125 6.55469 0.910156 5.78125 1.32422 5.08594C1.73828 4.39062 2.29297 3.83594 2.98828 3.42188C3.69141 3.00781 4.46484 2.80078 5.30859 2.80078H8.02734ZM8.02734 7.39453V4.67578H5.30859C4.55859 4.67578 3.91406 4.94141 3.375 5.47266C2.84375 6.00391 2.57812 6.64844 2.57812 7.40625C2.57812 8.15625 2.84375 8.79688 3.375 9.32812C3.90625 9.85938 4.54688 10.125 5.29688 10.125C6.05469 10.125 6.69922 9.85938 7.23047 9.32812C7.76172 8.78906 8.02734 8.14453 8.02734 7.39453Z" fill="#80DEEA" />
                    <path d="M14.9766 5.08594V12.3281H14.9648H14.9766C14.9766 13.1719 14.7695 13.9414 14.3555 14.6367C13.9414 15.3398 13.3828 15.8984 12.6797 16.3125C11.9844 16.7266 11.2148 16.9336 10.3711 16.9336V15.0586C11.1211 15.0586 11.7617 14.7891 12.293 14.25C12.832 13.7188 13.1016 13.0781 13.1016 12.3281V5.08594H14.9766ZM14.9766 1.42969V3.30469H13.1016V2.36719L14.9766 1.42969Z" fill="#80DEEA" />
                    <path d="M20.9766 2.80078C21.8281 2.80078 22.6016 3.00781 23.2969 3.42188C23.9922 3.83594 24.5469 4.39062 24.9609 5.08594C25.375 5.78125 25.582 6.55469 25.582 7.40625V12H20.9883C20.1445 12 19.3711 11.793 18.668 11.3789C17.9727 10.9648 17.418 10.4102 17.0039 9.71484C16.5898 9.01172 16.3828 8.23828 16.3828 7.39453C16.3828 6.55078 16.5898 5.78125 17.0039 5.08594C17.418 4.39062 17.9727 3.83594 18.668 3.42188C19.3633 3.00781 20.1328 2.80078 20.9766 2.80078ZM23.707 10.125V7.40625C23.707 6.64844 23.4414 6.00391 22.9102 5.47266C22.3789 4.94141 21.7344 4.67578 20.9766 4.67578C20.2266 4.67578 19.5859 4.94141 19.0547 5.47266C18.5234 6.00391 18.2578 6.64453 18.2578 7.39453C18.2578 8.14453 18.5234 8.78906 19.0547 9.32812C19.5938 9.85938 20.2383 10.125 20.9883 10.125H23.707Z" fill="#80DEEA" />
                    <path d="M31.1133 2.8125C31.8633 2.8125 32.5469 3 33.1641 3.375C33.7891 3.74219 34.2852 4.23828 34.6523 4.86328C35.0273 5.48047 35.2148 6.16797 35.2148 6.92578V12H33.3398V6.92578C33.3398 6.30859 33.1211 5.78125 32.6836 5.34375C32.2461 4.90625 31.7227 4.6875 31.1133 4.6875C30.4961 4.6875 29.9688 4.90625 29.5312 5.34375C29.0938 5.78125 28.875 6.30859 28.875 6.92578V12H27V6.92578C27 6.16797 27.1836 5.48047 27.5508 4.86328C27.9258 4.23828 28.4219 3.74219 29.0391 3.375C29.6641 3 30.3555 2.8125 31.1133 2.8125Z" fill="#80DEEA" />
                    <path d="M41.2266 2.80078C42.0781 2.80078 42.8516 3.00781 43.5469 3.42188C44.2422 3.83594 44.7969 4.39062 45.2109 5.08594C45.625 5.78125 45.832 6.55469 45.832 7.40625V12H41.2383C40.3945 12 39.6211 11.793 38.918 11.3789C38.2227 10.9648 37.668 10.4102 37.2539 9.71484C36.8398 9.01172 36.6328 8.23828 36.6328 7.39453C36.6328 6.55078 36.8398 5.78125 37.2539 5.08594C37.668 4.39062 38.2227 3.83594 38.918 3.42188C39.6133 3.00781 40.3828 2.80078 41.2266 2.80078ZM43.957 10.125V7.40625C43.957 6.64844 43.6914 6.00391 43.1602 5.47266C42.6289 4.94141 41.9844 4.67578 41.2266 4.67578C40.4766 4.67578 39.8359 4.94141 39.3047 5.47266C38.7734 6.00391 38.5078 6.64453 38.5078 7.39453C38.5078 8.14453 38.7734 8.78906 39.3047 9.32812C39.8438 9.85938 40.4883 10.125 41.2383 10.125H43.957Z" fill="#80DEEA" />
                    <path d="M53.6133 6.45703C54.3789 6.45703 55.0312 6.73047 55.5703 7.27734C56.1172 7.81641 56.3906 8.46875 56.3906 9.23438C56.3906 9.99219 56.1172 10.6445 55.5703 11.1914C55.0312 11.7305 54.3789 12 53.6133 12H47.625V10.125H53.6133C53.8633 10.125 54.0742 10.0391 54.2461 9.86719C54.4258 9.6875 54.5156 9.47656 54.5156 9.23438C54.5156 8.98438 54.4258 8.77344 54.2461 8.60156C54.0742 8.42188 53.8633 8.33203 53.6133 8.33203H52.9336H52.5586H50.0156C49.2578 8.33203 48.6055 8.0625 48.0586 7.52344C47.5195 6.98438 47.25 6.33203 47.25 5.56641C47.25 4.80078 47.5195 4.14844 48.0586 3.60938C48.6055 3.07031 49.2578 2.80078 50.0156 2.80078H56.0156V4.67578H50.0156C49.7734 4.67578 49.5625 4.76172 49.3828 4.93359C49.2109 5.10547 49.125 5.31641 49.125 5.56641C49.125 5.81641 49.2109 6.02734 49.3828 6.19922C49.5625 6.37109 49.7734 6.45703 50.0156 6.45703H52.5586H52.9336H53.6133Z" fill="#00BCD4" />
                    <path d="M62.3906 2.78906C63.2422 2.78906 64.0156 3 64.7109 3.42188C65.4062 3.83594 65.9609 4.39062 66.375 5.08594C66.7891 5.78125 66.9961 6.55078 66.9961 7.39453V11.0625V12V14.2383L65.1211 13.3008V12H62.4023C61.5586 12 60.7852 11.793 60.082 11.3789C59.3867 10.957 58.832 10.3984 58.418 9.70312C58.0039 9.00781 57.7969 8.23828 57.7969 7.39453C57.7969 6.55078 58.0039 5.78125 58.418 5.08594C58.832 4.38281 59.3867 3.82422 60.082 3.41016C60.7773 2.99609 61.5469 2.78906 62.3906 2.78906ZM62.4023 10.125H65.1211V7.39453C65.1211 6.64453 64.8555 6.00391 64.3242 5.47266C63.793 4.93359 63.1484 4.66406 62.3906 4.66406C61.6406 4.66406 61 4.93359 60.4688 5.47266C59.9375 6.00391 59.6719 6.64453 59.6719 7.39453C59.6719 8.14453 59.9375 8.78906 60.4688 9.32812C61.0078 9.85938 61.6523 10.125 62.4023 10.125Z" fill="#00BCD4" />
                    <path d="M74.7539 7.93359V2.78906H76.6289V7.93359C76.6289 8.68359 76.4414 9.36719 76.0664 9.98438C75.6992 10.5938 75.2031 11.082 74.5781 11.4492C73.9609 11.8164 73.2773 12 72.5273 12C71.7695 12 71.0781 11.8164 70.4531 11.4492C69.8359 11.082 69.3398 10.5938 68.9648 9.98438C68.5977 9.36719 68.4141 8.68359 68.4141 7.93359V2.78906H70.2891V7.93359C70.2891 8.54297 70.5078 9.06641 70.9453 9.50391C71.3828 9.93359 71.9102 10.1484 72.5273 10.1484C73.1367 10.1484 73.6602 9.93359 74.0977 9.50391C74.5352 9.06641 74.7539 8.54297 74.7539 7.93359Z" fill="#00BCD4" />
                    <path d="M82.6406 2.80078C83.4922 2.80078 84.2656 3.00781 84.9609 3.42188C85.6562 3.83594 86.2109 4.39062 86.625 5.08594C87.0391 5.78125 87.2461 6.55469 87.2461 7.40625V12H82.6523C81.8086 12 81.0352 11.793 80.332 11.3789C79.6367 10.9648 79.082 10.4102 78.668 9.71484C78.2539 9.01172 78.0469 8.23828 78.0469 7.39453C78.0469 6.55078 78.2539 5.78125 78.668 5.08594C79.082 4.39062 79.6367 3.83594 80.332 3.42188C81.0273 3.00781 81.7969 2.80078 82.6406 2.80078ZM85.3711 10.125V7.40625C85.3711 6.64844 85.1055 6.00391 84.5742 5.47266C84.043 4.94141 83.3984 4.67578 82.6406 4.67578C81.8906 4.67578 81.25 4.94141 80.7188 5.47266C80.1875 6.00391 79.9219 6.64453 79.9219 7.39453C79.9219 8.14453 80.1875 8.78906 80.7188 9.32812C81.2578 9.85938 81.9023 10.125 82.6523 10.125H85.3711Z" fill="#00BCD4" />
                    <path d="M95.9883 2.80078V1.48828L97.8633 0.550781V2.80078V3.73828V7.39453C97.8633 8.23828 97.6562 9.01172 97.2422 9.71484C96.8281 10.4102 96.2734 10.9648 95.5781 11.3789C94.8828 11.793 94.1094 12 93.2578 12C92.4141 12 91.6445 11.793 90.9492 11.3789C90.2539 10.9648 89.6992 10.4102 89.2852 9.71484C88.8711 9.01953 88.6641 8.25 88.6641 7.40625C88.6641 6.55469 88.8711 5.78125 89.2852 5.08594C89.6992 4.39062 90.2539 3.83594 90.9492 3.42188C91.6523 3.00781 92.4258 2.80078 93.2695 2.80078H95.9883ZM95.9883 7.39453V4.67578H93.2695C92.5195 4.67578 91.875 4.94141 91.3359 5.47266C90.8047 6.00391 90.5391 6.64844 90.5391 7.40625C90.5391 8.15625 90.8047 8.79688 91.3359 9.32812C91.8672 9.85938 92.5078 10.125 93.2578 10.125C94.0156 10.125 94.6602 9.85938 95.1914 9.32812C95.7227 8.78906 95.9883 8.14453 95.9883 7.39453Z" fill="#00BCD4" />
                </svg>
            </a>
        </div>
        </div>
    </nav>
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