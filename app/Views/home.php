<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<?php if (session('role') == 'Member'): ?>
    <?php
    $dbh = db('hutang');
    $belanja = $dbh->where('user_id', session('id'))->orderBy('status', 'ASC')->orderBy('tgl', 'ASC')->get()->getResultArray();
    ?>
    <div class="container">
        <h6>ANDA BELANJA ANDA BERAMAL</h6>
        <p>Daftar belanja Anda:</p>
        <div class="form-check form-check-inline">
            <input class="form-check-input status" type="radio" value="Lunas" name="status">
            <label class="form-check-label">Lunas</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input status" type="radio" value="Hutang" name="status" checked>
            <label class="form-check-label">Hutang</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input status" type="radio" value="" name="status">
            <label class="form-check-label">All</label>
        </div>
        <table class="table table-striped table-sm mt-2">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tgl</th>
                    <th>Barang</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody class="isi_hutang">
                <?php $total = 0; ?>
                <?php $lunas = 0; ?>
                <?php foreach ($belanja as $k => $i): ?>
                    <?php
                    $total += $i['total_harga'];
                    if ($i['status'] == 1) {
                        $lunas += $i['total_harga'];
                    }
                    ?>

                    <tr style="<?= ($i['status'] == 1 ? 'display:none' : ''); ?>" data-sta="<?= $i['status']; ?>" data-status="<?= ($i['status'] == 1 ? 'Lunas' : 'Hutang'); ?>">
                        <td><?= $k + 1; ?></td>
                        <td><?= date('d/m/Y', $i['tgl']); ?></td>
                        <td><?= $i['barang']; ?></td>
                        <td style="text-align: right;"><?= angka($i['harga_satuan']); ?></td>
                        <td style="text-align: center;"><?= $i['qty']; ?></td>
                        <td style="text-align: right;"><?= angka($i['total_harga']); ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="5">TOTAL</th>
                    <th style="text-align: right;" class="body_lunas"><?= angka($total - $lunas); ?></th>
                </tr>
            </tbody>
        </table>
    </div>

    <script>
        let total = parseInt(<?= $total; ?>);
        let lunas = parseInt(<?= $lunas; ?>);
        $(document).on('change', '.status', function(e) {
            e.preventDefault();
            let value = $(this).val();
            if (value == 'Lunas') {
                $('.body_lunas').text(angka(lunas));
            }
            if (value == 'Hutang') {
                $('.body_lunas').text(angka(total - lunas));
            }
            if (value == '') {
                $('.body_lunas').text(angka(total));
            }
            console.log(value);
            $('.isi_hutang tr').filter(function() {
                console.log($(this).data('status'));
                $(this).toggle($(this).data('status').indexOf(value) > -1);
            });

        });
    </script>
<?php else: ?>

    <?= view('home_admin'); ?>
<?php endif; ?>
<?= $this->endSection() ?>