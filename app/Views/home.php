<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


<div class="container">

    <div class="row g-3">
        <div class="col-md-6">
            <div class="div_card bg_primary border border-primary text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-brands fa-playstation mb-1"></i> PENDAPATAN PS
                        <div style="font-weight: normal;font-size:x-small" class="total_rental"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <select class="form-select get_pendapatan" data-tabel="rental">
                            <?php foreach (get_tahuns('rental') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div class="card p-2">
                    <canvas id="chart_rental" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="div_card bg_success border border-success text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-solid fa-bowling-ball mb-1"></i> PENDAPATAN BILLIARD
                        <div style="font-weight: normal;font-size:x-small" class="total_billiard"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <select class="form-select get_pendapatan" data-tabel="billiard">
                            <?php foreach (get_tahuns('billiard') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div class="card p-2">
                    <canvas id="chart_billiard" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
        <div class="col-md-6">
            <div class="div_card bg_purple border border-success text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6>
                        <i class="fa-solid fa-shop mb-1"></i> PENDAPATAN KANTIN
                        <div style="font-weight: normal;font-size:x-small" class="total_kantin"></div>
                    </h6>
                    <h6 class="d-flex gap-1">
                        <select class="form-select get_pendapatan" data-tabel="kantin">
                            <?php foreach (get_tahuns('kantin') as $i) : ?>
                                <option <?= ($i == date('Y') ? 'selected' : ''); ?> value="<?= $i; ?>"><?= $i; ?></option>
                            <?php endforeach; ?>
                            <option value="All">All</option>
                        </select>
                    </h6>
                </div>
                <div class="card p-2">
                    <canvas id="chart_kantin" style="width:100%;"></canvas>
                </div>
            </div>

        </div>
    </div>

</div>

<!-- Modal detail pendapatan-->
<div class="modal fade" id="detail_pendapatan" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body body_detail_pendapatan">

            </div>
        </div>
    </div>
</div>
<script>
    $(document).on('change', '.get_pendapatan', function(e) {
        e.preventDefault();

        let tabel = $(this).data('tabel');
        let tahun = $(this).val();

        chart_html(tabel, tahun);

    })

    const content_table = (order, tabel, data, index) => {
        let html = '';

        html += '<table class="table table-striped table-bordered table-sm">';
        html += '<thead>';
        html += '<tr>';
        html += '<th style="text-align: center;" scope="row">#</th>';
        html += '<th style="text-align: center;" scope="row">Tgl</th>';
        if (tabel == 'kantin') {
            html += '<th style="text-align: center;" scope="row">Qty</th>';
            html += '<th style="text-align: center;" scope="row">Harga' + (order == 'pemasukan' ? ' Satuan' : '') + '</th>';
        } else {
            if (order == 'pemasukan') {
                html += '<th style="text-align: center;" scope="row">Meja</th>';
                html += '<th style="text-align: center;" scope="row">Durasi</th>';

            } else {
                html += '<th style="text-align: center;" scope="row">Qty</th>';
                html += '<th style="text-align: center;" scope="row">Harga</th>';

            }

        }
        if (order == 'pemasukan') {
            html += '<th style="text-align: center;" scope="row">Diskon</th>';
            html += '<th style="text-align: center;" scope="row">' + (tabel == 'kantin' ? 'Harga' : 'Biaya') + '</th>';

        } else {
            html += '<th style="text-align: center;" scope="row">Pj</th>';
        }
        html += '</tr>';
        html += '</thead>';
        html += '<tbody>';
        let total_m = 0;
        data.forEach((val, idx) => {
            if (val.bulan == index) {
                total_m = val.total;
                val.data.forEach((e, i) => {
                    html += '<tr>';
                    html += '<td>' + (i + 1) + '</td>';
                    html += '<td style="text-align:center">' + e.tanggal + '</td>';
                    if (tabel == 'kantin') {
                        html += '<td style="text-align:center">' + e.qty + '</td>';
                        html += '<td style="text-align:right">' + angka((order == 'pemasukan' ? e.harga_satuan : e.harga)) + '</td>';
                    } else {
                        if (order == 'pemasukan') {
                            html += '<td>' + e.meja + '</td>';
                            html += '<td>' + e.durasi + '</td>';

                        } else {
                            html += '<td style="text-align:center">' + e.qty + '</td>';
                            html += '<td style="text-align:right">' + angka(e.harga) + '</td>';

                        }

                    }
                    if (order == 'pemasukan') {

                        html += '<td style="text-align:right">' + angka(e.diskon) + '</td>';
                        html += '<td style="text-align:right">' + angka(e.biaya) + '</td>';
                    } else {
                        html += '<td>' + (tabel == 'rental' && order == 'pemasukan' ? e.petugas : (tabel == 'rental' && order == 'pengeluaran' ? e.pembeli : e.pj)) + '</td>';

                    }
                    html += '</tr>';
                })
            }
        })

        html += '<tr>';
        html += '<th style="text-align:right" colspan="' + (order == 'pemasukan' ? 5 : 4) + '">TOTAL</th>';
        html += '<th style="text-align:right">' + angka(total_m) + '</th>';
        html += '</td>';

        html += '</tbody>';
        html += '</table>';

        let res = {
            total_m,
            html
        }
        return res;

    }

    const chart_html = (tabel, tahun) => {

        const bulans = <?= json_encode(bulan()); ?>;
        let valueY = [];
        bulans.forEach(e => {
            valueY.push(e.satuan);
        });

        post('home/get_pendapatan', {
            tabel,
            tahun
        }).then(res => {
            if (res.status == '200') {
                // total pemasukan
                let total_m = 0;
                res.data.forEach((val, idx) => {
                    total_m += val.total;
                })

                // total pengeluaran
                let total_p = 0;
                res.data2.forEach(e => {
                    total_p += e.total;
                })
                $('.total_' + tabel).text(angka(total_m) + ' - ' + angka(total_p) + ' = ' + ((total_m - total_p) < 0 ? '-' : '') + angka((total_m - total_p).toString()));


                valueX = [];

                res.data.forEach(e => {
                    valueX.push(e.total);
                })

                // const xPs = [100000, 6500000, 130000, 4590200, 452682, 987698, 4263528, 9876262, 665656, 879766, 879999, 0];
                // const yPs = bulan;

                new Chart("chart_" + tabel, {
                    type: "line",
                    data: {
                        labels: valueY,
                        datasets: [{
                            fill: false,
                            lineTension: 0,
                            backgroundColor: "rgba(0,0,255,1.0)",
                            borderColor: "rgba(0,0,255,0.1)",
                            data: valueX
                        }]
                    },
                    options: {
                        legend: {
                            display: false
                        },
                        onClick: (e, values) => {
                            let index = values[0]['_index'] + 1;
                            let body_table = content_table('pemasukan', tabel, res.data, index);

                            let html = '<div class="d-flex justify-content-between judul">';
                            html += '<div>';
                            html += 'DETAIL ' + tabel.toUpperCase();
                            html += '</div>';
                            html += '<div>';
                            html += '<a type="button" href="" class="text_danger" data-bs-dismiss="modal"><i class="fa-solid fa-circle-xmark"></i></a>';
                            html += '</div>';
                            html += '</div>';
                            html += '<div style="border-radius:5px;" class="judul_modal judul_' + tabel + '"></div>';
                            html += '<ul class="nav nav-tabs">';
                            html += '<li class="nav-item">';
                            html += '<a class="nav-link active detail_data" data-order="pemasukan" aria-current="page" href="#">Pemasukan</a>';
                            html += '</li>';
                            html += '<li class="nav-item">';
                            html += '<a class="nav-link detail_data" data-order="pengeluaran" aria-current="page" href="#">Pengeluaran</a>';
                            html += '</li>';
                            html += '</ul>';
                            html += '<div class="content_table">';
                            html += body_table.html;
                            html += '</div>';
                            $('.body_detail_pendapatan').html(html);
                            let myModal = document.getElementById('detail_pendapatan');
                            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                            modal.show();

                            let total_p = 0;
                            res.data2.forEach((val, idx) => {
                                if (val.bulan == index) {
                                    total_p = val.total;
                                }
                            })

                            $('.judul_' + tabel).text(angka(body_table.total_m) + ' - ' + angka(total_p) + ' = ' + ((body_table.total_m - total_p) < 0 ? '-' : '') + angka(body_table.total_m - total_p));

                            $(document).on('click', '.detail_data', function(e) {
                                e.preventDefault();
                                let order = $(this).data('order');
                                let content = content_table(order, tabel, (order == 'pemasukan' ? res.data : res.data2), index);

                                let elem = document.querySelectorAll('.detail_data');

                                elem.forEach(e => {
                                    e.classList.remove('active');
                                })
                                $(this).addClass('active');
                                $('.content_table').html(content.html);
                            })

                            // let datasetIndex = activeEls[0].datasetIndex;
                            // let dataIndex = activeEls[0].index;
                            // let datasetLabel = e.chart.data.datasets[datasetIndex].label;
                            // let value = e.chart.data.datasets[datasetIndex].data[dataIndex];
                            // let label = e.chart.data.labels[dataIndex];
                            // console.log("In click", datasetLabel, label, value);
                        }
                    }
                });
            } else {
                gagal_with_button(res.message);
            }
        })
    }

    chart_html('rental', '<?= date('Y'); ?>');
    chart_html('billiard', '<?= date('Y'); ?>');
    chart_html('kantin', '<?= date('Y'); ?>');
</script>

<?= $this->endSection() ?>