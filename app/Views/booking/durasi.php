    <div class="rounded target_durasi con d-none px-4 pt-4 mt-4" style="border: 1px solid #242b32;">
        <h6 class="text-center text-light mb-4">DURASI (JAM)</h6>
        <?php for ($i = 1; $i < 10; $i++) : ?>
            <?php if ($i == 1 || $i % 3 == 1): ?>
                <div class="d-flex justify-content-center gap-5 mb-5">
                <?php endif; ?>
                <div class="rounded-circle embos text-center p-2 fw-bold durasi" data-durasi="<?= $i; ?>" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32"><?= $i; ?></div>
                <?php if ($i > 0 && $i % 3 == 0): ?>
                </div>
            <?php endif; ?>
        <?php endfor; ?>
        <div class="sticky-bottom d-grid div_btn_ok">

        </div>
    </div>