<div class="d-flex date_time justify-content-center" style="margin-top: 160px;">
    <div class="card" style="background-color:transparent;width:400px;border:none">
        <div class="card-body text-info text-center">
            <div class="border-bottom border-info pb-2"><?= date("M d, Y"); ?></div>
            <div style="font-size: xx-large;" class="time"></div>
        </div>
    </div>
</div>

<script>
    setInterval(() => {
        const d = new Date();
        let mnt = (d.getMinutes().toString().length == 1 ? "0" : "") + d.getMinutes();
        let dtk = (d.getSeconds().toString().length == 1 ? "0" : "") + d.getSeconds();

        $('.time').text(d.getHours() + ":" + mnt + ":" + dtk);
    }, 1000);
</script>