<?php $divisi = ['barber', 'billiard', 'kantin', 'ps']; ?>
<?php helper('qr_code'); ?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul; ?></title>

    <style>
        table,
        td,
        th {
            border: 0px solid #033d62;
            padding: 0px;
            font-size: 12px;
            font-family: Arial, Helvetica, sans-serif;
        }

        td {
            padding: 0px;
        }

        table {
            border-collapse: separate;
            border-spacing: 0px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        div {
            font-size: 14px;
            font-family: Arial, Helvetica, sans-serif;
        }
    </style>

</head>

<body>
    <div style="text-align: center;"><?= $logo; ?></div>
    <h3 style="text-align: center;"><?= $judul; ?></h3>

    <h4>A. RANGKUMAN</h4>
    <h5>SALDO BULAN LALU: <?= angka($data['saldo_kemarin']); ?></h5>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Divisi</th>
            <th style="border: 1px solid grey;padding:2px">Masuk</th>
            <th style="border: 1px solid grey;padding:2px">Keluar</th>
            <th style="border: 1px solid grey;padding:2px">Saldo</th>

        </tr>
        <?php $total_masuk = 0;
        $total_keluar = 0 ?>
        <?php foreach ($divisi as $k => $i): ?>
            <?php $masuk = ($data['rangkuman'][$i]['masuk'] == null ? 0 : $data['rangkuman'][$i]['masuk']); ?>
            <?php $keluar = ($data['rangkuman'][$i]['keluar'] == null ? 0 : $data['rangkuman'][$i]['keluar']); ?>
            <?php $total_masuk += $masuk; ?>
            <?php $total_keluar += $keluar; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px"><?= upper_first($i); ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($masuk); ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($keluar); ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($masuk - $keluar); ?></td>
            </tr>

        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="2">TOTAL</th>
            <th style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total_masuk); ?></th>
            <th style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total_keluar); ?></th>
            <th style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total_masuk - $total_keluar); ?></th>
        </tr>
    </table>

    <h4>B. BARBER</h4>
    <h6 style="font-weight: normal;">1. Masuk</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['barber']['masuk'] as $k => $i): ?>
            <?php $total += $i['total_harga']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['layanan']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['total_harga']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>
    <h6 style="font-weight: normal;">2. Keluar</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['barber']['keluar'] as $k => $i): ?>
            <?php $total += $i['harga']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['layanan']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>

    <h4>C. BILLIARD</h4>
    <h6 style="font-weight: normal;">1. Masuk</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['billiard']['masuk'] as $k => $i): ?>
            <?php $total += $i['biaya']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['meja']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['biaya']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>
    <h6 style="font-weight: normal;">2. Keluar</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['billiard']['keluar'] as $k => $i): ?>
            <?php $total += $i['harga']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['barang']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>

    <h4>D. KANTIN</h4>
    <h6 style="font-weight: normal;">1. Masuk</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['kantin']['masuk'] as $k => $i): ?>
            <?php $total += $i['total_harga']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['barang']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['total_harga']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>
    <h6 style="font-weight: normal;">2. Keluar</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['kantin']['keluar'] as $k => $i): ?>
            <?php $total += $i['harga']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['barang']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>
    <h4>E. PS</h4>
    <h6 style="font-weight: normal;">1. Masuk</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['ps']['masuk'] as $k => $i): ?>
            <?php $total += ($i['biaya'] - $i['diskon']); ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['meja']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka(($i['biaya'] - $i['diskon'])); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>
    <h6 style="font-weight: normal;">2. Keluar</h6>
    <table style="margin-top: 10px;width:100%;">
        <tr>
            <th style="border: 1px solid grey;padding:2px">No.</th>
            <th style="border: 1px solid grey;padding:2px">Tgl</th>
            <th style="border: 1px solid grey;padding:2px">Barang</th>
            <th style="border: 1px solid grey;padding:2px">Harga</th>

        </tr>
        <?php $total = 0; ?>
        <?php foreach ($data['data']['ps']['keluar'] as $k => $i): ?>
            <?php $total += $i['harga']; ?>
            <tr>
                <td style="text-align:center;border: 1px solid grey;padding:4px"><?= ($k + 1); ?></td>
                <td style="border: 1px solid grey;padding:4px;text-align:center"><?= date('d/m/Y', $i['tgl']); ?></td>
                <td style="text-align: left;border: 1px solid grey;padding:4px"><?= $i['barang']; ?></td>
                <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($i['harga']); ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <th style="text-align:center;border: 1px solid grey;padding:4px" colspan="3">TOTAL</th>
            <td style="text-align: right;border: 1px solid grey;padding:4px"><?= angka($total); ?></td>
        </tr>

    </table>
    <br>
    <br>
    <div style="text-align: center;">Sragen, 1 <?= bulan($bulan)['bulan']; ?> <?= $tahun; ?></div>
    <div style="text-align: center;">Penanggung Jawab</div>
    <br>
    <br>
    <br>
    <div style="text-align: center;">Dimyati</div>




</body>

</html>