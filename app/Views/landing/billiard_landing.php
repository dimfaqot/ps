<div class="container">

    <?php if (count($meja) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <h6 class="bg_warning_light p-2 text-center"><a href=""><i class="fa-solid fa-circle-left"></i></a> <?= hari(date('l'))['indo'] . ', ' . date('d') . ' ' . bulan(date('m'))['bulan'] . ' ' . date('Y'); ?> <a href=""><i class="fa-solid fa-circle-right"></i></a></h6>
        <div class="row g-2">
            <?php foreach ($meja as $m) : ?>
                <div class="col-md-6">
                    <h6 class="judul"><?= $m['meja']; ?></h6>
                    <?php for ($i = 1; $i < 25; $i++) : ?>
                        <?php if ($i == 1) : ?>
                            <div class="div_card mb-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;" scope="col">Jam</th>
                                            <th style="text-align: center;" scope="col">Pemesan</th>
                                            <th style="text-align: center;" scope="col">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php for ($y = 1; $y < 6; $y++) : ?>
                                            <?php $jam = (strlen($y) <= 1 ? '0' . $y : $y); ?>
                                            <tr>
                                                <th style="text-align: center;" scope="row"><?= $jam . ".00"; ?></th>
                                                <?php foreach ($data as $d) : ?>
                                                    <?php if ($d['hari'] == hari(date('l'))['indo'] && $y == $d['jam'] && $d['meja'] == $m['meja']) : ?>
                                                        <td><?= $d['pemesan']; ?></td>
                                                        <td style="text-align: center;"><input data-biaya="<?= get_harga_billiard(); ?>" data-id="<?= $d['id']; ?>" class="form-check-input biaya" type="checkbox" value="" <?= $d['pemesan'] == '' || billiard_paid($d['id']) ? 'disabled' : ''; ?>> <?= rupiah(get_harga_billiard()); ?></td>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </tr>
                                        <?php endfor; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                        <?php if ($i % 6 == 0) : ?>

                            <div class="div_card mb-2">
                                <table class="table table-sm table-bordered">
                                    <thead>
                                        <tr>
                                            <th style="text-align: center;" scope="col">Jam</th>
                                            <th style="text-align: center;" scope="col">Pemesan</th>
                                            <th style="text-align: center;" scope="col">Biaya</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                        <?php for ($x = $i; $x < ($i + 6); $x++) : ?>
                                            <?php if ($x < 25) : ?>
                                                <?php $jam = (strlen($x) <= 1 ? '0' . $x : $x); ?>

                                                <tr>
                                                    <th style="text-align: center;" scope="row"><?= $jam . ".00"; ?></th>
                                                    <?php foreach ($data as $d) : ?>

                                                        <?php if ($d['hari'] == hari(date('l'))['indo'] && $x == $d['jam'] && $d['meja'] == $m['meja']) : ?>
                                                            <td><?= $d['pemesan']; ?></td>
                                                            <td style="text-align: center;"><input data-biaya="<?= get_harga_billiard(); ?>" data-id="<?= $d['id']; ?>" class="form-check-input biaya" type="checkbox" value="" <?= $d['pemesan'] == '' || billiard_paid($d['id']) ? 'disabled' : ''; ?>> <?= rupiah(get_harga_billiard()); ?></td>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endif; ?>
                                        <?php endfor; ?>

                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    <?php endfor; ?>

                </div>
            <?php endforeach; ?>

        </div>
    <?php endif; ?>
</div>