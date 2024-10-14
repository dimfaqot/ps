<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add Meja
    </button>

    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($data as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-harga="<?= $i['harga']; ?>" data-meja="<?= $i['meja']; ?>" data-id="<?= $i['id']; ?>">Meja <?= $i['meja']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><a href="" data-alert="Are you sure to delete this data?" data-function="delete_meja" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="jadwal_2" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>




<!-- Modal add-->
<div class="modal fade" id="add_<?= menu()['controller']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <div class="d-flex justify-content-center body_select d-none">

                </div>
                <h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Add <?= menu()['menu']; ?></h6>
                <hr>
                <form action="<?= base_url(menu()['controller']); ?>/add" method="post">
                    <div class="mb-2">
                        <div class="text_main2">Meja</div>
                        <input class="input" type="number" name="meja" placeholder="Nama meja" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Harga per Jam</div>
                        <input class="input uang" type="text" name="harga" placeholder="Biaya per Jam" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn_primary"><i class="fa-solid fa-cloud"></i> Save</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>



<!-- Modal detail-->
<div class="modal fade" id="detail" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_detail">

            </div>

        </div>
    </div>
</div>



<script>
    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let meja = $(this).data('meja');
        let harga = $(this).data('harga');

        let html = '';
        html += '<div class="input-group input-group-sm">';
        html += '<input type="text" value="' + angka(harga) + '" class="uang form-control update_harga_' + id + '">';
        html += '<input type="text" value="' + meja + '" class="form-control update_meja_' + id + '">';
        html += '<button class="btn btn-outline-secondary update_jadwal" data-id="' + id + '" type="button"><i class="fa-solid fa-floppy-disk"></i> Save</button>';
        html += '</div>';

        $('.body_detail').html(html);

        let myModal = document.getElementById('detail');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();
    });

    $(document).on('click', '.update_jadwal', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let meja = $('.update_meja_' + id).val();
        let harga = $('.update_harga_' + id).val();
        post("<?= menu()['controller']; ?>" + '/update_jadwal', {
            id,
            meja,
            harga
        }).then(res => {
            if (res.status == '200') {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 800);
            } else {
                gagal(res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>