<script>
    const base_url = "<?= base_url(); ?>";
    let lokasi = "<?= (!session('lokasi') ? strtolower($lokasi) : strtolower(session('lokasi'))); ?>";
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

    const show_modal = (id = "fullscreen", order = "show") => {
        let myModal = document.getElementById(id);
        let modal = bootstrap.Modal.getOrCreateInstance(myModal);
        if (order == "show") {
            modal.show();
        } else {
            modal.hide();
        }
    }

    const header_modal = (close, judul) => {
        let html = "";

        html += '<div class="text-center">';

        if (close == "loading") {
            html += '<div class="spinner-border text-light" role="status">';
            html += '<span class="visually-hidden">Loading...</span>';
            html += '</div>';
        } else {
            html += '<div data-modal_id="fullscreen" class="text-danger btn_close_modal" style="cursor:pointer;font-size: x-large;"><i class="fa-solid fa-circle-xmark"></i></div>';
        }

        if (judul !== "" && judul !== undefined) {
            html += '<div class="d-flex justify-content-center mt-5 mb-3">';
            html += '<div class="text-warning fw-bold pb-2 px-3 border-bottom border-info" style="font-size: medium;">' + judul + '</div>';
            html += '</div>';
        }
        html += '</div>';
        return html;
    }
    let session_now = 0;
    let menunggu = 0;
    let interval_countdown;
    const countdown = (seconds = 20, logout = "yes", id = "fullscreen", redirect = "rfid") => {
        let x = 1;
        interval_countdown = setInterval(() => {
            x++;
            if (x > seconds) {
                $(".header_" + id).html(header_modal("loading"));
                let html = '<h6 class="text-center text-danger" style="margin-top:300px">Waktu habis!.</h6>';
                $(".body_" + id).html(html);
                show_modal(id, "show");
                if (logout == "yes") {
                    post("rfid/logout", {
                        lokasi
                    }).then(res => {
                        if (res.status == "200") {
                            session_now = 0;
                            clearInterval(interval_countdown);
                            if (redirect !== "") {
                                window.location.href = base_url + "/" + redirect + "/" + lokasi;
                            } else {
                                show_modal(id, "hide");

                            }
                        }
                    })
                } else {
                    clearInterval(interval_countdown);
                    show_modal(id, "hide");
                    session_now = 0;
                }
                3
            } else {
                $(".body_countdown").text(x);
            }


        }, 1000);
    }
    const gagal_rfid = (message, logout = "yes", seconds = 3, id = "fullscreen") => {
        $('.header_' + id).html(header_modal('loading', "Error"));
        let html = '<div class="mt-5"></div>';
        let messages = message.split("|");
        messages.forEach(e => {
            html += '<div class="text-center text-danger">' + e + '</div>';
        })
        if (seconds !== "") {
            session_now = 1;
            html += '<div class="d-flex justify-content-center">';
            html += '<div style="cursor:pointer;font-size:35px;width: 75px;height:75px;" class="mt-5 fw-bold rounded-circle text-center pt-3 border border-secondary text-secondary body_countdown">1</div>';
            html += '</div>';
            $(".body_" + id).html(html);
            show_modal(id, "show");
            countdown(seconds, logout, id);
            return;
        }
        $(".body_" + id).html(html);
        show_modal(id, "show");

    }
    const sukses_rfid = (message, logout = "no", seconds = 3, id = "fullscreen") => {
        $('.header_' + id).html(header_modal("loading", "Menunggu"));
        let html = '<div class="mt-5"></div>';
        let messages = message.split("|");
        messages.forEach(e => {
            html += '<div class="text-center text-light">' + e + '</div>';
        })
        if (seconds !== '') {
            session_now = 1;
            html += '<div class="d-flex justify-content-center">';
            html += '<div style="cursor:pointer;font-size:35px;width: 75px;height:75px;" class="mt-5 fw-bold rounded-circle text-center pt-3 border border-secondary text-secondary body_countdown">1</div>';
            html += '</div>';
            $(".body_" + id).html(html);
            show_modal(id, "show");
            countdown(logout, seconds, id);
            return;
        }

        $(".body_" + id).html(html);
        show_modal(id, "show");

    }

    function angka(a, prefix) {
        let angka = a.toString();
        let number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }

        rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
    }
    const time_php_to_js = (date) => {
        let d = new Date(date * 1000);
        let res = d.getDate() + '/' + d.getMonth() + 1 + '/' + d.getFullYear();

        return res;
    }

    $(document).on('keyup', '.uang', function(e) {
        e.preventDefault();
        let val = $(this).val();
        $(this).val(angka(val));
    });
    $(document).on('click', '.btn_close_modal', function(e) {
        e.preventDefault();
        let modal_id = $(this).data("modal_id");
        show_modal(modal_id, "hide");
    });

    const logout = (message = "", countdown = "", modal = "", id = "fullscreen", redirect = "rfid") => {
        $(".body_btn_save").html("");
        post("rfid/logout", {
            lokasi
        }).then(res => {
            if (res.status == "200") {
                session_now = 0;
                if (countdown == "") {
                    clearInterval(interval_countdown);
                }
                if (message !== "") {
                    let html = '<div class="text-danger text-center">' + message + '</div>';
                    $(".body_" + id).html(html);
                }
                if (redirect !== "") {
                    window.location.href = base_url + "/" + redirect + "/" + lokasi;
                }

                if (modal !== "") {
                    show_modal(id, modal);
                }
            }
        })
    }

    const warning_message = (message, dur = 1, id = "warning") => {
        let html = "";
        html += '<div class="d-flex justify-content-center">';
        html += '<div class="text-danger text-center border px-5 rounded-2 border-light border-opacity-25 py-1 bg-dark">' + message + '</div>';
        html += '</div>';
        $(".body_" + id).html(html);
        show_modal(id);

        setTimeout(() => {
            show_modal(id, "hide");
        }, dur * 1000);
    }
    const konfirmasi = (order, id, message) => {
        let html = "";
        html += '<h6 class="text-center text-light">' + message + '</h6>';
        html += '<div class="d-flex justify-content-center gap-2 py-1">';
        html += '<div data-order="' + order + '" data-id="' + id + '" class="btn_konfirmasi text-center text-light px-5 border rounded-2 border-light border-opacity-25 py-1 bg-success bg-opacity-75" style="cursor: pointer;">Ok</div>';
        html += '<div data-modal_id="warning" class="btn_close_modal text-center text-light px-5 border rounded-2 border-light border-opacity-25 py-1 bg-dark bg-opacity-75" style="cursor: pointer;">Cancel</div>';
        html += '</div>';
        $(".body_warning").html(html);
    }
    const admin = (order, id) => {
        if (order == "meja") {
            konfirmasi(order, id, "Yakin akhiri permainan?");
            show_modal('warning');
            return;
        } else {
            $('.header_fullscreen').html(header_modal((order == "absen" || order == "shift" ? "loading" : "close"), order.toUpperCase()));
            $(".body_fullscreen").html('<div class="text-light text-center">Proses...</div>');
            show_modal();
        }
        if (order == "absen") {
            clearInterval(interval_countdown);
        }
        post("rfid/" + order, {
            id
        }).then(res => {
            if (res.status == "200") {
                if (order == 'absen' || order == 'perangkat' || order == 'shift') {
                    $(".body_fullscreen").html('<div class="text-light text-center">' + res.message + '</div>');
                }

                if (order == 'poin') {
                    let html = "";
                    html += '<h6 class="text-warning">POIN: ' + res.data2 + '</h6>';
                    html += '<table class="table table-sm table-border table-dark" style="font-size: small">';
                    html += '<thead>';
                    html += '<tr>';
                    html += '<td class="text-center">#</td>';
                    html += '<td class="text-center">Tgl</td>';
                    html += '<td class="text-center">Jenis</td>';
                    html += '<td class="text-center">Ket</td>';
                    html += '<td class="text-center">Poin</td>';
                    html += '</tr>';
                    html += '</thead>';
                    html += '<tbody>';
                    res.data.forEach((e, i) => {
                        html += '<tr>';
                        html += '<td style="text-align: center;">' + (i + 1) + '</td>';
                        html += '<td style="text-align: center;">' + e.tgl + '</td>';
                        html += '<td style="text-align:left">' + (e.ket == "Ontime" || e.ket == "Terlambat" || e.ket == "Ghoib" ? "Absen" : "Aturan") + '</td>';
                        html += '<td style="text-align:left">' + e.ket + '</td>';
                        html += '<td style="text-align: right;">' + e.poin + '</td>';
                        html += '</tr>';
                    })

                    html += '</tbody>';
                    html += '</table>';
                    $(".body_fullscreen").html(html);
                }
            } else {
                $(".body_fullscreen").html('<div class="text-danger text-center">' + res.message + '</div>');
            }
            if (order == "absen" || order == "shift") {
                setTimeout(() => {
                    logout("Waktu habis!.");
                }, 2000);

            }
        })
    }
</script>