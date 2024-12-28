<div class="text-center text-info ms-2">
    <span class="pe-2 tangan btn_admin" style="font-size: 27px;margin-left:-36px"><i class="fa-regular fa-hand-point-right"></i></span>
    <span style="cursor: pointer;" data-menu="Absen" class="btn_menu py-2 px-4 border rounded border-info">ABSEN</span>
    <span style="cursor: pointer;" data-menu="Ps" class="btn_menu py-2 px-4 rounded border border-info">PS</span>
    <span style="cursor: pointer;" data-menu="Billiard" class="btn_menu py-2 px-4 rounded border border-info">BILLIARD</span>
    <div style="margin-top: 30px;"></div>
    <span style="cursor: pointer;" data-menu="Hutang" class="btn_menu py-2 px-4 rounded border border-info">HUTANG</span>
    <span style="cursor: pointer;" data-menu="Saldo" class="btn_menu py-2 px-4 border rounded border-info">CEK SALDO</span>
</div>

<script>
    setInterval(() => {
        $(".tangan").addClass("text-danger");
    }, 500);
    setInterval(() => {
        $(".tangan").removeClass("text-danger");
    }, 1000);
</script>