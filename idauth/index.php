<<<<<<< HEAD
<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

echo '<pre>';
print_r($_SERVER);
exit;

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/../wp-blog-header.php' );
=======
<?php
/**
 * Front to the WordPress application. This file doesn't do anything, but loads
 * wp-blog-header.php which does and tells WordPress to load the theme.
 *
 * @package WordPress
 */

/**
 * Tells WordPress to load the WordPress theme and output it.
 *
 * @var bool
 */
define('WP_USE_THEMES', true);

echo '<pre>';
print_r($_SERVER);
exit;

/** Loads the WordPress Environment and Template */
require( dirname( __FILE__ ) . '/../wp-blog-header.php' );
>>>>>>> d76b2e7ccde0dbc4308b5918fbd215fcea94b08d