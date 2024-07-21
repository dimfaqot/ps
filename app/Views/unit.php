<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="mobile">
    <button type="button" class="btn_success mb-3" data-bs-toggle="modal" data-bs-target="#add_<?= menu()['controller']; ?>">
        Add <?= menu()['menu']; ?>
    </button>

    <?php if (count($data) == 0) : ?>
        <div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>
    <?php else : ?>
        <?php foreach ($data as $i) : ?>
            <div class="div_list">
                <div class="d-flex justify-content-between">
                    <div class="detail" data-id="<?= $i['id']; ?>"><?= $i['unit']; ?></div>
                    <div class="bg_purple_light px-2 fw-bold" style="border-radius: 5px;"><a style="font-size: medium;" href="" data-id="<?= $i['id']; ?>" class="detail_unit text-purple"><i class="fa-solid fa-circle-info"></i></a> <a href="" data-alert="Are you sure to delete this data?" data-function="check_unit_item" data-url="general/delete" data-id="<?= $i['id']; ?>" data-tabel="<?= menu()['tabel']; ?>" data-col="id" class="text_danger btn_confirm" style="font-size: medium;"><i class="fa-solid fa-circle-xmark"></i></a></div>
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
                        <div class="text_main2">Barang</div>
                        <input class="input" type="text" name="unit" placeholder="Nama unit" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Status</div>
                        <input class="input btn_select add_status" name="status" data-col="value" data-where="kategori=Status" data-tabel="options" data-orderby="value=ASC" data-target="add_status" type="text" value="Available" placeholder="Status" readonly>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Kode Harga</div>
                        <input class="input" type="text" name="kode_harga" placeholder="Kode harga" required>
                    </div>
                    <div class="mb-2">
                        <div class="text_main2">Desc</div>
                        <textarea class="input" name="desc" placeholder="Description unit" rows="3"></textarea>
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

<!-- Modal detail unit-->
<div class="modal fade" id="detail_unit" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_detail_unit">

            </div>
        </div>
    </div>
</div>

<!-- canvas detail_inv -->
<div class="offcanvas offcanvas-end" style="z-index:9999999" tabindex="-1" id="detail_inv" aria-labelledby="offcanvasRightLabel">

    <div class="offcanvas-body p-0 body_detail_inv">

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
        html += '<h6 class="text_main2 fw-bold"> <i class="fa-solid fa-user"></i> Update <?= menu()['menu']; ?> ' + data.unit + '</h6>';
        html += '<hr>';
        html += '<form action="<?= base_url(menu()['controller']); ?>/update" method="post">';
        html += '<input type="hidden" name="id" value="' + data.id + '">';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Nama Unit</div>';
        html += '<input class="input" type="text" name="unit" value="' + data.unit + '" placeholder="Unit" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Status</div>';
        html += '<input class="input btn_select update_status_' + id + '" name="status" data-tabel="options" data-col="value" data-orderby="value=ASC" data-where="kategori=Status" data-target="update_status_' + id + '" type="text" value="' + data.status + '" placeholder="Status" readonly>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Kode Harga</div>';
        html += '<input class="input" name="kode_harga" type="text" value="' + data.kode_harga + '" placeholder="Kode harga" required>';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<div class="text_main2">Desc</div>';
        html += '<textarea class="input" name="desc" placeholder="Description unit" rows="3">' + data.desc + '</textarea>';
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

    $(document).on('click', '.detail_unit', function(e) {
        e.preventDefault();

        let datas = <?= json_encode($data); ?>;

        let id = $(this).data('id');
        let data;
        datas.forEach(e => {
            if (e.id == id) {
                data = e;
            }
        });

        post("<?= menu()['controller']; ?>" + '/detail_unit', {
            id
        }).then(res => {
            if (res.status = '200') {

                let html = '';
                html += '<h6 class="text_main2 fw-bold"><i class="fa-brands fa-playstation"></i> Meja 1</h6>';
                html += '<p style="font-size: small;">' + data.desc + '</p>';
                html += '<hr>';
                html += '<div>Tambah Unit</div>';
                html += '<div class="d-flex justify-content-center body_select d-none"></div>';
                html += '<div class="mb-3 d-flex gap-2">';
                html += '<div class="w-100">';
                html += '<input class="input btn_select add_barang" data-target="add_barang" data-tabel="inventaris" data-col="barang" data-orderby="barang=ASC" data-where="" type="text" name="unit" placeholder="Nama unit" required readonly>';
                html += '</div>';
                html += '<div class="flex-shrink-1" style="font-size: x-large;"><a data-unit_id="' + data.id + '" href="" data-target="add_barang" class="add_unit_inv"><i class="fa-solid fa-circle-plus"></i></a></div>';
                html += '</div>';

                html += '<div>Daftar Unit</div>';
                html += '<div class="body_inv_list">';
                if (res.data.length == 0) {
                    html += '<div class="div_list text_warning"><i class="fa-solid fa-ban"></i> Data not found!.</div>';
                } else {
                    html += add_unit_inv(res.data);
                }
                html += '</div>';


                $('.body_detail_unit').html(html);
                let myModal = document.getElementById('detail_unit');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
            } else {
                gagal_with_button(res.message);
            }
        })
    })

    $(document).on('click', '.add_unit_inv', function(e) {
        e.preventDefault();
        let target = $(this).data('target');
        let inv_id = $('.' + target).attr('data-autofill_id');
        let unit_id = $(this).data('unit_id');
        post("<?= menu()['controller']; ?>" + '/add_unit_inv', {
            inv_id,
            unit_id
        }).then(res => {
            if (res.status == '200') {
                let html = add_unit_inv(res.data);
                $('.body_inv_list').html(html);
            } else {
                gagal(res.message);
            }
        })
    })

    $(document).on('click', '.detail_inv', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let unit_id = $(this).data('unit_id');
        post("<?= menu()['controller']; ?>/detail_inv", {
            id,
            unit_id
        }).then(res => {
            if (res.status == "200") {

                let html = '';
                html += '<div class="d-flex justify-content-between shadow px-3 py-2">';
                html += '<div class="fw-bold">';
                html += 'Lorem, ipsum dolor.';
                html += '</div>';
                html += '<div style="font-size:medium;">';
                html += '<a style="color: red;" href="" data-bs-dismiss="offcanvas"><i class="fa-solid fa-circle-xmark"></i></a>';
                html += '</div>';
                html += '</div>';

                html += '<div class="px-3 mt-3">';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">No. Inv</div>';
                html += '<input class="input" type="text" value="' + res.data.id + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Tgl. Beli</div>';
                html += '<input class="input" type="text" value="' + time_php_to_js(res.data.tgl) + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Barang</div>';
                html += '<input class="input" type="text" value="' + res.data.barang + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Harga</div>';
                html += '<input class="input" type="text" value="' + angka(res.data.harga) + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Kondisi</div>';
                html += '<input class="input" type="text" value="' + res.data.kondisi + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Lokasi Barang</div>';
                html += '<input class="input" type="text" value="' + res.data.lokasi + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Pembeli</div>';
                html += '<input class="input" type="text" value="' + res.data.pembeli + '" disabled>';
                html += '</div>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Ket. Inv</div>';
                html += '<textarea class="input" rows="3" disabled>' + res.data.ket + '</textarea>';
                html += '</div>';
                html += '<hr>';
                html += '<div class="mb-2">';
                html += '<div class="text_main2">Ket Unit</div>';
                html += '<textarea placeholder="Keterangan item dalam unit" class="input update_catatan" rows="3">' + res.data2.catatan + '</textarea>';
                html += '</div>';
                html += '<div class="d-grid">';
                html += '<button data-id="' + res.data2.id + '" class="btn_success btn_update_catatan"><i class="fa-solid fa-cloud"></i> Save</button>';
                html += '</div>';

                html += '</div>';
                $('.body_detail_inv').html(html);
                const bsOffcanvas = new bootstrap.Offcanvas('#detail_inv');
                bsOffcanvas.show();
            } else {
                gagal(res.message);
            }
        })
    })

    $(document).on('click', '.btn_update_catatan', function(e) {
        e.preventDefault();
        let id = $(this).data('id');
        let catatan = $('.update_catatan').val();

        post("<?= menu()['controller']; ?>" + '/update_catatan', {
            id,
            catatan
        }).then(res => {
            if (res.status == '200') {
                sukses(res.message);
            } else {
                gagal(res.message);
            }
        })
    })
</script>
<?= $this->endSection() ?>