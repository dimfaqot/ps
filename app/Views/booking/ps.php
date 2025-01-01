<?php

// 04812
// 04812
// 15913
// 261014
// 371115
// 3 7 11 15
$db = db('unit');
$q = $db->whereNotIn('status', ['Maintenance'])->orderBy('id', 'ASC')->get()->getResultArray();
$dbr = db('rental');
$ps = [];
foreach ($q as $i) {
    $qr = $dbr->where('unit_id', $i['id'])->where('is_active', 1)->get()->getRowArray();
    if ($qr) {
        $i['is_active'] = 1;
    } else {
        $i['is_active'] = 0;
    }
    $exp = explode(' ', $i['unit']);
    $i['meja'] = (int)end($exp);
    $ps[] = $i;
}

?>

<?php foreach ($ps as $ky => $m): ?>
    <?php if ($ky % 4 == 0): ?>
        <div class="d-flex justify-content-center gap-2 my-2">
        <?php endif; ?>
        <div data-meja="<?= $m['meja']; ?>" class="rounded-circle embos2 text-center body_content fw-bold meja <?= ($m['is_active'] == 1 ? 'active' : 'default'); ?>" data-meja="<?= $m['meja']; ?>" data-is_active="<?= $m['is_active']; ?>" style="cursor:pointer;padding:13px 5px 5px 6px;font-size:35px;width: 85px;height:85px;color:#7c6f3e;border:1px solid #fce882">
            <div class="text-center" style="font-size:9px;margin-bottom:-13px">MEJA</div><?= $m['meja']; ?>
            <div class="text-center div_durasi_<?= $m['meja']; ?>" style="font-size:9px;margin-top:-5px"><?= ($m['is_active'] == 1 ? "1h 58m" : "0h 0m"); ?></div>
        </div>
        <?php if ($ky % 4 == 3): ?>
        </div>
    <?php endif; ?>
<?php endforeach; ?>