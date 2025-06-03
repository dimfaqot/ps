<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        <i class="fa-solid fa-circle-plus"></i> <?= menu()['menu']; ?>
    </button>


    <div class="d-flex gap-2 mb-3">
        <div class="input-group nput-group-sm">
            <label style="font-size: small;" class="input-group-text">Tahun</label>
            <select class="form-select filter tahun" data-order="tahun">
                <?php foreach (get_tahuns(menu()['tabel']) as $i) : ?>
                    <option <?= ($i == $tahun ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="input-group nput-group-sm">
            <label style="font-size: small;" class="input-group-text">Bulan</label>
            <select class="form-select filter bulan" data-order="bulan">
                <?php foreach (bulan() as $i) : ?>
                    <option <?= ($i['angka'] == $bulan ? 'selected' : ''); ?> value="<?= $i['angka']; ?>">[<?= $i['angka']; ?>] <?= $i['bulan']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
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
                    <th style="text-align: center;" scope="col">Tgl</th>
                    <th style="text-align: center;" scope="col">Barang</th>
                    <th style="text-align: center;" class="d-none d-md-table-cell">Qty</th>
                    <th style="text-align: center;" scope="col">Harga</th>
                    <th style="text-align: center;" scope="col">Nt</th>
                    <th style="text-align: center;" class="d-none d-md-table-cell">Pj</th>
                    <th style="text-align: center;" scope="col">Del</th>
                </tr>
            </thead>
            <tbody class="tabel_search">
                <?php $nt = count($data); ?>
                <?php foreach ($data as $k => $i) : ?>
                    <tr class="<?= ($i['is_inv'] == 1 ? 'bg_success_bright' : ''); ?>">
                        <td style="text-align: center;"><?= ($k + 1); ?></td>
                        <td style="text-align: center;"><?= date('d/m/y', $i['tgl']); ?></td>
                        <td class="detail" style="cursor: pointer;" data-id="<?= $i['id']; ?>"><?= $i['barang']; ?></td>
                        <td style="text-align: center;" class="d-none d-md-table-cell"><?= $i['qty']; ?></td>
                        <td style="text-align: right;"><?= rupiah($i['harga']); ?></td>
                        <td style="text-align: right;"><?= $nt--; ?></td>
                        <td class="d-none d-md-table-cell"><?= $i['pj']; ?></td>
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

                    <div class="form-check mb-2">
                        <input class="form-check-input" name="is_inv" type="checkbox" value="1">
                        <label class="form-check-label">
                            Inventaris?
                        </label>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Barang</div>
                        <input class="input" type="text" name="barang" placeholder="Barang" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Qty</div>
                        <input class="input" type="number" name="qty" placeholder="Jumlah barang" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Harga</div>
                        <input class="input uang" type="text" name="harga" placeholder="Total harga" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Pj</div>
                        <input class="input" type="text" name="pj" placeholder="Petugas yang beli" required>
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
    $(document).on('change', '.filter', function(e) {
        e.preventDefault();
        let order = $(this).data('order');

        let tahun, bulan;

        if (order == 'tahun') {
            tahun = $(this).val();
            bulan = $('.bulan').val();
        }
        if (order == 'bulan') {
            bulan = $(this).val();
            tahun = $('.tahun').val();
        }

        window.location.href = '<?= base_url(menu()['controller']); ?>/' + tahun + '/' + bulan;
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

        html += '<div class="form-check mb-2">';
        html += '<input class="form-check-input" name="is_inv" ' + (data.is_inv == 1 ? 'checked' : '') + ' type="checkbox" value="1">';
        html += '<label class="form-check-label">';
        html += 'Inventaris?';
        html += '</label>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Barang</div>';
        html += '<input class="input" type="text" name="barang" value="' + data.barang + '" placeholder="Barang" required>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Qty</div>';
        html += '<input class="input" type="number" name="qty" value="' + data.qty + '" placeholder="Qty" required>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Harga</div>';
        html += '<input class="input uang" type="text" name="harga" value="' + angka(data.harga) + '" placeholder="Total harga" required>';
        html += '</div>';

        html += '<div class="mb-2">';
        html += '<div class="text_main2">Pj</div>';
        html += '<input class="input" type="text" name="pj" value="' + data.pj + '" placeholder="Petugas yang beli" required>';
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