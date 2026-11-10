<?php
// Caminhos relativos à página: funcionam também em /Raia7/app/.
$faviconVersion = (string) filemtime(__DIR__ . '/../assets/favicon/favicon.ico');
?>
<link rel="icon" type="image/x-icon" href="assets/favicon/favicon.ico?v=<?= $faviconVersion ?>">
<link rel="icon" type="image/png" sizes="96x96" href="assets/favicon/favicon-96x96.png?v=<?= $faviconVersion ?>">
<link rel="icon" type="image/svg+xml" href="assets/favicon/favicon.svg?v=<?= $faviconVersion ?>">
<link rel="apple-touch-icon" sizes="180x180" href="assets/favicon/apple-touch-icon.png?v=<?= $faviconVersion ?>">
<link rel="manifest" href="assets/favicon/site.webmanifest?v=<?= $faviconVersion ?>">
