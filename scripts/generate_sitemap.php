<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/env.php';

header('Content-Type: application/xml; charset=UTF-8');

$baseUrl = rtrim($APP_URL ?? ($_ENV['APP_URL'] ?? ''), '/');
if ($baseUrl === '') {
    $scheme  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host    = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $baseUrl = rtrim($scheme . $host, '/');
}

$urls = [
    ['loc' => $baseUrl . '/',          'changefreq' => 'weekly',  'priority' => '1.0'],
    ['loc' => $baseUrl . '/productos', 'changefreq' => 'daily',   'priority' => '0.9'],
    ['loc' => $baseUrl . '/nosotras',  'changefreq' => 'monthly', 'priority' => '0.6'],
    ['loc' => $baseUrl . '/contacto',  'changefreq' => 'monthly', 'priority' => '0.6'],
];

$sql = "SELECT slug, updated_at FROM productos WHERE activo = 1 ORDER BY id DESC";
foreach ($pdo->query($sql, PDO::FETCH_ASSOC) as $row) {
    $urls[] = [
        'loc'        => $baseUrl . '/producto/' . $row['slug'],
        'lastmod'    => !empty($row['updated_at']) ? date('c', strtotime($row['updated_at'])) : null,
        'changefreq' => 'weekly',
        'priority'   => '0.7',
    ];
}

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"https://www.sitemaps.org/schemas/sitemap/0.9\">\n";

foreach ($urls as $entry) {
    echo "  <url>\n";
    echo "    <loc>" . htmlspecialchars($entry['loc'], ENT_QUOTES, 'UTF-8') . "</loc>\n";
    if (!empty($entry['lastmod'])) {
        echo "    <lastmod>{$entry['lastmod']}</lastmod>\n";
    }
    if (!empty($entry['changefreq'])) {
        echo "    <changefreq>{$entry['changefreq']}</changefreq>\n";
    }
    if (!empty($entry['priority'])) {
        echo "    <priority>{$entry['priority']}</priority>\n";
    }
    echo "  </url>\n";
}

echo "</urlset>";
