<?php
require dirname(__DIR__) . '/app/bootstrap.php';
header('Content-Type: application/xml; charset=utf-8');
echo '<?xml version="1.0" encoding="UTF-8"?>', "\n", '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
foreach (array_merge([''], array_keys(actieve_installateurs()), ['privacy/']) as $p) {
    echo '  <url><loc>', e(url($p)), '</loc></url>', "\n";
}
echo '</urlset>', "\n";
