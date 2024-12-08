<?= $this->extend('guest') ?>

<?= $this->section('content') ?>

<h1><?= $judul; ?></h1>
<div class="bg-light d-flex justify-content-center py-5">
    <?php foreach ($data as $i): ?>
        <div class="card">
            <div class="card-body">
                <div class="form-check form-switch">
                    <input class="form-check-input saklar" type="checkbox" role="switch" <?= ($i['status'] == 1 ? 'checked' : ''); ?> data-id="<?= $i['id']; ?>" value="<?= $i['status']; ?>">
                    <label class="form-check-label"><?= $i['kategori']; ?> Meja <?= $i['meja']; ?></label>
                </div>
            </div>
        </div>

    <?php endforeach; ?>

</div>

<script>
    $(document).on('change', '.saklar', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        post('api/update_iot_rental', {
            id
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
            } else {
                gagal(res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>