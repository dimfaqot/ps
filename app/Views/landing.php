<?= $this->extend('guest') ?>

<?= $this->section('content') ?>

<div class="container bg-light py-5" style="margin-bottom: 40px;">

    <div class="fixed-top">
        <div class="container bg-light">
            <h3 style="text-align: center;">HAYU PLAYGROUND</h3>

            <div class="row mb-5 g-3">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text_primary detail" data-order="rental" style="text-align: center;cursor:pointer">
                            <i style="font-size: 100px;" class="fa-brands fa-playstation"></i>
                            <h5>Playstation</h5>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body text_purple detail" data-order="billiard" style="text-align: center;cursor:pointer">
                            <i style="font-size: 100px;" class="fa-solid fa-bowling-ball"></i>
                            <h5>Billiard</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="d-none d-md-block" style="margin-top: 180px;"></div>
    <div class="d-block d-md-none d-sm-block" style="margin-top: 350px;"></div>
    <div class="body_rental">
        <?= view('landing/rental_landing', ['data' => $rental]); ?>
    </div>
    <div class="body_billiard" style="display: none;">
        <?= view('landing/billiard_landing', ['data' => $billiard, 'meja' => $meja]); ?>
    </div>
</div>


<script>
    $(document).on('click', '.detail', function(e) {
        e.preventDefault();
        let order = $(this).data('order');
        let target = (order == 'rental' ? 'billiard' : 'rental');
        if ($('.body_' + target).is(':visible')) {
            $('.body_' + target).hide();
        }
        $('.body_' + order).toggle();
    })
</script>
<?= $this->endSection() ?>