<?php
$ps = [];
$dbp = db('unit');
$qps = $dbp->whereNotIn('status', ["Maintenance"])->orderBy('id', 'ASC')->get()->getResultArray();

foreach ($qps as $i) {
    $exp = explode(" ", $i['unit']);
    $i['meja'] = (int)end($exp);
    $i['is_active'] = ($i['status'] == "In Game" ? 1 : 0);

    $dbr = db('rental');
    $qp = $dbr->where('meja', $i['unit'])->where('is_active', 1)->get()->getRowArray();
    if ($qp) {
        $dur = explode(":", durasi($qp['ke'], time()));
        $i['status'] = ($qp['durasi'] == -1 ? "Open" : $dur[0] . "h " . $dur[1] . "m");
    } else {
        $i['status'] = "Available";
    }
    $ps[] = $i;
}

dd($ps);
?>
<?= $this->extend('rfid/template') ?>

<?= $this->section('content') ?>


<div style="margin-top: 250px;">
    <h5 class="text-center text-warning">SILAHKAN TAP UNTUK MEMULAI</h5>
    <div class="d-flex date_time justify-content-center mt-5 ">
        <div class="card" style="background-color:transparent;width:400px;border:none">
            <div class="card-body text-info text-center">
                <div class="border-bottom border-info pb-2"><?= date("M d, Y"); ?></div>
                <div style="font-size: xx-large;" class="time"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // show_modal("fullscreen", "show");
    // gagal_rfid();
    let interval_date_time;
    const date_time = () => {
        interval_date_time = setInterval(() => {
            const d = new Date();
            let mnt = (d.getMinutes().toString().length == 1 ? "0" : "") + d.getMinutes();
            let dtk = (d.getSeconds().toString().length == 1 ? "0" : "") + d.getSeconds();

            $('.time').text(d.getHours() + ":" + mnt + ":" + dtk);
            get_session();

        }, 1000);
    }
    const get_session = () => {
        post('rfid/session', {
            lokasi
        }).then(res => {
            if (res.status == "200") {
                if (res.data.lokasi !== "") {
                    if (res.data.status == "400" || res.data.status == "300") {
                        if (session_now == 0) {
                            console.log(session_now);
                            session_now = 1;
                            gagal_rfid(res.data.message);

                        }
                    }

                    if (res.data.status == "200") {
                        clearInterval(interval_date_time);
                        sukses_rfid(res.data.message, "no", 20, "fullscreen");
                        location.href = base_url + "rfid/execute/" + res.data.url;
                    }
                }
            }
        })
    }

    date_time();
</script>


<?= $this->endSection() ?>