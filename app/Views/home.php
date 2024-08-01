<?= $this->extend('logged') ?>

<?= $this->section('content') ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>


<div class="container">
    <div class="row g-3">
        <div class="col-md-6">
            <div class="div_card bg_primary border border-primary text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6><i class="fa-brands fa-playstation"></i> PENDAPATAN PS</h6>
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
                    <h6><i class="fa-solid fa-bowling-ball"></i> PENDAPATAN BILLIARD</h6>
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
            <div class="div_card bg_success border border-success text-white" style="border-radius:5px;">
                <div class="d-flex justify-content-between">
                    <h6><i class="fa-solid fa-bowling-ball"></i> PENDAPATAN KANTIN</h6>
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
                            let html = '';
                            html += '<table class="table table-striped table-bordered table-sm">';
                            html += '<thead>';
                            html += '<tr>';
                            html += '<th style="text-align: center;" scope="row">#</th>';
                            html += '<th style="text-align: center;" scope="row">Tgl</th>';
                            if (tabel == 'kantin') {
                                html += '<th style="text-align: center;" scope="row">Harga Satuan</th>';
                                html += '<th style="text-align: center;" scope="row">Qty</th>';
                            } else {
                                html += '<th style="text-align: center;" scope="row">Meja</th>';
                                html += '<th style="text-align: center;" scope="row">Durasi</th>';

                            }
                            html += '<th style="text-align: center;" scope="row">Diskon</th>';
                            html += '<th style="text-align: center;" scope="row">' + (tabel == 'kantin' ? 'Harga' : 'Biaya') + '</th>';
                            html += '</tr>';
                            html += '</thead>';
                            html += '<tbody>';
                            let total = 0;
                            res.data.forEach((val, idx) => {
                                if (val.bulan == (values[0]._index + 1)) {
                                    total = val.total;
                                    val.data.forEach((e, i) => {
                                        html += '<tr>';
                                        html += '<td>' + (i + 1) + '</td>';
                                        html += '<td style="text-align:center">' + e.tanggal + '</td>';
                                        if (tabel == 'kantin') {
                                            html += '<td style="text-align:right">' + angka(e.harga_satuan) + '</td>';
                                            html += '<td style="text-align:center">' + e.qty + '</td>';
                                        } else {
                                            html += '<td>' + e.meja + '</td>';
                                            html += '<td>' + e.durasi + '</td>';

                                        }
                                        html += '<td style="text-align:right">' + angka(e.diskon) + '</td>';
                                        html += '<td style="text-align:right">' + angka(e.biaya) + '</td>';
                                        html += '</tr>';
                                    })
                                }
                            })

                            html += '<tr>';
                            html += '<th style="text-align:right" colspan="5">TOTAL</th>';
                            html += '<th style="text-align:right">' + angka(total) + '</th>';
                            html += '</td>';

                            html += '</tbody>';
                            html += '</table>';
                            $('.body_detail_pendapatan').html(html);
                            let myModal = document.getElementById('detail_pendapatan');
                            let modal = bootstrap.Modal.getOrCreateInstance(myModal)
                            modal.show();
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