<?php
define( 'WP_CACHE', true ); // Added by WP Rocket

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );


/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '0aL4KU1Z8CgTBQc8FrzSnWNe4duUGhbAfeQXJ4e7QFVwRHsPzXYOiWrvx0MClt+7XZTqkVwXyowKVn+/Bi9Qtg==');
define('SECURE_AUTH_KEY',  '0WI+R5DziooWd3BlXFInXMfpkGfvCvJY//lFfhG9S0NupMDEKADS3a6FNAvdtJ52ichOkNamkoxasr+oCcFrlA==');
define('LOGGED_IN_KEY',    'StAAufb9ZBtckaFR0oI457zX4wgV64HNSmWUtj66r3PFAwyn9hpyVMRjFkVlvZdr2s1yp5p0O3iIUtuzD1wpxg==');
define('NONCE_KEY',        'qKTtYzfZfVEZUIyWk97DFCQcjQklztv371/20ghBp4fAoIb3AuyEwQqSilu4jBO7YgHeypHJaPYN7QMA5/qlmQ==');
define('AUTH_SALT',        'Mc3ALj/H2uhslW+wuTuYLEv6kWXfu7wB4t7E1aG5yAcY+k45hKVXHn+fouXqbhXMQBBRjMKaVZwpL/jbRX954g==');
define('SECURE_AUTH_SALT', 'XNKbNhE+DD0P3icgZdh4Ti4bE4ToopHhewnkESu3qjc0Mb9vyfEoxENIqVT1A4P2wG884fDvKFJ8QizT8iSJKg==');
define('LOGGED_IN_SALT',   '0URSFuN9pWyVOF6I+P/XbzJjSewR4HRWdRb9ybYi0Uh1eiPl1Hp1A3qL5cZZffkzKnBlnJ0MRWvTHJiZcZBwPg==');
define('NONCE_SALT',       'QB9GW93JL65nfKMzO+sCUmvvYJVTAUCzcMhAPVb65pTgWx+BVw1SNjpMCtnjt+xG5Kh18sVGz/sSF2lYDpX5RQ==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

define( 'WP_DEBUG',            true );
define( 'WP_DEBUG_DISPLAY',    true );
define( 'WP_DEBUG_LOG',        true );
define( 'ALLOW_UNFILTERED_UPLOADS', true );


define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
