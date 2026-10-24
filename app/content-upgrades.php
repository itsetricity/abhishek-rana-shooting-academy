<?php
// Database content is private runtime state and is never deployed through Git.
// Apply each release's content upgrade once, after a protected SQLite snapshot.
function upgrade_deployed_content(): void {
    if (record('settings','research-2026-10',true)) return;
    $database = getenv('ARSA_DB_PATH') ?: ROOT.'/storage/academy.sqlite';
    $backupDirectory = dirname($database).'/backups';
    if (!is_dir($backupDirectory) && !mkdir($backupDirectory,0700,true) && !is_dir($backupDirectory)) {
        throw new RuntimeException('Cannot create the protected content-upgrade backup directory.');
    }
    $backup = $backupDirectory.'/academy-before-research-'.date('Ymd-His').'-'.bin2hex(random_bytes(4)).'.sqlite';
    query('VACUUM INTO ?',[$backup]);
    chmod($backup,0600);
    require_once __DIR__.'/sporting-update.php';
    require_once __DIR__.'/research-update.php';
    db()->exec('BEGIN IMMEDIATE');
    try {
        // Another request may have completed the upgrade while this one waited.
        if (!record('settings','research-2026-10',true)) {
            apply_sporting_update();
            apply_research_update();
            audit('Applied verified academy content upgrade','research-2026-10; database snapshot: '.basename($backup));
        }
        db()->exec('COMMIT');
    } catch (Throwable $error) {
        db()->exec('ROLLBACK');
        throw $error;
    }
}
