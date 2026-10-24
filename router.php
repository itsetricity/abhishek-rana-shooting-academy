<?php
// Development router. Apache uses .htaccess for the same boundary.
$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/');
if (preg_match('#^/(assets|uploads)/[a-zA-Z0-9_./-]+\.(css|js|png|jpe?g|webp|svg|woff2|ttf|ico)$#i', $path) && !str_contains($path, '..')) {
    if (is_file(__DIR__ . $path)) return false;
}
if (preg_match('#^/(?:app|storage|docs|tests|tmp|\.)(?:/|$)#', $path) || preg_match('#\.(env|pdf|md|sqlite3?|db|log|json|ini|bak|sql)$#i', $path) || $path === '/router.php') {
    http_response_code(403); exit('Forbidden');
}
require __DIR__ . '/index.php';
