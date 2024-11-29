<?= $this->extend('guest') ?>

<?= $this->section('content') ?>

<h1><?= $judul; ?></h1>
<div class="bg-light d-flex justify-content-center py-5">
    <div class="card">
        <div class="card-body">
            <h6 class="mb-2">SAKLAR LAMPU BILLIARD</h6>
            <div class="d-flex justify-content-between">
                <div class="form-check form-switch body_saklar">
                    <input class="form-check-input saklar" name="saklar" type="checkbox" role="switch" <?= ($data == 'ON' ? 'checked' : ''); ?> value="<?= $data; ?>">
                </div>
                <div>
                    <i class="fa-solid fa-circle-dot <?= ($data == 'OFF' ? 'text-danger' : 'text-success'); ?> lampu"></i>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).on('change', '.saklar', function(e) {
        e.preventDefault();
        let val = $(this).val();



        post('api/update_by_js', {
            val: (val == 'ON' ? 'OFF' : 'ON')
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                if (val == "ON") {
                    $(this).val("OFF");
                    $('.lampu').removeClass('text-success');
                    $('.lampu').addClass('text-danger');
                }
                if (val == "OFF") {
                    $(this).val("ON");
                    $('.lampu').removeClass('text-danger');
                    $('.lampu').addClass('text-success');
                }
            } else {
                gagal(res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>