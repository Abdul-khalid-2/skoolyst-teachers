<?php
/**
 * Global application configuration.
 *
 * This file itself is safe to commit — it contains no secrets. Real,
 * per-environment values (DB credentials, SMTP credentials) live in a
 * ".env" file at the project root, which is gitignored and never pushed.
 * Copy .env.example to .env and fill in your real values there.
 */

require __DIR__ . '/../core/Env.php';
Env::load(dirname(__DIR__) . '/.env');

// ----- Environment -----
define('APP_ENV', Env::get('APP_ENV', 'production'));   // 'local' | 'production'
define('APP_DEBUG', Env::get('APP_DEBUG', false));       // true shows PHP errors, keep false in production

// ----- Base URL -----
// Leave $FORCE_BASE_URL as null to auto-detect (recommended — this makes the
// app work whether it's installed at your domain root in production, or
// inside a subfolder on localhost, e.g.
// http://localhost/projects/teacher-portfolio/).
// Only set FORCE_BASE_URL in .env if auto-detection is wrong for your setup
// (e.g. behind a reverse proxy/CDN that rewrites the host).
$FORCE_BASE_URL = Env::get('FORCE_BASE_URL', null); // e.g. 'https://teachers.skoolyst.com'

if ($FORCE_BASE_URL) {
    define('BASE_URL', rtrim($FORCE_BASE_URL, '/'));
} else {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ($_SERVER['SERVER_PORT'] ?? '') == 443
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    $protocol = $isHttps ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Folder the app lives in relative to the web server's document root,
    // e.g. "/projects/teacher-portfolio" on localhost, or "" at a domain root.
    $scriptDir = isset($_SERVER['SCRIPT_NAME']) ? str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])) : '';
    $basePath = ($scriptDir === '/' || $scriptDir === '\\' || $scriptDir === '.') ? '' : rtrim($scriptDir, '/');
    define('BASE_URL', $protocol . '://' . $host . $basePath);
}
// The path portion of BASE_URL (e.g. "/projects/teacher-portfolio"), used by
// the router to correctly match routes when the app isn't at the domain root.
define('BASE_PATH', (string) parse_url(BASE_URL, PHP_URL_PATH));

// ----- Database -----
define('DB_HOST', Env::get('DB_HOST', 'localhost'));
define('DB_NAME', Env::get('DB_NAME', 'skoolyst_teachers'));
define('DB_USER', Env::get('DB_USER', 'root'));
define('DB_PASS', Env::get('DB_PASS', ''));
define('DB_CHARSET', Env::get('DB_CHARSET', 'utf8mb4'));

// ----- Paths -----
define('ROOT_PATH', dirname(__DIR__));
define('ASSETS_PATH', ROOT_PATH . '/assets');
define('UPLOAD_PROFILE_DIR', ASSETS_PATH . '/uploads/profile');
define('UPLOAD_RESUME_DIR', ASSETS_PATH . '/uploads/resume');
define('ASSETS_URL', BASE_URL . '/assets');

// ----- Security -----
define('SESSION_NAME', 'teacher_portfolio_sid');
define('PASSWORD_MIN_LENGTH', 8);

// ----- Uploads -----
define('MAX_PROFILE_PHOTO_SIZE', 3 * 1024 * 1024);   // 3MB
define('MAX_RESUME_SIZE', 5 * 1024 * 1024);           // 5MB
define('ALLOWED_PHOTO_TYPES', ['image/jpeg', 'image/png', 'image/webp']);
define('ALLOWED_RESUME_TYPES', ['application/pdf']);

// ----- Mail (SMTP) -----
define('MAIL_HOST', Env::get('MAIL_HOST', 'smtp.gmail.com'));
define('MAIL_PORT', (int) Env::get('MAIL_PORT', 587));
define('MAIL_ENCRYPTION', Env::get('MAIL_ENCRYPTION', 'tls'));   // 'tls' | 'ssl'
define('MAIL_USERNAME', Env::get('MAIL_USERNAME', ''));           // SMTP auth username
define('MAIL_PASSWORD', Env::get('MAIL_PASSWORD', ''));           // SMTP auth password / app password
define('MAIL_FROM_ADDRESS', Env::get('MAIL_FROM_ADDRESS', 'no-reply@skoolyst.com'));
define('MAIL_FROM_NAME', Env::get('MAIL_FROM_NAME', 'Skoolyst Teachers'));

// ----- Error reporting -----
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

// ----- Session -----
if (session_status() === PHP_SESSION_NONE) {
    session_name(SESSION_NAME);
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

date_default_timezone_set('Asia/Karachi');
