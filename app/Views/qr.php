<?= $this->extend('guest') ?>

<?= $this->section('content') ?>
<img width="500px;" src="<?= set_qr_code(base_url('presentation/tes'), 'logo', 'Absen'); ?>" alt="Absen Playground">

<?= $this->endSection() ?>