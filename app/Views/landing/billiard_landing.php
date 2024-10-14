<div class="container">

    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <div class="row g-2">
            <?php foreach ($data as $i) : ?>
                <div class="col-md-4">
                    <div class="rounded py-3 <?= ($i['is_active'] == 0 ? 'div_card_success' : 'div_card_failed'); ?>">
                        <div class="d-flex justify-content-between">
                            <h6><i class="fa-solid fa-bowling-ball"></i> Meja <?= $i['meja']; ?></h6>
                            <div class="<?= $i['is_active'] == 0 ? 'btn_success' : 'btn_danger'; ?>"><?= $i['status']; ?></div>
                        </div>
                        <div class="text-center">
                            <div><?= $i['paket']; ?></div>
                            <h5><?= $i['durasi']; ?></h5>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>


        </div>
    <?php endif; ?>
</div>

<script>

</script>