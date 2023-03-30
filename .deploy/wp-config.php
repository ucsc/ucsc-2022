<?php

function fromenv($key, $default = null) {
  $value = getenv($key);
  if ($value === false) {
    $value = $default;
  }
  return $value;
}

// Composer Autoload
require_once __DIR__ . '/vendor/autoload.php';

// Move the location of the content dir
define('WP_CONTENT_DIR', dirname(__FILE__).'/wp-content');

// Database settings
$DSN = parse_url(fromenv('DATABASE_URL', ''));

define('DB_NAME', substr($DSN['path'], 1));
define('DB_USER', $DSN['user']);
define('DB_PASSWORD', $DSN['pass']);
define('DB_HOST', $DSN['host']);
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');

define('WP_DEBUG', (bool)fromenv('WP_DEBUG', false));

// S3-Uploads Plugin
define( 'S3_UPLOADS_BUCKET', fromenv('S3_UPLOADS_BUCKET', ''));
define( 'S3_UPLOADS_REGION', fromenv('S3_UPLOADS_REGION', 'us-west-2'));
define( 'S3_UPLOADS_KEY', fromenv('S3_UPLOADS_KEY', ''));
define( 'S3_UPLOADS_SECRET', fromenv('S3_UPLOADS_SECRET', ''));

// Table WP Prefix
$table_prefix  = fromenv('TABLE_PREFIX', 'wp_');

// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
if ( isset($_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' )
{
    $_SERVER['HTTPS'] = 'on';
}

// Absolute path to the WordPress directory.
if ( !defined('ABSPATH') )
  define('ABSPATH', dirname(__FILE__) . '/wp/');

// Sets up WordPress vars and included files.
require_once(ABSPATH . 'wp-settings.php');
