<div class="text-center text-info">
    <span class="pe-2 tangan btn_topup" data-menu="Topup" style="font-size: 27px;"><i class="fa-regular fa-hand-point-right"></i></span>
    <span style="cursor: pointer;" data-menu="Ps" class="btn_menu py-3 px-4 rounded border border-info">PS</span>
    <span style="cursor: pointer;" data-menu="Billiard" class="btn_menu py-3 px-4 rounded border border-info">BILLIARD</span>
    <span style="cursor: pointer;" data-menu="Kantin" class="btn_menu py-3 px-4 rounded border border-info">KANTIN</span>
</div>

<script>
    setInterval(() => {
        $(".tangan").addClass("text-danger");
    }, 500);
    setInterval(() => {
        $(".tangan").removeClass("text-danger");
    }, 1000);
</script>