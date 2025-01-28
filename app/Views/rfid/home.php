<?= $this->extend('rfid/template') ?>

<?= $this->section('content') ?>


<div style="margin-top: 50px;">
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
<div class="status_meja mt-4"></div>

<script>
    const status_meja = (data) => {
        let html = "";
        data.forEach((e, i) => {
            if (i % 4 == 0) {
                html += '<div class="d-flex justify-content-center gap-2 mb-2">';
            }
            html += '<div class="rounded-circle p-2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'disable' : '') + '" data-meja="' + e.meja + '" data-menu="Ps" data-is_active="' + e.is_active + '" style="cursor:pointer;font-size:35px;width: 75px;height:75px;color:#7c6f3e;border:1px solid #fce882">';
            html += '<div class="text-center" style="font-size:9px;margin-bottom:-2px">MEJA</div>' + e.meja;
            html += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px">' + e.status + '</div>';
            html += '</div>';
            if (i % 4 == 3) {
                html += '</div>';
            }
        })
        $(".status_meja").html(html);
    }
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
            } else {
                if (res.data.length > 0) {
                    status_meja(res.data);
                }
            }
        })
    }

    date_time();
</script>


<?= $this->endSection() ?>