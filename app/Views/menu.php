<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add <?= menu()['menu']; ?>
    </button>

    <select class="form-select form-select-sm mb-2 roles">
        <?php foreach ($role as $i) : ?>
            <option <?= ($i['role'] == url(4) ? 'selected' : ''); ?> value="<?= $i['role']; ?>"><?= $i['role']; ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($data as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-id="<?= $i['id']; ?>"><?= $i['role']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><?= $i['menu']; ?> <a href="" data-alert="Are you sure to delete this data?" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
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
                        <div class="text_main2">Role</div>
                        <input class="input btn_select add_role" name="role" data-col="value" data-where="kategori=Role" data-tabel="options" data-orderby="value=ASC" data-target="add_role" type="text" value="Member" placeholder="Role" readonly>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Menu</div>
                        <input class="input" type="text" name="menu" placeholder="Menu" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Tabel</div>
                        <input class="input" type="text" name="tabel" placeholder="Tabel" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Controller</div>
                        <input class="input" type="text" name="controller" placeholder="Controller" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Icon</div>
                        <input class="input" type="text" name="icon" placeholder="Icon" required>
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
        let datas = <?= json_encode($data); ?>;
        let id = $(this).data('id');

        let data;
        datas.forEach(e => {
            if (e.id == id) {
                data = e;
            }
        });

        let html = '';

        html += '<div class="d-flex justify-content-center body_select d-none">';

        html += '</div>';
        html += '<h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Update <?= menu()['menu']; ?> ' + data.menu + '</h6>';
        html += '<hr>';
        html += '<form action="<?= base_url(menu()['controller']); ?>/update" method="post">';
        html += '<input type="hidden" name="id" value="' + data.id + '">';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Role</div>';
        html += '<input class="input btn_select update_role_' + id + '" name="role" data-tabel="options" data-col="value" data-orderby="value=ASC" data-where="kategori=Role" data-target="update_role_' + id + '" type="text" value="' + data.role + '" placeholder="Role" readonly>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Menu</div>';
        html += '<input class="input" type="text" name="menu" value="' + data.menu + '" placeholder="Menu" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Tabel</div>';
        html += '<input class="input" type="text" name="tabel" value="' + data.tabel + '" placeholder="Tabel" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Controller</div>';
        html += '<input class="input" type="text" name="controller" value="' + data.controller + '" placeholder="Controller" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Icon</div>';
        html += '<input class="input" type="text" name="icon" value="' + data.icon + '" placeholder="Icon" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Urutan</div>';
        html += '<input class="input" type="text" name="urutan" value="' + data.urutan + '" placeholder="Urutan" required>';
        html += '</div>';


        html += '<div class="d-grid">';
        html += '<button type="submit" class="btn_primary"><i class="fa-solid fa-cloud"></i> Update</button>';
        html += '</div>';

        html += '</form>';

        $('.body_detail').html(html);

        let myModal = document.getElementById('detail');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    });

    $(document).on('change', '.roles', function() {
        location.href = "<?= base_url(menu()['controller']); ?>/" + $(this).val();
    })
</script>
<?= $this->endSection() ?>