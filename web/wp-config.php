<?php
/**
 * WordPress configuration — dotenv with DDEV fallback.
 *
 * Load order:
 *   1. DDEV settings (if running in DDEV — sets DB creds, WP_HOME, etc.)
 *   2. .env file from project root (fills anything DDEV didn't set)
 *   3. Hardcoded defaults as final fallback
 */

// DDEV settings first — they take priority in the dev environment
$ddev_settings = __DIR__ . '/wp-config-ddev.php';
if (getenv('IS_DDEV_PROJECT') == 'true' && is_readable($ddev_settings)) {
    require_once $ddev_settings;
}

// Composer autoloader (one level up from web/)
$autoloader = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
}

// Load .env from project root (one level up from web/)
$dotenvPath = dirname(__DIR__);
if (class_exists(\Dotenv\Dotenv::class) && file_exists($dotenvPath . '/.env')) {
    $dotenv = \Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->safeLoad();
}

/**
 * Read from $_ENV, $_SERVER, getenv(), or return a default.
 */
function env(string $key, mixed $default = null): mixed
{
    if (isset($_ENV[$key])) return $_ENV[$key];
    if (isset($_SERVER[$key])) return $_SERVER[$key];
    $val = getenv($key);
    return $val !== false ? $val : $default;
}

/** Database */
defined('DB_NAME')     || define('DB_NAME',     env('DB_NAME', 'wordpress'));
defined('DB_USER')     || define('DB_USER',     env('DB_USER', 'root'));
defined('DB_PASSWORD') || define('DB_PASSWORD', env('DB_PASSWORD', ''));
defined('DB_HOST')     || define('DB_HOST',     env('DB_HOST', 'localhost'));
defined('DB_CHARSET')  || define('DB_CHARSET',  env('DB_CHARSET', 'utf8'));
defined('DB_COLLATE')  || define('DB_COLLATE',  env('DB_COLLATE', ''));
$table_prefix = env('DB_PREFIX', 'wp_');

/** URLs */
defined('WP_HOME')    || define('WP_HOME',    env('WP_HOME', 'https://localhost'));
defined('WP_SITEURL') || define('WP_SITEURL', env('WP_SITEURL', WP_HOME));

/** Debug */
defined('WP_DEBUG')         || define('WP_DEBUG', filter_var(env('WP_DEBUG', false), FILTER_VALIDATE_BOOLEAN));
defined('WP_DEBUG_LOG')     || define('WP_DEBUG_LOG', filter_var(env('WP_DEBUG_LOG', false), FILTER_VALIDATE_BOOLEAN));
defined('WP_DEBUG_DISPLAY') || define('WP_DEBUG_DISPLAY', filter_var(env('WP_DEBUG_DISPLAY', false), FILTER_VALIDATE_BOOLEAN));

/** Authentication Keys and Salts */
defined('AUTH_KEY')         || define('AUTH_KEY',         env('AUTH_KEY',         'put-your-unique-phrase-here'));
defined('SECURE_AUTH_KEY')  || define('SECURE_AUTH_KEY',  env('SECURE_AUTH_KEY',  'put-your-unique-phrase-here'));
defined('LOGGED_IN_KEY')    || define('LOGGED_IN_KEY',    env('LOGGED_IN_KEY',    'put-your-unique-phrase-here'));
defined('NONCE_KEY')        || define('NONCE_KEY',        env('NONCE_KEY',        'put-your-unique-phrase-here'));
defined('AUTH_SALT')        || define('AUTH_SALT',        env('AUTH_SALT',        'put-your-unique-phrase-here'));
defined('SECURE_AUTH_SALT') || define('SECURE_AUTH_SALT', env('SECURE_AUTH_SALT', 'put-your-unique-phrase-here'));
defined('LOGGED_IN_SALT')   || define('LOGGED_IN_SALT',  env('LOGGED_IN_SALT',   'put-your-unique-phrase-here'));
defined('NONCE_SALT')       || define('NONCE_SALT',       env('NONCE_SALT',       'put-your-unique-phrase-here'));

/* That's all, stop editing! */

defined('ABSPATH') || define('ABSPATH', __DIR__ . '/');

require_once ABSPATH . 'wp-settings.php';
