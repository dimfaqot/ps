    <div class="rounded target_topup d-none px-4 pt-4 mt-5">
        <h6 class="text-center text-light mb-4">TOPUP</h6>
        <?php for ($i = 1; $i < 10; $i++) : ?>
            <?php $angka = ($i == 7 ? 10 : ($i == 8 ? 20 : ($i == 9 ? 30 : $i))); ?>
            <?php if ($i == 1 || $i % 3 == 1): ?>
                <div class="d-flex justify-content-center gap-5 mb-5">
                <?php endif; ?>
                <div class="rounded-circle embos text-center p-2 fw-bold durasi" data-durasi="<?= $angka; ?>" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32"><?= $angka; ?></div>
                <?php if ($i > 0 && $i % 3 == 0): ?>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
        <div class="sticky-bottom d-grid div_btn_ok">

        </div>

    </div>