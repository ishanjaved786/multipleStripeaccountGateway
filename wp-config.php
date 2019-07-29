<?php
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
define( 'DB_NAME', 'bUWjlR5R8d' );

/** MySQL database username */
define( 'DB_USER', 'bUWjlR5R8d' );

/** MySQL database password */
define( 'DB_PASSWORD', 'k377SIB2Qd' );

/** MySQL hostname */
define( 'DB_HOST', 'remotemysql.com' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '5;2 %p*AG0bYMQF;1n!s:P@sBEDVA *X1 Cfx_08b_*YBc)/K[G9HG!Y(r<O,[C;' );
define( 'SECURE_AUTH_KEY',  '/wah/MytuL3^xPjtB@bI$;QI:5,/9@qz^^EM~}Fp6Te]%ryXzHBqz+?P5q;Ce:ja' );
define( 'LOGGED_IN_KEY',    'Kt*.)B$bvU{R<U!h|<6lUU0CeoB:sMX@ 2SW0eITD4QEC)*9.0ecBZ<U%<7Fs2&`' );
define( 'NONCE_KEY',        'u>Mk2xncd+]?(P,Ccq0[12#8QJ;f#TG{>jWst]pN6/dcBqdgUa&R1ieufXND)Kcj' );
define( 'AUTH_SALT',        '+1+TZx!DY.*h)v#t$t;yLx|G >c z2Kp7&4I*;z;ab]+8.H~;jiyHW2=N^lq)F$g' );
define( 'SECURE_AUTH_SALT', 'F.HbSr[;fy|9qL;grYE]FYXYlUBO|_=zN(AZf}[gQ]b#$FAgLrP!1-`KsVs @r|n' );
define( 'LOGGED_IN_SALT',   '}RTdFB[0#mzGIibA=3_@Tjo*389IxJ,+#Agf%jw;C3Iql>+X[(CJ u52b/Zd&VH1' );
define( 'NONCE_SALT',       'R4}FP#P0=tK|~~1J<=7V*Vt|YQC4>RJgCP+G1l<4g6Z6|ew4>:$^+LPtNgyyqanM' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once( ABSPATH . 'wp-settings.php' );
