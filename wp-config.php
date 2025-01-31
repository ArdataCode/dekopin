<?php

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings
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
define( 'DB_NAME', "landingu_dkopin" );

/** Database username */
define( 'DB_USER', "landingu_dkpin" );

/** Database password */
define( 'DB_PASSWORD', "Ardata2024" );

/** Database hostname */
define( 'DB_HOST', "localhost" );

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
define( 'AUTH_KEY',          'dG}$u<;=!}@/k`m*C<?v5J1;tmm2[_-kf-X/5H:Ni!Vn&2|E5|`;P=h5O&+jC64>' );
define( 'SECURE_AUTH_KEY',   'Zp}:QmbHqv7Qz6:N;?#j3Sxsraf>_%<lY4pZ99jhV&@iuf~d(Wg:4LL:B#].Y}f4' );
define( 'LOGGED_IN_KEY',     '@K_*u3ceUM2Xef^Qh2X9G[{Uwp[9txc%:Pmxw.e}kjmc[pO!1kOh{6DOexnWDq:T' );
define( 'NONCE_KEY',         'j,j;<hM>vrk!iO0Zwtn:9+rZ:N<x-,_C_Ry-S:G1gW4fZB49Y{[xRyDL2)raUTdX' );
define( 'AUTH_SALT',         'm__7Cl`IsCG.NrWm12WvLL^>^CwJ%#Zy|_&fXjD0LhFij;*P`UBV)d=oKJ 8w>oS' );
define( 'SECURE_AUTH_SALT',  'M0!fgR^l4%e)(XkkJ+@E9Y$Ua>sok&J*dg!NLmWru3lz8AC`a#vb0f6DwYv-Ui,j' );
define( 'LOGGED_IN_SALT',    'iwyUEfaVqg-y~d=_ww^-~-9gdS$pz~I0TZlsWr_+9iDza.ZOY!#h_,0X9JYG+rld' );
define( 'NONCE_SALT',        '/>>ynFnWq8#4`/cs]8qO-p}XfJ?[ReUp[uMw*-vIJ&WT$Oe]9?RGGE+R]SHEL~ej' );
define( 'WP_CACHE_KEY_SALT', '5l@bX`cM`3CRc.?NTTj!Dhgu+g artp&Z}n}rYfHh|90lkaAI%{JJTk9y:aarRf%' );


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



define( 'FS_METHOD', 'direct' );
define( 'WP_DEBUG_DISPLAY', false );
define( 'WP_MEMORY_LIMIT', '256M' );
define( 'WP_MAX_MEMORY_LIMIT', '512M' );
define( 'WP_DEBUG_LOG', true );
define( 'CONCATENATE_SCRIPTS', false );
define( 'AUTOSAVE_INTERVAL', 600 );
define( 'WP_POST_REVISIONS', 5 );
define( 'EMPTY_TRASH_DAYS', 21 );
#define( 'DUPLICATOR_AUTH_KEY', 'c!_<MZ1? QM68CD4WK}55x)|&w8d6vN)%_s<LP[dwtH4|,H]98_N,BV<L+Q,<iD;' );
#define( 'WP_PLUGIN_DIR', '/home/u1567848/public_html/dekopin.or.id/wp-content/plugins' );
#define( 'WPMU_PLUGIN_DIR', '/home/u1567848/public_html/dekopin.or.id/wp-content/mu-plugins' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname(__FILE__) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
