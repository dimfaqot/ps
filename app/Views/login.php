<?= $this->extend('guest') ?>

<?= $this->section('content') ?>


<div style="padding: 17%">
    <div style="border: 2px solid white;border-radius:10px;background-color:white">
        <div class="d-flex gap-2">
            <div style="padding:40px;" class="flex-fill">
                <form action="<?= base_url('auth'); ?>" method="post">
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text"><i style="color:#0e8aed;" class="fa-solid fa-user-large"></i></span>
                        <input type="text" class="form-control" name="username" placeholder="Username">
                    </div>
                    <div class="input-group input-group-sm mb-2">
                        <span class="input-group-text"><i style="color:#0e8aed;" class="fa-solid fa-lock"></i></span>
                        <input type="password" class="form-control" name="password" placeholder="Password">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn_info">LOGIN</button>
                    </div>
                </form>

            </div>
            <div class="flex-fill d-md-block d-none px-4 py-3" style="background: rgb(124,197,255);
background: linear-gradient(90deg, rgba(124,197,255,1) 31%, rgba(220,239,255,1) 100%);padding:6px;border-radius:0px 10px 10px 0px">
                <h5 style="text-align: center;background-color:#fff;border-radius:20px;padding:10px"><i style="color: #61b4ff;" class="fa-solid fa-gamepad"></i> <span style="color: #054d8e;">PS MANIA</span></h5>
                <p style="text-align: center;font-size:small">"If you do what you love, it is the best way to relax." </p>
                <hr>
                <h1 style="text-align: center;"><i class="fa-brands fa-playstation"></i></h1>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>