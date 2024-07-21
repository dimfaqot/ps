<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add <?= menu()['menu']; ?>
    </button>

    <select class="form-select form-select-sm mb-2 roles">
        <?php foreach ($kondisi as $i) : ?>
            <option <?= ($i['value'] == url(4) ? 'selected' : ''); ?> value="<?= $i['value']; ?>"><?= $i['value']; ?></option>
        <?php endforeach; ?>
    </select>
    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($data as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-id="<?= $i['id']; ?>"><?= $i['barang']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><?= $i['id']; ?> <a href="" data-alert="Are you sure to delete this data?" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
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
                        <div class="text_main2">Tgl</div>
                        <input type="date" class="input" name="tgl" data-date-format="DD/MM/YYYY" value="<?= date('Y-m-d'); ?>">
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Barang</div>
                        <input class="input" type="text" name="barang" placeholder="Barang" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Harga</div>
                        <input class="input uang" type="text" name="harga" placeholder="Harga" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Lokasi Barang</div>
                        <input class="input" type="text" name="lokasi" placeholder="Letak barang ditempatkan" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Kondisi</div>
                        <input class="input btn_select add_kondisi" name="kondisi" data-col="value" data-tabel="options" data-orderby="value=ASC" data-where="kategori=Kondisi" data-target="add_kondisi" type="text" value="Baik" placeholder="Kondisi" readonly>
                    </div>

                    <div class="mb-2">
                        <div class="text_main2">Pembeli</div>
                        <input class="input" type="text" name="pembeli" placeholder="Nama orang yang membeli barang" required>
                    </div>

                    <div class="mb-2">
                        <div class="text_main2">Keterangan</div>
                        <textarea class="input" name="ket" placeholder="Keterangan barang" rows="3"></textarea>
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
        html += '<h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Update <?= menu()['menu']; ?> ' + data.barang + '</h6>';
        html += '<hr>';
        html += '<form action="<?= base_url(menu()['controller']); ?>/update" method="post">';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">No. Inv</div>';
        html += '<input class="input" type="text" name="id" value="' + data.id + '" placeholder="No. Inv" readonly>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Tgl</div>';
        html += '<input type="date" class="input" name="tgl" data-date-format="DD/MM/YYYY" value="' + data.tgl_str + '">';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Barang</div>';
        html += '<input class="input" type="text" name="barang" value="' + data.barang + '" placeholder="Barang" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Harga</div>';
        html += '<input class="input uang" type="text" name="harga" value="' + angka(data.harga) + '" placeholder="Harga" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Lokasi Barang</div>';
        html += '<input class="input" type="text" name="lokasi" value="' + data.lokasi + '" placeholder="Letak barang ditempatkan" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Kondisi</div>';
        html += '<input class="input btn_select add_kondisi" data-tabel="options" data-col="value" data-orderby="value=ASC" data-where="kategori=Kondisi" data-target="add_kondisi" type="text" name="kondisi" value="' + data.kondisi + '" placeholder="Kondisi" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Pembeli</div>';
        html += '<input class="input" type="text" name="pembeli" value="' + data.pembeli + '" placeholder="Nama orang yang membeli barang" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Keterangan</div>';
        html += '<textarea class="input" name="ket" placeholder="Keterangan barang" rows="3">' + data.ket + '</textarea>';
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