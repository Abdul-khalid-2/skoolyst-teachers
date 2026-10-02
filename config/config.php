<?php
/**
 * Global application configuration.
 *
 * This file itself is safe to commit — it contains no secrets. Real,
 * per-environment values (DB credentials, API keys) live in a
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

// ----- Support contact -----
// Shown on the Privacy Policy and Terms pages (and used by Google's OAuth
// consent screen review), so it must be a mailbox someone actually reads.
define('SUPPORT_EMAIL', Env::get('SUPPORT_EMAIL', 'admin@skoolyst.com'));

// ----- Email (Skoolyst Email API) -----
// Mail is sent through the shared service at ads.skoolyst.com. Get a key
// from Admin -> Email Accounts -> API Clients there; EMAIL_SOURCE_APP must
// match the app name the key was issued for. Keep the key server-side only.
define('EMAIL_API_BASE', Env::get('EMAIL_API_BASE', 'https://ads.skoolyst.com/api/v1'));
define('EMAIL_API_KEY', (string) Env::get('SKOOLYST_EMAIL_API_KEY', ''));
define('EMAIL_SOURCE_APP', Env::get('EMAIL_SOURCE_APP', 'skoolyst-teachers'));

// ----- Login with Skoolyst (central SSO at skoolyst.com) -----
// Get the client id/secret from skoolyst.com -> Dashboard -> Connected Apps.
// SKOOLYST_AUTH_REDIRECT_URI must match the registered redirect URL
// byte-for-byte; it defaults to this app's /auth/skoolyst/callback route.
define('SKOOLYST_AUTH_BASE', Env::get('SKOOLYST_AUTH_BASE', 'https://skoolyst.com'));
define('SKOOLYST_AUTH_CLIENT_ID', (string) Env::get('SKOOLYST_AUTH_CLIENT_ID', ''));
define('SKOOLYST_AUTH_CLIENT_SECRET', (string) Env::get('SKOOLYST_AUTH_CLIENT_SECRET', ''));
define('SKOOLYST_AUTH_REDIRECT_URI', Env::get('SKOOLYST_AUTH_REDIRECT_URI', BASE_URL . '/auth/skoolyst/callback'));

// ----- Continue with Google (this app's own Google Cloud OAuth client) -----
// Google Cloud Console -> APIs & Services -> Credentials -> OAuth client ID
// (Web application). GOOGLE_REDIRECT_URI must be listed there EXACTLY under
// "Authorized redirect URIs"; it defaults to this app's /auth/google/callback.
define('GOOGLE_CLIENT_ID', (string) Env::get('GOOGLE_CLIENT_ID', ''));
define('GOOGLE_CLIENT_SECRET', (string) Env::get('GOOGLE_CLIENT_SECRET', ''));
define('GOOGLE_REDIRECT_URI', Env::get('GOOGLE_REDIRECT_URI', BASE_URL . '/auth/google/callback'));

// ----- AdEngine (Skoolyst Ads) -----
// Register this app first at https://ads.skoolyst.com/admin/apps.php to
// get an API key and define placement codes, then fill these in .env.
define('ADS_API_BASE', Env::get('ADS_API_BASE', 'https://ads.skoolyst.com/api/v1'));
define('ADS_API_KEY', Env::get('ADS_API_KEY', ''));
define('ADS_PLACEMENT_HOME_TOP', Env::get('ADS_PLACEMENT_HOME_TOP', 'home_top'));

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
