<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add User
    </button>

    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($data as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-id="<?= $i['id']; ?>"><?= $i['nama']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><?= $i['role']; ?> <a href="" data-alert="Are you sure to delete this data?" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
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
                <h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Add User</h6>
                <hr>
                <form action="<?= base_url(menu()['controller']); ?>/add" method="post">
                    <div class="mb-2">
                        <div class="text_main2">Nama</div>
                        <input class="input" type="text" name="nama" placeholder="Nama" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Username</div>
                        <input class="input check_is_exist" data-order="add" data-target="add_username" data-tabel="users" data-col="username" type="text" name="username" placeholder="Username" required>
                        <div class="body_check_is_exist_add_username"></div>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Whatsapp</div>
                        <input class="input" type="text" name="hp" placeholder="Whatsapp" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Role</div>
                        <input class="input btn_select add_role" name="role" data-tabel="options" data-where="kategori=Role" data-col="value" data-orderby="value=ASC" data-target="add_role" type="text" value="Member" placeholder="Role" readonly>
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
        html += '<h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Update User ' + data.nama + '</h6>';
        html += '<hr>';
        html += '<form action="<?= base_url(menu()['controller']); ?>/update" method="post">';
        html += '<input type="hidden" name="id" value="' + data.id + '">';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Nama</div>';
        html += '<input class="input" type="text" name="nama" value="' + data.nama + '" placeholder="Nama" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Username</div>';
        html += '<input class="input check_is_exist" value="' + data.username + '" data-order="update" data-target="update_username_' + data.id + '" data-id="' + data.id + '" data-tabel="users" data-col="username" type="text" name="username" placeholder="Username" required>';
        html += '<div class="body_check_is_exist_update_username_' + data.id + '"></div>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Whatsapp</div>';
        html += '<input class="input" type="text" value="' + data.hp + '" name="hp" placeholder="Whatsapp" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Password</div>';
        html += '<input class="input" type="text" value="" name="password" placeholder="Password">';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Role</div>';
        html += '<input class="input btn_select update_role_' + id + '" name="role" data-tabel="options" data-col="value" data-orderby="value=ASC" data-where="kategori=Role" data-target="update_role_' + id + '" type="text" value="' + data.role + '" placeholder="Role" readonly>';
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
</script>
<?= $this->endSection() ?>