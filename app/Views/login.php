<?= $this->extend('guest') ?>

<?= $this->section('content') ?>

<div style="padding-left: 17%;padding-right: 17%">
    <div class="map_lokasi_saya">

    </div>
    <div class="body_login" style="border: 2px solid white;border-radius:10px;background-color:white;display:none">


    </div>
</div>


<script>
    let lat_atas = parseFloat(-7.4412100);
    let lat_bawah = parseFloat(-7.4410350);
    let long_atas = parseFloat(111.0362000);
    let long_bawah = parseFloat(111.035000);


    const successCb = (position) => {
        let maphtml = '';
        let latitude = position.coords.latitude;
        let longitude = position.coords.longitude;

        maphtml += '<p>Latitude: ' + latitude + ' Longitude: ' + longitude + '</p>';
        maphtml += '<iframe width="100%" height="300" src="https://maps.google.com/maps?q=' + latitude + ',' + longitude + '&amp;z=15&amp;output=embed"></iframe>';

        $('.map_lokasi_saya').html(maphtml);

        if (latitude > lat_atas && latitude < lat_bawah) {
            $('.body_login').text("(lat) LOKASIMU DI LUAR AREA!.");
            return false;
        }

        if (longitude > long_atas && longitude < lat_bawah) {
            $('.body_login').text("(long) LOKASIMU DI LUAR AREA!.");
            return false;
        }

        let html = '';
        html += '<div class="d-flex gap-2">';
        html += '<div style="padding:40px;" class="flex-fill">';
        html += '<form action="<?= base_url('auth'); ?>" method="post">';
        html += '<input type="hidden" class="ip" name="ip">';
        html += '<input type="hidden" class="latitude" name="latitude" value="' + latitude + '">';
        html += '<input type="hidden" class="longitude" name="longitude" value="' + longitude + '">';
        html += '<div class="input-group input-group-sm mb-2">';
        html += '<span class="input-group-text"><i style="color:#0e8aed;" class="fa-solid fa-user-large"></i></span>';
        html += '<input type="text" class="form-control" name="username" placeholder="Username">';
        html += '</div>';
        html += '<div class="input-group input-group-sm mb-2">';
        html += '<span class="input-group-text"><i style="color:#0e8aed;" class="fa-solid fa-lock"></i></span>';
        html += '<input type="password" class="form-control" name="password" placeholder="Password">';
        html += '</div>';
        html += '<div class="d-grid">';
        html += '<button type="submit" class="btn_info">LOGIN</button>';
        html += '</div>';
        html += '</form>';

        html += '</div>';


        html += '<div class="flex-fill d-md-block d-none px-4 py-3" style="background: rgb(124,197,255);background: linear-gradient(90deg, rgba(124,197,255,1) 31%, rgba(220,239,255,1) 100%);padding:6px;border-radius:0px 10px 10px 0px">';
        html += '<h5 style="text-align: center;background-color:#fff;border-radius:20px;padding:10px"><i style="color: #61b4ff;" class="fa-solid fa-gamepad"></i> <span style="color: #054d8e;">PS MANIA</span></h5>';
        html += '<p style="text-align: center;font-size:small">"If you do what you love, it is the best way to relax." </p>';
        html += '<hr>';
        html += '<h1 style="text-align: center;"><i class="fa-brands fa-playstation"></i></h1>';
        html += '</div>';
        html += '</div>';
        $('.body_login').html(html);
        $('.body_login').show();
    }

    const errorCb = (error) => {
        console.error(error);
    }

    // $(document).on('click', '.lokasi_saya', function(e) {
    //     e.preventDefault();


    navigator.geolocation.getCurrentPosition(successCb, errorCb, {
        enableHighAccuracy: true,
        maximumAge: 0
    });

    //     $(this).remove();

    // })
</script>

<?= $this->endSection() ?>