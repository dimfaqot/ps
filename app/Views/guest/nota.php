<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $judul; ?></title>
    <style>
        td,
        th {
            font-size: 16px;
            padding: 2px;
        }
    </style>
</head>

<body style="font-size:16px;font-family:'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;padding:0px;">
    <div style="text-align: center;margin-bottom:20px">
        <div style="font-weight: bold;font-size:26px">SONGO PLAYGROUND</div>
        <div style="font-size:small;">Karangmalang Sragen Jawa Tengah</div>
        <div style="font-size: small;">0857-4461-6165</div>
    </div>
    <div style="padding: 0px; margin: 0;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 4px;">Nota</td>
                <td style="width: 2px;">:</td>
                <td><?= $no_nota; ?></td>
            </tr>

            <tr>
                <td style="width: 4px;">Tgl</td>
                <td style="width:2px">:</td>
                <td><?= date("d-m-Y H:i:s"); ?></td>
            </tr>
            <tr>
                <td style="width: 4px;">Kasir</td>
                <td style="width: 2px;">:</td>
                <td><?= $data[0]['petugas']; ?></td>
            </tr>
        </table>
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td colspan="4" style="padding-top: 10px;border-bottom:1px solid grey"></td>
            </tr>
            <tr>
                <th style="text-align: center;">Barang</th>
                <th style="text-align: center;">Harga</th>
                <th style="text-align: center;">Qty</th>
                <th style="text-align: center;">Total</th>
            </tr>
            <tr>
                <td colspan="4" style="border-top:1px solid grey"></td>
            </tr>
            <?php $total = 0;
            $diskon = 0;
            $biaya = 0; ?>
            <?php foreach ($data as $i): ?>
                <?php
                $total += (int)$i['total'];
                $diskon += (int)$i['diskon'];
                $biaya += (int)$i['jml'];
                ?>
                <tr>
                    <td><?= $i['barang']; ?></td>
                    <td style="text-align: right;"><?= angka($i['harga']); ?></td>
                    <td style="text-align: center;"><?= angka($i['qty']); ?></td>
                    <td style="text-align: right;"><?= angka($i['total']); ?></td>
                </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="4" style="border-top:1px solid grey"></td>
            </tr>
            <tr>
                <td></td>
                <td>Sub Total</td>
                <td colspan="2" style="text-align: right;"><?= angka($total); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Diskon</td>
                <td colspan="2" style="text-align: right;"><?= angka($diskon); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Total</td>
                <td colspan="2" style="text-align: right;"><?= angka($biaya); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Uang</td>
                <td colspan="2" style="text-align: right;"><?= angka($data[0]['uang']); ?></td>
            </tr>
            <tr>
                <td></td>
                <td>Kembalian</td>
                <td colspan="2" style="text-align: right;"><?= angka($data[0]['uang'] - $total); ?></td>
            </tr>
            <tr>
                <td colspan="4" style="border-top:1px solid grey"></td>
            </tr>
        </table>

    </div>
    <div style="text-align: center;margin-top:20px">- Terima Kasih -</div>

</body>

</html>