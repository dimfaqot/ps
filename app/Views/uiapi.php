<?= $this->extend('guest') ?>

<?= $this->section('content') ?>

<h1><?= $judul; ?></h1>
<div class="bg-light d-flex justify-content-center py-5">

    <div class="card">
        <div class="card-body">
            <div class="form-check form-switch">
                <input class="form-check-input saklar" type="checkbox" role="switch" <?= ($data['status'] == 1 ? 'checked' : ''); ?> data-id="<?= $data['id']; ?>" value="<?= $data['status']; ?>">
                <label class="form-check-label"><?= $data['kategori']; ?> Meja <?= $data['meja']; ?></label>
            </div>
        </div>
    </div>



</div>

<script>
    $(document).on('change', '.saklar', function(e) {
        e.preventDefault();

        post('api/tes_update_iot_rental', {
            id: 1
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