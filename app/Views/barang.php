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
                    <th style="text-align: center;" scope="col">Barang</th>
                    <th style="text-align: center;" scope="col">Stok</th>
                    <th style="text-align: center;" scope="col">Harga</th>
                    <th style="text-align: center;" scope="col">Del</th>
                </tr>
            </thead>
            <tbody class="tabel_search">
                <?php foreach ($data as $k => $i) : ?>
                    <tr>
                        <td style="text-align: center;"><?= ($k + 1); ?></td>
                        <td class="detail" style="cursor: pointer;" data-id="<?= $i['id']; ?>"><?= $i['barang']; ?></td>
                        <td style="text-align: center;"><?= $i['stok']; ?></td>
                        <td style="text-align: right;"><?= rupiah($i['harga_satuan']); ?></td>
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
                        <div class="text_main2">Jenis <a class="cari_barang" href="">Cari Barang</a></div>
                        <select name="jenis" class="form-select form-select-sm">
                            <?php foreach (options('Jenis Menu') as $i): ?>
                                <option <?= ($i['value'] == 'Makanan' ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Barang</div>
                        <input class="input" type="text" name="barang" placeholder="Barang" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Stok</div>
                        <input class="input" type="number" name="stok" placeholder="Stok barang" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Harga</div>
                        <input class="input uang" type="text" name="harga_satuan" placeholder="Harga satuan" required>
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

<div class="offcanvas offcanvas-start" data-bs-backdrop="static" tabindex="-1" aria-labelledby="staticBackdropLabel" style="z-index:9999999;max-width:400px;" id="cari_barang">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasScrollingLabel">Cari Barang</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="input-group input-group-sm mb-2">
            <span class="input-group-text">Cari Barang</span>
            <input type="text" class="form-control input_cari_barang" placeholder="Ketik sesuatu...">

        </div>
        <table class="table table-sm table-bordered table-striped">
            <thead>
                <tr>
                    <th style="text-align: center;" scope="col">#</th>
                    <th style="text-align: center;" scope="col">Barang</th>
                    <th style="text-align: center;" scope="col">Qty</th>
                    <th style="text-align: center;" scope="col">Harga</th>
                </tr>
            </thead>
            <tbody class="tabel_cari_barang">

            </tbody>
        </table>
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
    $(document).on('keyup', '.input_cari_barang', function(e) {
        e.preventDefault();
        let value = $(this).val();
        post('barang/cari_barang', {
            value
        }).then(res => {
            if (res.status == '200') {
                let html = '';
                res.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td style="text-align: center;">' + (i + 1) + '</td>';
                    html += '<td>' + e.barang + '</td>';
                    html += '<td style="text-align: center;">' + e.qty + '</td>';
                    html += '<td style="text-align: right;">' + angka(e.harga) + '</td>';
                    html += '</tr>';

                })

                $('.tabel_cari_barang').html(html);
            } else {
                gagal(res.message);
            }
        })

    });
    $(document).on('click', '.cari_barang', function(e) {
        e.preventDefault();
        const bsOffcanvas = new bootstrap.Offcanvas('#cari_barang');
        bsOffcanvas.show();

    });

    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let jenis_menu = <?= json_encode(options('Jenis Menu')); ?>;

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
        html += '<div class="text_main2">Jenis</div>';
        html += '<select name="jenis" class="form-select form-select-sm">';

        for (let idx = 0; idx < jenis_menu.length; idx++) {
            html += '<option ' + (jenis_menu[idx].value == data.jenis ? 'selected' : '') + ' value = "' + jenis_menu[idx].value + '">' + jenis_menu[idx].value + '</option>';

        }

        html += '</select>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Barang</div>';
        html += '<input class="input" type="text" name="barang" value="' + data.barang + '" placeholder="Barang" required>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Stok</div>';
        html += '<input class="input" type="number" name="stok" value="' + data.stok + '" placeholder="Stok" required>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Harga</div>';
        html += '<input class="input uang" type="text" name="harga_satuan" value="' + angka(data.harga_satuan) + '" placeholder="Total harga" required>';
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