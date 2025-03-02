<?php
$data = basil_bersih($bulan, $tahun);
// dd($data);
// dd($data['data']["Billiard"]['Masuk']);
?>
<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">

    <div class="d-flex gap-3 mb-3">
        <select class="form-select form-select-sm tahun change_data">
            <?php foreach (get_tahuns("billiard_2") as $i): ?>
                <option value="<?= $i; ?>" <?= ($tahun == $i ? 'selected' : ''); ?>><?= $i; ?></option>

            <?php endforeach; ?>
            <option value="All" <?= ($tahun == "All" ? 'selected' : ''); ?>>All</option>
        </select>
        <select class="form-select form-select-sm bulan change_data">
            <?php foreach (bulan() as $i): ?>
                <option value="<?= $i['angka']; ?>" <?= ($bulan == $i['angka'] ? 'selected' : ''); ?>>[<?= $i['angka']; ?>] <?= $i['bulan']; ?></option>
            <?php endforeach; ?>
            <option value="All" <?= ($bulan == "All" ? 'selected' : ''); ?>>All</option>
        </select>
    </div>

    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th class="text-center" scope="col">#</th>
                <th class="text-center" scope="col">Kategori</th>
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
            <?php foreach ($data['orders'] as $k => $i): ?>
                <?php $total_masuk += (int) $data['data'][$i]['Masuk']; ?>
                <tr>
                    <td class="text-center"><?= ($k + 1); ?></td>
                    <td><?= $i; ?></td>
                    <td class="text-end"><?= angka($data['data'][$i]['Masuk']); ?></td>
                    <?php foreach ($data['persen'] as $g): ?>
                        <?php if ($i == $g['kategori']): ?>
                            <?php $totals[$g['kepada']] += (int)$data['data'][$i][$g['kepada']]; ?>
                            <td class="text-end"><?= angka($data['data'][$i][$g['kepada']]); ?></td>

                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="2"> TOTAL</th>
                <th class="text-end"><?= angka($total_masuk); ?></th>
                <?php foreach ($totals as $i): ?>
                    <th class="text-end"><?= angka($i); ?></th>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>


    <form class="d-grid" action="<?= base_url('basil/save'); ?>" method="post">
        <input type="hidden" name="bulan" value="<?= $bulan; ?>">
        <input type="hidden" name="tahun" value="<?= $tahun; ?>">
        <button type="submit" class="btn btn-sm btn-primary"><i class="fa-solid fa-floppy-disk"></i> Simpan</button>
    </form>

</div>

<script>
    const change_data = () => {
        let tahun = $('.tahun').val();
        let bulan = $('.bulan').val();
        window.location.href = "<?= base_url("basil_bersih"); ?>/" + tahun + "/" + bulan;
    }

    $(document).on("change", ".change_data", function(e) {
        change_data();
    })
</script>
<?= $this->endSection() ?>