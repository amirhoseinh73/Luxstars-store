<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'store_wholesale' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          'V+Rz>l]+*mL>f8=1N@Vk1(CP?!B=:i}`Q[0.}d5l3QE5MT&U2^6I~7|?V*.M->qB' );
define( 'SECURE_AUTH_KEY',   'X~m:@/,GEz%fxB2|ux+qc#q./e/f8;6+D}(=c5gI(@J3?zk1y.FUg<96zq3:EEgt' );
define( 'LOGGED_IN_KEY',     'I)B65EB<X]azcp~[U=::<CwpbAPa@m%ge-J2_GoxH.(w:~ET-+%rhN<dW_fDYRxs' );
define( 'NONCE_KEY',         '^7][>U1800X3.P97Tl?y+~l=&nv%<^</Y-^)a!3#Ij6m^Gpd3#qFU RN!7H-<Hw^' );
define( 'AUTH_SALT',         'k/31.t`ei&SOl}=5-9pB#m+@#;XDmGS46>rNu}VA|2^ZE&0!t>z[btHO` ANvZem' );
define( 'SECURE_AUTH_SALT',  'vXGesVj#jIY@reM2x#/SuG>rut9X*8M=6R`z]6ZK%|9e6/DCje+ gPPd^:IB*=la' );
define( 'LOGGED_IN_SALT',    '?4sM=#5`.E&r_PWlOMsKRq[cKD[MNN~h?KINiCZ^~{Q.0C<w,[6$6bMMi5bCxu&^' );
define( 'NONCE_SALT',        '!IGg8.-O$pR43F,<;Bzs%.uBoqX~!xur-X^f}w%M747W=6z*r jAPGQ7F;MUEz)+' );
define( 'WP_CACHE_KEY_SALT', '?/nnFI#c)?f;}U~=_4GwzbdG0 zWcsh>4c,[!8x{Zjk2Ek/Xxri}5Zg8#ot6w1D`' );


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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
