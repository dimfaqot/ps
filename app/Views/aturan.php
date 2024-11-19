<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        <i class="fa-solid fa-circle-plus"></i> <?= menu()['menu']; ?>
    </button>


    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text">Search</span>
            <input type="text" class="form-control cari" placeholder="Ketik sesuatu...">
        </div>
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th style="text-align: center;" scope="col">#</th>
                    <th style="text-align: center;" scope="col">Aturan</th>
                    <th style="text-align: center;" scope="col">Poin</th>
                    <th style="text-align: center;" scope="col">Del</th>
                </tr>
            </thead>
            <tbody class="tabel_search">
                <?php foreach ($data as $k => $i) : ?>
                    <tr>
                        <td style="text-align: center;"><?= ($k + 1); ?></td>
                        <td class="detail" style="cursor: pointer;" data-id="<?= $i['id']; ?>"><?= $i['aturan']; ?></td>
                        <td style="text-align: right;"><?= $i['poin']; ?></td>
                        <td style="text-align: center;"><a href="" data-alert="Are you sure to delete this data?" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm"><i class="fa-solid fa-circle-xmark"></i></a></td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>


<!-- Modal add-->
<div class="modal fade" id="add_<?= menu()['controller']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Add <?= menu()['menu']; ?></h6>
                <hr>
                <form action="<?= base_url(menu()['controller']); ?>/add" method="post">
                    <div class="mb-2">
                        <div class="text_main2">Aturan</div>
                        <input class="input" type="text" name="aturan" placeholder="Aturan" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Poin</div>
                        <input class="input" type="number" name="poin" placeholder="Poin barang" required>
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
    $(document).on('keyup', '.cari', function(e) {
        e.preventDefault();
        let value = $(this).val().toLowerCase();
        $('.tabel_search tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });

    });
    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        let datas = <?= json_encode($data); ?>;

        let data;
        datas.forEach(e => {
            if (e.id == id) {
                data = e;
            }
        });
        let html = '';
        html += '<h6 class="text_main2 fw-bold"> <i class="<?= menu()['icon']; ?>"></i> Update ' + data.barang + '</h6>';
        html += '<hr>';
        html += '<form action="<?= base_url(menu()['controller']); ?>/update" method="post">';
        html += '<input type="hidden" name="id" value="' + data.id + '">';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Aturan</div>';
        html += '<input class="input" type="text" name="aturan" value="' + data.aturan + '" placeholder="Aturan" required>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Poin</div>';
        html += '<input class="input" type="number" name="poin" value="' + data.poin + '" placeholder="Poin" required>';
        html += '</div>';

        html += '<div class="d-grid">';
        html += '<button type="submit" class="btn_primary"><i class="fa-solid fa-cloud"></i> Update</button>';
        html += '</div>';

        html += '</form>';

        $('.body_detail').html(html);

        let myModal = document.getElementById('detail');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();
    })
</script>
<?= $this->endSection() ?>