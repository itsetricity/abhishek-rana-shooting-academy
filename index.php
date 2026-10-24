<?php
declare(strict_types=1);
require __DIR__ . '/app/bootstrap.php';
$path = rtrim(rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/'), '/') ?: '/';
if ($path === '/superadmin' || str_starts_with($path, '/superadmin/')) {
    require __DIR__ . '/app/admin.php';
} else {
    require __DIR__ . '/app/public.php';
}
