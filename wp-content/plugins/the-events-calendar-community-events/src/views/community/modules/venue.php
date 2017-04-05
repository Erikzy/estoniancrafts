<?php
/**
 * Event Submission Form Metabox For Venues
 * This is used to add a metabox to the event submission form to allow for choosing or
 * creating a venue for user submitted events.
 *
 * This is ALSO used in the Venue edit view. Be careful to test changes in both places.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/venue.php
 *
 * @package Tribe__Events__Community__Main
 * @since  2.1
 * @version 4.4
 * @author Modern Tribe Inc.
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

// We need the variables here otherwise it will throw notices
$venue_label_singular = tribe_get_venue_label_singular();

if ( ! isset( $event ) ) {
	$event = null;
}
?>

<!-- Venue -->
<div id="event_tribe_venue" class="tribe-events-community-details eventForm bubble tribe-event-venue">
	<div class="tribe_sectionheader sectionheader-label" tabindex="0">
		<label class="<?php echo tribe_community_events_field_has_error( 'venue' ) ? 'error' : ''; ?>">
			<?php
			printf( esc_html__( '%s Details', 'tribe-events-community' ), $venue_label_singular );
			echo tribe_community_required_field_marker( 'venue' );
			?>
		</label>
	</div>
	<div class="event-community-venue">
		<table class="tribe-community-event-info" role="presentation">
			<colgroup>
				<col style="width:33%">
				<col style="width:67%">
			</colgroup>

			<?php
			tribe_community_events_venue_select_menu( $event );

			// The organizer meta box will render everything within a <tbody>
			$metabox = new Tribe__Events__Linked_Posts__Chooser_Meta_Box( $event, Tribe__Events__Venue::POSTTYPE );
			$metabox->render();
			?>
		</table><!-- #event_venue -->
	</div>
</div>