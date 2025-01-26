<?php
$db = db('menu_tap');
$group = $db->where('role', $role)->get()->getResultArray();
?>
<div></div>