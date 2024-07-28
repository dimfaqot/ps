<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add <?= menu()['menu']; ?>
    </button>

    <?php if (count($meja) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($meja as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-id="<?= $i['meja']; ?>"><?= $i['meja']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><a href="" data-alert="Are you sure to delete this data?" data-function="delete_meja" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
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
                        <input class="input" type="text" name="meja" placeholder="Nama meja" required>
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body body_detail">

            </div>

        </div>
    </div>
</div>



<script>
    let html_detail = (id, data) => {
        let hari = <?= json_encode(hari()); ?>;
        let html = '';
        html += '<h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Update <?= menu()['menu']; ?> ' + id + '</h6>';
        html += '<hr>';
        html += '<table class="table table-bordered table-sm">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align: center;width:80px;" scope="col">JAM | HARI</th>';
        hari.forEach(h => {
            html += '<th style="text-align: center;" scope="col">' + h.singkatan.toUpperCase() + '</th>';

        })
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        for (i = 1; i < 25; i++) {
            let jam = (i.length <= 1 ? '0' + i : i);
            html += '<tr>';
            html += '<th style="text-align: center;" scope="row">' + i + ".00" + '</th>';
            data.forEach(e => {
                if (i == e.jam) {
                    hari.forEach(h => {
                        if (e.hari == h.indo) {
                            html += '<td contenteditable="true" class="update_jadwal" data-col="pemesan" data-meja="' + e.meja + '" data-id="' + e.id + '">' + e.pemesan + '</td>';

                        }

                    })

                }

            })
            html += '</tr>';

        }

        html += '</tbody>';
        html += '</table>';


        $('.body_detail').html(html);

        let myModal = document.getElementById('detail');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    }
    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');

        let datas = <?= json_encode($data); ?>;
        let data = [];
        datas.forEach(e => {
            if (e.meja == id) {
                data.push(e);
            }
        });
        html_detail(id, data);
    });

    $(document).on('blur', '.update_jadwal', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let meja = $(this).data('meja');
        let col = $(this).data('col');
        let val = $(this).text();

        post("<?= menu()['controller']; ?>" + '/update_jadwal', {
            id,
            col,
            meja,
            val
        }).then(res => {
            if (res.status == '200') {
                sukses(res.message);
                html_detail(meja, res.data);
            } else {
                gagal(res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>