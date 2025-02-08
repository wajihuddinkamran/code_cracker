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
define( 'DB_NAME', 'my_portfolio' );

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
define( 'AUTH_KEY',         '9oeO?>@<;`eO3:^_ie9Q74{d:7=F)9Y%1D=m0_^w ;,]Icr]=]Lpcx2ZqN#v=-O8' );
define( 'SECURE_AUTH_KEY',  '{Z_rC;F{cDf2NLvwm1QK/ICw1UOdZEG@>I<P6U(jzkd<RM^fQB5ZV@3i4`r-h8*J' );
define( 'LOGGED_IN_KEY',    'b?k<_(8~x! Ko):<v@35&6h~aX#QSivGbaPFpy 2|8^K1mu5RM*%wwH&FlV@U#:&' );
define( 'NONCE_KEY',        '9M:}txMPbt`s{p`f ;h<sSS|Q8q#Qcm|dYru5hm;*5=Ty18JpgpZu7h~1ft%rqhf' );
define( 'AUTH_SALT',        'g-kdg&JZ5_R^F:-);43G@6p_:{XQodfeEJU.vPqUW:gN:/egO#3f&/,A)~Na5Kw/' );
define( 'SECURE_AUTH_SALT', 'f&a:MlIjhO,&~gPg>tdF,-C8+$A5=KH-fP]g_Qvw#n/ADKJs02V?*=-|ru_^qCRk' );
define( 'LOGGED_IN_SALT',   '{kNk0L<66$i`T&%B!kFt=> iUxa>vfSyBNu(=^`%5,JgC.[cg7 B)6^ptU[P|Q G' );
define( 'NONCE_SALT',       'hZE+cOD+[^dtWhpQ82JvdX/aC>[:xX//$dxJTkR:JJ-k&s{X$.]>cj-qW!M9c0(B' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
