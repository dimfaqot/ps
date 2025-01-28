<script>
    // execute

    let role = "<?= $role; ?>";
    let data_ps = <?= json_encode($ps); ?>;
    let html_ps = "";
    let data_billiard = <?= json_encode($billiard); ?>;
    let html_billiard = "";
    let data_perangkat = <?= json_encode($perangkat); ?>;
    let data_rfid = <?= json_encode($rfid); ?>;
    let data_finger = <?= json_encode($finger); ?>;
    data_ps.forEach((e, i) => {
        if (i % 4 == 0) {
            html_ps += '<div class="d-flex justify-content-center gap-2 mb-2">';
        }
        html_ps += '<div class="rounded-circle p-2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'disable' : '') + '" data-meja="' + e.meja + '" data-menu="Ps" data-is_active="' + e.is_active + '" style="cursor:pointer;font-size:35px;width: 75px;height:75px;color:#7c6f3e;border:1px solid #fce882">';
        html_ps += '<div class="text-center" style="font-size:9px;margin-bottom:-2px">MEJA</div>' + e.meja;
        html_ps += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px">Available</div>';
        html_ps += '</div>';
        if (i % 4 == 3) {
            html_ps += '</div>';
        }
    })
    data_billiard.forEach((e, i) => {
        if (i % 4 == 0) {
            html_billiard += '<div class="d-flex justify-content-center gap-2 mb-2">';
        }
        html_billiard += '<div class="rounded-circle p-2 text-center fw-bold btn_meja_' + e.meja + ' btn_meja ' + (e.is_active == 1 ? 'disable' : '') + '" data-meja="' + e.meja + '" data-menu="Billiard" data-is_active="' + e.is_active + '" style="cursor:pointer;font-size:35px;width: 75px;height:75px;color:#7c6f3e;border:1px solid #fce882">';
        html_billiard += '<div class="text-center" style="font-size:9px;margin-bottom:-2px">MEJA</div>' + e.meja;
        html_billiard += '<div class="text-center div_durasi_' + e.meja + '" style="font-size:9px;margin-top:-5px">Available</div>';
        html_billiard += '</div>';
        if (i % 4 == 3) {
            html_billiard += '</div>';
        }
    })

    let data_others = [{
        nama: "perangkat",
        data: data_perangkat
    }, {
        nama: "rfid",
        data: data_rfid
    }, {
        nama: "finger",
        data: data_finger
    }];

    let html_others = {};
    data_others.forEach(e => {
        let html = "";
        html += '<div class="container text-center mb-2 mt-4">';
        html += '<div class="row g-2">';
        e.data.forEach((d, i) => {
            html += '<div class="col-4">';
            if (e.nama == "perangkat") {
                html += '<div class="rounded border ' + (d.status == 1 ? "border-light text-light" : "border-secondary text-secondary") + ' py-1 btn_sub_menu" data-sub_menu="' + d.id + '" data-menu="perangkat" style="font-size: 16px;">' + d.nama + '</div>';
            } else {
                html += '<div class="rounded border border-secondary text-secondary py-1 btn_sub_menu" data-sub_menu="' + d.controller + '" data-menu="rfid" style="font-size: 16px;">' + d.menu + '</div>';
            }
            html += '</div>';
        })

        html += '</div>';
        html += '</div>';

        html_others[e.nama] = html;
    })

    const durasi_member = `<h6 class="text-center text-light mb-4">DURASI (JAM)</h6>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center p-2 fw-bold btn_durasi" data-durasi="1" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">1</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="2" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">2</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center p-2 fw-bold btn_durasi" data-durasi="3" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">3</div>
                    </div>
                    <div class="d-flex justify-content-center gap-3 mb-3">
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center p-2 fw-bold btn_durasi" data-durasi="4" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">4</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center p-2 mx-4 fw-bold btn_durasi" data-durasi="5" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">5</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center p-2 fw-bold btn_durasi" data-durasi="6" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">6</div>
                    </div>`;
    const durasi_admin = '<div class="d-flex justify-content-center"><div class="border border-secondary border-opacity-50 text-center border-warning rounded-pill text-light px-4 py-2 btn_durasi" data-durasi="0" style="cursor:pointer;font-size:large;">Open</div></div>';
    const menus = {
        member: `<span class="pe-2 tangan" data-menu="admin" style="font-size: 27px;margin-top:-12px"><i class="fa-regular fa-hand-point-right"></i></span>
                    <div class="text-center text-info ms-2">
                        <span style="cursor: pointer;" data-menu="Barber" class="btn_menu py-2 px-4 border rounded border-info">BARBER</span>
                        <span style="cursor: pointer;" data-menu="Ps" class="btn_menu py-2 px-4 rounded border border-info">PS</span>
                        <span style="cursor: pointer;" data-menu="Billiard" class="btn_menu py-2 px-4 rounded border border-info">BILLIARD</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Hutang" class="btn_menu py-2 px-4 rounded border border-info">HUTANG</span>
                        <span style="cursor: pointer;" data-menu="Saldo" class="btn_menu py-2 px-4 rounded border border-info">SALDO</span>
                        </div>`,
        admin: `<span class="pe-2 tangan" data-menu="member" style="font-size: 27px;margin-top:-12px"><i class="fa-regular fa-hand-point-right"></i></span>
                        <div class="text-center text-info ms-2">
                        <span style="cursor: pointer;" data-menu="Absen" class="btn_menu py-2 px-4 border rounded border-info">ABSEN</span>
                        <span style="cursor: pointer;" data-menu="Poin" class="btn_menu py-2 px-4 rounded border border-info">POIN</span>
                        <span style="cursor: pointer;" data-menu="Panel" class="btn_menu py-2 px-4 rounded border border-info">PANEL</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Topup" class="btn_menu py-2 px-4 rounded border border-info">TOPUP</span>
                        <span style="cursor: pointer;" data-menu="Daftar" class="btn_menu py-2 px-4 border rounded border-info">DAFTAR</span>
                        <span style="cursor: pointer;" data-menu="Remove" class="btn_menu py-2 px-4 rounded border border-info">REMOVE</span>
                        <div style="margin-top: 30px;"></div>
                        <span style="cursor: pointer;" data-menu="Reload" class="btn_menu py-2 px-4 rounded border border-danger text-danger"><i class="fa-solid fa-arrows-rotate"></i> RELOAD</span>
                        <span style="cursor: pointer;" data-menu="Add" class="btn_menu py-2 px-4 rounded border border-info">ADD</span>
                        <span style="cursor: pointer;" data-menu="Delete" class="btn_menu py-2 px-4 rounded border border-info">DELETE</span>
                    </div>`,
        topup: `<div class="rounded px-4 pt-4">
                    <h6 class="text-center text-light mb-4">TOPUP</h6>
                    <div class="d-flex justify-content-center gap-5 mb-3">
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 fw-bold btn_sub_menu" data-sub_menu="1" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">1</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 mx-2 fw-bold btn_sub_menu" data-sub_menu="2" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">2</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 fw-bold btn_sub_menu" data-sub_menu="3" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">3</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-3">
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 fw-bold btn_sub_menu" data-sub_menu="4" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">4</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 mx-2 fw-bold btn_sub_menu" data-sub_menu="5" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">5</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 fw-bold btn_sub_menu" data-sub_menu="6" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">6</div>
                    </div>
                    <div class="d-flex justify-content-center gap-5 mb-3">
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 fw-bold btn_sub_menu" data-sub_menu="10" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">10</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 mx-2 fw-bold btn_sub_menu" data-sub_menu="20" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">20</div>
                    <div class="rounded-circle border border-secondary border-opacity-50 text-center px-3 fw-bold btn_sub_menu" data-sub_menu="30" style="cursor:pointer;font-size:x-large;width: 55px;height:55px;color:#cbf4f0;border:1px solid #242b32">30</div>
                    </div>
                    </div>`,
        durasi: (role == "Member" ? durasi_member : durasi_admin),
        ps: html_ps,
        billiard: html_billiard,
        perangkat: html_others.perangkat,
        rfid: html_others.rfid,
        finger: html_others.finger,
        btn_save: '<div class="border-warning border rounded-2 py-1 text-center text-warning border-opacity-50 btn_save" style="font-size:large">OK</div>'
    }
</script>