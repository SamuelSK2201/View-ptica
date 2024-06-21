<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'viewop05_wp' );

/** Database username */
define( 'DB_USER', 'viewop05_wp' );

/** Database password */
define( 'DB_PASSWORD', 'DT]06pS3B]' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'omxhxtgpvxeg13tkmrgznksjlgv5kswdskixpmnm8htndz7v5qkpjp0fxmlwcjas' );
define( 'SECURE_AUTH_KEY',  'fqolfom4hgwf4kn89iu7ok8w9hi4ays8hstkjlvtwrrooo3ogoa5uktyajyrldr1' );
define( 'LOGGED_IN_KEY',    'jtywcxkjcwem6rrrj8cxz5cyzbr7getq1wbjx336ty7xczx4cmhufyacwbwosjby' );
define( 'NONCE_KEY',        'pqrwdu7fxtpv7mexty8kh9moftnq6ibhw8p2esogch82t8xueiz0ibbhge5ulv4c' );
define( 'AUTH_SALT',        'okm1f3upknreu84rz8l9engzwq7iouh4aey7sncudmgtajzbc1ghtzqelqnbjpok' );
define( 'SECURE_AUTH_SALT', 'k5mkenur6kr4ndpuaxfkgddqziimduqg91xbdiikxsgg8babqj0ppf7pcj5jpl3z' );
define( 'LOGGED_IN_SALT',   '3bqckunpyn8nfdes84g6amb6xzxzprj4scdnpet8vvdes5o3ej4u5uk5ydssywtj' );
define( 'NONCE_SALT',       'wd3maacwcyk3ij9lzjm7i0gfduyynrmwgnyhrml4nsgmewruas3inul2s1yddcbh' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
