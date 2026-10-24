<?php
declare(strict_types=1);

date_default_timezone_set('Asia/Kolkata');
define('ROOT', dirname(__DIR__));
$envPath = ROOT . '/.env';
$env = [];
foreach (is_file($envPath) ? file($envPath, FILE_IGNORE_NEW_LINES) : [] as $line) {
    if (!preg_match('/^\s*([A-Z_][A-Z0-9_]*)\s*=\s*(.*?)\s*$/', $line, $match)) continue;
    $value = $match[2];
    if (strlen($value) >= 2 && (($value[0] === '"' && str_ends_with($value, '"')) || ($value[0] === "'" && str_ends_with($value, "'")))) $value = substr($value, 1, -1);
    $env[$match[1]] = $value;
}
function env(string $key, string $default = ''): string { global $env; return (string)($env[$key] ?? $default); }
if (PHP_SAPI !== 'cli') {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', ROOT . '/storage/php.log');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
    header("Content-Security-Policy: default-src 'self'; img-src 'self' data:; style-src 'self'; script-src 'self'; font-src 'self'; connect-src 'self'; frame-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'self'");
    session_name('arsa_session');
    session_set_cookie_params(['httponly'=>true, 'secure'=>!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', 'samesite'=>'Lax', 'path'=>'/']);
    session_start();
}

function db(): PDO {
    static $db;
    if ($db) return $db;
    $file = getenv('ARSA_DB_PATH') ?: ROOT . '/storage/academy.sqlite';
    $db = new PDO('sqlite:' . $file, null, null, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
    $db->exec('PRAGMA foreign_keys=ON; PRAGMA busy_timeout=5000;');
    $db->exec('CREATE TABLE IF NOT EXISTS content (id INTEGER PRIMARY KEY AUTOINCREMENT, type TEXT NOT NULL, slug TEXT NOT NULL, title TEXT NOT NULL, status TEXT NOT NULL DEFAULT "draft", sort_order INTEGER NOT NULL DEFAULT 0, data TEXT NOT NULL, updated_at TEXT NOT NULL, UNIQUE(type,slug));
        CREATE TABLE IF NOT EXISTS enquiries (id INTEGER PRIMARY KEY AUTOINCREMENT, reference TEXT NOT NULL UNIQUE, kind TEXT NOT NULL, name TEXT NOT NULL, email TEXT NOT NULL, phone TEXT NOT NULL, branch TEXT, program TEXT, experience TEXT, message TEXT, preferred_date TEXT, preferred_time TEXT, status TEXT NOT NULL DEFAULT "New", booking_status TEXT NOT NULL DEFAULT "Requested", scheduled_at TEXT, coach TEXT, notes TEXT NOT NULL DEFAULT "", delivery TEXT NOT NULL DEFAULT "[]", consent_at TEXT NOT NULL, created_at TEXT NOT NULL);
        CREATE TABLE IF NOT EXISTS audit_log (id INTEGER PRIMARY KEY AUTOINCREMENT, actor TEXT NOT NULL, action TEXT NOT NULL, details TEXT NOT NULL, created_at TEXT NOT NULL);
        CREATE TABLE IF NOT EXISTS media (id INTEGER PRIMARY KEY AUTOINCREMENT, path TEXT NOT NULL UNIQUE, alt TEXT NOT NULL, created_at TEXT NOT NULL);
        CREATE TABLE IF NOT EXISTS rate_limits (key TEXT PRIMARY KEY, attempts INTEGER NOT NULL, started INTEGER NOT NULL);
        CREATE INDEX IF NOT EXISTS idx_content_listing ON content(type,status,sort_order);
        CREATE INDEX IF NOT EXISTS idx_enquiries_kind_date ON enquiries(kind,created_at DESC);
        CREATE INDEX IF NOT EXISTS idx_enquiries_status ON enquiries(status);');
    return $db;
}
function query(string $sql, array $params = []): PDOStatement { $st = db()->prepare($sql); $st->execute($params); return $st; }
function e(mixed $value): string { return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function now(): string { return date('Y-m-d H:i:s'); }
function redirect(string $url): never { header('Location: ' . $url, true, 303); exit; }
function token(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf(): string { return '<input type="hidden" name="csrf" value="'.e(token()).'">'; }
function valid_csrf(): bool { return isset($_POST['csrf']) && is_string($_POST['csrf']) && hash_equals(token(), $_POST['csrf']); }
function is_admin(): bool {
    if (empty($_SESSION['admin']) || ($_SESSION['last_active'] ?? 0) < time() - 28800) return false;
    $_SESSION['last_active'] = time(); return true;
}
function audit(string $action, string $details): void { query('INSERT INTO audit_log(actor,action,details,created_at) VALUES(?,?,?,?)', [$_SESSION['admin'] ?? 'Website visitor', $action, $details, now()]); }
function flash(string $message, string $type = 'success'): void { $_SESSION['flash'] = [$message,$type]; }
function flash_html(): string { if (!isset($_SESSION['flash'])) return ''; [$message,$type] = $_SESSION['flash']; unset($_SESSION['flash']); return '<div class="flash '.e($type).'" role="status">'.e($message).'</div>'; }
function limited(string $key, int $max, int $seconds): bool {
    $key = hash('sha256', $key);
    query('INSERT INTO rate_limits(key,attempts,started) VALUES(?,1,?) ON CONFLICT(key) DO UPDATE SET attempts=CASE WHEN started < ? THEN 1 ELSE attempts+1 END, started=CASE WHEN started < ? THEN excluded.started ELSE started END', [$key,time(),time()-$seconds,time()-$seconds]);
    return (int)query('SELECT attempts FROM rate_limits WHERE key=?', [$key])->fetchColumn() > $max;
}
function records(string $type, bool $all = false): array {
    $rows = query('SELECT * FROM content WHERE type=?' . ($all ? '' : " AND status='published'") . ' ORDER BY sort_order,id', [$type])->fetchAll();
    return array_map('decode_record', $rows);
}
function decode_record(array $row): array { $row['fields'] = json_decode($row['data'], true) ?: []; return $row; }
function record(string $type, string $slug, bool $all = false): ?array {
    $r=query('SELECT * FROM content WHERE type=? AND slug=?' . ($all ? '' : " AND status='published'"),[$type,$slug])->fetch(); return $r ? decode_record($r) : null;
}
function settings(string $slug = 'site'): array { return (record('settings', $slug, true)['fields'] ?? []); }
function field(array $record, string $key, string $default = ''): string { return (string)($record['fields'][$key] ?? $default); }
function paragraphs(string $text): string { $parts = preg_split('/\n\s*\n/', trim($text)); return implode('', array_map(fn($p)=>'<p>'.nl2br(e($p)).'</p>', array_filter($parts))); }
function slugify(string $text): string { return trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($text)), '-') ?: 'item'; }
function image_path(string $path): string { return preg_match('#^/(?:uploads/[a-f0-9]+\.(?:jpg|png|webp)|assets/images/[a-zA-Z0-9_.-]+\.(?:jpg|jpeg|png|webp|svg))$#', $path) ? $path : ''; }
function safe_link(string $url): string {
    if (preg_match('~^/(?!/)[a-zA-Z0-9/?=&_.%#-]*$~', $url)) return $url;
    return filter_var($url,FILTER_VALIDATE_URL) && in_array(strtolower(parse_url($url,PHP_URL_SCHEME) ?? ''),['https','http'],true) ? $url : '';
}
function format_date(?string $date, string $format = 'd M Y'): string { return $date && strtotime($date) ? date($format,strtotime($date)) : '—'; }
function icon(string $name): string {
    $paths=[
      'arrow'=>'<path d="M5 12h14m-6-6 6 6-6 6"/>',
      'target'=>'<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1"/>',
      'grid'=>'<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>',
      'page'=>'<path d="M14 3H5v18h14V8l-5-5v5h5M8 12h8M8 16h6"/>',
      'pin'=>'<path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
      'users'=>'<circle cx="9" cy="8" r="3"/><path d="M3 21v-4a6 6 0 0 1 12 0v4m1-16a3 3 0 0 1 0 6m2 3a5 5 0 0 1 3 5v2"/>',
      'medal'=>'<circle cx="12" cy="9" r="6"/><path d="m8 14-2 8 6-3 6 3-2-8"/>',
      'image'=>'<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8" cy="8" r="1"/><path d="m3 17 5-5 4 4 4-6 5 7"/>',
      'calendar'=>'<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 2v6m10-6v6M3 11h18m-13 5h3"/>',
      'mail'=>'<rect x="3" y="5" width="18" height="15" rx="2"/><path d="m3 6 9 7 9-7"/>',
      'settings'=>'<circle cx="12" cy="12" r="3"/><path d="m9 3-1 3-3 1-2 3 2 2-1 3 3 2 2 4h4l1-3 3-1 3-3-2-3 1-3-3-2-2-3Z"/>',
      'check'=>'<path d="m5 12 4 4L19 6"/>',
      'chevron'=>'<path d="m9 5 7 7-7 7"/>',
      'clock'=>'<circle cx="12" cy="12" r="9"/><path d="M12 7v6l4 2"/>',
      'logout'=>'<path d="M10 4H4v16h6m4-13 5 5-5 5m-6-5h13"/>',
      'plus'=>'<path d="M12 5v14M5 12h14"/>',
      'external'=>'<path d="M14 3h7v7m0-7L10 14M9 3H3v18h18v-6"/>',
      'shield'=>'<path d="m12 2 9 4v6c0 6-9 10-9 10S3 18 3 12V6l9-4Z"/><path d="m8 12 3 3 5-6"/>',
      'menu'=>'<path d="M4 6h16M4 12h16M4 18h16"/>',
      'download'=>'<path d="M12 3v12m-5-5 5 5 5-5M4 16v5h16v-5"/>',
    ];
    return '<svg class="icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">'.($paths[$name] ?? $paths['target']).'</svg>';
}

require __DIR__ . '/schema.php';
require __DIR__ . '/website-defaults.php';
if (!(int)query('SELECT count(*) FROM content')->fetchColumn()) require __DIR__ . '/seed.php';
require_once __DIR__.'/content-upgrades.php';
upgrade_deployed_content();
