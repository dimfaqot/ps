<?= $this->extend('guest') ?>

<?= $this->section('content') ?>

<script>
    async function getData() {

        const url = "https://api.callmebot.com/whatsapp.php?phone=62895346286566&text=jjk&apikey=8234961";
        try {
            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Response status: ${response.status}`);
            }

            const json = await response.json();
            console.log(json);
        } catch (error) {
            console.error(error.message);
        }
    }
    getData();
</script>
<?= $this->endSection() ?>