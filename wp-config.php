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
define('DB_NAME', 'madis_estoniancrafts');

/** MySQL database username */
define('DB_USER', 'madis');

/** MySQL database password */
define('DB_PASSWORD', '4u15EnVc');

/** MySQL hostname */
define('DB_HOST', '127.0.0.1');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('FS_METHOD', 'direct'); // Allows you to install plugin without FTP details

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '/-Ji~?I;+3 KfsAP%64A+UT08SmV}-8wmrXYfA6!04g&2~q0/A. lRuU_gutUUdz');
define('SECURE_AUTH_KEY',  '!i:w?[rfao4#f,O+-)Q ,BD`dk}MuT9D%[S(F@IkWf_1z&V}9pI4>U#<7kj=Sl-^');
define('LOGGED_IN_KEY',    '[6ZKKXiZ+,Fj:=iV,2^m]W]9aharIKQS#Ji#.>  gU%p,MH$@>U%Q32W>8D]uulE');
define('NONCE_KEY',        '=JBO,|g+h$R$?D(GNp1fYGoA!_f8G93`up|+b5UL.MD]3>03]7vk`>qz?g$U8.G4');
define('AUTH_SALT',        '+ Z?5M tF(;VL*J5ur6=_}y@e1IGU]~:lukHE;g<#^viI?)uUAt34X430YwE q0r');
define('SECURE_AUTH_SALT', ')l$EO3W31*@{$.0LYW;/H}~|vt%-L_V0Nl{ipG]3&H[g B?3;kNH2NwcT/M%{H}E');
define('LOGGED_IN_SALT',   'c9]!WP+O]1!{<wd!/n.xxup[ ,Oz<|Lt]dUUM]8kJ4-gN)C*-khrnT86*I{_k`7R');
define('NONCE_SALT',       'Lw)rSJ:pv-zhL7wgK+>M0[-V73>K`juF[)Pnn7JKtxx f;18 uJxZWcynXP4^+p(');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */s<
$table_prefix  = 'ktt_';

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
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_MEMORY_LIMIT', '256M');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
