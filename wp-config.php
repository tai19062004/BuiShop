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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'buishop' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

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
define( 'AUTH_KEY',         'vm-?&/TMf,Okf.sJ2pnM^/v}5T=a,j]EeY`~ N[[3ig>50_Ex,P2&SnGB{KfEL$G' );
define( 'SECURE_AUTH_KEY',  'zua<{!)Ta60j!5{Pp^R>WXA?>nAyIvf0#p=]&R.v.$yBU@^LKi4jMDkz/]+0wPJf' );
define( 'LOGGED_IN_KEY',    '_RiPr0{+F+DJ4W@1qQh``.h+Kpe`6kDk9ksM@Y! aM=.&{0oP4E|P.DJzpBa5x&K' );
define( 'NONCE_KEY',        '-JWKL=1B4+*LS2,)W @X_uTc=}adL>;8x%xRxyPCRtZ#[9wXXpCy&KHb(16u(>uh' );
define( 'AUTH_SALT',        '5_8WytFa(U@-S|l:56J18; (E5^i#jlsU_2`DypQld}ni#JmX3TPCkh( IO{+xw+' );
define( 'SECURE_AUTH_SALT', '?iQ)=flxjzL(o&8`;X%bDgfC[Dc 5Omme*WKUd>5`M8Ui08`Ky!U/4+KEsYbj<tZ' );
define( 'LOGGED_IN_SALT',   '~BIn4Fz~>9{pHU7^S_.Z6;qCkOcTnF2/|D0yesO#o_bNE:|ng1PWNOm.0xGVP_fk' );
define( 'NONCE_SALT',       '!WWWb)r@~v=M(gAV50?U2o4teVxBH,f!]#,uOzqXyNZp,|6+)x]PwmqrG<fP%ia:' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
// Enable Debug logging to the /wp-content/debug.log file
define( 'WP_DEBUG_LOG', true );

// Disable display of errors and warnings
define( 'WP_DEBUG_DISPLAY', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
