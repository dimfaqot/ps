<!DOCTYPE html>
<html>

<head>
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://kit.fontawesome.com/a193ca89ae.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <?= view('functions_js'); ?>
    <title>ABSEN</title>
</head>

<body>
    <div class="mt-5" style="padding: 0% 5%;">
        <div class="lokasimu"></div>
        <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-secondary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-success" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-danger" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-info" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <div class="spinner-grow text-dark" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
    <div style="padding: 0% 5%;"><?= date('d/m/Y H:i:s'); ?></div>
    <div class="map_lokasi_saya" style="padding: 0% 5%;"></div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        let lat_bawah = parseFloat(-7.4412100);
        let lat_atas = parseFloat(-7.4410950);
        let long_atas = parseFloat(111.0364900);
        let long_bawah = parseFloat(111.035000);


        const successCb = (position) => {

            // console.log(111.0360068 < long_atas);
            // console.log(111.0360068 > long_bawah);
            let maphtml = '';
            let latitude = position.coords.latitude;
            let longitude = position.coords.longitude;

            // console.log(latitude + ' > ' + lat_atas + ' = ' + (latitude > lat_bawah));
            // console.log(latitude + ' < ' + lat_atas + ' = ' + (latitude > lat_atas));
            // console.log(longitude + ' > ' + long_atas + ' = ' + (longitude > long_atas));
            // console.log(longitude + ' < ' + long_bawah + ' = ' + (longitude > long_bawah));
            maphtml += '<p>Latitude: ' + latitude + ' Longitude: ' + longitude + '</p>';
            maphtml += '<iframe width="100%" height="600" src="https://maps.google.com/maps?q=' + latitude + ',' + longitude + '&amp;z=15&amp;output=embed"></iframe>';
            $('.map_lokasi_saya').html(maphtml);
            if (latitude < lat_atas && latitude > lat_bawah && longitude < long_atas && longitude > lat_bawah) {
                $('.lokasimu').html('<h3 class="lokasimu" style="color:green"><i class="fa-solid fa-circle-check"></i> KAMU BERADA DALAM AREA!</h3>');
                setTimeout(() => {
                    const d = new Date();
                    let time = d.getTime();
                    let data = {
                        latitude,
                        longitude,
                        id: '<?= session('id'); ?>',
                        time
                    }

                    post('absen/encode', {
                        data
                    }).then(res => {
                        if (res.status == '200') {
                            window.location.href = "<?= base_url('presentation/'); ?>" + res.data;
                        }
                    })
                }, 2000);

            } else {

                $('.lokasimu').html('<h3 style="color:red"><i class="fa-solid fa-triangle-exclamation"></i> LOKASIMU DI LUAR AREA!</h3>');


            }

            // if (longitude > long_atas && longitude < lat_bawah) {
            //     $('.body_login').text("(long) LOKASIMU DI LUAR AREA!.");
            //     return false;
            // }


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
    </script>
</body>

</html>