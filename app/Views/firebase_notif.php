<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <div class="message" style="min-height: 80px;"></div>
    <script src="https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <!-- <script>
        const firebaseConfig = {
                apiKey: "AIzaSyCoifdOl0LCaVfaOfcc6hR4QT7rHe0hI6A",
                authDomain: "hayu-playground.firebaseapp.com",
                projectId: "hayu-playground",
                storageBucket: "hayu-playground.firebasestorage.app",
                messagingSenderId: "985888759431",
                appId: "1:985888759431:web:57f26e92d5646593e62222"
            };

             const app = initializeApp(firebaseConfig);
             const messaging=firebase.messaging();
             messaging.getToken({vapidKey:"BI_63Eu-7Ow9M_CB4djGT8IFe9KuubndzmPBZ_lrqpBin0Kq0R1nqOeI7vAYTn8yuoHK8MtCGn_suKq9nScr-1o"});
             messaging.onMessage((palyload)=>{
                console.log('Message receive: ', palyload);

                $('.message').text(JSON.stringify(palyload,null,2));
             })

    </script> -->

    <script type="module">
        // Import the functions you need from the SDKs you need
        import {
            initializeApp
        } from "https://www.gstatic.com/firebasejs/11.0.2/firebase-app.js";
        import {
            firebase
        } from "https://firebase.google.com/docs/web/setup#available-libraries";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        const firebaseConfig = {
            apiKey: "AIzaSyCoifdOl0LCaVfaOfcc6hR4QT7rHe0hI6A",
            authDomain: "hayu-playground.firebaseapp.com",
            projectId: "hayu-playground",
            storageBucket: "hayu-playground.firebasestorage.app",
            messagingSenderId: "985888759431",
            appId: "1:985888759431:web:57f26e92d5646593e62222"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        const messaging = firebase.messaging();
        messaging.getToken({
            vapidKey: "BI_63Eu-7Ow9M_CB4djGT8IFe9KuubndzmPBZ_lrqpBin0Kq0R1nqOeI7vAYTn8yuoHK8MtCGn_suKq9nScr-1o"
        });
        messaging.onMessage((palyload) => {
            console.log('Message receive: ', palyload);

            $('.message').text(JSON.stringify(palyload, null, 2));
        })
    </script>
</body>

</html>