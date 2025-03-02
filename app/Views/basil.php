<?php
$data = basil();
?>
<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<div class="container">
    <!-- Modal -->
    <div class="modal fade" id="fullscreen" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content bg-dark">
                <div class="d-flex justify-content-center mt-3">
                    <a data-bs-dismiss="modal" style="text-decoration: none;color:red;font-size:x-large" href=""><i class="fa-solid fa-circle-xmark"></i></a>
                </div>
                <div class="modal-body body_fullscreen">

                </div>
            </div>
        </div>
    </div>
    <table class="table table-sm table-bordered">
        <thead>
            <tr>
                <th class="text-center" scope="col">#</th>
                <th class="text-center" scope="col">Kategori</th>
                <th class="text-center" scope="col">Masuk</th>
                <?php foreach ($data as $k => $i): ?>
                    <?php if ($k == 0): ?>
                        <?php foreach ($i['data'] as $d): ?>
                            <th class="text-center" scope="col"><a class="modal_pengeluaran" data-kepada="<?= $d['kepada']; ?>" style="text-decoration: none;" href=""><?= $d['kepada']; ?></a></th>

                        <?php endforeach; ?>
                        <?php break; ?>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $total_masuk = 0;
            $totals = [];
            foreach ($data as $k => $i) {
                if ($k == 0) {
                    foreach ($i['data'] as $d) {
                        $totals[$d['kepada']] = 0;
                    }
                    break;
                }
            }
            ?>
            <?php foreach ($data as $k => $d): ?>
                <?php $total_masuk += (int) $d['jml']; ?>
                <tr>
                    <th class="text-center" scope="row"><?= ($k + 1); ?></th>
                    <td><?= $d['kategori']; ?></td>
                    <td class="text-end"><?= angka($d['jml']); ?></td>
                    <?php foreach ($d['data'] as $i): ?>
                        <?php if ($i['kategori'] == $d['kategori']): ?>
                            <?php $totals[$i['kepada']] += $i['jml']; ?>
                            <td class="text-end"><?= angka($i['jml']); ?></td>
                        <?php endif; ?>
                    <?php endforeach; ?>


                </tr>

            <?php endforeach; ?>
            <tr>
                <th class="text-center" colspan="2"> TOTAL</th>
                <th class="text-end"><?= angka($total_masuk); ?></th>
                <?php foreach ($totals as $k => $i): ?>
                    <th class="text-end"><a href="" class="modal_add_pengeluaran" data-kepada="<?= $k; ?>" data-jml="<?= $i; ?>" style="text-decoration: none;"><?= angka($i); ?></a></th>
                <?php endforeach; ?>
            </tr>
            <tr>
                <?php
                $saldo = 0;
                foreach ($totals as $k => $i) {
                    $saldo += (int)$i;
                }
                ?>
                <th class="text-center" colspan="3"> SALDO</th>
                <th class="text-center" colspan="4"><?= angka($saldo); ?></th>
            </tr>
        </tbody>
    </table>
</div>
<script>
    $(document).on('click', '.modal_pengeluaran', function(e) {
        e.preventDefault();
        let kepada = $(this).data("kepada");
        post('basil/data_pengeluaran', {
            kepada
        }).then(res => {
            if (res.status == '200') {
                let html = '';
                if (res.data.length == 0) {
                    html += '<div class="text-secondary"><i class="fa-solid fa-triangle-exclamation"></i> Data tidak ditemukan!.</div>';
                } else {
                    html += '<table class="table table-sm table stripped table-dark">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<th class="text-center">#</th>';
                    html += '<th class="text-center">TGL</th>';
                    html += '<th class="text-center">PENERIMA</th>';
                    html += '<th class="text-center">JML</th>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    let total = 0;
                    res.data.forEach((e, i) => {
                        total += parseInt(e.jml);
                        html += '<tr>';
                        html += '<td class="text-center">' + (i + 1) + '</td>';
                        html += '<td>' + time_php_to_js(e.tgl) + '</td>';
                        html += '<td>' + e.penerima + '</td>';
                        html += '<td class="text-end">' + angka(e.jml) + '</td>';
                        html += '</tr>';
                    });
                    html += '<tr>';
                    html += '<th colspan="3" class="text-center">TOTAL</th>';
                    html += '<td class="text-end">' + angka(total) + '</td>';
                    html += '</tr>';
                }
                html += '</tbody>';
                html += '</table>';

                $(".body_fullscreen").html(html);
                let myModal = document.getElementById('fullscreen');
                let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                modal.show();
            } else {
                gagal(res.message);
            }
        })

    });
    $(document).on('click', '.modal_add_pengeluaran', function(e) {
        e.preventDefault();
        let kepada = $(this).data("kepada");
        let jml = $(this).data("jml");
        let html = "";

        html += '<div class="container">';
        html += '<h3 class="text-light">' + kepada + ' - ' + angka(jml) + '</h3>';
        html += '<div class="mb-2">';
        html += '<label class="form-label text-secondary">Penerima:</label>';
        html += '<input type="text" class="form-control penerima_keluar form-control-sm bg-dark text-light" placeholder="Penerima">';
        html += '</div>';
        html += '<div class="mb-2">';
        html += '<label class="form-label text-secondary">Jumlah:</label>';
        html += '<input type="text" class="form-control jml_keluar form-control-sm uang bg-dark text-light limit" data-jml="' + jml + '" placeholder="Jumlah">';
        html += '</div>';
        html += '<div class="d-grid">';
        html += '<button data-jml="' + jml + '" data-kepada="' + kepada + '" class="btn btn-sm btn-primary btn_add_pengeluaran">Save</button>'
        html += '</div>';
        html += '</div>';
        $(".body_fullscreen").html(html);
        let myModal = document.getElementById('fullscreen');
        let modal = bootstrap.Modal.getOrCreateInstance(myModal)
        modal.show();

    });
    $(document).on('keyup', '.limit', function(e) {
        e.preventDefault();
        let kepada = $(this).data("kepada");
        let jml = $(this).data("jml");
        let val = parseInt(str_replace(".", "", $(this).val()));

        if (parseInt(val) > parseInt(jml)) {
            gagal("Melebihi limit!.");
            $(this).val(angka(jml));
            return;
        }

    });
    $(document).on('click', '.btn_add_pengeluaran', function(e) {
        e.preventDefault();
        let kepada = $(this).data("kepada");
        let penerima_keluar = $(".penerima_keluar").val();
        let jml = parseInt($(this).data("jml"));
        let jml_keluar = parseInt(str_replace(".", "", $(".jml_keluar").val()));

        if (jml_keluar > jml) {
            gagal("Melebihi limit!.");
            return;
        }

        post('basil/add_pengeluaran', {
            jml,
            jml_keluar,
            penerima_keluar,
            kepada
        }).then(res => {
            if (res.status == "200") {
                sukses(res.message);
                setTimeout(() => {
                    location.reload();
                }, 1200);
            } else {
                gagal(res.message);
            }
        })

    });
</script>
<?= $this->endSection() ?>