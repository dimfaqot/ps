<?php
$data = basil_kotor($tahun, $bulan);
// dd(url(6));
?>
<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <div class="d-flex gap-3 mb-3">
        <select class="form-select form-select-sm tahun change_data">
            <?php foreach (get_tahuns("billiard_2") as $i): ?>
                <option value="<?= $i; ?>" <?= ($tahun == $i ? 'selected' : ''); ?>><?= $i; ?></option>

            <?php endforeach; ?>
        </select>
        <select class="form-select form-select-sm bulan change_data">
            <?php foreach (bulan() as $i): ?>
                <option value="<?= $i['angka']; ?>" <?= ($bulan == $i['angka'] ? 'selected' : ''); ?>>[<?= $i['angka']; ?>] <?= $i['bulan']; ?></option>
            <?php endforeach; ?>
        </select>
        <div class="form-check form-switch pt-1">
            <input class="form-check-input show_hide change_data" type="checkbox" <?= (url(6) == "hide" || url(6) == "" ? "" : "checked"); ?>>
            <label class="form-check-label">Detail</label>
        </div>
    </div>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th class="text-center" scope="col">#</th>
                <th class="text-center" scope="col">Kategori</th>
                <?php if ($order == "show"): ?>
                    <th class="text-center" scope="col">Tgl</th>
                    <th class="text-center" scope="col">Barang</th>
                <?php endif; ?>
                <th class="text-center" scope="col">Masuk</th>
                <?php foreach ($data['grup'] as $i): ?>
                    <th class="text-center" scope="col"><?= $i['kepada']; ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_masuk = 0;
            $totals = [];
            foreach ($data['grup'] as $g) {
                $totals[$g['kepada']] = 0;
            }
            ?>
            <?php foreach ($data['orders'] as $x): ?>
                <?php
                $sub_total_masuk = 0;
                $total = [];
                foreach ($data['grup'] as $g) {
                    $total[$g['kepada']] = 0;
                }
                ?>
                <?php foreach ($data['data'][$x] as $k => $i): ?>
                    <?php $sub_total_masuk += (int)$i['masuk']; ?>
                    <?php $total_masuk += (int) $i["masuk"]; ?>
                    <tr>
                        <?php if ($order == "show"): ?>
                            <th class="text-center" scope="row"><?= ($k + 1); ?></th>
                            <td><?= $i['kategori']; ?></td>
                            <td class="text-center"><?= $i['tgl']; ?></td>
                            <td><?= $i['barang']; ?></td>
                            <td class="text-end"><?= angka($i['masuk']); ?></td>

                        <?php endif; ?>
                        <?php foreach ($data['grup'] as $d): ?>
                            <?php $total[$d['kepada']] += $i[$d['kepada']]; ?>
                            <?php $totals[$d['kepada']] += $i[$d['kepada']]; ?>
                            <?php if ($order == "show"): ?>
                                <td class="text-end"><?= angka($i[$d['kepada']]); ?></th>
                                <?php endif; ?>
                            <?php endforeach; ?>
                    </tr>

                <?php endforeach; ?>
                <?php if ($order == "show"): ?>
                    <tr>
                        <th colspan="4" class="text-center">SUB TOTAL</th>
                        <th class="text-end"><?= angka($sub_total_masuk); ?></th>
                        <?php foreach ($total as $t): ?>
                            <th class="text-end"><?= angka($t); ?></th>
                        <?php endforeach; ?>
                    </tr>
                <?php else: ?>
                    <tr>
                        <td class="text-center">-</td>
                        <td><?= $x; ?></td>
                        <th class="text-end"><?= angka($sub_total_masuk); ?></th>
                        <?php foreach ($total as $t): ?>
                            <th class="text-end"><?= angka($t); ?></th>
                        <?php endforeach; ?>
                    </tr>
                <?php endif; ?>

            <?php endforeach; ?>

            <?php if ($order == "show"): ?>
                <tr>
                    <th colspan="4" class="text-center">TOTAL</th>
                    <th class="text-end"><?= angka($total_masuk); ?></th>
                    <?php foreach ($totals as $t): ?>
                        <th class="text-end"><?= angka($t); ?></th>
                    <?php endforeach; ?>
                </tr>

            <?php else: ?>
                <tr>
                    <th colspan="2" class="text-center">TOTAL</th>
                    <th class="text-end"><?= angka($total_masuk); ?></th>
                    <?php foreach ($totals as $t): ?>
                        <th class="text-end"><?= angka($t); ?></th>
                    <?php endforeach; ?>
                </tr>

            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    const change_data = () => {
        let tahun = $('.tahun').val();
        let bulan = $('.bulan').val();
        let show_hide = ($('.show_hide').is(':checked') ? "show" : "hide");
        window.location.href = "<?= base_url("basil_kotor"); ?>/" + tahun + "/" + bulan + "/" + show_hide;
    }

    $(document).on("change", ".change_data", function(e) {
        change_data();
    })
</script>
<?= $this->endSection() ?>