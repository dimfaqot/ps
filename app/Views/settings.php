<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add <?= menu()['menu']; ?>
    </button>
    <button type="button" class="btn_purple mb-3" data-bs-toggle="modal" data-bs-target="#make_user">
        Make User
    </button>

    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($data as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-id="<?= $i['id']; ?>"><?= $i['nama_setting']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><a href="" data-alert="Are you sure to delete this data?" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
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
                        <div class="text_main2">Nama Setting</div>
                        <input class="input" type="text" name="nama_setting" placeholder="Nama setting" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Value Int</div>
                        <input class="input uang" type="text" name="value_int" placeholder="Value int">
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Value Str</div>
                        <input class="input" type="text" name="value_str" placeholder="Value str">
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
<!-- Modal detail-->
<div class="modal fade" id="make_user" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_make_user">
                <div class="d-flex justify-content-center body_select d-none">

                </div>
                <div class="mb-2">
                    <div class="text_main2">Role</div>
                    <input class="input btn_select update_role" name="role" data-tabel="options" data-col="value" data-orderby="value=ASC" data-where="kategori=Role" data-target="update_role" type="text" value="Member" placeholder="Role" readonly>
                </div>
                <div class="d-grid">
                    <button style="border-radius: 8px;" class="btn_purple make_secret_user"><i class="fa-solid fa-user-secret"></i> Make Secret User</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).on('click', '.make_secret_user', function(e) {
        e.preventDefault();
        let role = $('.update_role').val();

        post('settings/make_user_jwt', {
            role
        }).then(res => {
            if (res.status == '200') {
                let html = '';
                html += '<textarea class="mb-3 form-control form-control-sm" rows="4"><?= base_url('ext/a/'); ?>' + res.data + '</textarea>';
                html += '<div class="d-grid"><button data-text="<?= base_url('ext/a/'); ?>' + res.data + '" class="btn_purple copy_text"><i class="fa-solid fa-copy"></i> Copy</button></div>';
                $('.body_make_user').html(html)
            } else {
                gagal(res.message);
            }
        })
    })
    $(document).on('click', '.copy_text', function(e) {
        e.preventDefault();
        let text = $(this).data('text');
        navigator.clipboard.writeText(text);
        sukses('Copied.');
    })

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
        html += '<div class="text_main2">Nama Setting</div>';
        html += '<input class="input" type="text" name="nama_setting" value="' + data.nama_setting + '" placeholder="Nama setting" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Value Int</div>';
        html += '<input class="input uang" type="text" name="value_int" value="' + angka(data.value_int) + '" placeholder="Value int">';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Value Str</div>';
        html += '<input class="input" type="text" name="value_str" value="' + data.value_str + '" placeholder="Value str">';
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