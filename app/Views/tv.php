<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <script src="https://kit.fontawesome.com/a193ca89ae.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        .lingkaran {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            font-weight: bold;
            margin: 25px;
            text-align: center;
            line-height: 1.2;
            font-size: 0.8rem;
            padding: 30px;
            overflow: hidden;
        }


        .running-text {
            white-space: nowrap;
            overflow: hidden;
            display: inline-block;
            animation: scroll 30s linear infinite;
        }


        @keyframes scroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }
    </style>
</head>

<body class="bg-dark text-light pt-4">
    <div class="main_body" style="display:none">

    </div>


    <div class="fixed-bottom bg-white text-dark p-2">
        <div class="running-text">
            <span id="text" class="fw-bold fs-5"></span>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    <script>
        async function post(url = '', data = {}) {

            const response = await fetch("<?= base_url(); ?>" + url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            });

            return response.json(); // parses JSON response into native JavaScript objects
        }
        // $(document).ready(function() {
        //     function rotateDivs() {
        //         $("#rotatingDiv1").fadeIn(5000).delay(2000).fadeOut(5000);
        //         $("#rotatingDiv2").delay(30000).fadeIn(5000).delay(2000).fadeOut(5000);
        //         $("#rotatingDiv3").delay(6000).fadeIn(5000).delay(2000).fadeOut(5000, function() {
        //             rotateDivs(); // Repeat
        //         });
        //     }
        //     rotateDivs();
        // });

        let running_text = "";

        const main_body = (order) => {

            post("ext/tv", {
                order
            }).then(res => {
                if (res.data3 !== null) {
                    running_text = res.data3;

                }
                let html = ``;
                if (order == "Ps" || order == "Billiard") {

                    html += ` <div class="row g-0 text-center">
                        <div class="col-md-8">
                            <h1>${order.toUpperCase()}</h1>
                            <div class="row">`;
                    res.data.forEach(e => {
                        html += `<div class="col">
                                            <div class="lingkaran ${e.text}">
                                                <h6>MEJA</h6>
                                                <h2>${e.meja}</h2>
                                                <p>${e.status}</p>
                                                <h4>${e.durasi}</h4>
                                            </div>
                                        </div>`;
                    });
                    html += `</div>

                        </div>
                        <div class="col-md-4">
                            <h1>WAITING LISTS</h1>
                            <div class="table-responsive-lg" style="margin-top: -40px;">
                                <table class="table table-dark mt-5">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Nama</th>
                                            <th scope="col">Meja</th>
                                            <th scope="col">Jam</th>
                                        </tr>
                                    </thead>
                                    <tbody>`
                    res.data2.forEach((e, i) => {
                        html += `<tr>
                                                    <th scope="row">${(i+1)}</th>
                                                    <td class="text-start">${e.nama}</td>
                                                    <td>${e.meja}</td>
                                                    <td>${e.jam}</td>
                                                </tr>`;

                    })
                    html += `</tbody>
                                </table>
                            </div>

                        </div>
                    </div>`;
                } else {
                    html += `<div style="background-image: url('${res.data}');background-size: cover;background-position: center;background-repeat: no-repeat;height: 100%;width: 100%;position: fixed;top: 0;left: 0;"></div>`;
                }

                $(".main_body").html(html);
                $(".main_body").fadeIn(2000).delay(5000).fadeOut(2000);
            })


        }
        const orders = ["Billiard", "Ps", "Image"];
        let index = 0;

        setInterval(() => {
            index = (index + 1) % orders.length;
            main_body(orders[index]);

        }, 9000);

        setInterval(() => {
            text = running_text + " - ";

            console.log(text);
            let repeatedText = text.repeat(20); // Mengurangi pengulangan untuk memperlambat efek
            $('#text').text(repeatedText);


        }, 3000);
    </script>
</body>

</html>