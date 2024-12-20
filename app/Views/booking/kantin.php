<div class="target_menu d-none mt-5" data-target_menu="Kantin">
    <?php for ($i = 0; $i < 4; $i++): ?>
        <?php if ($i % 4 == 0): ?>
            <div class="d-flex justify-content-center gap-2 my-2">
            <?php endif; ?>
            <div data-meja="<?= ($i + 1); ?>" class="rounded-circle embos2 text-center fw-bold meja" data-meja="<?= ($i + 1); ?>" style="cursor:pointer;padding:13px 5px 5px 6px;font-size:35px;width: 85px;height:85px;color:#7c6f3e;border:1px solid #fce882">
                <div class="text-center" style="font-size:9px;margin-bottom:-9px">MEJA</div><?= ($i + 1); ?>
            </div>
            <?php if ($i % 4 == 3): ?>
            </div>
        <?php endif; ?>
    <?php endfor; ?>
</div>