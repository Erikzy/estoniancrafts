<?php
/**
 * Event Submission Form Website Block
 * Renders the website fields in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/website.php
 *
 * @package Tribe__Events__Community__Main
 * @since  3.1
 * @version 4.4
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// If posting back, then use $POST values
if ( ! $_POST ) {
	$event_url = function_exists( 'tribe_get_event_website_url' ) ? tribe_get_event_website_url() : tribe_community_get_event_website_url();
} else {
	$event_url = isset( $_POST['EventURL'] ) ? esc_attr( $_POST['EventURL'] ) : '';
}
?>

<!-- Event Website -->
<?php do_action( 'tribe_events_community_before_the_website' ); ?>

<div id="event_website" class="tribe-events-community-details eventForm bubble event-website">
	<div class="tribe_sectionheader">
		<h4 tabindex="0"><?php printf( __( '%s Website', 'tribe-events-community' ), tribe_get_event_label_singular() ); ?></h4>
	</div><!-- .tribe_sectionheader -->

	<div class="event-website-details">
		<?php tribe_community_events_field_label( 'EventURL', __( 'URL:', 'tribe-events-community' ) ); ?>
		<input type="text" id="EventURL" name="EventURL" size="25" value="<?php echo esc_url( $event_url ); ?>" />
	</div>
	<?php do_action( 'tribe_events_community_after_the_website' );?>
</div>