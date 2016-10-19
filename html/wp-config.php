<?php

/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'l8372309100676');

/** MySQL database username */
define('DB_USER', 'l8372309100676');

/** MySQL database password */
define('DB_PASSWORD', 'lsX-|3_}aN8');

/** MySQL hostname */
define('DB_HOST', 'l8372309100676.db.2309100.hostedresource.com:3306');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'wF$DN+m=xO_-YwhP#S1w');
define('SECURE_AUTH_KEY',  '&JF(H4 SL)B3gzOP0LL6');
define('LOGGED_IN_KEY',    'nc=0Vf-nR4K5Pn$q+IpO');
define('NONCE_KEY',        'v+8Ew+$%wyHUFU$W11wy');
define('AUTH_SALT',        '-t31NS4*%Xc9IEt8C0f9');
define('SECURE_AUTH_SALT', 'kr$&c %!Q#hCXfqT6zWy');
define('LOGGED_IN_SALT',   '43zP5)SRYf2MSLy!9jA=');
define('NONCE_SALT',       '3B&An&Lb6n5vSmmx/UFc');
define('WP_MEMORY_LIMIT', '256M');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_5d9h6fmbq7_';
define( 'FORCE_SSL_LOGIN', 1 );
define( 'FORCE_SSL_ADMIN', 1 );

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);
require_once( dirname( __FILE__ ) . '/gd-config.php' );
define( 'FS_METHOD', 'direct');
define('FS_CHMOD_DIR', (0705 & ~ umask()));
define('FS_CHMOD_FILE', (0604 & ~ umask()));


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');