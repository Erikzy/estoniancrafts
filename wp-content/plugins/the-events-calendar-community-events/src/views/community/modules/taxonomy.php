<?php
/**
 * Event Submission Form Taxonomy Block
 * Renders the taxonomy field in the submission form.
 *
 * Override this template in your own theme by creating a file at
 * [your-theme]/tribe-events/community/modules/taxonomy.php
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

$event_cats = get_terms( Tribe__Events__Main::TAXONOMY, array( 'hide_empty' => false ) );
$taxonomy_label = sprintf( esc_html__( '%s Categories', 'tribe-events-community' ), tribe_get_event_label_singular() );

// only display categories if there are any
if ( ! empty( $event_cats ) ) {
	?>
	<!-- Event Categories -->
	<?php do_action( 'tribe_events_community_before_the_categories' ); ?>
	<div id="event_taxonomy" class="tribe-events-community-details event-taxonomy eventForm bubble">
		<div class="tribe_sectionheader">
			<h4 tabindex="0"><?php tribe_community_events_field_label( 'taxonomy', $taxonomy_label ); ?></h4>
		</div>
		<?php Tribe__Events__Community__Modules__Taxonomy_Block::instance()->the_category_checklist( get_post() ); ?>
	</div><!-- .tribe-events-community-details -->
	<?php
	do_action( 'tribe_events_community_after_the_categories' );
}
